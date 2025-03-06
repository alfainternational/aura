<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\VoiceCall;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\VoiceCall>
 */
class VoiceCallFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = VoiceCall::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'initiated_by' => function () {
                return User::factory()->create()->id;
            },
            'status' => $this->faker->randomElement(['initiated', 'active', 'ended']),
            'started_at' => now(),
            'ended_at' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Indicate that the call is initiated.
     *
     * @return static
     */
    public function initiated()
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'initiated',
            'started_at' => now(),
            'ended_at' => null,
        ]);
    }

    /**
     * Indicate that the call is active.
     *
     * @return static
     */
    public function active()
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
            'started_at' => now()->subMinutes(rand(1, 10)),
            'ended_at' => null,
        ]);
    }

    /**
     * Indicate that the call has ended.
     *
     * @return static
     */
    public function ended()
    {
        $startedAt = now()->subMinutes(rand(5, 30));
        
        return $this->state(fn (array $attributes) => [
            'status' => 'ended',
            'started_at' => $startedAt,
            'ended_at' => $startedAt->copy()->addMinutes(rand(1, 20)),
        ]);
    }
}
