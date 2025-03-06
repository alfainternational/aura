<?php

namespace Database\Factories;

use App\Models\Message;
use App\Models\MessageStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MessageStatus>
 */
class MessageStatusFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = MessageStatus::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'message_id' => function () {
                return Message::factory()->create()->id;
            },
            'user_id' => function () {
                return User::factory()->create()->id;
            },
            'status' => $this->faker->randomElement(['sent', 'delivered', 'read']),
            'delivered_at' => null,
            'read_at' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Indicate that the message status is sent.
     *
     * @return static
     */
    public function sent()
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'sent',
            'delivered_at' => null,
            'read_at' => null,
        ]);
    }

    /**
     * Indicate that the message status is delivered.
     *
     * @return static
     */
    public function delivered()
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'delivered',
            'delivered_at' => now(),
            'read_at' => null,
        ]);
    }

    /**
     * Indicate that the message status is read.
     *
     * @return static
     */
    public function read()
    {
        $deliveredAt = now()->subMinutes(rand(1, 5));
        
        return $this->state(fn (array $attributes) => [
            'status' => 'read',
            'delivered_at' => $deliveredAt,
            'read_at' => now(),
        ]);
    }
}
