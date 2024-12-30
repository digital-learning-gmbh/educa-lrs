<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Actor;

class ActorController extends Controller
{
    // Store a new actor
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


    // Retrieve actor by ID
    public function show($id)
    {
        $actor = Actor::findOrFail($id);
        return response()->json($actor);
    }

    // Update actor details
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

    // Delete an actor
    public function destroy($id)
    {
        $actor = Actor::findOrFail($id);
        $actor->delete();

        return response()->json(['message' => 'Actor deleted successfully']);
    }
}
