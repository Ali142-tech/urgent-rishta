<?php

namespace App\Http\Controllers;

use App\ChatMessage;
use App\Services\ChatService;
use App\User;
use Illuminate\Http\Request;

/**
 * Member-to-member chat (see ChatService for the rules). The page is a shell; everything else is small JSON endpoints.
 */
class ChatController extends Controller
{
    public function __construct(private ChatService $chat)
    {
        $this->middleware(['auth', 'verified']);
    }

    /** The chat page. ?with=DATAID opens that conversation straight away. */
    public function index(Request $request)
    {
        $me = User::retrieveUserObject();
        return view('member.chat', [
            'meDataid' => $me->dataid,
            'meId'     => $me->id,
            'open'     => (string) $request->query('with', ''),
            'realtime' => $this->chat->clientConfig(),
        ]);
    }

    /** Left-hand list: everyone I may chat with, or have chatted with, newest conversation first. */
    public function conversations()
    {
        $me = User::retrieveUserObject();
        $mine = ChatMessage::where(fn ($q) => $q->where('sender_id', $me->id)->orWhere('receiver_id', $me->id))
            ->orderByDesc('id')->limit(1000)->get();

        $last = [];
        $unread = [];
        foreach ($mine as $m) {
            $other = (int) ((int) $m->sender_id === (int) $me->id ? $m->receiver_id : $m->sender_id);
            $last[$other] ??= $m;
            if ((int) $m->receiver_id === (int) $me->id && $m->read_at === null) {
                $unread[$other] = ($unread[$other] ?? 0) + 1;
            }
        }

        $ids = array_values(array_unique(array_merge($this->chat->partnerIds($me), array_keys($last))));
        $users = User::whereIn('id', $ids)->get()->keyBy('id');

        $rows = [];
        foreach ($ids as $id) {
            $u = $users->get($id);
            if (!$u) {
                continue;
            }
            $m = $last[$id] ?? null;
            $rows[] = [
                'dataid'   => $u->dataid,
                'name'     => $u->first_name,
                'avatar'   => $this->avatar($u),
                'last'     => $m ? mb_strimwidth(((int) $m->sender_id === (int) $me->id ? 'You: ' : '') . $m->body, 0, 60, '…') : 'Say salaam 👋',
                'at'       => $m ? $m->created_at->toIso8601String() : null,
                'unread'   => $unread[$id] ?? 0,
                'can_chat' => $this->chat->canChat($me, $u),
            ];
        }
        usort($rows, fn ($a, $b) => strcmp((string) $b['at'], (string) $a['at']));

        return response()->json(['conversations' => $rows, 'unread' => array_sum($unread)]);
    }

    /** Messages with one member. ?after=ID returns only newer ones (used by polling). Opening a thread marks theirs as read. */
    public function messages(Request $request, string $dataid)
    {
        $me = User::retrieveUserObject();
        $other = User::where('dataid', $dataid)->first();
        if (!$other || (int) $other->id === (int) $me->id) {
            return response()->json(['message' => 'Member not found.'], 404);
        }

        $q = ChatMessage::between($me->id, $other->id);
        if ($after = (int) $request->query('after', 0)) {
            $q->where('id', '>', $after);
        } else {
            $q->where('id', '>', 0);
        }
        // the latest 200 on first load, oldest-first for display
        $rows = $after ? $q->orderBy('id')->limit(200)->get() : $q->orderByDesc('id')->limit(200)->get()->reverse()->values();

        $read = ChatMessage::where('sender_id', $other->id)->where('receiver_id', $me->id)->whereNull('read_at')
            ->update(['read_at' => now()]);
        if ($read) {
            $this->chat->broadcastRead($me, $other);
        }

        return response()->json([
            'member'   => ['dataid' => $other->dataid, 'name' => $other->first_name, 'avatar' => $this->avatar($other)],
            'can_chat' => $this->chat->canChat($me, $other),
            'messages' => $rows->map(fn ($m) => $this->chat->present($m, $me))->all(),
            'peer_read_up_to' => (int) ChatMessage::where('sender_id', $me->id)->where('receiver_id', $other->id)->whereNotNull('read_at')->max('id'),
        ]);
    }

    public function send(Request $request, string $dataid)
    {
        $me = User::retrieveUserObject();
        $other = User::where('dataid', $dataid)->first();
        if (!$other) {
            return response()->json(['message' => 'Member not found.'], 404);
        }
        if (!$this->chat->canChat($me, $other)) {
            return response()->json(['message' => 'Chat opens once one of you has accepted the other\'s interest.'], 403);
        }

        $body = trim((string) $request->input('body', ''));
        $body = preg_replace("/\r\n?/", "\n", $body);
        $body = preg_replace("/\n{3,}/", "\n\n", $body);
        if ($body === '') {
            return response()->json(['message' => 'Type a message first.'], 422);
        }
        if (mb_strlen($body) > ChatService::MAX_LENGTH) {
            return response()->json(['message' => 'Message is too long (max ' . ChatService::MAX_LENGTH . ' characters).'], 422);
        }

        $m = ChatMessage::create(['sender_id' => $me->id, 'receiver_id' => $other->id, 'body' => $body]);
        $this->chat->broadcast($m, $me, $other);

        return response()->json(['message' => $this->chat->present($m, $me)]);
    }

    /** Total unread, for the sidebar badge (also the polling fallback). */
    public function unread()
    {
        return response()->json(['unread' => $this->chat->unreadCount(User::retrieveUserObject())]);
    }

    /** Pusher private-channel authorisation: a member may only listen on their own channel. */
    public function pusherAuth(Request $request)
    {
        $me = User::retrieveUserObject();
        $channel = (string) $request->input('channel_name');
        $socket = (string) $request->input('socket_id');
        $pusher = $this->chat->pusher();

        if (!$pusher || $channel !== ChatService::channelFor($me->id) || $socket === '') {
            abort(403);
        }
        return response($pusher->authorizeChannel($channel, $socket))->header('Content-Type', 'application/json');
    }

    private function avatar(User $u): string
    {
        try {
            return (string) $u->getProfileImage(true);
        } catch (\Throwable $e) {
            return '';
        }
    }
}
