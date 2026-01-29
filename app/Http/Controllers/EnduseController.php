<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Enduse;

class EnduseController extends Controller
{
    // Get all enduses
    public function index(Request $request)
    {
        $search  = $request->query('search');
        $perPage = (int) $request->query('per_page', 10);

        $query = Enduse::query();

        if ($search) {
            $query->where('enduse_name', 'like', "%{$search}%");
        }

        return $query->paginate($perPage);
    }

    // Store new enduse
    public function store(Request $request)
    {
        $request->validate([
            'enduse_name' => 'required|string|max:255|unique:enduses,enduse_name',
        ]);

        $enduse = Enduse::create([
            'enduse_name' => $request->enduse_name,
        ]);

        return response()->json($enduse, 201);
    }

    // Update enduse
    public function update(Request $request, $id)
    {
        $enduse = Enduse::findOrFail($id);

        $request->validate([
            'enduse_name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('enduses', 'enduse_name')->ignore($enduse->id),
            ],
        ]);

        $enduse->update([
            'enduse_name' => $request->enduse_name,
        ]);

        return response()->json($enduse);
    }

    // Delete enduse
    public function destroy($id) {
        $enduse = Enduse::findOrFail($id);
        $enduse->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }
}

