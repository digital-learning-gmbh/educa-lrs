<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Statement extends Model
{
    use HasFactory;

    protected $fillable = [
        'actor_id',
        'verb_id',
        'object_id',
        'result',
        'context',
        'timestamp',
    ];

    protected $casts = [
        'result' => 'array', // Automatically handle JSON
        'context' => 'array', // Automatically handle JSON
    ];

    public function actor()
    {
        return $this->belongsTo(Actor::class);
    }

    public function verb()
    {
        return $this->belongsTo(Verb::class);
    }

    public function object()
    {
        return $this->belongsTo(LearningObject::class, 'object_id');
    }

    public function toXapiFormat()
    {
        return [
            'id' => $this->id,
            'actor' => [
                'objectType' => $this->actor->objectType ?? 'Agent',
                'name' => $this->actor->name ?? null,
                'mbox' => $this->actor->mbox ?? null,
            ],
            'verb' => [
                'id' => $this->verb->iri ?? null,
                'display' => [
                    'en-US' => $this->verb->name ?? null,
                ],
            ],
            'object' => [
                'objectType' => $this->object->type ?? 'Activity',
                'id' => $this->object->iri ?? null,
                'definition' => [
                    'name' => [
                        'en-US' => $this->object->name ?? null,
                    ],
                    'description' => [
                        'en-US' => $this->object->description ?? null,
                    ],
                ],
            ],
            'result' => $this->result,
            'context' => $this->context,
            'timestamp' => $this->timestamp ?? now()->toIso8601String(),
        ];
    }
}
