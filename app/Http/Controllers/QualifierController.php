<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Qualifier;

class QualifierController extends Controller
{
    // Get all qualifiers
    public function index(Request $request)
    {
        $search  = $request->query('search');
        $perPage = (int) $request->query('per_page', 10);

        $query = Qualifier::query();

        if ($search) {
            $query->where('qualifier_name', 'like', "%{$search}%");
        }

        // newest first (like department)
        $query->orderBy('id', 'desc');

        return $query->paginate($perPage);
    }

    // Store
    public function store(Request $request)
    {
        $validated = $request->validate([
            'qualifier_name' => 'required|string|max:255|unique:qualifiers,qualifier_name',
        ],[
           'qualifier_name.unique' => 'This qualifier already exists. Please enter a unique qualifier.',
        ]);

        $qualifier = Qualifier::create($validated);

        return response()->json($qualifier, 201);
    }

    // Update
    public function update(Request $request, $id)
    {
        $qualifier = Qualifier::findOrFail($id);

        $validated = $request->validate([
            'qualifier_name' => 'required|string|max:255|unique:qualifiers,qualifier_name,' . $id,
        ],[
           'qualifier_name.unique' => 'This qualifier already exists. Please enter a unique qualifier.',
        ]);

        $qualifier->update($validated);

        return response()->json($qualifier);
    }

    // Delete
    public function destroy($id)
    {
        $qualifier = Qualifier::findOrFail($id);
        $qualifier->delete();

        return response()->json(['message' => 'Qualifier deleted']);
    }
}
