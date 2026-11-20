<?php

namespace App\Services;

use App\ChatMessage;
use App\Interest;
use App\User;
use Illuminate\Support\Facades\Log;

/**
 * Rules and plumbing for member-to-member chat.
 *
 *  - Who may chat: only two members where one has ACCEPTED the other's interest (interest.interest_back = 1), in either direction.
 *  - Real time: messages are pushed through Pusher (keys in .env). If Pusher is not configured or is down, nothing breaks:
 *    the chat page also polls, so messages still arrive a few seconds later.
 */
class ChatService
{
    public const MAX_LENGTH = 1000;

    /** True when both ends of the interest are known and the receiver accepted. */
    public function canChat(User $a, User $b): bool
    {
        if ((int) $a->id === (int) $b->id) {
            return false;
        }
        return Interest::where('interest_back', 1)
            ->where(function ($q) use ($a, $b) {
                $q->where(fn ($x) => $x->where('sender', $a->id)->where('receiver', $b->id))
                  ->orWhere(fn ($x) => $x->where('sender', $b->id)->where('receiver', $a->id));
            })->exists();
    }

    /** Per-request memo of each member's accepted-interest partners, so a list of 24 cards costs one query, not 24. */
    private static array $partnerMemo = [];

    /** canChat() by user id, for lists of cards (one query per page instead of one per card). */
    public function canChatWithId(User $me, int $otherId): bool
    {
        if ((int) $me->id === $otherId) {
            return false;
        }
        try {
            $ids = self::$partnerMemo[$me->id] ??= $this->partnerIds($me);
        } catch (\Throwable $e) {
            return false;   // a chat problem must never take a member list down
        }
        return in_array($otherId, $ids, true);
    }

    /** user ids this member has an accepted interest with (the people they may chat with). */
    public function partnerIds(User $me): array
    {
        $ids = Interest::where('interest_back', 1)
            ->where(fn ($q) => $q->where('sender', $me->id)->orWhere('receiver', $me->id))
            ->get(['sender', 'receiver'])
            ->map(fn ($i) => (int) ((int) $i->sender === (int) $me->id ? $i->receiver : $i->sender))
            ->all();
        return array_values(array_unique($ids));
    }

    public function unreadCount(User $me): int
    {
        try {
            return ChatMessage::where('receiver_id', $me->id)->whereNull('read_at')->count();
        } catch (\Throwable $e) {
            return 0;   // e.g. the chat migration has not been run yet: never take a dashboard page down for a badge
        }
    }

    /** The private Pusher channel every member listens on for their own chats. */
    public static function channelFor(int $userId): string
    {
        return 'private-chat-user-' . $userId;
    }

    /** Public shape of one message, shared by the JSON endpoints and the realtime payload. */
    public function present(ChatMessage $m, User $me): array
    {
        return [
            'id'      => $m->id,
            'mine'    => (int) $m->sender_id === (int) $me->id,
            'body'    => $m->body,
            'at'      => $m->created_at->toIso8601String(),
            'read'    => $m->read_at !== null,
        ];
    }

    /** "reverb" (our own server) or "pusher" (hosted), from CHAT_REALTIME in .env. */
    public function driver(): string
    {
        return config('broadcasting.chat_driver') === 'reverb' ? 'reverb' : 'pusher';
    }

    /** The connection settings of the chosen service, or null when its keys are not filled in. */
    private function connection(): ?array
    {
        $c = config('broadcasting.connections.' . $this->driver());
        if (empty($c['key']) || empty($c['secret']) || empty($c['app_id'])) {
            return null;
        }
        if ($this->driver() === 'reverb' && empty($c['options']['host'])) {
            return null;
        }
        return $c;
    }

    /** Server-side client used to publish events (Pusher's PHP library talks to Reverb too: same protocol). */
    public function pusher(): ?\Pusher\Pusher
    {
        $c = $this->connection();
        if (!$c || !class_exists(\Pusher\Pusher::class)) {
            return null;
        }
        $o = $c['options'] ?? [];
        try {
            $options = $this->driver() === 'reverb'
                ? ['host' => $o['host'], 'port' => (int) ($o['port'] ?? 443), 'scheme' => $o['scheme'] ?? 'https', 'useTLS' => ($o['scheme'] ?? 'https') === 'https', 'timeout' => 3]
                : ['cluster' => $o['cluster'] ?? 'mt1', 'useTLS' => true, 'timeout' => 3];
            return new \Pusher\Pusher($c['key'], $c['secret'], $c['app_id'], $options);
        } catch (\Throwable $e) {
            Log::warning('Chat: could not create the realtime client: ' . $e->getMessage());
            return null;
        }
    }

    /** Public settings for the browser (key / host are public by design; the secret never leaves the server). */
    public function clientConfig(): ?array
    {
        $c = $this->connection();
        if (!$c) {
            return null;
        }
        $o = $c['options'] ?? [];
        if ($this->driver() === 'reverb') {
            $tls = ($o['scheme'] ?? 'https') === 'https';
            return [
                'driver' => 'reverb', 'key' => $c['key'], 'cluster' => 'mt1',   // pusher-js insists on a cluster name; Reverb ignores it
                'wsHost' => $o['host'], 'wsPort' => (int) ($o['port'] ?? ($tls ? 443 : 80)), 'forceTLS' => $tls,
            ];
        }
        return ['driver' => 'pusher', 'key' => $c['key'], 'cluster' => $o['cluster'] ?? 'mt1'];
    }

    /** Push a new message to both people (the sender's other tabs/devices too). Never throws. */
    public function broadcast(ChatMessage $m, User $sender, User $receiver): void
    {
        $pusher = $this->pusher();
        if (!$pusher) {
            return;
        }
        try {
            $pusher->trigger(self::channelFor($receiver->id), 'chat.message', [
                'from' => $sender->dataid, 'message' => $this->present($m, $receiver),
            ]);
            $pusher->trigger(self::channelFor($sender->id), 'chat.message', [
                'from' => $receiver->dataid, 'message' => $this->present($m, $sender),
            ]);
        } catch (\Throwable $e) {
            Log::warning('Chat: realtime push failed (polling will still deliver): ' . $e->getMessage());
        }
    }

    /** Tell the other person's open window that their messages were read. Never throws. */
    public function broadcastRead(User $reader, User $author): void
    {
        $pusher = $this->pusher();
        if (!$pusher) {
            return;
        }
        try {
            $pusher->trigger(self::channelFor($author->id), 'chat.read', ['by' => $reader->dataid]);
        } catch (\Throwable $e) {
            Log::warning('Chat: read-receipt push failed: ' . $e->getMessage());
        }
    }
}
