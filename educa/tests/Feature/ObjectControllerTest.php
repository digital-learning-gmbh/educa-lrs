<?php

namespace Tests\Feature;

use App\Models\AuthToken;
use App\Models\LearningObject;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ObjectControllerTest extends TestCase
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
    public function it_can_create_an_object()
    {
        $data = [
            'name' => 'Module 1',
            'type' => 'module',
            'iri' => 'http://example.com/objects/module1',
        ];

        $response = $this->postJson('/api/objects', $data, [
            'Authorization' => $this->authToken,
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'id' => $response['id'],
                'definition' => [
                    'name' => ['en-US' => $data['name']],
                    'type' => $data['type'],
                    'iri' => $data['iri'],
                ],
            ]);

        $this->assertDatabaseHas('learning_objects', $data);
    }

    /** @test */
    public function it_can_list_all_objects()
    {
        LearningObject::factory()->count(3)->create();

        $response = $this->getJson('/api/objects', [
            'Authorization' => $this->authToken,
        ]);

        $response->assertStatus(200)
            ->assertJsonCount(3);

        foreach (LearningObject::all() as $object) {
            $response->assertJsonFragment([
                'id' => $object->id,
                'definition' => [
                    'name' => ['en-US' => $object->name],
                    'type' => $object->type,
                    'iri' => $object->iri,
                ],
            ]);
        }
    }

    /** @test */
    public function it_can_retrieve_a_specific_object()
    {
        $object = LearningObject::factory()->create();

        $response = $this->getJson("/api/objects/{$object->id}", [
            'Authorization' => $this->authToken,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'id' => $object->id,
                'definition' => [
                    'name' => ['en-US' => $object->name],
                    'type' => $object->type,
                    'iri' => $object->iri,
                ],
            ]);
    }

    /** @test */
    public function it_can_update_an_object()
    {
        $object = LearningObject::factory()->create();

        $updateData = [
            'name' => 'Updated Module',
            'type' => 'module',
            'iri' => 'http://example.com/objects/updated-module',
        ];

        $response = $this->putJson("/api/objects/{$object->id}", $updateData, [
            'Authorization' => $this->authToken,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'id' => $object->id,
                'definition' => [
                    'name' => ['en-US' => $updateData['name']],
                    'type' => $updateData['type'],
                    'iri' => $updateData['iri'],
                ],
            ]);

        $this->assertDatabaseHas('learning_objects', $updateData);
    }

    /** @test */
    public function it_can_delete_an_object()
    {
        $object = LearningObject::factory()->create();

        $response = $this->deleteJson("/api/objects/{$object->id}", [], [
            'Authorization' => $this->authToken,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Object deleted successfully',
            ]);

        $this->assertDatabaseMissing('learning_objects', ['id' => $object->id]);
    }
}
