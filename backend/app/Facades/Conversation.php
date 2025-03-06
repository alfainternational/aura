<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;
use App\Services\ConversationService;

/**
 * @method static \App\Models\Conversation createConversation(\App\Models\User $creator, array $participantIds, ?string $title = null, ?string $avatar = null, bool $isGroup = false)
 * @method static \App\Models\Conversation|null findDirectConversation(int $userId1, int $userId2)
 * @method static bool addParticipants(\App\Models\Conversation $conversation, array $userIds, \App\Models\User $addedBy)
 * @method static bool removeParticipant(\App\Models\Conversation $conversation, int $userId, \App\Models\User $removedBy)
 * @method static \App\Models\Conversation updateConversation(\App\Models\Conversation $conversation, array $data, \App\Models\User $updatedBy)
 * @method static bool changeParticipantRole(\App\Models\Conversation $conversation, int $userId, string $role, \App\Models\User $changedBy)
 * 
 * @see \App\Services\ConversationService
 */
class Conversation extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return ConversationService::class;
    }
}
