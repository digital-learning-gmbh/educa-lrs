<?php

namespace App\Http\Controllers;

use App\Models\Actor;
use App\Models\LearningObject;
use App\Models\Statement;
use App\Models\Verb;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * @OA\Tag(
 *      name="Statements",
 *      description="API Endpoints for managing Statements"
 *  )
 */
class StatementController extends Controller
{
    /**
     * @OA\Post(
     *     path="/statements",
     *     summary="Store a new statement",
     *     tags={"Statements"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"actor", "verb", "object"},
     *             @OA\Property(property="actor", type="object",
     *                 @OA\Property(property="objectType", type="string", example="Agent"),
     *                 @OA\Property(property="name", type="string", example="John Doe"),
     *                 @OA\Property(property="mbox", type="string", example="mailto:john.doe@example.com")
     *             ),
     *             @OA\Property(property="verb", type="object",
     *                 @OA\Property(property="id", type="string", example="http://adlnet.gov/expapi/verbs/completed"),
     *                 @OA\Property(property="display", type="object",
     *                     @OA\Property(property="en-US", type="string", example="completed")
     *                 )
     *             ),
     *             @OA\Property(property="object", type="object",
     *                 @OA\Property(property="objectType", type="string", example="Activity"),
     *                 @OA\Property(property="id", type="string", example="http://example.com/activities/module-1"),
     *                 @OA\Property(property="definition", type="object",
     *                     @OA\Property(property="name", type="object",
     *                         @OA\Property(property="en-US", type="string", example="Module 1")
     *                     ),
     *                     @OA\Property(property="description", type="object",
     *                         @OA\Property(property="en-US", type="string", example="A module description")
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Statement stored successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="actor", type="object",
     *                 @OA\Property(property="name", type="string", example="John Doe"),
     *                 @OA\Property(property="mbox", type="string", example="mailto:john.doe@example.com")
     *             ),
     *             @OA\Property(property="verb", type="object",
     *                 @OA\Property(property="id", type="string", example="http://adlnet.gov/expapi/verbs/completed"),
     *                 @OA\Property(property="display", type="object",
     *                     @OA\Property(property="en-US", type="string", example="completed")
     *                 )
     *             ),
     *             @OA\Property(property="object", type="object",
     *                 @OA\Property(property="objectType", type="string", example="Activity"),
     *                 @OA\Property(property="id", type="string", example="http://example.com/activities/module-1"),
     *                 @OA\Property(property="definition", type="object",
     *                     @OA\Property(property="name", type="object",
     *                         @OA\Property(property="en-US", type="string", example="Module 1")
     *                     ),
     *                     @OA\Property(property="description", type="object",
     *                         @OA\Property(property="en-US", type="string", example="A module description")
     *                     )
     *                 )
     *             )
     *         )
     *     )
     * )
     */
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

    /**
     * @OA\Post(
     *     path="/statements/bulk",
     *     summary="Store multiple statements in bulk",
     *     tags={"Statements"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"statements"},
     *             @OA\Property(property="statements", type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     required={"actor", "verb", "object"},
     *                     @OA\Property(property="actor", type="object",
     *                         @OA\Property(property="objectType", type="string", example="Agent"),
     *                         @OA\Property(property="name", type="string", example="John Doe"),
     *                         @OA\Property(property="mbox", type="string", example="mailto:john.doe@example.com")
     *                     ),
     *                     @OA\Property(property="verb", type="object",
     *                         @OA\Property(property="id", type="string", example="http://adlnet.gov/expapi/verbs/completed"),
     *                         @OA\Property(property="display", type="object",
     *                             @OA\Property(property="en-US", type="string", example="completed")
     *                         )
     *                     ),
     *                     @OA\Property(property="object", type="object",
     *                         @OA\Property(property="objectType", type="string", example="Activity"),
     *                         @OA\Property(property="id", type="string", example="http://example.com/activities/module-1"),
     *                         @OA\Property(property="definition", type="object",
     *                             @OA\Property(property="name", type="object",
     *                                 @OA\Property(property="en-US", type="string", example="Module 1")
     *                             ),
     *                             @OA\Property(property="description", type="object",
     *                                 @OA\Property(property="en-US", type="string", example="A module description")
     *                             )
     *                         )
     *                     ),
     *                     @OA\Property(property="result", type="object",
     *                         @OA\Property(property="completion", type="boolean", example=true),
     *                         @OA\Property(property="success", type="boolean", example=true),
     *                         @OA\Property(property="score", type="object",
     *                             @OA\Property(property="scaled", type="number", format="float", example=0.95)
     *                         )
     *                     ),
     *                     @OA\Property(property="context", type="object",
     *                         @OA\Property(property="contextActivities", type="object",
     *                             @OA\Property(property="parent", type="array",
     *                                 @OA\Items(type="object",
     *                                     @OA\Property(property="id", type="string", example="http://example.com/activities/parent-activity")
     *                                 )
     *                             )
     *                         )
     *                     ),
     *                     @OA\Property(property="timestamp", type="string", format="date-time", example="2024-12-30T12:34:56Z")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Statements stored successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="statements", type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="actor", type="object",
     *                         @OA\Property(property="name", type="string", example="John Doe"),
     *                         @OA\Property(property="mbox", type="string", example="mailto:john.doe@example.com")
     *                     ),
     *                     @OA\Property(property="verb", type="object",
     *                         @OA\Property(property="id", type="string", example="http://adlnet.gov/expapi/verbs/completed"),
     *                         @OA\Property(property="display", type="object",
     *                             @OA\Property(property="en-US", type="string", example="completed")
     *                         )
     *                     ),
     *                     @OA\Property(property="object", type="object",
     *                         @OA\Property(property="objectType", type="string", example="Activity"),
     *                         @OA\Property(property="id", type="string", example="http://example.com/activities/module-1"),
     *                         @OA\Property(property="definition", type="object",
     *                             @OA\Property(property="name", type="object",
     *                                 @OA\Property(property="en-US", type="string", example="Module 1")
     *                             ),
     *                             @OA\Property(property="description", type="object",
     *                                 @OA\Property(property="en-US", type="string", example="A module description")
     *                             )
     *                         )
     *                     )
     *                 )
     *             )
     *         )
     *     )
     * )
     */
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

    /**
     * @OA\Get(
     *     path="/statements",
     *     summary="Retrieve all statements",
     *     tags={"Statements"},
     *     @OA\Response(
     *         response=200,
     *         description="List of statements",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="actor", type="object",
     *                     @OA\Property(property="name", type="string", example="John Doe"),
     *                     @OA\Property(property="mbox", type="string", example="mailto:john.doe@example.com")
     *                 ),
     *                 @OA\Property(property="verb", type="object",
     *                     @OA\Property(property="id", type="string", example="http://adlnet.gov/expapi/verbs/completed"),
     *                     @OA\Property(property="display", type="object",
     *                         @OA\Property(property="en-US", type="string", example="completed")
     *                     )
     *                 ),
     *                 @OA\Property(property="object", type="object",
     *                     @OA\Property(property="objectType", type="string", example="Activity"),
     *                     @OA\Property(property="id", type="string", example="http://example.com/activities/module-1"),
     *                     @OA\Property(property="definition", type="object",
     *                         @OA\Property(property="name", type="object",
     *                             @OA\Property(property="en-US", type="string", example="Module 1")
     *                         ),
     *                         @OA\Property(property="description", type="object",
     *                             @OA\Property(property="en-US", type="string", example="A module description")
     *                         )
     *                     )
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function index()
    {
        return response()->json(Statement::with(['actor', 'verb', 'object'])->get());
    }

    /**
     * @OA\Get(
     *     path="/statements/filter",
     *     summary="Filter statements based on criteria",
     *     tags={"Statements"},
     *     @OA\Parameter(
     *         name="actor_name",
     *         in="query",
     *         description="Filter statements by actor name",
     *         required=false,
     *         @OA\Schema(type="string", example="John Doe")
     *     ),
     *     @OA\Parameter(
     *         name="verb_name",
     *         in="query",
     *         description="Filter statements by verb name",
     *         required=false,
     *         @OA\Schema(type="string", example="completed")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Filtered list of statements",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="actor", type="object",
     *                     @OA\Property(property="name", type="string", example="John Doe"),
     *                     @OA\Property(property="mbox", type="string", example="mailto:john.doe@example.com")
     *                 ),
     *                 @OA\Property(property="verb", type="object",
     *                     @OA\Property(property="id", type="string", example="http://adlnet.gov/expapi/verbs/completed"),
     *                     @OA\Property(property="display", type="object",
     *                         @OA\Property(property="en-US", type="string", example="completed")
     *                     )
     *                 ),
     *                 @OA\Property(property="object", type="object",
     *                     @OA\Property(property="objectType", type="string", example="Activity"),
     *                     @OA\Property(property="id", type="string", example="http://example.com/activities/module-1"),
     *                     @OA\Property(property="definition", type="object",
     *                         @OA\Property(property="name", type="object",
     *                             @OA\Property(property="en-US", type="string", example="Module 1")
     *                         ),
     *                         @OA\Property(property="description", type="object",
     *                             @OA\Property(property="en-US", type="string", example="A module description")
     *                         )
     *                     )
     *                 )
     *             )
     *         )
     *     )
     * )
     */
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
