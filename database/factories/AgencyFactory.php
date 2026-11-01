<?php

namespace Database\Factories;

use App\Enums\AgencyType;
use App\Models\Agency;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Agency>
 */
class AgencyFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = fake()->randomElement(AgencyType::cases());

        return [
            'name' => $type->label() . ' ' . fake()->unique()->city(),
            'type' => $type,
            'contact' => fake()->numerify('08##########'),
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['is_active' => false]);
    }
}
