<?php

namespace Database\Factories;

use App\Models\Actor;
use App\Models\LearningObject;
use App\Models\Statement;
use App\Models\Verb;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Statement>
 */
class StatementFactory extends Factory
{
    protected $model = Statement::class;

    public function definition()
    {
        return [
            'actor_id' => Actor::factory(),
            'verb_id' => Verb::factory(),
            'object_id' => LearningObject::factory(),
        ];
    }
}
