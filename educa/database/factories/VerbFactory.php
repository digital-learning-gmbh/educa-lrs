<?php

namespace Database\Factories;

use App\Models\Verb;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Verb>
 */
class VerbFactory extends Factory
{
    protected $model = Verb::class;

    public function definition()
    {
        return [
            'name' => $this->faker->unique()->word,
            'iri' => $this->faker->unique()->url,
        ];
    }
}
