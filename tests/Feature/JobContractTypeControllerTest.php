<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\JobContractType;
use Tests\TestCase;

class JobContractTypeControllerTest extends TestCase
{
    private $user;

    protected function setUp(): void
    {
        parent::setUp();

        JobContractType::query()->delete();

        // Créer un utilisateur authentifié
        $this->user = User::factory()->create();
    }

    public function test_if_lists_all_job_contract_types()
    {
        $this->actingAs($this->user);

        JobContractType::factory()->count(3)->create();

        $response = $this->getJson('/api/job-contract-types');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'name']
                ]
            ])
            ->assertJsonCount(3, 'data');
    }

    public function test_if_creates_a_new_job_contract_type()
    {
        $this->actingAs($this->user);

        $payload = ['name' => 'CDI'];

        $response = $this->postJson('/api/job-contract-types', $payload);

        $response->assertCreated()
            ->assertJsonPath('data.name', 'CDI');

        $this->assertDatabaseHas('job_contract_types', ['name' => 'CDI']);
    }

    public function test_if_updates_a_job_contract_type()
    {
        $this->actingAs($this->user);

        $type = JobContractType::factory()->create(['name' => 'CDD']);
        $payload = ['name' => 'Freelance'];

        $response = $this->putJson("/api/job-contract-types/{$type->id}", $payload);

        $response->assertOk()
            ->assertJsonPath('data.name', 'Freelance');

        $this->assertDatabaseHas('job_contract_types', ['id' => $type->id, 'name' => 'Freelance']);
    }

    public function test_if_deletes_a_job_contract_type()
    {
        $this->actingAs($this->user);

        $type = JobContractType::factory()->create();

        $response = $this->deleteJson("/api/job-contract-types/{$type->id}");

        $response->assertOk()
            ->assertJsonPath('message', 'Type de contrat supprimé avec succès.');

        $this->assertDatabaseMissing('job_contract_types', ['id' => $type->id]);
    }

    public function test_if_returns_404_when_deleting_nonexistent_type()
    {
        $this->actingAs($this->user);

        $response = $this->deleteJson('/api/job-contract-types/999');

        $response->assertNotFound()
            ->assertJsonPath('message', 'Type de contrat non trouvé.');
    }
}
