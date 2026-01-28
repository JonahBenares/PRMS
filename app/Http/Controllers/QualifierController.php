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

        return $query->paginate($perPage);
    }

    // Store new qualifier
    public function store(Request $request) {
        $request->validate([
            'qualifier_name' => 'required|string|max:255',
        ]);

        $qualifier = Qualifier::create([
            'qualifier_name' => $request->qualifier_name,
        ]);

        return response()->json($qualifier, 201);
    }

    // Update qualifier
    public function update(Request $request, $id) {
        $request->validate([
            'qualifier_name' => 'required|string|max:255',
        ]);

        $qualifier = Qualifier::findOrFail($id);
        $qualifier->update([
            'qualifier_name' => $request->qualifier_name,
        ]);

        return response()->json($qualifier);
    }

    // Delete qualifier
    public function destroy($id) {
        $qualifier = Qualifier::findOrFail($id);
        $qualifier->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }
}
