<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LearningObject extends Model
{
    protected $table = 'learning_objects';

    use HasFactory;
    protected $fillable = ['name', 'type', 'iri'];

    public function toXapiFormat()
    {
        return [
            'id' => $this->id,
            'definition' => [
                'name' => [
                    'en-US' => $this->name,
                ],
                'type' => $this->type,
                'iri' => $this->iri,
            ],
        ];
    }
}
