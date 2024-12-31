<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Actor;

/**
 *
 * @OA\Tag(
 *     name="Actors",
 *     description="API Endpoints for managing Actors"
 * )
 */
class ActorController extends Controller
{
    /**
     * @OA\Post(
     *     path="/actors",
     *     summary="Create a new actor",
     *     tags={"Actors"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "mbox"},
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="mbox", type="string", example="mailto:john.doe@example.com"),
     *             @OA\Property(property="account_homepage", type="string", example="http://example.com"),
     *             @OA\Property(property="account_name", type="string", example="johndoe")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Actor created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="mbox", type="string", example="mailto:john.doe@example.com"),
     *             @OA\Property(property="account_homepage", type="string", example="http://example.com"),
     *             @OA\Property(property="account_name", type="string", example="johndoe")
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'mbox' => ['required', 'regex:/^mailto:.+@.+\..+$/'], // Custom regex for mailto
            'account_homepage' => 'nullable|url',
            'account_name' => 'nullable|string',
        ]);

        $actor = Actor::create([
            'name' => $validated['name'],
            'mbox' => $validated['mbox'],
            'mbox_sha1sum' => sha1($validated['mbox']),
            'account_homepage' => data_get($validated, 'account_homepage', null), // Handle optional
            'account_name' => data_get($validated, 'account_name', null),         // Handle optional
        ]);

        return response()->json($actor, 201);
    }

    /**
     * @OA\Get(
     *     path="/actors/{id}",
     *     summary="Retrieve an actor by ID",
     *     tags={"Actors"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the actor to retrieve",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Actor retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="mbox", type="string", example="mailto:john.doe@example.com"),
     *             @OA\Property(property="account_homepage", type="string", example="http://example.com"),
     *             @OA\Property(property="account_name", type="string", example="johndoe")
     *         )
     *     )
     * )
     */
    public function show($id)
    {
        $actor = Actor::findOrFail($id);
        return response()->json($actor);
    }

    /**
     * @OA\Get(
     *     path="/actors",
     *     summary="List all actors",
     *     tags={"Actors"},
     *     @OA\Response(
     *         response=200,
     *         description="List of actors",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="John Doe"),
     *                 @OA\Property(property="mbox", type="string", example="mailto:john.doe@example.com"),
     *                 @OA\Property(property="account_homepage", type="string", example="http://example.com"),
     *                 @OA\Property(property="account_name", type="string", example="johndoe")
     *             )
     *         )
     *     )
     * )
     */
    public function index()
    {
        $actors = Actor::all();
        return response()->json($actors);
    }

    /**
     * @OA\Put(
     *     path="/actors/{id}",
     *     summary="Update actor details",
     *     tags={"Actors"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the actor to update",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="John Updated"),
     *             @OA\Property(property="mbox", type="string", example="mailto:updated.john@example.com"),
     *             @OA\Property(property="account_homepage", type="string", example="http://updated.example.com"),
     *             @OA\Property(property="account_name", type="string", example="updatedjohn")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Actor updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="John Updated"),
     *             @OA\Property(property="mbox", type="string", example="mailto:updated.john@example.com"),
     *             @OA\Property(property="account_homepage", type="string", example="http://updated.example.com"),
     *             @OA\Property(property="account_name", type="string", example="updatedjohn")
     *         )
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        $actor = Actor::findOrFail($id);

        $validated = $request->validate([
            'name' => 'nullable|string',
            'mbox' => 'nullable|email|unique:actors,mbox,' . $actor->id,
            'account_homepage' => 'nullable|url',
            'account_name' => 'nullable|string',
        ]);

        $actor->update($validated);

        return response()->json($actor);
    }

    /**
     * @OA\Delete(
     *     path="/actors/{id}",
     *     summary="Delete an actor",
     *     tags={"Actors"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the actor to delete",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Actor deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Actor deleted successfully")
     *         )
     *     )
     * )
     */
    public function destroy($id)
    {
        $actor = Actor::findOrFail($id);
        $actor->delete();

        return response()->json(['message' => 'Actor deleted successfully']);
    }
}
