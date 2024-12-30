<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Verb;

class VerbController extends Controller
{
    public function index()
    {
        return response()->json(Verb::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:verbs',
            'iri' => 'required|url|unique:verbs',
        ]);

        $verb = Verb::create($validated);

        return response()->json($verb, 201);
    }

    public function show($id)
    {
        $verb = Verb::findOrFail($id);

        return response()->json($verb);
    }

    public function update(Request $request, $id)
    {
        $verb = Verb::findOrFail($id);

        $validated = $request->validate([
            'name' => 'nullable|string|unique:verbs,name,' . $verb->id,
            'iri' => 'nullable|url|unique:verbs,iri,' . $verb->id,
        ]);

        $verb->update($validated);

        return response()->json($verb);
    }

    public function destroy($id)
    {
        $verb = Verb::findOrFail($id);
        $verb->delete();

        return response()->json(['message' => 'Verb deleted successfully']);
    }
}
