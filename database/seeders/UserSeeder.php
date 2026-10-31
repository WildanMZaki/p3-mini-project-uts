<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $accounts = [
            ['andi', 'Andi Pratama', UserRole::Pelapor, true],
            ['dodi', 'Dodi Saputra', UserRole::Relawan, true],
            ['budi', 'Budi Santoso', UserRole::Koordinator, true],
        ];

        foreach ($accounts as [$username, $name, $role, $isActive]) {
            $user = new User([
                'name' => $name,
                'username' => $username,
                'email' => "{$username}@gmail.com",
                'phone' => '08' . str_pad((string) random_int(0, 99999999), 10, '0', STR_PAD_LEFT),
                'password' => 'password',
            ]);

            // fill not fillable columns
            $user->role = $role;
            $user->is_active = $isActive;
            $user->save();
        }
    }
}
