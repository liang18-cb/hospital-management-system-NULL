<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register()
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('users', ['email' => 'test@example.com']);
    }

    public function test_user_can_login()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create([
            'password' => bcrypt('password')
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertStatus(200);
    }

    public function test_user_can_logout()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        
        $this->actingAs($user);

        $response = $this->postJson('/api/v1/auth/logout');

        $response->assertStatus(200);
    }
}