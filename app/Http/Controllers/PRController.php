<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Qualifier;
use App\Models\Purpose;
use App\Models\Enduse;
use App\Models\Items;
use App\Models\ItemVariants;
use App\Models\PRSeries;
use App\Models\PRHead;
use App\Models\PRItems;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PRController extends Controller
{

   public function index(Request $request)
    {
        $search  = $request->query('search');
        $perPage = (int) $request->query('per_page', 10);

        $query = PRHead::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('pr_no', 'like', "%{$search}%")
                ->orWhere('department_name', 'like', "%{$search}%")
                ->orWhere('purpose_name', 'like', "%{$search}%")
                ->orWhere('enduse_name', 'like', "%{$search}%")
                ->orWhere('company_name', 'like', "%{$search}%")
                ->orWhere('requestor_name', 'like', "%{$search}%");
            });
        }

        $query->orderBy('date_prepared', 'desc');

        return $query->paginate($perPage);
    }
    public function getCompaniesWithLocations()
    {
        $companies = Company::with(['companylocation:id,company_id,location'])
            ->select('id','company_name','company_code')
            ->get();

        return response()->json($companies);
    }

    public function getDepartments()
    {
        return Department::select('id','department_name','department_code')->get();
    }

    public function getEmployees()
    {
        return Employee::select('id','employee_name','position')->get();
    }

    public function getUrgencies()
    {
        return Qualifier::select('id', 'qualifier_name')->get();
    }
    
    public function getPurposes()
    {
        return Purpose::select('id', 'purpose_name')->get();
    }

    public function getEnduses()
    {
        return Enduse::select('id', 'enduse_name')->get();
    }

    public function getItems()
    {
        return Items::with('item_Variants')->get();
    }

    public function searchItems(Request $request)
    {
        $q = $request->query('q', '');

        $items = Items::where('item_code', 'like', "%{$q}%")
            ->orWhere('item_description', 'like', "%{$q}%")
            ->orWhereHas('item_variants', function ($query) use ($q) {
                $query->where('variant_item_code', 'like', "%{$q}%")
                    ->orWhere('brand', 'like', "%{$q}%")
                    ->orWhere('type', 'like', "%{$q}%")
                    ->orWhere('model', 'like', "%{$q}%")
                    ->orWhere('part_no', 'like', "%{$q}%");
            })
            ->with('item_variants')
            ->get();

        return response()->json($items);
    }

    public function getNextPRSeries(Request $request)
    {
        $company_name = $request->query('company_name');
        $department_name = $request->query('department_name');

        // Get company code (no validation)
        $company_code = Company::where('company_name', $company_name)->first()->company_code;

        // Current year
        $year = Carbon::now()->year;
        $year_short = substr($year, -2);

        // Get max PR series for this company & year
        $maxSeries = PRSeries::where('year', $year)
            ->where('company_code', $company_code)
            ->max('series');

        $nextSeries = $maxSeries ? str_pad($maxSeries + 1, 4, '0', STR_PAD_LEFT) : '0001';

        // Optionally include full PR number format if department selected
        $pr_number = '';
        if ($department_name) {
            $department_code = \App\Models\Department::where('department_name', $department_name)->first()->department_code;
            $pr_number = "{$department_code}{$year_short}-{$nextSeries}-{$company_code}";
        }

        return response()->json([
            'pr_number' => $pr_number
        ]);
    }

    public function savePR(Request $request)
    {
        DB::beginTransaction();

        try {
            $year = Carbon::now()->year;

            // Get company code
            $company_code = $request->company_code;

            // 1️⃣ Get current max series for company/year
            $maxSeries = PRSeries::where('year', $year)
                ->where('company_code', $company_code)
                ->max('series');

            $nextSeries = $maxSeries ? $maxSeries + 1 : 1;

            // 2️⃣ Save PR Series
            PRSeries::create([
                'year' => $year,
                'series' => $nextSeries,
                'company_code' => $company_code,
            ]);

            // 3️⃣ Save PR Head
            $prHead = PRHead::create([
                'company_id' => $request->company_id,
                'company_name' => $request->company_name,
                'pr_no' => $request->pr_no,
                'location_id' => $request->location_id,
                'location_name' => $request->location_name,
                'date_prepared' => $request->date_prepared,
                'department_id' => $request->department_id,
                'department_name' => $request->department_name,
                'department_code' => $request->department_code,
                'requestor_id' => $request->requestor_id,
                'requestor_name' => $request->requestor_name,
                'urgency_id' => $request->urgency_id,
                'urgency_name' => $request->urgency_name,
                'purpose_id' => $request->purpose_id,
                'purpose_name' => $request->purpose_name,
                'enduse_id' => $request->enduse_id,
                'enduse_name' => $request->enduse_name,
                'notes' => $request->notes ?? '',
                'status' => $request->status,
                'user_id' => auth('sanctum')->id()
            ]);

            // 4️⃣ Save PR Items
            foreach ($request->items as $item) {
            $descParts = [];

            // Include Item Name and PN first
            if (!empty($item['itemName'])) {
                $descParts[] = $item['itemName'];
                if (!empty($item['pn'])) {
                    $descParts[count($descParts)-1] .= " (PN: {$item['pn']})";
                }
            }

            // Other fields
            foreach (['brand', 'model', 'size', 'color', 'material', 'unit', 'serial', 'type'] as $field) {
                if (!empty($item[$field])) {
                    $descParts[] = ucfirst($field) . ': ' . $item[$field];
                }
            }

            $description = implode('; ', $descParts);

            PRItems::create([
                'pr_head_id' => $prHead->id,
                'qty' => $item['qty'],
                'item_code' => $item['itemCode'],
                'item_variant_id' => $item['item_variant_id'],
                'uom' => $item['uom'] ?? '',
                'description' => $description,
                'wh_stocks' => $item['whStock'] ?? '0',
                'date_needed' => $item['dateNeeded'] ?? null,
            ]);
        }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'PR saved successfully',
                'pr_id' => $prHead->id
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Failed to save PR: '.$e->getMessage()
            ], 500);
        }
    }

    public function get_pr($prId)
    {
        try {
            // Load PR head with company, company locations, and PR items
            $pr = PRHead::with(['company.companylocation', 'pr_items'])->findOrFail($prId);

            // Prepare company addresses array
            $company_addresses = $pr->company->companylocation->pluck('address')->toArray();

            // Get first telefax
            $first_telefax = $pr->company->companylocation->first()?->telefax ?? '';

            return response()->json([
                'success' => true,
                'data' => $pr,
                'company_addresses' => $company_addresses,
                'first_telefax' => $first_telefax,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch PR: '.$e->getMessage()
            ], 500);
        }
    }

}
