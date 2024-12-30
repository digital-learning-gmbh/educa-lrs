<?php

namespace Database\Seeders;

use App\Models\LearningObject;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ObjectSeeder extends Seeder
{
    public function run()
    {
        $objects = [
            ['name' => 'Course 1', 'type' => 'course', 'iri' => 'http://example.com/objects/course1'],
            ['name' => 'Module 1', 'type' => 'module', 'iri' => 'http://example.com/objects/module1'],
            ['name' => 'Video 1', 'type' => 'video', 'iri' => 'http://example.com/objects/video1'],
        ];

        foreach ($objects as $object) {
            LearningObject::create($object);
        }
    }
}
