<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PRLocation;

class PRLocationController extends Controller
{
    // Get all locations
    public function index(Request $request)
    {
        $search  = $request->query('search');
        $perPage = (int) $request->query('per_page', 10);

        $query = PRLocation::query();

        if ($search) {
            $query->where('location', 'like', "%{$search}%");
        }

        return $query
            ->orderBy('location', 'ASC')
            ->paginate($perPage);
    }

    // Store new location
    public function store(Request $request)
    {
        $request->validate([
            'location' => 'required|string|max:255|unique:pr_locations,location',
        ],[
           'location.unique' => 'This location already exists. Please enter a unique location.',
        ]);

        $location = PRLocation::create(
            $request->only('location')
        );

        return response()->json($location);
    }

    // Update location
    public function update(Request $request, $id)
    {
        $location = PRLocation::findOrFail($id);

        $request->validate([
            'location' => 'required|string|max:255|unique:pr_locations,location,' . $id,
        ],[
           'location.unique' => 'This location already exists. Please enter a unique location.',
        ]);

        $location->update(
            $request->only('location')
        );

        return response()->json($location);
    }

    // Delete
    public function destroy($id)
    {
        $location = PRLocation::findOrFail($id);
        $location->delete();

        return response()->json([
            'message' => 'Location deleted successfully'
        ]);
    }
}
