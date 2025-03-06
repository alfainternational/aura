<?php

namespace Database\Factories;

use App\Models\Conversation;
use App\Models\ConversationParticipant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ConversationParticipant>
 */
class ConversationParticipantFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ConversationParticipant::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'conversation_id' => function () {
                return Conversation::factory()->create()->id;
            },
            'user_id' => function () {
                return User::factory()->create()->id;
            },
            'status' => 'active',
            'muted_until' => null,
            'last_read_at' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Indicate that the participant has muted the conversation.
     *
     * @return static
     */
    public function muted()
    {
        return $this->state(fn (array $attributes) => [
            'muted_until' => now()->addDays(rand(1, 30)),
        ]);
    }

    /**
     * Indicate that the participant has read all messages.
     *
     * @return static
     */
    public function read()
    {
        return $this->state(fn (array $attributes) => [
            'last_read_at' => now(),
        ]);
    }

    /**
     * Indicate that the participant has left the conversation.
     *
     * @return static
     */
    public function left()
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'left',
        ]);
    }

    /**
     * Indicate that the participant has been removed from the conversation.
     *
     * @return static
     */
    public function removed()
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'removed',
        ]);
    }

    /**
     * Indicate that the participant has blocked the conversation.
     *
     * @return static
     */
    public function blocked()
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'blocked',
        ]);
    }
}
