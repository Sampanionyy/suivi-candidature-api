<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LoginTest extends TestCase
{
    public function test_user_can_login_with_valid_credentials(): void
    {
        $email = 'samp'.uniqid().'@gmail.com';
        $user = \App\Models\User::factory()->create([
            'email' => $email,
            'password' => bcrypt('Password123'),
        ]);

        $payload = [
            'email' => $email,
            'password' => 'Password123',
        ];

        $response = $this->postJson('/api/auth/login', $payload);
        // Vérifie que le statut est correct 
        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data' => [
                'user',
                'token',
                'token_type'
            ]
        ]);
    }

    public function test_user_cannot_login_with_invalid_credentials(): void
    {
        $email = 'samp'.uniqid().'@gmail.com';
        $user = \App\Models\User::factory()->create([
            'email' => $email,
            'password' => bcrypt('Password123'),
        ]);

        $payload = [
            'email' => $email,
            'password' => 'WrongPassword',
        ];

        $response = $this->postJson('/api/auth/login', $payload);

        // Vérifie que le statut HTTP est 401 (Unauthorized)
        $response->assertStatus(401);

        // Vérifie le message d'erreur exact
        $response->assertJson([
            "success" => false,
            "message" => "Identifiants invalides"
        ]);
    }

    public function test_login_fails_with_missing_fields(): void
    {
        $response = $this->postJson('/api/auth/login', []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email', 'password']);
    }
}
