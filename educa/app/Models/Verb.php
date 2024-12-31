<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Verb extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'iri'];

    public function toXapiFormat()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'iri' => $this->iri,
        ];
    }
}
