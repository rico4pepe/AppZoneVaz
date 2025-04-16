<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\ChatMessage;
use App\Models\User;
use App\Models\Report;
use App\Models\ChatBan;

class ChatModeration extends Component
{
    public $search = '';
    public $showReportedOnly = false;
    public $userMessages = [];
    public $viewingUserId = null;


    public function hideMessage($id)
    {
        $message = ChatMessage::find($id);
        if ($message) {
            $message->is_hidden = true;
            $message->save();
            session()->flash('message', 'Message hidden.');
        }
    }

    public function banUser($userId, $reason = 'Inappropriate chat')
{
    ChatBan::create([
        'user_id' => $userId,
        'reason' => $reason,
        'banned_until' => null,
        'issued_by' => auth()->id(),
    ]);

    session()->flash('message', 'User banned successfully.');
}

public function viewUserMessages($userId)
{
    $this->viewingUserId = $userId;
    $this->userMessages = ChatMessage::where('user_id', $userId)
        ->latest()
        ->take(20)
        ->get();
}

public function resolveReport($messageId, $status)
{
    Report::where('message_id', $messageId)
        ->where('status', 'pending')
        ->update(['status' => $status]);

    session()->flash('message', "Report marked as $status.");
}

    public function render()
    {
        $query = ChatMessage::with('user')->latest();

        if ($this->search) {
            $query->where('message', 'like', '%' . $this->search . '%');
        }

        if ($this->showReportedOnly) {
            $query->whereIn('id', Report::pluck('message_id'));
        }

        $messages = $query->take(50)->get();

        return view('livewire.chat-moderation', compact('messages'));
    }
}
