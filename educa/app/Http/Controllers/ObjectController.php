<?php

namespace App\Http\Controllers;

use App\Models\LearningObject;
use Illuminate\Http\Request;

class ObjectController extends Controller
{
    public function index()
    {
        return response()->json(LearningObject::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'type' => 'required|string',
            'iri' => 'required|url',
        ]);

        $object = LearningObject::create($validated);

        return response()->json($object, 201);
    }

    public function show($id)
    {
        $object = LearningObject::findOrFail($id);

        return response()->json($object);
    }

    public function update(Request $request, $id)
    {
        $object = LearningObject::findOrFail($id);

        $validated = $request->validate([
            'name' => 'nullable|string',
            'type' => 'nullable|string',
            'iri' => 'nullable|url|unique:learning_objects,iri,' . $object->id,
        ]);

        $object->update($validated);

        return response()->json($object);
    }

    public function destroy($id)
    {
        $object = LearningObject::findOrFail($id);
        $object->delete();

        return response()->json(['message' => 'Object deleted successfully']);
    }
}
