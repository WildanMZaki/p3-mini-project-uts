<?php

namespace Database\Factories;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'username' => fake()->unique()->userName(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->numerify('08##########'),
            'role' => UserRole::Pelapor,
            'is_active' => true,
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    public function pelapor(): static
    {
        return $this->state(fn() => ['role' => UserRole::Pelapor]);
    }

    public function relawan(): static
    {
        return $this->state(fn() => ['role' => UserRole::Relawan]);
    }

    public function koordinator(): static
    {
        return $this->state(fn() => ['role' => UserRole::Koordinator]);
    }

    public function inactive(): static
    {
        return $this->state(fn() => ['is_active' => false]);
    }
}
