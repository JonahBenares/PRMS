<?php

namespace App\Http\Controllers;
use App\Models\Category;
use App\Models\SubCategory;

use Illuminate\Http\Request;

class CategoryController extends Controller
{
    // Get all Categories
    public function index()
    {
        return Category::with('subcategories')->get();
    }

    public function store_category(Request $request)
    {
        $request->validate(['category_name' => 'required']);
        Category::create($request->only('category_name'));
    }

    public function update_category(Request $request, $id)
    {
        $request->validate([
        'category_name' => 'required|string|max:255',
        ]);

        Category::findOrFail($id)->update($request->only('category_name'));
    }

    public function store_subcategory(Request $request)
    {
        $request->validate([
            'sub_cat_name' => 'required',
            'category_id' => 'required|exists:category,id'
        ]);

        SubCategory::create([
            'sub_cat_name' => $request->sub_cat_name,
            'category_id' => $request->category_id,
        ]);
    }

    public function update_subcategory(Request $request, $id)
{
    $request->validate([
        'sub_cat_name' => 'required|string|max:255',
    ]);

    SubCategory::findOrFail($id)->update($request->only('sub_cat_name'));
}
}
