<?php
namespace App\Services;

use App\Models\ChatMessage;

class ChatSystemMessageService
{
    public function goal($match)
    {
        $text = "⚽ GOAL — {$match->homeTeam->name} {$match->home_score} - {$match->away_score} {$match->awayTeam->name}";

        ChatMessage::create([
            'user_id' => null,
            'match_id' => $match->id,
            'message' => $text,
            'type' => 'system',
            'is_hidden' => false,
        ]);
    }
}