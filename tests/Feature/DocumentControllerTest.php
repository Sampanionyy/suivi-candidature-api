<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Document;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DocumentControllerTest extends TestCase
{

    public function test_lists_only_authenticated_user_documents()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        Document::factory()->count(2)->create(['user_id' => $user->id]);
        Document::factory()->create(['user_id' => $otherUser->id]);

        $this->actingAs($user)
            ->getJson('/api/documents')
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonStructure(['success', 'data']);
    }

    public function test_creates_a_new_cv_document()
    {
        Storage::fake('public');
        $user = User::factory()->create();

        $payload = [
            'name' => 'Mon CV',
            'type' => 'CV', // Type CV
            'file' => UploadedFile::fake()->create('cv.pdf', 100),
        ];

        $response = $this->actingAs($user)->postJson('/api/documents', $payload);

        $response->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonStructure(['data' => ['id', 'name', 'type', 'file_url']]);

        $this->assertDatabaseHas('documents', [
            'name' => 'Mon CV',
            'type' => 'CV',
            'user_id' => $user->id,
        ]);
    }

    public function test_creates_a_new_lm_document()
    {
        Storage::fake('public');
        $user = User::factory()->create();

        $payload = [
            'name' => 'Ma Lettre de Motivation',
            'type' => 'LM', // Type LM
            'file' => UploadedFile::fake()->create('lettre.pdf', 100),
        ];

        $response = $this->actingAs($user)->postJson('/api/documents', $payload);

        $response->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.type', 'LM');

        $this->assertDatabaseHas('documents', [
            'name' => 'Ma Lettre de Motivation',
            'type' => 'LM',
            'user_id' => $user->id,
        ]);
    }

    public function test_deletes_a_document_and_file()
    {
        Storage::fake('public');
        $user = User::factory()->create();
        
        $fileName = 'test-document.pdf';
        $filePath = 'documents/' . $fileName;
        Storage::disk('public')->put($filePath, 'dummy content');
        
        $document = Document::factory()->create([
            'user_id' => $user->id,
            'type' => 'CV',
            'file_url' => $filePath
        ]);

        $response = $this->actingAs($user)
            ->deleteJson("/api/documents/{$document->id}");

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure(['success', 'message']);

        $this->assertDatabaseMissing('documents', ['id' => $document->id]);
        Storage::disk('public')->assertMissing($filePath);
    }

    public function test_cannot_delete_other_user_document()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $document = Document::factory()->create(['user_id' => $otherUser->id]);

        $this->actingAs($user)
            ->deleteJson("/api/documents/{$document->id}")
            ->assertForbidden();

        $this->assertDatabaseHas('documents', ['id' => $document->id]);
    }

    public function test_rejects_invalid_document_type()
    {
        Storage::fake('public');
        $user = User::factory()->create();

        $payload = [
            'name' => 'Test Document',
            'type' => 'INVALID', // Type invalide
            'file' => UploadedFile::fake()->create('document.pdf', 100),
        ];

        $response = $this->actingAs($user)->postJson('/api/documents', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['type']);
    }
}