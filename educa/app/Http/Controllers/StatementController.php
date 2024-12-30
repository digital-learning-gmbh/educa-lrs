<?php

namespace App\Http\Controllers;

use App\Models\LearningObject;
use App\Models\Statement;
use App\Models\Verb;
use Illuminate\Http\Request;

class StatementController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'actor_id' => 'required|exists:actors,id',
            'verb' => 'required|array',
            'object' => 'required|array',
            'result' => 'nullable|array',
            'context' => 'nullable|array',
            'timestamp' => 'nullable|date',
        ]);

        $timestamp = isset($validated['timestamp']) ? date('Y-m-d H:i:s', strtotime($validated['timestamp'])) : now();

        // Create or retrieve verb
        $verb = Verb::firstOrCreate([
            'name' => $validated['verb']['name'],
            'iri' => $validated['verb']['iri'],
        ]);

        // Create or retrieve object
        $object = LearningObject::firstOrCreate([
            'name' => $validated['object']['name'],
            'type' => $validated['object']['type'],
            'iri' => $validated['object']['iri'],
        ]);

        // Create statement
        $statement = Statement::create([
            'actor_id' => $validated['actor_id'],
            'verb_id' => $verb->id,
            'object_id' => $object->id,
            'result' => $validated['result'] ?? null,
            'context' => $validated['context'] ?? null,
            'timestamp' => $timestamp,
        ]);

        return response()->json($statement->load('actor', 'verb', 'object'), 201);
    }

    public function index()
    {
        return response()->json(Statement::with(['actor', 'verb', 'object'])->get());
    }
}
