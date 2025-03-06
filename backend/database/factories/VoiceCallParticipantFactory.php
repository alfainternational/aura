<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\VoiceCall;
use App\Models\VoiceCallParticipant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\VoiceCallParticipant>
 */
class VoiceCallParticipantFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = VoiceCallParticipant::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'voice_call_id' => function () {
                return VoiceCall::factory()->create()->id;
            },
            'user_id' => function () {
                return User::factory()->create()->id;
            },
            'status' => $this->faker->randomElement(['invited', 'joined', 'declined', 'left']),
            'joined_at' => null,
            'left_at' => null,
            'is_muted' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Indicate that the participant has joined the call.
     *
     * @return static
     */
    public function joined()
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'joined',
            'joined_at' => now()->subMinutes(rand(1, 10)),
            'left_at' => null,
        ]);
    }

    /**
     * Indicate that the participant has declined the call.
     *
     * @return static
     */
    public function declined()
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'declined',
            'joined_at' => null,
            'left_at' => null,
        ]);
    }

    /**
     * Indicate that the participant has left the call.
     *
     * @return static
     */
    public function left()
    {
        $joinedAt = now()->subMinutes(rand(5, 20));
        
        return $this->state(fn (array $attributes) => [
            'status' => 'left',
            'joined_at' => $joinedAt,
            'left_at' => $joinedAt->copy()->addMinutes(rand(1, 10)),
        ]);
    }

    /**
     * Indicate that the participant is muted.
     *
     * @return static
     */
    public function muted()
    {
        return $this->state(fn (array $attributes) => [
            'is_muted' => true,
        ]);
    }
}
