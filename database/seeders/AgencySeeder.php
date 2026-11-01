<?php

namespace Database\Seeders;

use App\Enums\AgencyType;
use App\Models\Agency;
use Illuminate\Database\Seeder;

class AgencySeeder extends Seeder
{
    /**
     * Data pihak terkait fiktif (kontak dummy).
     */
    public function run(): void
    {
        $agencies = [
            ['BPBD Kabupaten (contoh)', AgencyType::Bpbd, '0711-000111'],
            ['Daops Manggala Agni (contoh)', AgencyType::ManggalaAgni, '0711-000222'],
            ['Damkar Kabupaten (contoh)', AgencyType::Damkar, '0711-000333'],
            ['Pemerintah Desa Setempat (contoh)', AgencyType::PemerintahDesa, '0812-0000-4444'],
            ['Pengelola Kawasan Perkebunan (contoh)', AgencyType::PengelolaKawasan, '0812-0000-5555'],
        ];

        foreach ($agencies as [$name, $type, $contact]) {
            Agency::firstOrCreate(
                ['name' => $name],
                [
                    'type' => $type,
                    'contact' => $contact,
                    'is_active' => true,
                ]
            );
        }
    }
}
