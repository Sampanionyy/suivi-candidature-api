<?php

namespace Tests\Feature;

use App\Models\Offer;
use App\Models\User;
use App\Models\JobContractType;
use App\Models\WorkMode;
use Tests\TestCase;

class OfferControllerTest extends TestCase
{
    private $user;

    protected function setUp(): void
    {
        parent::setUp();

        Offer::query()->delete();
        WorkMode::query()->delete();
        JobContractType::query()->delete();

        $this->user = User::factory()->create();

        $this->workMode = WorkMode::factory()->create(); // création unique
        $this->contractType = JobContractType::factory()->create(); // création unique
    }


    public function test_if_lists_all_offers()
    {
        $this->actingAs($this->user);

        Offer::factory()->count(3)->create();

        $response = $this->getJson('/api/offers');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id', 'title', 'company', 'location', 'url', 'description',
                        'external_id', 'source', 'company_logo_url',
                        'salary_min', 'salary_max', 'is_active',
                        'scraped_at', 'published_at', 'raw_data',
                        'job_contract_type_id', 'work_mode_id'
                    ]
                ]
            ])
            ->assertJsonCount(3, 'data');
    }

    public function test_if_creates_a_new_offer()
    {
        $this->actingAs($this->user);

        $payload = Offer::factory()->make([
            'job_contract_type_id' => $this->contractType->id,
            'work_mode_id' => $this->workMode->id
        ])->toArray();


        $response = $this->postJson('/api/offers', $payload);

        $response->assertCreated()
            ->assertJsonPath('data.title', $payload['title']);

        $this->assertDatabaseHas('offers', ['title' => $payload['title']]);
    }

    public function test_if_updates_an_offer()
    {
        $this->actingAs($this->user);

        $offer = Offer::factory()->create();

        $payload = ['title' => 'Nouvelle offre test'];

        $response = $this->putJson("/api/offers/{$offer->id}", $payload);

        $response->assertOk()
            ->assertJsonPath('data.title', 'Nouvelle offre test');

        $this->assertDatabaseHas('offers', ['id' => $offer->id, 'title' => 'Nouvelle offre test']);
    }

    public function test_if_deletes_an_offer()
    {
        $this->actingAs($this->user);

        $offer = Offer::factory()->create();

        $response = $this->deleteJson("/api/offers/{$offer->id}");

        $response->assertOk()
            ->assertJsonPath('message', 'Offre supprimée');

        $this->assertDatabaseMissing('offers', ['id' => $offer->id]);
    }

    public function test_if_returns_404_when_deleting_nonexistent_offer()
    {
        $this->actingAs($this->user);

        $response = $this->deleteJson('/api/offers/999');

        $response->assertNotFound()
            ->assertJsonPath('message', 'Offre non trouvée');
    }
}
