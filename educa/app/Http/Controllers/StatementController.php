<?php

namespace App\Http\Controllers;

use App\Models\Actor;
use App\Models\LearningObject;
use App\Models\Statement;
use App\Models\Verb;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
            'mbox' => $validated['actor']['mbox'],
        ], [
            'name' => $validated['actor']['name'] ?? null,
            'objectType' => $validated['actor']['objectType'] ?? 'Agent',
        ]);

        // Create or retrieve verb
        $verb = Verb::firstOrCreate([
            'iri' => $validated['verb']['id'],
        ], [
            'name' => $validated['verb']['display']['en-US'] ?? null,
        ]);

        // Create or retrieve object
        $object = LearningObject::firstOrCreate([
            'iri' => $validated['object']['id'],
        ], [
            'name' => $validated['object']['definition']['name']['en-US'] ?? null,
            'type' => $validated['object']['objectType'] ?? 'Activity',
            'description' => $validated['object']['definition']['description']['en-US'] ?? null,
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

        return response()->json($statement->toXapiFormat(), 201);
    }

    public function bulkStore(Request $request)
    {
        $validated = $request->validate([
            'statements' => 'required|array',
            'statements.*.actor' => 'required|array',
            'statements.*.verb' => 'required|array',
            'statements.*.object' => 'required|array',
            'statements.*.result' => 'nullable|array',
            'statements.*.context' => 'nullable|array',
            'statements.*.timestamp' => 'nullable|date',
        ]);

        $createdStatements = [];

        DB::transaction(function () use ($validated, &$createdStatements) {
            foreach ($validated['statements'] as $statementData) {
                $timestamp = isset($statementData['timestamp']) ? date('Y-m-d H:i:s', strtotime($statementData['timestamp'])) : now();

                // Create or retrieve actor
                $actor = Actor::firstOrCreate([
                    'mbox' => $statementData['actor']['mbox'],
                ], [
                    'name' => $statementData['actor']['name'] ?? null,
                    'objectType' => $statementData['actor']['objectType'] ?? 'Agent',
                ]);

                // Create or retrieve verb
                $verb = Verb::firstOrCreate([
                    'iri' => $statementData['verb']['id'],
                ], [
                    'name' => $statementData['verb']['display']['en-US'] ?? null,
                ]);

                // Create or retrieve object
                $object = LearningObject::firstOrCreate([
                    'iri' => $statementData['object']['id'],
                ], [
                    'name' => $statementData['object']['definition']['name']['en-US'] ?? null,
                    'type' => $statementData['object']['objectType'] ?? 'Activity',
                    'description' => $statementData['object']['definition']['description']['en-US'] ?? null,
                ]);

                // Create statement
                $statement = Statement::create([
                    'actor_id' => $actor->id,
                    'verb_id' => $verb->id,
                    'object_id' => $object->id,
                    'result' => $statementData['result'] ?? null,
                    'context' => $statementData['context'] ?? null,
                    'timestamp' => $timestamp,
                ]);

                $createdStatements[] = $statement->toXapiFormat();
            }
        });

        return response()->json(['statements' => $createdStatements], 201);
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

        $statements = $query->get()->map->toXapiFormat();

        return response()->json($statements);
    }
}
