<?php
namespace App\Http\Controllers;

use App\Models\LearningObject;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Objects",
 *     description="API Endpoints for managing Objects"
 * )
 */
class ObjectController extends Controller
{
    /**
     * @OA\Get(
     *     path="/objects",
     *     summary="List all objects",
     *     tags={"Objects"},
     *     @OA\Response(
     *         response=200,
     *         description="List of objects",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="definition", type="object",
     *                     @OA\Property(property="name", type="string", example="Module 1"),
     *                     @OA\Property(property="type", type="string", example="module"),
     *                     @OA\Property(property="iri", type="string", example="http://example.com/modules/1")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function index()
    {
        $objects = LearningObject::all()->map(fn($object) => $object->toXapiFormat());

        return response()->json($objects);
    }

    /**
     * @OA\Post(
     *     path="/objects",
     *     summary="Create a new object",
     *     tags={"Objects"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "type", "iri"},
     *             @OA\Property(property="name", type="string", example="Module 1"),
     *             @OA\Property(property="type", type="string", example="module"),
     *             @OA\Property(property="iri", type="string", example="http://example.com/modules/1")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Object created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="definition", type="object",
     *                 @OA\Property(property="name", type="string", example="Module 1"),
     *                 @OA\Property(property="type", type="string", example="module"),
     *                 @OA\Property(property="iri", type="string", example="http://example.com/modules/1")
     *             )
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'type' => 'required|string',
            'iri' => 'required|url',
        ]);

        $object = LearningObject::create($validated);

        return response()->json($object->toXapiFormat(), 201);
    }

    /**
     * @OA\Get(
     *     path="/objects/{id}",
     *     summary="Retrieve an object by ID",
     *     tags={"Objects"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the object to retrieve",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Object retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="definition", type="object",
     *                 @OA\Property(property="name", type="string", example="Module 1"),
     *                 @OA\Property(property="type", type="string", example="module"),
     *                 @OA\Property(property="iri", type="string", example="http://example.com/modules/1")
     *             )
     *         )
     *     )
     * )
     */
    public function show($id)
    {
        $object = LearningObject::findOrFail($id);

        return response()->json($object->toXapiFormat());
    }

    /**
     * @OA\Put(
     *     path="/objects/{id}",
     *     summary="Update an object",
     *     tags={"Objects"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the object to update",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Updated Module"),
     *             @OA\Property(property="type", type="string", example="updated-module"),
     *             @OA\Property(property="iri", type="string", example="http://example.com/modules/updated")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Object updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="definition", type="object",
     *                 @OA\Property(property="name", type="string", example="Updated Module"),
     *                 @OA\Property(property="type", type="string", example="updated-module"),
     *                 @OA\Property(property="iri", type="string", example="http://example.com/modules/updated")
     *             )
     *         )
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        $object = LearningObject::findOrFail($id);

        $validated = $request->validate([
            'name' => 'nullable|string',
            'type' => 'nullable|string',
            'iri' => 'nullable|url|unique:learning_objects,iri,' . $object->id,
        ]);

        $object->update($validated);

        return response()->json($object->toXapiFormat());
    }

    /**
     * @OA\Delete(
     *     path="/objects/{id}",
     *     summary="Delete an object",
     *     tags={"Objects"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the object to delete",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Object deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Object deleted successfully")
     *         )
     *     )
     * )
     */
    public function destroy($id)
    {
        $object = LearningObject::findOrFail($id);
        $object->delete();

        return response()->json(['message' => 'Object deleted successfully']);
    }
}
