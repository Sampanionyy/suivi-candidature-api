<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\SkillCategory;
use App\Models\Skill;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class SkillCategoryControllerTest extends TestCase
{
    private $user;

    protected function setUp(): void
    {
        parent::setUp();

        SkillCategory::query()->delete();
        Skill::query()->delete();

        // Création d’un utilisateur authentifié
        $this->user = User::factory()->create();
    }

    public function test_if_lists_all_skill_categories()
    {
        $this->actingAs($this->user);

        SkillCategory::factory()->count(3)->create();

        $response = $this->getJson('/api/skill-categories');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'name', 'skills_count']
                ]
            ])
            ->assertJsonCount(3, 'data');
    }

    public function test_if_creates_a_new_skill_category()
    {
        $this->actingAs($this->user);

        $payload = ['name' => 'Développement Web'];

        $response = $this->postJson('/api/skill-categories', $payload);

        $response->assertCreated()
            ->assertJsonPath('data.name', 'Développement Web');

        $this->assertDatabaseHas('skill_categories', ['name' => 'Développement Web']);
    }

    public function test_if_shows_a_specific_skill_category()
    {
        $this->actingAs($this->user);

        $category = SkillCategory::factory()->create(['name' => 'Backend']);

        $response = $this->getJson("/api/skill-categories/{$category->id}");

        $response->assertOk()
            ->assertJsonPath('data.id', $category->id)
            ->assertJsonPath('data.name', 'Backend');
    }

    public function test_if_returns_404_if_category_not_found()
    {
        $this->actingAs($this->user);

        $response = $this->getJson('/api/skill-categories/999');

        $response->assertNotFound()
            ->assertJsonPath('message', 'Catégorie non trouvée.');
    }

    public function test_if_updates_a_skill_category()
    {
        $this->actingAs($this->user);

        $category = SkillCategory::factory()->create(['name' => 'Frontend']);
        $payload = ['name' => 'Fullstack'];

        $response = $this->putJson("/api/skill-categories/{$category->id}", $payload);

        $response->assertOk()
            ->assertJsonPath('data.name', 'Fullstack');

        $this->assertDatabaseHas('skill_categories', ['id' => $category->id, 'name' => 'Fullstack']);
    }

    public function test_if_returns_404_when_updating_nonexistent_category()
    {
        $this->actingAs($this->user);

        $payload = ['name' => 'Inconnue'];

        $response = $this->putJson('/api/skill-categories/999', $payload);

        $response->assertNotFound()
            ->assertJsonPath('message', 'Catégorie non trouvée.');
    }

    public function test_if_deletes_a_skill_category()
    {
        $this->actingAs($this->user);

        $category = SkillCategory::factory()->create();

        $response = $this->deleteJson("/api/skill-categories/{$category->id}");

        $response->assertOk()
            ->assertJsonPath('message', 'Catégorie supprimée avec succès.');

        $this->assertDatabaseMissing('skill_categories', ['id' => $category->id]);
    }

    public function test_if_prevents_deletion_if_category_has_skills()
    {
        $this->actingAs($this->user);

        $category = SkillCategory::factory()->create();
        Skill::factory()->create(['skill_category_id' => $category->id]);

        $response = $this->deleteJson("/api/skill-categories/{$category->id}");

        $response->assertStatus(422)
            ->assertJsonPath('message', 'Impossible de supprimer cette catégorie car elle contient des compétences.');

        $this->assertDatabaseHas('skill_categories', ['id' => $category->id]);
    }

    public function test_if_returns_404_when_deleting_nonexistent_category()
    {
        $this->actingAs($this->user);

        $response = $this->deleteJson('/api/skill-categories/999');

        $response->assertNotFound()
            ->assertJsonPath('message', 'Catégorie non trouvée.');
    }
}
