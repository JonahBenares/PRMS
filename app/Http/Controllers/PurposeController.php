<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Purpose;

class PurposeController extends Controller
{
    // Get all purposes
    public function index(Request $request)
    {
        $search  = $request->query('search');
        $perPage = (int) $request->query('per_page', 10);

        $query = Purpose::query();

        if ($search) {
            $query->where('purpose_name', 'like', "%{$search}%");
        }

        return $query->paginate($perPage);
    }

    // Store new purpose
    public function store(Request $request) {
        $request->validate([
            'purpose_name' => 'required|string|max:255|unique:purposes,purpose_name',
        ],[
           'purpose_name.unique' => 'This purpose already exists. Please enter a unique purpose.',
        ]);

        $purpose = Purpose::create([
            'purpose_name' => $request->purpose_name,
        ]);

        return response()->json($purpose, 201);
    }

    // Update purpose
    public function update(Request $request, $id) {

        $purpose = Purpose::findOrFail($id);

        $request->validate([
            'purpose_name' => 'required|string|max:255|unique:purposes,purpose_name,' . $id,

        ]);

        $purpose->update($request->only('purpose_name'));
        return response()->json($purpose);
    }

    // Delete purpose
    public function destroy($id) {
        $purpose = Purpose::findOrFail($id);
        $purpose->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }
}
