<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\Http;

class GenerateXapiData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'xapi:generate {--count=1 : Number of xAPI statements to generate} {--bulk_size=100 : Number of xAPI statements for one batch}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate xAPI data';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $count = (int) $this->option('count');
        $batchSize= (int) $this->option('bulk_size');
        $authToken = 'test-token-educa'; // Replace with your actual token
        $server = "http://localhost:8000";
        $statements = [];

        $this->output->progressStart($count);

        for ($i = 0; $i < $count; $i++) {
            $statements[] = $this->generateXapiStatement();

            // If the batch size is reached or the last statement, send the batch
            if (count($statements) === $batchSize || $i === $count - 1) {
                $response = Http::withToken($authToken)->post($server . '/api/statements/bulk', [
                    'statements' => $statements,
                ]);

                if ($response->successful()) {
                    $this->info("Successfully posted batch of " . count($statements) . " statements.");
                } else {
                    $this->error("Failed to post batch. Response: " . $response->body());
                }

                $statements = []; // Clear the batch after sending
            }

            $this->output->progressAdvance();
        }

        $this->output->progressFinish();

        return Command::SUCCESS;
    }

    /**
     * Generate a single xAPI statement.
     *
     * @return array
     */
    private function generateXapiStatement(): array
    {
        $activities = [
            [
                'id' => 'http://example.com/activities/h5p-multiple-choice',
                'name' => 'H5P Multiple Choice',
                'description' => 'A multiple-choice activity.',
            ],
            [
                'id' => 'http://example.com/activities/h5p-interactive-video',
                'name' => 'H5P Interactive Video',
                'description' => 'An interactive video activity.',
            ],
            [
                'id' => 'http://example.com/activities/h5p-drag-and-drop',
                'name' => 'H5P Drag and Drop',
                'description' => 'A drag-and-drop activity.',
            ],
            [
                'id' => 'http://example.com/activities/h5p-quiz',
                'name' => 'H5P Quiz',
                'description' => 'A quiz activity.',
            ],
            [
                'id' => 'http://example.com/activities/h5p-summary',
                'name' => 'H5P Summary',
                'description' => 'A summary activity.',
            ],
        ];

        $activity = $activities[array_rand($activities)];
        $users = [
            ['name' => 'Alice Johnson', 'email' => 'alice.johnson@example.com'],
            ['name' => 'Bob Smith', 'email' => 'bob.smith@example.com'],
            ['name' => 'Charlie Brown', 'email' => 'charlie.brown@example.com'],
            ['name' => 'Diana Prince', 'email' => 'diana.prince@example.com'],
            ['name' => 'Ethan Hunt', 'email' => 'ethan.hunt@example.com'],
        ];

        $user = $users[array_rand($users)];
        $verbs = [
            [
                'id' => 'http://adlnet.gov/expapi/verbs/completed',
                'display' => ['en-US' => 'completed'],
            ],
            [
                'id' => 'http://adlnet.gov/expapi/verbs/attempted',
                'display' => ['en-US' => 'attempted'],
            ],
            [
                'id' => 'http://adlnet.gov/expapi/verbs/answered',
                'display' => ['en-US' => 'answered'],
            ],
            [
                'id' => 'http://adlnet.gov/expapi/verbs/interacted',
                'display' => ['en-US' => 'interacted'],
            ],
        ];

        $verb = $verbs[array_rand($verbs)];

        return [
            'id' => Uuid::uuid4()->toString(),
            'actor' => [
                'objectType' => 'Agent',
                'name' => $user['name'],
                'mbox' => 'mailto:' . $user['email'],
            ],
            'verb' => $verb,
            'object' => [
                'objectType' => 'Activity',
                'id' => $activity['id'],
                'definition' => [
                    'name' => [
                        'en-US' => $activity['name'],
                    ],
                    'description' => [
                        'en-US' => $activity['description'],
                    ],
                ],
            ],
            'result' => [
                'score' => [
                    'scaled' => round(mt_rand(0, 100) / 100, 2),
                ],
                'completion' => (bool) mt_rand(0, 1),
                'success' => (bool) mt_rand(0, 1),
                'response' => 'Sample response ' . mt_rand(1, 1000),
            ],
            'timestamp' => now()->toIso8601String(),
            'context' => [
                'contextActivities' => [
                    'parent' => [
                        ['id' => 'http://example.com/activities/parent-activity-' . mt_rand(1, 5)],
                    ],
                ],
                'extensions' => [
                    'http://example.com/extensions/session-id' => 'session-' . Uuid::uuid4()->toString(),
                ],
            ],
        ];
    }
}
