<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;
use App\Services\MessageService;

/**
 * @method static \App\Models\Message createMessage(\App\Models\Conversation $conversation, \App\Models\User $sender, string $content, ?\Illuminate\Http\UploadedFile $media = null)
 * @method static bool updateMessageStatus(\App\Models\Message $message, string $status)
 * @method static bool deleteMessage(\App\Models\Message $message, \App\Models\User $user, bool $forEveryone = false)
 * 
 * @see \App\Services\MessageService
 */
class Message extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return MessageService::class;
    }
}
