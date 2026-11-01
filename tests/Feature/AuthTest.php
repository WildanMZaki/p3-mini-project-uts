<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * AC-01 Authentication
 */
class AuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(UserSeeder::class);
    }

    public function test_login_page_can_be_opened_by_guest(): void
    {
        $this->get('/login')->assertOk()->assertSee('Masuk');
    }

    public function test_user_with_valid_account_can_login(): void
    {
        $user = User::where('username', 'andi')->firstOrFail();

        $this->post('/login', ['username' => 'andi', 'password' => 'password'])
            ->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_all_seeded_roles_can_login(): void
    {
        foreach (['andi', 'dodi', 'budi'] as $username) {
            $user = User::where('username', $username)->firstOrFail();

            $this->post('/login', ['username' => $username, 'password' => 'password'])
                ->assertRedirect(route('dashboard'));

            $this->assertAuthenticatedAs($user);

            $this->post('/logout');
            $this->assertGuest();
        }
    }

    public function test_login_fails_with_wrong_password(): void
    {
        $this->post('/login', ['username' => 'andi', 'password' => 'salah'])
            ->assertSessionHasErrors('username');

        $this->assertGuest();
    }

    public function test_inactive_account_cannot_login(): void
    {
        // Seeder saat ini hanya berisi akun aktif (andi, dodi, budi),
        // sehingga akun nonaktif dibuat melalui factory untuk pengujian skenario ini.
        User::factory()->relawan()->inactive()->create(['username' => 'eko']);

        $this->post('/login', ['username' => 'eko', 'password' => 'password'])
            ->assertSessionHasErrors('username');

        $this->assertGuest();
    }

    public function test_user_can_logout(): void
    {
        $user = User::where('username', 'andi')->firstOrFail();

        $this->actingAs($user)->post('/logout')->assertRedirect(route('login'));

        $this->assertGuest();
    }

    public function test_guest_cannot_open_protected_page(): void
    {
        $this->get('/dashboard')->assertRedirect(route('login'));
    }

    public function test_guest_cannot_logout_directly(): void
    {
        $this->post('/logout')->assertRedirect(route('login'));
    }
}
