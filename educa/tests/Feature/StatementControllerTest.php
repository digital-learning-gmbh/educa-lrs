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
                'objectType' => 'Agent',
                'mbox' => 'mailto:test@example.com',
                'name' => 'Test Actor'
            ],
            'verb' => [
                'id' => 'http://adlnet.gov/expapi/verbs/completed',
                'display' => [
                    'en-US' => 'completed'
                ]
            ],
            'object' => [
                'objectType' => 'Activity',
                'id' => 'http://example.com/activities/module-1',
                'definition' => [
                    'name' => [
                        'en-US' => 'Module 1'
                    ]
                ]
            ]
        ];

        $response = $this->postJson('/api/statements', $data, [
            'Authorization' => $this->authToken,
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('verb.display.en-US', 'completed')
            ->assertJsonPath('object.definition.name.en-US', 'Module 1')
            ->assertJsonPath('actor.name', 'Test Actor');

        $this->assertDatabaseHas('actors', ['mbox' => 'mailto:test@example.com']);
        $this->assertDatabaseHas('statements', []);
    }

    /** @test */
    public function it_can_create_a_statement_with_all_properties()
    {
        $data = [
            'actor' => [
                'objectType' => 'Agent',
                'mbox' => 'mailto:john.doe@example.com',
                'name' => 'John Doe'
            ],
            'verb' => [
                'id' => 'http://adlnet.gov/expapi/verbs/attempted',
                'display' => [
                    'en-US' => 'attempted'
                ]
            ],
            'object' => [
                'objectType' => 'Activity',
                'id' => 'http://example.com/activities/course-1',
                'definition' => [
                    'name' => [
                        'en-US' => 'Course 1'
                    ],
                    'description' => [
                        'en-US' => 'A course activity.'
                    ]
                ]
            ],
            'result' => [
                'score' => [
                    'scaled' => 0.85
                ],
                'completion' => true,
                'success' => true,
                'response' => 'Well done!'
            ],
            'context' => [
                'contextActivities' => [
                    'parent' => [
                        [
                            'id' => 'http://example.com/activities/parent-activity'
                        ]
                    ]
                ],
                'extensions' => [
                    'test' => 'session-123'
                ]
            ],
            'timestamp' => now()->toIso8601String(),
        ];

        $response = $this->postJson('/api/statements', $data, [
            'Authorization' => $this->authToken,
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('result.success', true)
            ->assertJsonPath('result.score.scaled', 0.85)
            ->assertJsonPath('context.extensions.test', 'session-123')
            ->assertJsonPath('actor.name', 'John Doe');

        $this->assertDatabaseHas('actors', ['mbox' => 'mailto:john.doe@example.com']);
        $this->assertDatabaseHas('statements', []);
    }

    /** @test */
    public function it_fails_to_create_a_statement_with_missing_actor()
    {
        $data = [
            'verb' => [
                'id' => 'http://adlnet.gov/expapi/verbs/completed',
                'display' => [
                    'en-US' => 'completed'
                ]
            ],
            'object' => [
                'objectType' => 'Activity',
                'id' => 'http://example.com/activities/module-1',
                'definition' => [
                    'name' => [
                        'en-US' => 'Module 1'
                    ]
                ]
            ]
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
                'objectType' => 'Agent',
                'mbox' => 'mailto:test@example.com',
                'name' => 'Test Actor'
            ],
            'verb' => [
                'id' => 'http://adlnet.gov/expapi/verbs/completed',
                'display' => [
                    'en-US' => 'completed'
                ]
            ],
            'object' => [
                'objectType' => 'Activity',
                'id' => 'http://example.com/activities/module-1',
                'definition' => [
                    'name' => [
                        'en-US' => 'Module 1'
                    ]
                ]
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
