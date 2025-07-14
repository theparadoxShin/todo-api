<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post('api/auth/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);


        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'token',
                'token_type',
            ],
        ]);
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('password123'),
        ]);

        $this->post('api/auth/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('password123'),
        ]);

        $loginResponse = $this->post('api/auth/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $loginResponse->json('data.token'),
        ])->post('api/auth/logout');

        $response->assertNoContent();
    }
}
