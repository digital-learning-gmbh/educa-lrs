<?php
namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Actor;
use App\Models\AuthToken;

class ActorControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $authToken = 'test-token';

    protected function setUp(): void
    {
        parent::setUp();

        // Seed the AuthToken table
        AuthToken::create(['token' => $this->authToken]);
    }

    /** @test */
    public function it_can_create_an_actor()
    {
        $data = [
            'name' => 'John Doe',
            'mbox' => 'mailto:john.doe@example.com',
        ];

        $response = $this->postJson('/api/actors', $data, [
            'Authorization' => $this->authToken,
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'name' => $data['name'],
                'mbox' => $data['mbox'],
            ]);

        $this->assertDatabaseHas('actors', $data);
    }

    /** @test */
    public function it_can_retrieve_an_actor()
    {
        $actor = Actor::factory()->create();

        $response = $this->getJson("/api/actors/{$actor->id}", [
            'Authorization' => $this->authToken,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'id' => $actor->id,
                'name' => $actor->name,
                'mbox' => $actor->mbox,
            ]);
    }

    /** @test */
    public function it_can_update_an_actor()
    {
        $actor = Actor::factory()->create();

        $updateData = [
            'name' => 'Jane Doe',
        ];

        $response = $this->putJson("/api/actors/{$actor->id}", $updateData, [
            'Authorization' => $this->authToken,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'id' => $actor->id,
                'name' => $updateData['name'],
            ]);

        $this->assertDatabaseHas('actors', $updateData);
    }

    /** @test */
    public function it_can_delete_an_actor()
    {
        $actor = Actor::factory()->create();

        $response = $this->deleteJson("/api/actors/{$actor->id}", [], [
            'Authorization' => $this->authToken,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Actor deleted successfully',
            ]);

        $this->assertDatabaseMissing('actors', ['id' => $actor->id]);
    }

    /** @test */
    public function it_validates_required_fields_when_creating_an_actor()
    {
        $response = $this->postJson('/api/actors', [], [
            'Authorization' => $this->authToken,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'mbox']);
    }

    /** @test */
    public function it_fails_without_valid_auth_token()
    {
        $response = $this->postJson('/api/actors',[] ,[
            'Authorization' => 'invalid-token',
        ]);

        $response->assertStatus(401)
            ->assertJson(['error' => 'Invalid or expired token']);
    }
}
