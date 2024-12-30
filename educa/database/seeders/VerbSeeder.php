<?php

namespace Database\Seeders;

use App\Models\Verb;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VerbSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $verbs = [
            ['name' => 'Completed', 'iri' => 'http://adlnet.gov/expapi/verbs/completed'],
            ['name' => 'Viewed', 'iri' => 'http://adlnet.gov/expapi/verbs/viewed'],
            ['name' => 'Attempted', 'iri' => 'http://adlnet.gov/expapi/verbs/attempted'],
            ['name' => 'Passed', 'iri' => 'http://adlnet.gov/expapi/verbs/passed'],
            ['name' => 'Failed', 'iri' => 'http://adlnet.gov/expapi/verbs/failed'],
        ];

        foreach ($verbs as $verb) {
            Verb::create($verb);
        }
    }
}
