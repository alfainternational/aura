<?php

namespace Database\Factories;

use App\Models\KycVerification;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\KycVerification>
 */
class KycVerificationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = KycVerification::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'user_id' => function () {
                return User::factory()->create()->id;
            },
            'id_type' => $this->faker->randomElement(['national_id', 'passport', 'driving_license']),
            'id_number' => $this->faker->numerify('##########'),
            'full_name' => $this->faker->name(),
            'date_of_birth' => $this->faker->date('Y-m-d', '-18 years'),
            'address' => $this->faker->address(),
            'id_front_image' => 'images/kyc/id_front_' . rand(1, 5) . '.jpg',
            'id_back_image' => 'images/kyc/id_back_' . rand(1, 5) . '.jpg',
            'selfie_image' => 'images/kyc/selfie_' . rand(1, 5) . '.jpg',
            'status' => $this->faker->randomElement(['pending', 'approved', 'rejected']),
            'notes' => $this->faker->optional()->sentence(),
            'verified_at' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Indicate that the verification is pending.
     *
     * @return static
     */
    public function pending()
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
            'verified_at' => null,
        ]);
    }

    /**
     * Indicate that the verification is approved.
     *
     * @return static
     */
    public function approved()
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'approved',
            'verified_at' => now(),
            'notes' => 'Verification approved',
        ]);
    }

    /**
     * Indicate that the verification is rejected.
     *
     * @return static
     */
    public function rejected()
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'rejected',
            'verified_at' => now(),
            'notes' => $this->faker->sentence(),
        ]);
    }
}
