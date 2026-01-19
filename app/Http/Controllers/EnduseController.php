<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Enduse;

class EnduseController extends Controller
{
    // Get all enduses
    public function index() {
        return Enduse::all();
    }

    // Store new enduse
    public function store(Request $request) {
        $request->validate([
            'enduse_name' => 'required|string|max:255',
        ]);

        $enduse = Enduse::create([
            'enduse_name' => $request->enduse_name,
        ]);

        return response()->json($enduse, 201);
    }

    // Update enduse
    public function update(Request $request, $id) {
        $request->validate([
            'enduse_name' => 'required|string|max:255',
        ]);

        $enduse = Enduse::findOrFail($id);
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

