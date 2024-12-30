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
}
