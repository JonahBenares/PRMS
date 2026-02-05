<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    // Server-side pagination + search
    public function index(Request $request)
    {
        $search  = $request->query('search');
        $perPage = (int) $request->query('per_page', 10);

        $query = Category::with('subcategories');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('category_name', 'like', "%{$search}%")
                ->orWhereHas('subcategories', function ($sq) use ($search) {
                    $sq->where('sub_cat_name', 'like', "%{$search}%");
                });
            });
        }

        return $query->paginate($perPage);
    }

    // Create Category
    public function store_category(Request $request)
    {
        $request->validate([
            'category_name' => 'required|string|max:255|unique:category,category_name',
        ], [
            'category_name.unique' => 'This category name already exists. Please enter a unique name.',
        ]);

        $category = Category::create([
            'category_name' => $request->category_name
        ]);

        return response()->json($category, 201);
    }

    // Update Category
    public function update_category(Request $request, $id)
    {
        $request->validate([
            'category_name' => 'required|string|max:255|unique:category,category_name',
        ],[
           'category_name.unique' => 'This category name already exists. Please enter a unique name.',
        ]);

        $category = Category::findOrFail($id);
        $category->update([
            'category_name' => $request->category_name
        ]);

        return response()->json($category, 200);
    }

    // Create Subcategory
    // public function store_subcategory(Request $request)
    // {
    //     $request->validate([
    //         'sub_cat_name' => 'required|string|max:255',
    //         'category_id' => 'required|exists:categories,id'
    //     ]);

    //     $sub = SubCategory::create([
    //         'sub_cat_name' => $request->sub_cat_name,
    //         'category_id' => $request->category_id
    //     ]);

    //     return response()->json($sub, 201);
    // }

    public function store_subcategory(Request $request)
    {
        try {
            $validated = $request->validate([
                'sub_cat_name' => 'required|string|max:255|unique:sub_category,sub_cat_name',
                'category_id'  => 'required|exists:category,id',
            ],[
            'sub_cat_name.unique' => 'This subcategory name already exists. Please enter a unique name.',
            ]);

            SubCategory::create($validated);

            return response()->json(['message' => 'Subcategory created'], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // Update Subcategory
    public function update_subcategory(Request $request, $id)
    {
        $request->validate([
            'sub_cat_name' => 'required|string|max:255|unique:sub_category,sub_cat_name',
        ],[
           'sub_cat_name.unique' => 'This subcategory name already exists. Please enter a unique name.',
        ]);

        $sub = SubCategory::findOrFail($id);
        $sub->update([
            'sub_cat_name' => $request->sub_cat_name
        ]);

        return response()->json($sub, 200);
    }

    // Delete Category
    public function destroy_category($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();

        return response()->json(['message' => 'Category deleted'], 200);
    }

    // Delete Subcategory
    public function destroy_subcategory($id)
    {
        $sub = SubCategory::findOrFail($id);
        $sub->delete();

        return response()->json(['message' => 'Subcategory deleted'], 200);
    }
}
