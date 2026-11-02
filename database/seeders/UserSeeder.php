<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Akun demo untuk setiap role. Semua password: "password".
     */
    public function run(): void
    {
        $accounts = [
            ['andi', 'Andi Pratama', UserRole::Pelapor, true],
            ['citra', 'Citra Lestari', UserRole::Pelapor, true],
            ['sari', 'Sari Wulandari', UserRole::Relawan, true],
            ['dodi', 'Dodi Saputra', UserRole::Relawan, true],
            ['budi', 'Budi Santoso', UserRole::Koordinator, true],
            ['eko', 'Eko Nonaktif', UserRole::Relawan, false],
        ];

        foreach ($accounts as [$username, $name, $role, $isActive]) {
            $user = new User([
                'name' => $name,
                'username' => $username,
                'email' => "{$username}@gmail.com",
                'phone' => '0812'.str_pad((string) random_int(0, 99999999), 8, '0', STR_PAD_LEFT),
                'password' => 'password',
            ]);

            // role & is_active bukan fillable, jadi diisi eksplisit
            $user->role = $role;
            $user->is_active = $isActive;
            $user->save();
        }
    }
}
