<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Application;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ApplicationControllerTest extends TestCase
{
    public function test_lists_only_authenticated_user_applications()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        Application::factory()->count(2)->create(['user_id' => $user->id]);
        Application::factory()->create(['user_id' => $otherUser->id]);

        $this->actingAs($user)
            ->getJson('/api/applications')
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonStructure(['success', 'data']);
    }

    public function test_creates_a_new_application_with_files()
    {
        Storage::fake('public');
        $user = User::factory()->create();

        $payload = [
            'position' => 'Développeur Fullstack',
            'company' => 'OpenAI',
            'status' => 'accepted',
            'applied_date' => now()->toDateString(),
            'cv_path' => UploadedFile::fake()->create('cv.pdf', 120),
            'cover_letter_path' => UploadedFile::fake()->create('motivation.pdf', 150),
        ];

        $response = $this->actingAs($user)->postJson('/api/applications', $payload);

        $response->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonStructure(['data' => ['id', 'position', 'company']]);

        $this->assertDatabaseHas('applications', [
            'position' => 'Développeur Fullstack',
            'company' => 'OpenAI',
            'user_id' => $user->id,
        ]);

        Storage::disk('public')->assertExists('cvs');
        Storage::disk('public')->assertExists('cover_letters');
    }

    public function test_shows_a_single_application_if_owned_by_user()
    {
        $user = User::factory()->create();
        $app = Application::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)
            ->getJson("/api/applications/{$app->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $app->id);
    }

    public function test_denies_access_to_other_user_application()
    {
        $user = User::factory()->create();
        $app = Application::factory()->create(); 

        $this->actingAs($user)
            ->getJson("/api/applications/{$app->id}")
            ->assertForbidden()
            ->assertJsonPath('success', false);
    }

    public function test_updates_an_application()
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $app = Application::factory()->create(['user_id' => $user->id]);

        $payload = [
            'position' => 'Lead Dev',
            'status' => 'accepted',
            'cv_path' => UploadedFile::fake()->create('new_cv.pdf', 120)
        ];

        $response = $this->actingAs($user)->putJson("/api/applications/{$app->id}", $payload);

        $response->assertOk()
            ->assertJsonPath('data.position', 'Lead Dev');

        $this->assertDatabaseHas('applications', ['position' => 'Lead Dev']);
    }

    public function test_deletes_an_application()
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $app = Application::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)
            ->deleteJson("/api/applications/{$app->id}")
            ->assertOk()
            ->assertJsonPath('message', 'Candidature supprimée avec succès');

        $this->assertDatabaseMissing('applications', ['id' => $app->id]);
    }

    public function test_returns_interviews()
    {
        $user = User::factory()->create();
        Application::factory()->create([
            'user_id' => $user->id,
            'position' => 'Dev',
            'company' => 'Google',
            'interview_date' => now()->addDays(3),
            'status' => 'accepted'
        ]);

        $this->actingAs($user)
            ->getJson('/api/applications-interviews')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure(['data' => [['id', 'position', 'company', 'interview_date', 'status']]]);
    }
}
