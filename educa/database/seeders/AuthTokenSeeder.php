<?php

namespace Database\Seeders;

use App\Models\AuthToken;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AuthTokenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        AuthToken::create(['token' => 'test-token-educa']);
    }
}
