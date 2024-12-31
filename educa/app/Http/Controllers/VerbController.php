<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Verb;

/**
 *
 * @OA\Tag(
 *     name="Verbs",
 *     description="API Endpoints for managing Verbs"
 * )
 */
class VerbController extends Controller
{
    /**
     * @OA\Get(
     *     path="/verbs",
     *     summary="List all verbs",
     *     tags={"Verbs"},
     *     @OA\Response(
     *         response=200,
     *         description="List of verbs",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="completed"),
     *                 @OA\Property(property="iri", type="string", example="http://adlnet.gov/expapi/verbs/completed")
     *             )
     *         )
     *     )
     * )
     */
    public function index()
    {
        $verbs = Verb::all()->map(fn($verb) => $verb->toXapiFormat());

        return response()->json($verbs);
    }

    /**
     * @OA\Post(
     *     path="/verbs",
     *     summary="Create a new verb",
     *     tags={"Verbs"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "iri"},
     *             @OA\Property(property="name", type="string", example="completed"),
     *             @OA\Property(property="iri", type="string", example="http://adlnet.gov/expapi/verbs/completed")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Verb created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="completed"),
     *             @OA\Property(property="iri", type="string", example="http://adlnet.gov/expapi/verbs/completed")
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:verbs',
            'iri' => 'required|url|unique:verbs',
        ]);

        $verb = Verb::create($validated);

        return response()->json($verb->toXapiFormat(), 201);
    }

    /**
     * @OA\Get(
     *     path="/verbs/{id}",
     *     summary="Retrieve a verb by ID",
     *     tags={"Verbs"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the verb to retrieve",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Verb retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="completed"),
     *             @OA\Property(property="iri", type="string", example="http://adlnet.gov/expapi/verbs/completed")
     *         )
     *     )
     * )
     */
    public function show($id)
    {
        $verb = Verb::findOrFail($id);

        return response()->json($verb->toXapiFormat());
    }

    /**
     * @OA\Put(
     *     path="/verbs/{id}",
     *     summary="Update a verb",
     *     tags={"Verbs"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the verb to update",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="updated-completed"),
     *             @OA\Property(property="iri", type="string", example="http://adlnet.gov/expapi/verbs/updated-completed")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Verb updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="updated-completed"),
     *             @OA\Property(property="iri", type="string", example="http://adlnet.gov/expapi/verbs/updated-completed")
     *         )
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        $verb = Verb::findOrFail($id);

        $validated = $request->validate([
            'name' => 'nullable|string|unique:verbs,name,' . $verb->id,
            'iri' => 'nullable|url|unique:verbs,iri,' . $verb->id,
        ]);

        $verb->update($validated);

        return response()->json($verb->toXapiFormat());
    }

    /**
     * @OA\Delete(
     *     path="/verbs/{id}",
     *     summary="Delete a verb",
     *     tags={"Verbs"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the verb to delete",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Verb deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Verb deleted successfully")
     *         )
     *     )
     * )
     */
    public function destroy($id)
    {
        $verb = Verb::findOrFail($id);
        $verb->delete();

        return response()->json(['message' => 'Verb deleted successfully']);
    }
}
