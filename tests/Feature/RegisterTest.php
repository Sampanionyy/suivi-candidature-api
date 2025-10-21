<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class RegisterTest extends TestCase
{
    public function test_user_valid(): void
    {
        $email = 'samp'.uniqid().'@example.com';
        $payload = [
            'name' => 'Sampaniony',
            'email' => $email,
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
        ];

        $response = $this->postJson('/api/auth/register', $payload);
        $response->assertStatus(201);

        $this->assertDatabaseHas('users', [
            'email' => $email,
            'name' => 'Sampaniony'
        ]);

        $user = $response->json()['data']['user'];
        $accessToken = $response->json()['data']['token'];
        $tokenType = $response->json()['data']['token_type'];

        $response->assertJsonStructure([
            'data' => [
                'user',
                'token',
                'token_type'
            ]
        ]);
    }

    public function test_registration_fails_with_empty_fields()
    {
        $payload = [
            'name' => '',
            'email' => '',
            'password' => '',
            'password_confirmation' => '',
        ];

        $response = $this->postJson('/api/auth/register', $payload);

        $response->assertStatus(422); // Validation error
        $response->assertJsonValidationErrors(['name', 'email', 'password']);
    }

    public function test_registration_fails_with_invalid_email()
    {
        $payload = [
            'name' => 'Samp',
            'email' => 'not-an-email',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->postJson('/api/auth/register', $payload);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email']);
    }

    public function test_registration_fails_when_password_not_confirmed()
    {
        $payload = [
            'name' => 'Samp',
            'email' => 'samp'.uniqid().'@example.com',
            'password' => 'password123',
            'password_confirmation' => 'differentpassword',
        ];

        $response = $this->postJson('/api/auth/register', $payload);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['password']);
    }

    public function test_registration_fails_when_email_already_exists()
    {
        User::factory()->create([
            'email' => 'samp'.uniqid().'@example.com'
        ]);

        $payload = [
            'name' => 'Samp',
            'email' => 'samp3@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->postJson('/api/auth/register', $payload);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email']);
    }
}
