<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    public function test_user_can_logout(): void
    {
        $user = \App\Models\User::factory()->create([
            'email' => 'samp'.uniqid().'example.com',
            'password' => bcrypt('Password123'),
        ]);

        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->postJson('/api/auth/logout');

        $response->assertStatus(200);

        $response->assertJson([
            'message' => 'Déconnexion réussie',
        ]);

        $this->assertCount(0, $user->tokens()->get());
    }

}
