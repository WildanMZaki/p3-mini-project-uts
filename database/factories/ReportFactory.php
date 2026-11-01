<?php

namespace Database\Factories;

use App\Enums\ConditionLevel;
use App\Enums\ReportStatus;
use App\Models\Area;
use App\Models\Report;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Report>
 */
class ReportFactory extends Factory
{
    private static int $sequence = 0;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => sprintf('KHL-%s-%04d', now()->format('Ym'), ++self::$sequence),
            'reporter_id' => User::factory(),
            'area_id' => Area::factory(),
            'title' => fake()->randomElement([
                'Asap tebal dari arah perkebunan',
                'Terlihat titik api di lahan kosong',
                'Bau asap menyengat di sekitar jalan desa',
                'Api kecil di pinggir hutan',
                'Lahan gambut mengeluarkan asap',
            ]),
            'description' => fake()->paragraph(),
            'location_detail' => 'Dekat ' . fake()->streetName(),
            // Rentang kira-kira wilayah Sumatera & Kalimantan
            'latitude' => fake()->latitude(-4, 2),
            'longitude' => fake()->longitude(100, 117),
            'occurred_at' => fake()->dateTimeBetween('-20 days', '-1 hour'),
            'condition_level' => fake()->randomElement(ConditionLevel::cases()),
            'status' => ReportStatus::Baru,
        ];
    }

    public function status(ReportStatus $status): static
    {
        return $this->state(fn() => ['status' => $status]);
    }
}
