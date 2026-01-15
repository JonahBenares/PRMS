<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\Items;
use App\Models\ItemVariants;
use App\Models\SubCategory;

class ItemController extends Controller
{

/**
     * Return all items with variants
     */
    public function index()
    {
        $items = Items::with('item_Variants')->get();

        // Transform to the structure Vue expects
        $result = $items->map(function ($item) {
            return [
                'id' => $item->id,
                'item_code' => $item->item_code,
                'item_description' => $item->item_description,
                'category_name' => $item->category_name,
                'sub_cat_name' => $item->sub_category_name, // adjust field name
                'item_Variants' => $item->item_Variants->map(function ($v) {
                    return [
                        'id' => $v->id,
                        'variant_item_code' => $v->variant_item_code,
                        'brand' => $v->brand,
                        'type' => $v->type,
                        'model' => $v->model,
                        'part_no' => $v->part_no,
                        'size' => $v->size,
                        'color' => $v->color,
                        'material' => $v->material,
                        'uom' => $v->uom,
                        'img1' => $v->img1,
                        'img2' => $v->img2,
                        'img3' => $v->img3,
                    ];
                }),
            ];
        });

        return response()->json($result);
    }

    
    /**
     * Get subcategories with category info
     */
    public function subcategories()
    {
        $subcats = Subcategory::with('categories')->get();

        return $subcats->map(function($sub) {
            return [
                'id' => $sub->id,
                'sub_cat_name' => $sub->sub_cat_name,
                'category_id' => $sub->categories?->id ?? '',
                'category_name' => $sub->categories?->category_name ?? ''
            ];
        });
    }

    /**
     * Get next item and variant code
     */
public function getNextItemAndVariantCodes()
{
    // Get the max numeric value of item_code
    $maxItemCode = Items::whereNotNull('item_code')
        ->where('item_code', '!=', '')
        ->selectRaw('MAX(CAST(item_code AS UNSIGNED)) as max_code')
        ->first()
        ->max_code;

    // If no items exist, start from 1
    $nextItemNumber = $maxItemCode ? ((int)$maxItemCode + 1) : 1;

    // Format as 4-digit code
    $nextItemCode = str_pad($nextItemNumber, 4, '0', STR_PAD_LEFT);

    // First variant code
    $nextVariantCode = $nextItemCode . '-001';

    // Debug: check what you are returning
    // return response()->json(['max' => $maxItemCode, 'next' => $nextItemCode]);

    return response()->json([
        'item_code' => $nextItemCode,
        'first_variant_code' => $nextVariantCode
    ]);
}


    /**
     * Store item + variants
     */
    public function store(Request $request)
    {
        $request->validate([
            'sub_category_id'  => 'required',
            'item_description' => 'required',
            'variants'         => 'required|array|min:1',
        ]);

        DB::transaction(function () use ($request , &$item) {

            // Generate next item code
            $lastItem = Items::whereNotNull('item_code')->where('item_code', '!=', '')->orderBy('id', 'desc')->first();
            $nextItemNumber = 1;
            if ($lastItem && preg_match('/(\d{4})$/', $lastItem->item_code, $matches)) {
                $nextItemNumber = (int)$matches[1] + 1;
            }
            $itemCode = str_pad($nextItemNumber, 4, '0', STR_PAD_LEFT);

            // Get Subcategory + Category
            $sub = Subcategory::with('categories')->findOrFail($request->sub_category_id);

            // Create main item
            $item = Items::create([
                'item_code'         => $itemCode,
                'category_id'       => $sub->category_id,
                'category_name'     => $sub->categories->category_name,
                'sub_cat_id'   => $sub->id,
                'sub_cat_name' => $sub->sub_cat_name,
                'item_description'  => $request->item_description,
            ]);

            // Handle variants
            foreach ($request->variants as $index => $variant) {
                $variantSeries = str_pad($index + 1, 3, '0', STR_PAD_LEFT);
                $variantCode = $itemCode . '-' . $variantSeries;

                $img1 = $img2 = $img3 = null;

                for ($i = 0; $i < 3; $i++) {
                    if ($request->hasFile("variants.$index.images.$i")) {

                        $file = $request->file("variants.$index.images.$i");

                        // Clean item description to be filename-safe
                        $cleanName = preg_replace('/[^A-Za-z0-9\-]/', '_', $item->item_description);

                        // Filename: item_description + variant_code + _index
                        $filename = $cleanName . '_' . $variantCode . '_' . ($i + 1) . '.' . $file->getClientOriginalExtension();

                        // Store in public/items folder
                        $path = $file->storeAs('items', $filename, 'public');

                        // Save only the filename in DB (without 'items/' folder)
                        ${'img' . ($i + 1)} = $filename;
                    }
                }


                ItemVariants::create([
                    'item_id'           => $item->id,
                    'variant_item_code' => $variantCode,
                    'brand'             => $variant['brand'] ?? null,
                    'type'              => $variant['type'] ?? null,
                    'model'             => $variant['model'] ?? null,
                    'part_no'           => $variant['partNo'] ?? null,
                    'size'              => $variant['size'] ?? null,
                    'color'             => $variant['color'] ?? null,
                    'material'          => $variant['material'] ?? null,
                    'uom'               => $variant['uom'] ?? null,
                    'img1'              => $img1,
                    'img2'              => $img2,
                    'img3'              => $img3,
                ]);
            }
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Item and variants saved successfully',
            'item_id' => $item->id, // <-- return the new ID
        ]);
    }

    /**
     * Show item details + variants (read-only)
     */
    public function show($id)
    {
        $item = Items::with('item_variants')->findOrFail($id);

        return response()->json($item);
    }

    /**
     * Add new variants to existing item
     */
public function addVariants(Request $request, $id)
{
    $item = Items::findOrFail($id);

    $request->validate([
        'variants' => 'required|array|min:1',
        'variants.*.variant_item_code' => 'required|string',
    ]);

    DB::transaction(function() use ($request, $item) {
        foreach ($request->variants as $index => $variantData) {

            if (!empty($variantData['isNew'])) {
                $variantCode = $variantData['variant_item_code'];

                $img1 = $img2 = $img3 = null;

                // Handle 3 images
                for ($i = 0; $i < 3; $i++) {
                    if ($request->hasFile("variants.$index.images.$i")) {
                        $file = $request->file("variants.$index.images.$i");

                        // Clean item description for filename
                        $cleanName = preg_replace('/[^A-Za-z0-9\-]/', '_', $item->item_description);

                        // Filename: itemDescription_variantCode_index
                        $filename = $cleanName . '_' . $variantCode . '_' . ($i+1) . '.' . $file->getClientOriginalExtension();

                        // Store in public/items
                        $file->storeAs('items', $filename, 'public');

                        ${'img'.($i+1)} = $filename;
                    }
                }

                // Save variant
                ItemVariants::create([
                    'item_id' => $item->id,
                    'variant_item_code' => $variantCode,
                    'brand' => $variantData['brand'] ?? null,
                    'part_no' => $variantData['partNo'] ?? null,
                    'type' => $variantData['type'] ?? null,
                    'model' => $variantData['model'] ?? null,
                    'size' => $variantData['size'] ?? null,
                    'color' => $variantData['color'] ?? null,
                    'material' => $variantData['material'] ?? null,
                    'uom' => $variantData['uom'] ?? null,
                    'img1' => $img1,
                    'img2' => $img2,
                    'img3' => $img3,
                ]);
            }
        }
    });

    return response()->json([
        'status' => 'success',
        'message' => 'New variants added successfully'
    ]);
}


}