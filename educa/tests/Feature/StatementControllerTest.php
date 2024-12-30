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
        $data = [
            'actor' => [
                'mbox' => 'mailto:test@example.com',
                'name' => 'Test Actor'
            ],
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
            ->assertJsonPath('object.name', 'Module 1')
            ->assertJsonPath('actor.name', 'Test Actor');

        $this->assertDatabaseHas('actors', ['name' => 'Test Actor']);
        $this->assertDatabaseHas('statements', []);
    }

    /** @test */
    public function it_can_create_a_statement_with_all_properties()
    {
        $data = [
            'actor' => [
                'mbox' => 'mailto:john.doe@example.com',
                'name' => 'John Doe'
            ],
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
            ->assertJsonPath('context.team.name', 'Team B')
            ->assertJsonPath('actor.name', 'John Doe');

        $this->assertDatabaseHas('actors', ['name' => 'John Doe']);
        $this->assertDatabaseHas('statements', []);
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
            ->assertJsonValidationErrors(['actor']);
    }

    /** @test */
    public function it_fails_to_create_a_statement_with_invalid_timestamp()
    {
        $data = [
            'actor' => [
                'mbox' => 'mailto:test@example.com',
                'name' => 'Test Actor'
            ],
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
    public function it_can_filter_statements()
    {
        Actor::factory()->create(['name' => 'John Doe']);
        Verb::factory()->create(['name' => 'completed']);
        LearningObject::factory()->create(['name' => 'Module 1']);
        Statement::factory()->create([
            'actor_id' => Actor::where('name', 'John Doe')->first()->id,
            'verb_id' => Verb::where('name', 'completed')->first()->id,
            'object_id' => LearningObject::where('name', 'Module 1')->first()->id,
            'timestamp' => now(),
        ]);

        $response = $this->postJson('/api/statements/filter',[
            "actor_name" => "John Doe",
        ], [
            'Authorization' => $this->authToken,
        ]);

        $response->assertStatus(200)
            ->assertJsonCount(1);
    }
}
