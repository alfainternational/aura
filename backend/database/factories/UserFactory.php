<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => Hash::make('password'), // password
            'remember_token' => Str::random(10),
            'user_type' => $this->faker->randomElement(['user', 'merchant', 'admin']),
            'phone_number' => $this->faker->phoneNumber(),
            'country_id' => null,
            'city_id' => null,
            'is_active' => true,
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return static
     */
    public function unverified()
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Indicate that the user is an admin.
     *
     * @return static
     */
    public function admin()
    {
        return $this->state(fn (array $attributes) => [
            'user_type' => 'admin',
        ]);
    }

    /**
     * Indicate that the user is a merchant.
     *
     * @return static
     */
    public function merchant()
    {
        return $this->state(fn (array $attributes) => [
            'user_type' => 'merchant',
        ]);
    }

    /**
     * Indicate that the user is a regular user.
     *
     * @return static
     */
    public function user()
    {
        return $this->state(fn (array $attributes) => [
            'user_type' => 'user',
        ]);
    }
}
