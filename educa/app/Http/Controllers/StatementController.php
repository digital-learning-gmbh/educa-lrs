<?php

namespace App\Http\Controllers;

use App\Models\Actor;
use App\Models\LearningObject;
use App\Models\Statement;
use App\Models\Verb;
use Illuminate\Http\Request;

class StatementController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'actor' => 'required|array',
            'verb' => 'required|array',
            'object' => 'required|array',
            'result' => 'nullable|array',
            'context' => 'nullable|array',
            'timestamp' => 'nullable|date',
        ]);

        $timestamp = isset($validated['timestamp']) ? date('Y-m-d H:i:s', strtotime($validated['timestamp'])) : now();

        // Create or retrieve actor
        $actor = Actor::firstOrCreate([
            'mbox' => $validated['actor']['mbox'] ?? null,
            'name' => $validated['actor']['name'] ?? null,
        ]);

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
            'actor_id' => $actor->id,
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

    public function filter(Request $request)
    {
        $query = Statement::query()->with(['actor', 'verb', 'object']);

        if ($request->has('actor_name')) {
            $query->whereHas('actor', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->input('actor_name') . '%');
            });
        }

        if ($request->has('verb_name')) {
            $query->whereHas('verb', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->input('verb_name') . '%');
            });
        }

        if ($request->has('object_name')) {
            $query->whereHas('object', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->input('object_name') . '%');
            });
        }

        if ($request->has('from_date') && $request->has('to_date')) {
            $query->whereBetween('timestamp', [
                $request->input('from_date'),
                $request->input('to_date')
            ]);
        }

        return response()->json($query->get());
    }
}
