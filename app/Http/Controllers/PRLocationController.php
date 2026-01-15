<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PRLocation;

class PRLocationController extends Controller
{
    // GET /api/pr-locations
    public function index()
    {
        return PRLocation::all();
    }

    // POST /api/pr-locations
    public function store(Request $request)
    {
        $request->validate([
            'location' => 'required|string|max:255',
        ]);

        $location = PRLocation::create([
            'location' => $request->location,
        ]);

        return response()->json($location, 201);
    }

    // PUT /api/pr-locations/{id}
    public function update(Request $request, $id)
    {
        $location = PRLocation::findOrFail($id);

        $request->validate([
            'location' => 'required|string|max:255',
        ]);

        $location->update([
            'location' => $request->location,
        ]);

        return response()->json($location);
    }

    // DELETE /api/pr-locations/{id}
    public function destroy($id)
    {
        $location = PRLocation::findOrFail($id);
        $location->delete();

        return response()->json(['message' => 'Deleted successfully']);
    }
}