<?php

namespace Database\Seeders;

use App\Models\Area;
use Illuminate\Database\Seeder;

class AreaSeeder extends Seeder
{
    public function run(): void
    {
        $areas = [
            ['Kec. Pedamaran', 'Kab. Ogan Komering Ilir', 'Sumatera Selatan', true],
            ['Kec. Tulung Selapan', 'Kab. Ogan Komering Ilir', 'Sumatera Selatan', true],
            ['Kec. Rupat', 'Kab. Bengkalis', 'Riau', true],
            ['Kec. Kumpeh', 'Kab. Muaro Jambi', 'Jambi', true],
            ['Kec. Jabiren Raya', 'Kab. Pulang Pisau', 'Kalimantan Tengah', true],
            ['Kec. Sungai Ambawang', 'Kab. Kubu Raya', 'Kalimantan Barat', false],
        ];

        foreach ($areas as [$name, $regency, $province, $isActive]) {
            Area::firstOrCreate(
                ['name' => $name, 'regency' => $regency],
                ['province' => $province, 'is_active' => $isActive]
            );
        }
    }
}
