<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    protected $table = 'chat_messages';

    protected $fillable = ['sender_id', 'receiver_id', 'body', 'read_at'];

    protected $casts = ['read_at' => 'datetime'];

    /** All messages between two users, either direction. */
    public function scopeBetween($query, int $a, int $b)
    {
        return $query->where(function ($q) use ($a, $b) {
            $q->where(fn ($x) => $x->where('sender_id', $a)->where('receiver_id', $b))
              ->orWhere(fn ($x) => $x->where('sender_id', $b)->where('receiver_id', $a));
        });
    }
}
