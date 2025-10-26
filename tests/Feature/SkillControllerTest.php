<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Skill;
use App\Models\SkillCategory;
use Tests\TestCase;

class SkillControllerTest extends TestCase
{
    private $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Vider les tables avant chaque test
        SkillCategory::query()->delete();
        Skill::query()->delete();

        // Créer un utilisateur authentifié
        $this->user = User::factory()->create();
    }

    public function test_if_lists_all_skills()
    {
        $this->actingAs($this->user);

        $category = SkillCategory::factory()->create();
        Skill::factory()->count(3)->create(['skill_category_id' => $category->id]);

        $response = $this->getJson('/api/skills');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'name', 'skill_category_id']
                ]
            ])
            ->assertJsonCount(3, 'data');
    }

    public function test_if_creates_a_new_skill()
    {
        $this->actingAs($this->user);

        $category = SkillCategory::factory()->create();
        $payload = [
            'name' => 'Laravel',
            'skill_category_id' => $category->id,
        ];

        $response = $this->postJson('/api/skills', $payload);

        $response->assertCreated()
            ->assertJsonPath('data.name', 'Laravel');

        $this->assertDatabaseHas('skills', ['name' => 'Laravel', 'skill_category_id' => $category->id]);
    }

    public function test_if_updates_a_skill()
    {
        $this->actingAs($this->user);

        $category = SkillCategory::factory()->create();
        $skill = Skill::factory()->create(['name' => 'PHP', 'skill_category_id' => $category->id]);

        $payload = ['name' => 'PHP 8'];

        $response = $this->putJson("/api/skills/{$skill->id}", $payload);

        $response->assertOk()
            ->assertJsonPath('data.name', 'PHP 8');

        $this->assertDatabaseHas('skills', ['id' => $skill->id, 'name' => 'PHP 8']);
    }

    public function test_if_deletes_a_skill()
    {
        $this->actingAs($this->user);

        $category = SkillCategory::factory()->create();
        $skill = Skill::factory()->create(['skill_category_id' => $category->id]);

        $response = $this->deleteJson("/api/skills/{$skill->id}");

        $response->assertOk()
            ->assertJsonPath('message', 'Compétence supprimée avec succès.');

        $this->assertDatabaseMissing('skills', ['id' => $skill->id]);
    }
}
