<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use DatabaseTransactions;

    public function test_profile_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/profile');

        $response->assertOk();
    }

    public function test_profile_information_can_be_updated(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->patch('/profile', [
            'nama'  => 'Nama Baru Test',
            'email' => 'update-' . uniqid() . '@example.com',
        ]);

        $response->assertSessionHasNoErrors()->assertRedirect('/profile');

        $user->refresh();

        $this->assertSame('Nama Baru Test', $user->nama);
        $this->assertNull($user->email_verified_at);
    }

    public function test_email_verification_unchanged_when_email_not_changed(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->patch('/profile', [
            'nama'  => 'Nama Lain',
            'email' => $user->email,
        ]);

        $response->assertSessionHasNoErrors()->assertRedirect('/profile');

        $this->assertNotNull($user->refresh()->email_verified_at);
    }

    public function test_user_can_delete_their_account(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->delete('/profile', [
            'password' => 'password',
        ]);

        $response->assertSessionHasNoErrors()->assertRedirect('/');

        $this->assertGuest();
        $this->assertNull($user->fresh());
    }

    public function test_wrong_password_blocks_account_deletion(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->from('/profile')->delete('/profile', [
            'password' => 'salah-password',
        ]);

        $response->assertSessionHasErrorsIn('userDeletion', 'password')
                 ->assertRedirect('/profile');

        $this->assertNotNull($user->fresh());
    }
}
