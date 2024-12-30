<?php

namespace Tests\Feature;

use App\Models\AuthToken;
use App\Models\Verb;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class VerbControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $authToken = 'test-token';

    protected function setUp(): void
    {
        parent::setUp();

        // Seed an authentication token
        AuthToken::create(['token' => $this->authToken]);
    }

    /** @test */
    public function it_can_create_a_verb()
    {
        $data = [
            'name' => 'Completed',
            'iri' => 'http://adlnet.gov/expapi/verbs/completed',
        ];

        $response = $this->postJson('/api/verbs', $data, [
            'Authorization' => $this->authToken,
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'name' => $data['name'],
                'iri' => $data['iri'],
            ]);

        $this->assertDatabaseHas('verbs', $data);
    }

    /** @test */
    public function it_can_list_all_verbs()
    {
        Verb::factory()->count(3)->create();

        $response = $this->getJson('/api/verbs', [
            'Authorization' => $this->authToken,
        ]);

        $response->assertStatus(200)
            ->assertJsonCount(3);
    }

    /** @test */
    public function it_can_retrieve_a_specific_verb()
    {
        $verb = Verb::factory()->create();

        $response = $this->getJson("/api/verbs/{$verb->id}", [
            'Authorization' => $this->authToken,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'id' => $verb->id,
                'name' => $verb->name,
                'iri' => $verb->iri,
            ]);
    }

    /** @test */
    public function it_can_update_a_verb()
    {
        $verb = Verb::factory()->create();

        $updateData = [
            'name' => 'Interacted',
            'iri' => 'http://adlnet.gov/expapi/verbs/interacted',
        ];

        $response = $this->putJson("/api/verbs/{$verb->id}", $updateData, [
            'Authorization' => $this->authToken,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'id' => $verb->id,
                'name' => $updateData['name'],
                'iri' => $updateData['iri'],
            ]);

        $this->assertDatabaseHas('verbs', $updateData);
    }

    /** @test */
    public function it_can_delete_a_verb()
    {
        $verb = Verb::factory()->create();

        $response = $this->deleteJson("/api/verbs/{$verb->id}", [], [
            'Authorization' => $this->authToken,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Verb deleted successfully',
            ]);

        $this->assertDatabaseMissing('verbs', ['id' => $verb->id]);
    }

    /** @test */
    public function it_fails_without_auth_token()
    {
        $response = $this->getJson('/api/verbs');

        $response->assertStatus(401)
            ->assertJson(['error' => 'Authorization token missing']);
    }

    /** @test */
    public function it_fails_with_invalid_auth_token()
    {
        $response = $this->getJson('/api/verbs', [
            'Authorization' => 'invalid-token',
        ]);

        $response->assertStatus(401)
            ->assertJson(['error' => 'Invalid or expired token']);
    }
}
