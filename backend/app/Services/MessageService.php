<?php

namespace App\Services;

use App\Models\User;
use App\Models\Message;
use App\Models\Conversation;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class MessageService
{
    /**
     * Send a message in a conversation
     */
    public function sendMessage(
        User $sender, 
        Conversation $conversation, 
        string $content, 
        string $type = 'text', 
        ?array $metadata = null
    ): Message {
        // Validate sender is part of the conversation
        if (!$conversation->isParticipant($sender->id)) {
            throw new \Exception('المرسل ليس جزءًا من المحادثة');
        }

        // Create message
        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $sender->id,
            'content' => $content,
            'type' => $type,
            'status' => 'sent',
            'metadata' => $metadata
        ]);

        // Update conversation's last message
        $conversation->update([
            'last_message_id' => $message->id
        ]);

        // Log message
        Log::info('Message sent', [
            'message_id' => $message->id,
            'conversation_id' => $conversation->id,
            'sender_id' => $sender->id,
            'type' => $type
        ]);

        // Trigger notification to other participants
        $this->notifyParticipants($message);

        return $message;
    }

    /**
     * Send an image message
     */
    public function sendImageMessage(
        User $sender, 
        Conversation $conversation, 
        string $imagePath
    ): Message {
        // Validate image
        if (!Storage::exists($imagePath)) {
            throw new \Exception('الملف غير موجود');
        }

        return $this->sendMessage(
            $sender, 
            $conversation, 
            $imagePath, 
            'image', 
            ['file_path' => $imagePath]
        );
    }

    /**
     * Mark messages as read for a user in a conversation
     */
    public function markMessagesAsRead(User $user, Conversation $conversation)
    {
        // Find unread messages in this conversation
        $unreadMessages = Message::where('conversation_id', $conversation->id)
            ->where('status', '!=', 'read')
            ->where('sender_id', '!=', $user->id)
            ->get();

        // Mark messages as read
        $unreadMessages->each(function($message) {
            $message->markAsRead();
        });

        Log::info('Messages marked as read', [
            'conversation_id' => $conversation->id,
            'user_id' => $user->id,
            'messages_count' => $unreadMessages->count()
        ]);

        return $unreadMessages->count();
    }

    /**
     * Delete a message
     */
    public function deleteMessage(User $user, Message $message)
    {
        // Validate user is the sender
        if ($message->sender_id !== $user->id) {
            throw new \Exception('لا يمكنك حذف رسالة لم ترسلها');
        }

        // Soft delete or actual delete based on requirements
        $message->delete();

        Log::info('Message deleted', [
            'message_id' => $message->id,
            'user_id' => $user->id
        ]);

        return true;
    }

    /**
     * Create a new conversation
     */
    public function createConversation(
        User $initiator, 
        array $participantIds, 
        ?string $title = null
    ): Conversation {
        // Ensure initiator is included
        $participantIds = array_unique(
            array_merge([$initiator->id], $participantIds)
        );

        // Create conversation
        $conversation = Conversation::create([
            'type' => count($participantIds) > 2 ? 'group' : 'single',
            'title' => $title
        ]);

        // Attach participants
        $conversation->participants()->attach($participantIds);

        Log::info('Conversation created', [
            'conversation_id' => $conversation->id,
            'type' => $conversation->type,
            'participants_count' => count($participantIds)
        ]);

        return $conversation;
    }

    /**
     * Notify conversation participants about a new message
     */
    private function notifyParticipants(Message $message)
    {
        // Placeholder for WebSocket or push notification logic
        // In a real-world scenario, this would send real-time notifications
        $participants = $message->conversation->participants
            ->where('id', '!=', $message->sender_id);

        Log::info('Participants notification triggered', [
            'message_id' => $message->id,
            'participants_count' => $participants->count()
        ]);
    }

    /**
     * Get conversation messages
     */
    public function getConversationMessages(
        Conversation $conversation, 
        int $limit = 50, 
        ?int $beforeId = null
    ) {
        $query = Message::where('conversation_id', $conversation->id)
            ->orderBy('created_at', 'desc');

        if ($beforeId) {
            $query->where('id', '<', $beforeId);
        }

        return $query->limit($limit)->get()->reverse();
    }
}
