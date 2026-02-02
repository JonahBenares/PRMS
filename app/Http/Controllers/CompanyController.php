<?php

namespace App\Http\Controllers;
use App\Models\Company;
use App\Models\CompanyLocation;

use Illuminate\Http\Request;

class CompanyController extends Controller
{
    // Get all Company
    public function index(Request $request)
    {
        $search  = $request->query('search');
        $perPage = (int) $request->query('per_page', 10);

        $query = Company::with('companylocation');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('company_name', 'like', "%{$search}%")
                  ->orWhere('company_code', 'like', "%{$search}%")
                  ->orWhereHas('companylocation', function ($lq) use ($search) {
                      $lq->where('location', 'like', "%{$search}%");
                  });
            });
        }

        return $query->paginate($perPage);
    }

    public function store_company(Request $request)
    {
        $data = $request->validate([
            'company_name' => 'required|string',
            'company_code' => 'required|string|unique:company,company_code',
            'company_logo' => 'nullable|image|max:2048'
        ],[
           'company_code.unique' => 'This company code already exists. Please enter a unique code.',
        ]);

        if ($request->hasFile('company_logo')) {
            $file = $request->file('company_logo');

            $companyCode = strtolower($request->company_code);
            $extension = $file->getClientOriginalExtension();
            $filename = $companyCode . '_' . now()->format('Ymd') . '.' . $extension;

            // Save in storage/app/public/logos
            $file->storeAs('logos', $filename, 'public');

            $data['company_logo'] = $filename; // Save only filename in DB
        }

        return Company::create($data);
    }

    public function update_company(Request $request, $id)
    {
        $company = Company::findOrFail($id);

        // Validate only if logo is uploaded
        $data = $request->validate([
            'company_name' => 'required|string',
            'company_code' => 'required|string',
            'company_logo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        // Check if a new logo file is uploaded
        if ($request->hasFile('company_logo')) {

            // Delete old logo if exists
            if ($company->company_logo) {
                Storage::disk('public')->delete('logos/' . $company->company_logo);
            }

            $file = $request->file('company_logo');
            $companyCode = strtolower($request->company_code);
            $extension = $file->getClientOriginalExtension();
            $filename = $companyCode . '_' . now()->format('Ymd_His') . '.' . $extension;

            // Store new file
            $file->storeAs('logos', $filename, 'public');

            $data['company_logo'] = $filename;
        } else {
            // If no file uploaded, remove company_logo from $data so it won't overwrite existing
            unset($data['company_logo']);
        }

        $company->update($data);

        return response()->json([
            'success' => true,
            'company' => $company
        ]);
    }

    public function storeLocation(Request $request, Company $company)
    {
        $data = $request->validate([
            'company_id' => 'required|integer|exists:company,id',
            'location' => 'required|string',
            'address' => 'required|string',
            'telephone' => 'nullable|string',
            'telefax' => 'nullable|string',
        ]);

        return CompanyLocation::create($data);
    }

    public function updateLocation(Request $request, $id)
    {
        $location = CompanyLocation::findOrFail($id);

        $data = $request->validate([
            'location' => 'required|string',
            'address' => 'required|string',
            'telephone' => 'nullable|string',
            'telefax' => 'nullable|string',
        ]);

        $location->update($data);

        return $location;
    }
}
