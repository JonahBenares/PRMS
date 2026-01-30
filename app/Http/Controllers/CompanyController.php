<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\CompanyLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CompanyController extends Controller
{
    // LIST
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

        return $query
            ->orderBy('company_name', 'ASC')
            ->paginate($perPage);
    }

    // CREATE COMPANY
    public function store(Request $request)
    {
        $data = $request->validate([
            'company_name' => 'required|string|max:255',
            'company_code' => 'required|string|max:100|unique:companies,company_code',
            'company_logo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048'
        ]);

        if ($request->hasFile('company_logo')) {
            $file = $request->file('company_logo');
            $filename = strtolower($request->company_code) . '_' . now()->format('Ymd_His')
                . '.' . $file->getClientOriginalExtension();

            $file->storeAs('logos', $filename, 'public');
            $data['company_logo'] = $filename;
        }

        $company = Company::create($data);

        return response()->json($company);
    }

    // UPDATE COMPANY
    public function update(Request $request, $id)
    {
        $company = Company::findOrFail($id);

        $data = $request->validate([
            'company_name' => 'required|string|max:255',
            'company_code' => 'required|string|max:100|unique:companies,company_code,' . $id,
            'company_logo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048'
        ]);

        if ($request->hasFile('company_logo')) {

            if ($company->company_logo) {
                Storage::disk('public')->delete('logos/' . $company->company_logo);
            }

            $file = $request->file('company_logo');
            $filename = strtolower($request->company_code) . '_' . now()->format('Ymd_His')
                . '.' . $file->getClientOriginalExtension();

            $file->storeAs('logos', $filename, 'public');
            $data['company_logo'] = $filename;
        } else {
            unset($data['company_logo']);
        }

        $company->update($data);

        return response()->json($company);
    }

    // DELETE COMPANY
    public function destroy($id)
    {
        $company = Company::findOrFail($id);

        if ($company->company_logo) {
            Storage::disk('public')->delete('logos/' . $company->company_logo);
        }

        $company->delete();

        return response()->json([
            'message' => 'Company deleted successfully'
        ]);
    }

    // CREATE LOCATION
    public function storeLocation(Request $request)
    {
        $data = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'location'   => 'required|string|max:255',
            'address'    => 'required|string|max:255',
            'telephone'  => 'nullable|string|max:50',
            'telefax'    => 'nullable|string|max:50',
        ]);

        return response()->json(
            CompanyLocation::create($data)
        );
    }

    // UPDATE LOCATION
    public function updateLocation(Request $request, $id)
    {
        $location = CompanyLocation::findOrFail($id);

        $data = $request->validate([
            'location'  => 'required|string|max:255',
            'address'   => 'required|string|max:255',
            'telephone' => 'nullable|string|max:50',
            'telefax'   => 'nullable|string|max:50',
        ]);

        $location->update($data);

        return response()->json($location);
    }

    // DELETE LOCATION
    public function destroyLocation($id)
    {
        CompanyLocation::findOrFail($id)->delete();

        return response()->json([
            'message' => 'Location deleted'
        ]);
    }
}
