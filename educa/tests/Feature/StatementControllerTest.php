<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Actor;
use App\Models\Verb;
use App\Models\LearningObject;
use App\Models\Statement;

class StatementControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $authToken = 'test-token-educa';

    protected function setUp(): void
    {
        parent::setUp();

        // Seed an authentication token
        \App\Models\AuthToken::create(['token' => $this->authToken]);
    }

    /** @test */
    public function it_can_create_a_statement_with_minimum_properties()
    {
        $actor = Actor::factory()->create();
        $data = [
            'actor_id' => $actor->id,
            'verb' => [
                'name' => 'completed',
                'iri' => 'http://adlnet.gov/expapi/verbs/completed',
            ],
            'object' => [
                'name' => 'Module 1',
                'type' => 'module',
                'iri' => 'http://example.com/modules/1',
            ],
        ];

        $response = $this->postJson('/api/statements', $data, [
            'Authorization' => $this->authToken,
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('verb.name', 'completed')
            ->assertJsonPath('object.name', 'Module 1');

        $this->assertDatabaseHas('statements', ['actor_id' => $actor->id]);
    }

    /** @test */
    public function it_can_create_a_statement_with_all_properties()
    {
        $actor = Actor::factory()->create();
        $data = [
            'actor_id' => $actor->id,
            'verb' => [
                'name' => 'attempted',
                'iri' => 'http://adlnet.gov/expapi/verbs/attempted',
            ],
            'object' => [
                'name' => 'Course 1',
                'type' => 'course',
                'iri' => 'http://example.com/courses/1',
            ],
            'result' => [
                'success' => true,
                'completion' => false,
                'score' => ['scaled' => 0.85],
            ],
            'context' => [
                'platform' => 'LMS',
                'team' => ['id' => 'team-456', 'name' => 'Team B'],
            ],
            'timestamp' => now()->toIso8601String(),
        ];

        $response = $this->postJson('/api/statements', $data, [
            'Authorization' => $this->authToken,
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('result.success', true)
            ->assertJsonPath('context.team.name', 'Team B');
    }

    /** @test */
    public function it_fails_to_create_a_statement_with_missing_actor()
    {
        $data = [
            'verb' => [
                'name' => 'completed',
                'iri' => 'http://adlnet.gov/expapi/verbs/completed',
            ],
            'object' => [
                'name' => 'Module 1',
                'type' => 'module',
                'iri' => 'http://example.com/modules/1',
            ],
        ];

        $response = $this->postJson('/api/statements', $data, [
            'Authorization' => $this->authToken,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['actor_id']);
    }

    /** @test */
    public function it_fails_to_create_a_statement_with_invalid_timestamp()
    {
        $actor = Actor::factory()->create();
        $data = [
            'actor_id' => $actor->id,
            'verb' => [
                'name' => 'completed',
                'iri' => 'http://adlnet.gov/expapi/verbs/completed',
            ],
            'object' => [
                'name' => 'Module 1',
                'type' => 'module',
                'iri' => 'http://example.com/modules/1',
            ],
            'timestamp' => 'invalid-date',
        ];

        $response = $this->postJson('/api/statements', $data, [
            'Authorization' => $this->authToken,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['timestamp']);
    }

    /** @test */
    public function it_can_list_all_statements()
    {
        Statement::factory()->count(5)->create();

        $response = $this->getJson('/api/statements', [
            'Authorization' => $this->authToken,
        ]);

        $response->assertStatus(200)
            ->assertJsonCount(5);
    }

    /** @test */
    public function it_fails_to_access_statements_without_auth_token()
    {
        $response = $this->getJson('/api/statements');

        $response->assertStatus(401)
            ->assertJson(['error' => 'Authorization token missing']);
    }

    /** @test */
    public function it_fails_to_access_statements_with_invalid_auth_token()
    {
        $response = $this->getJson('/api/statements', [
            'Authorization' => 'invalid-token',
        ]);

        $response->assertStatus(401)
            ->assertJson(['error' => 'Invalid or expired token']);
    }
}
