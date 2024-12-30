<?php
namespace Database\Factories;

use App\Models\LearningObject;
use Illuminate\Database\Eloquent\Factories\Factory;

class LearningObjectFactory extends Factory
{
    protected $model = LearningObject::class;

    public function definition()
    {
        return [
            'name' => $this->faker->unique()->word,
            'type' => $this->faker->randomElement(['module', 'course', 'video']),
            'iri' => $this->faker->unique()->url,
        ];
    }
}
