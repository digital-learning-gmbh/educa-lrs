<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use App\Models\AuthToken;

class CreateAuthToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auth:token';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a random new auth token';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $token = Str::random(40);

        AuthToken::create(['token' => $token]);

        $this->info('New Auth Token Created:');
        $this->line($token);

        return Command::SUCCESS;
    }
}
