<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_creates_user(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Test User',
            'phone' => '+994501112233',
            'email' => 'test@example.com',
            'address' => 'Test Address',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('users', ['phone' => '+994501112233']);
    }

    public function test_login_with_correct_credentials(): void
    {
        $user = User::factory()->create([
            'phone' => '+994501112233',
            'password' => Hash::make('secret123'),
        ]);

        $response = $this->postJson('/api/login', [
            'phone' => '+994501112233',
            'password' => 'secret123',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['token']);
    }

    public function test_login_fails_with_invalid_credentials(): void
    {
        $user = User::factory()->create([
            'phone' => '+994501112233',
            'password' => Hash::make('correctpassword'),
        ]);

        $response = $this->postJson('/api/login', [
            'phone' => '+994501112233',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401);
        $response->assertJson(['message' => 'Incorrect phone or password']);
    }
}
