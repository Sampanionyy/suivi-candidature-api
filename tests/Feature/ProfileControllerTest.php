<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Profile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ProfileControllerTest extends TestCase
{
    public function test_can_show_profile_with_relations()
    {
        $user = User::factory()->create();
        $profile = Profile::factory()->for($user)->create();

        $response = $this->actingAs($user)->getJson('/api/profile');

        $response->assertOk()
                 ->assertJsonPath('success', true)
                 ->assertJsonPath('data.id', $profile->id);
    }

    public function test_can_update_profile_and_sync_relations()
    {
        $user = User::factory()->create();
        $profile = Profile::factory()->for($user)->create();

        $payload = [
            'address' => 'Nouvelle bio',
            'skills' => [],
            'job_contract_types' => [],
            'work_modes' => []
        ];

        $response = $this->actingAs($user)->putJson('/api/profile', $payload);

        $response->assertOk()
                 ->assertJsonPath('success', true)
                 ->assertJsonPath('data.address', 'Nouvelle bio');

        $this->assertDatabaseHas('profiles', [
            'id' => $profile->id,
            'address' => 'Nouvelle bio'
        ]);
    }

    public function test_can_update_profile_photo()
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $profile = Profile::factory()->for($user)->create();

        $file = UploadedFile::fake()->create('avatar.jpg', 100, 'image/jpeg');

        $response = $this->actingAs($user)
                        ->postJson('/api/profile/photo', [
                            'photo' => $file
                        ]);

        $response->assertOk()
                ->assertJsonPath('success', true);

        $profile->refresh();
        $this->assertNotNull($profile->photo_url);
        Storage::disk('public')->assertExists(str_replace('/storage/', '', $profile->photo_url));
    }

    public function test_can_delete_profile_photo()
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $profile = Profile::factory()->for($user)->create([
            'photo_url' => '/storage/profile_photos/avatar.jpg'
        ]);

        Storage::disk('public')->put('profile_photos/avatar.jpg', 'fake content');

        $response = $this->actingAs($user)
                         ->deleteJson('/api/profile/photo');

        $response->assertOk()
                 ->assertJsonPath('success', true);

        $profile->refresh();
        $this->assertNull($profile->photo_url);
        Storage::disk('public')->assertMissing('profile_photos/avatar.jpg');
    }
}
