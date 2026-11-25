<?php

namespace Tests\Feature;

use App\Models\Media;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MediaApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_media_crud_search_and_relationship_resource(): void
    {
        $payload = $this->mediaPayload('The Clockwork Fable');

        $created = $this->postJson('/api/media', $payload)
            ->assertCreated()
            ->assertJsonPath('data.title', 'The Clockwork Fable')
            ->assertJsonPath('data.users', []);

        $mediaId = $created->json('data.id');

        $this->getJson('/api/media?search=Clockwork')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $mediaId);

        $this->getJson("/api/media/{$mediaId}")
            ->assertOk()
            ->assertJsonPath('data.id', $mediaId)
            ->assertJsonPath('data.users', []);

        $this->patchJson("/api/media/{$mediaId}", [
            'title' => 'The Clockwork Fable: Revised',
        ])->assertOk()
            ->assertJsonPath('data.title', 'The Clockwork Fable: Revised');

        $this->deleteJson("/api/media/{$mediaId}")->assertNoContent();
        $this->getJson("/api/media/{$mediaId}")->assertNotFound();
    }

    public function test_invalid_and_duplicate_media_titles_return_json_422(): void
    {
        $this->postJson('/api/media', [
            'category' => 'Anime',
            'genre' => 'Fantasy',
            'synopsis' => 'A required title is missing.',
            'release_year' => 2025,
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['title']);

        $payload = $this->mediaPayload('Unique Demo Title');
        $this->postJson('/api/media', $payload)->assertCreated();

        $this->postJson('/api/media', $payload)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['title']);
    }

    public function test_user_can_view_attach_and_detach_media_from_collection(): void
    {
        $user = User::factory()->create();
        $media = Media::create($this->mediaPayload('Collection Test Media'));

        $this->getJson("/api/users/{$user->id}/collection")
            ->assertOk()
            ->assertJsonPath('data.media', []);

        $this->postJson("/api/users/{$user->id}/collection/{$media->id}")
            ->assertCreated()
            ->assertJsonPath('data.media.0.id', $media->id)
            ->assertJsonPath('data.media.0.title', 'Collection Test Media');

        $this->getJson("/api/users/{$user->id}/collection")
            ->assertOk()
            ->assertJsonPath('data.media.0.id', $media->id);

        $this->deleteJson("/api/users/{$user->id}/collection/{$media->id}")
            ->assertOk()
            ->assertJsonPath('data.media', []);
    }

    public function test_unknown_media_and_user_return_json_404(): void
    {
        $this->getJson('/api/media/999999')->assertNotFound();
        $this->getJson('/api/users/999999/collection')->assertNotFound();
    }

    /**
     * @return array<string, mixed>
     */
    private function mediaPayload(string $title): array
    {
        return [
            'title' => $title,
            'category' => 'Anime',
            'genre' => 'Fantasy · Adventure',
            'synopsis' => 'A test story for the FlipPlay media API.',
            'release_year' => 2025,
        ];
    }
}
