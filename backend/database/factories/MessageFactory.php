<?php

namespace Database\Factories;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Message>
 */
class MessageFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Message::class;

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
            'sender_id' => function () {
                return User::factory()->create()->id;
            },
            'type' => 'text',
            'message' => $this->faker->sentence(),
            'attachment_path' => null,
            'attachment_type' => null,
            'attachment_name' => null,
            'attachment_size' => null,
            'is_deleted' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Indicate that the message is a text message.
     *
     * @return static
     */
    public function text()
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'text',
            'message' => $this->faker->paragraph(),
            'attachment_path' => null,
        ]);
    }

    /**
     * Indicate that the message is an image message.
     *
     * @return static
     */
    public function image()
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'image',
            'message' => $this->faker->optional()->sentence(),
            'attachment_path' => 'images/test/test-image-' . rand(1, 5) . '.jpg',
            'attachment_type' => 'image/jpeg',
            'attachment_name' => 'test-image.jpg',
            'attachment_size' => rand(50000, 500000),
        ]);
    }

    /**
     * Indicate that the message has been sent.
     *
     * @return static
     */
    public function sent()
    {
        return $this->state(fn (array $attributes) => [
            // Removed status field
        ]);
    }

    /**
     * Indicate that the message has been delivered.
     *
     * @return static
     */
    public function delivered()
    {
        return $this->state(fn (array $attributes) => [
            // Removed status field
        ]);
    }

    /**
     * Indicate that the message has been read.
     *
     * @return static
     */
    public function read()
    {
        return $this->state(fn (array $attributes) => [
            // Removed status field
        ]);
    }
}
