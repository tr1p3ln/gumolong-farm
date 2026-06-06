<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use DatabaseTransactions;

    // ─── LOGIN ───────────────────────────────────────────────────────────────

    public function test_login_page_is_accessible(): void
    {
        $this->get('/login')->assertOk();
    }

    public function test_super_admin_login_redirects_to_dashboard(): void
    {
        $user = User::factory()->superAdmin()->create();

        $this->post('/login', ['email' => $user->email, 'password' => 'password'])
             ->assertRedirect(route('dashboard'));
    }

    public function test_admin_login_redirects_to_dashboard(): void
    {
        $user = User::factory()->admin()->create();

        $this->post('/login', ['email' => $user->email, 'password' => 'password'])
             ->assertRedirect(route('dashboard'));
    }

    public function test_kepala_kandang_login_redirects_to_kk_dashboard(): void
    {
        $user = User::factory()->kepalaKandang()->create();

        $this->post('/login', ['email' => $user->email, 'password' => 'password'])
             ->assertRedirect(route('kk.dashboard'));
    }

    public function test_pengurus_kandang_login_redirects_to_pk_dashboard(): void
    {
        $user = User::factory()->pengurusKandang()->create();

        $this->post('/login', ['email' => $user->email, 'password' => 'password'])
             ->assertRedirect(route('pk.dashboard'));
    }

    public function test_login_fails_with_wrong_password(): void
    {
        $user = User::factory()->create();

        $this->post('/login', ['email' => $user->email, 'password' => 'salah123'])
             ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_login_fails_with_unknown_email(): void
    {
        $this->post('/login', ['email' => 'tidak@ada.com', 'password' => 'password'])
             ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_nonaktif_user_cannot_login(): void
    {
        $user = User::factory()->nonaktif()->create();

        $this->post('/login', ['email' => $user->email, 'password' => 'password'])
             ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_login_requires_email_and_password(): void
    {
        $this->post('/login', [])->assertSessionHasErrors(['email', 'password']);
    }

    public function test_login_requires_valid_email_format(): void
    {
        $this->post('/login', ['email' => 'bukan-email', 'password' => 'password'])
             ->assertSessionHasErrors('email');
    }

    // ─── LOGOUT ──────────────────────────────────────────────────────────────

    public function test_authenticated_user_can_logout(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
             ->post('/logout')
             ->assertRedirect(route('login'));

        $this->assertGuest();
    }

    // ─── AUTHENTICATED REDIRECT ───────────────────────────────────────────────

    public function test_authenticated_admin_accessing_root_redirects_to_dashboard(): void
    {
        $user = User::factory()->admin()->create();

        $this->actingAs($user)->get('/')->assertRedirect(route('dashboard'));
    }

    public function test_authenticated_pk_accessing_root_redirects_to_pk_dashboard(): void
    {
        $user = User::factory()->pengurusKandang()->create();

        $this->actingAs($user)->get('/')->assertRedirect(route('pk.dashboard'));
    }

    public function test_unauthenticated_access_to_dashboard_redirects_to_login(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
    }

    public function test_last_login_is_updated_on_successful_login(): void
    {
        $user = User::factory()->create(['last_login' => null]);

        $this->post('/login', ['email' => $user->email, 'password' => 'password']);

        $this->assertNotNull($user->fresh()->last_login);
    }
}
