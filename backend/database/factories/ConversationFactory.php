<?php

namespace Database\Factories;

use App\Models\Conversation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Conversation>
 */
class ConversationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Conversation::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'type' => $this->faker->randomElement(['individual', 'group']),
            'name' => $this->faker->optional(0.3)->sentence(2),
            'created_by' => function () {
                return User::factory()->create()->id;
            },
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Indicate that the conversation is individual.
     *
     * @return static
     */
    public function individual()
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'individual',
            'name' => null,
        ]);
    }

    /**
     * Indicate that the conversation is a group.
     *
     * @return static
     */
    public function group()
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'group',
            'name' => $this->faker->sentence(2),
        ]);
    }
}
