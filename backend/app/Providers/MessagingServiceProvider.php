<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\MessageService;
use App\Services\ConversationService;
use App\Services\VoiceCallService;

class MessagingServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(MessageService::class, function ($app) {
            return new MessageService();
        });
        
        $this->app->singleton(ConversationService::class, function ($app) {
            return new ConversationService();
        });
        
        $this->app->singleton(VoiceCallService::class, function ($app) {
            return new VoiceCallService();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
