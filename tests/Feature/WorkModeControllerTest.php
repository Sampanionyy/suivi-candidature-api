<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\WorkMode;
use Tests\TestCase;

class WorkModeControllerTest extends TestCase
{
    private $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Vider la table avant chaque test
        WorkMode::query()->delete();

        // Créer un utilisateur authentifié
        $this->user = User::factory()->create();
    }

    public function test_if_lists_all_work_modes()
    {
        $this->actingAs($this->user);

        WorkMode::factory()->count(3)->create();

        $response = $this->getJson('/api/work-modes');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'name']
                ]
            ])
            ->assertJsonCount(3, 'data');
    }

    public function test_if_creates_a_new_work_mode()
    {
        $this->actingAs($this->user);

        $payload = ['name' => 'Télétravail'];

        $response = $this->postJson('/api/work-modes', $payload);

        $response->assertCreated()
            ->assertJsonPath('data.name', 'Télétravail');

        $this->assertDatabaseHas('work_modes', ['name' => 'Télétravail']);
    }

    public function test_if_updates_a_work_mode()
    {
        $this->actingAs($this->user);

        $mode = WorkMode::factory()->create(['name' => 'Présentiel']);
        $payload = ['name' => 'Hybride'];

        $response = $this->putJson("/api/work-modes/{$mode->id}", $payload);

        $response->assertOk()
            ->assertJsonPath('data.name', 'Hybride');

        $this->assertDatabaseHas('work_modes', ['id' => $mode->id, 'name' => 'Hybride']);
    }

    public function test_if_deletes_a_work_mode()
    {
        $this->actingAs($this->user);

        $mode = WorkMode::factory()->create();

        $response = $this->deleteJson("/api/work-modes/{$mode->id}");

        $response->assertOk()
            ->assertJsonPath('message', 'Mode de travail supprimé avec succès.');

        $this->assertDatabaseMissing('work_modes', ['id' => $mode->id]);
    }

    public function test_if_returns_404_when_deleting_nonexistent_work_mode()
    {
        $this->actingAs($this->user);

        $response = $this->deleteJson('/api/work-modes/999');

        $response->assertNotFound()
            ->assertJsonPath('message', 'Mode de travail non trouvé.');
    }
}
