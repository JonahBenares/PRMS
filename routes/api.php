<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\EnduseController;
use App\Http\Controllers\PurposeController;
use App\Http\Controllers\QualifierController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PRLocationController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\PRController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/login_form', [AuthController::class,'login_form']);
Route::post('/login_process', [AuthController::class,'login_process']);
Route::middleware('auth:sanctum')->get('/dashboard', [AuthController::class, 'dashboard']);

Route::get('/categories', [CategoryController::class, 'index']);
Route::post('/new_category', [CategoryController::class, 'store_category']);
Route::put('/update_category/{id}', [CategoryController::class, 'update_category']);

Route::post('/new_subcategory', [CategoryController::class, 'store_subcategory']);
Route::put('/update_subcategory/{id}', [CategoryController::class, 'update_subcategory']);

Route::get('/companies', [CompanyController::class, 'index']);
Route::post('/companies', [CompanyController::class, 'store_company']);
Route::put('/companies/{id}', [CompanyController::class, 'update_company']);
Route::delete('/companies/{id}', [CompanyController::class, 'destroy']);

// locations (same controller)
Route::post('/company_locations', [CompanyController::class, 'storeLocation']);
Route::put('/company_locations/{id}', [CompanyController::class, 'updateLocation']);
Route::delete('/company_locations/{id}', [CompanyController::class, 'destroyLocation']);

Route::get('/departments', [DepartmentController::class, 'index']);
Route::post('/departments', [DepartmentController::class, 'store']);
Route::put('/departments/{id}', [DepartmentController::class, 'update']);
Route::delete('/departments/{id}', [DepartmentController::class, 'destroy']);

Route::get('/enduses', [EnduseController::class, 'index']);
Route::post('/enduses', [EnduseController::class, 'store']);
Route::put('/enduses/{id}', [EnduseController::class, 'update']);
Route::delete('/enduses/{id}', [EnduseController::class, 'destroy']);

Route::get('/employees', [EmployeeController::class, 'index']);
Route::post('/employees', [EmployeeController::class, 'store']);
Route::put('/employees/{id}', [EmployeeController::class, 'update']);
Route::get('/department_list',[EmployeeController::class,'all_department']);

Route::get('/subcategories', [ItemController::class, 'subcategories']);
Route::get('/items/last_series', [ItemController::class, 'getNextItemAndVariantCodes']);
Route::get('/items', [ItemController::class, 'index']);
Route::post('/items', [ItemController::class, 'store']);
Route::prefix('items')->group(function () {
    Route::get('/{id}', [ItemController::class, 'show']);             // View item + variants
    Route::post('/{id}/variants', [ItemController::class, 'addVariants']); // Add new variants only
});

Route::get('/purposes', [PurposeController::class, 'index']);
Route::post('/purposes', [PurposeController::class, 'store']);
Route::put('/purposes/{id}', [PurposeController::class, 'update']);
Route::delete('/purposes/{id}', [PurposeController::class, 'destroy']);

Route::get('/qualifiers', [QualifierController::class, 'index']);
Route::post('/qualifiers', [QualifierController::class, 'store']);
Route::put('/qualifiers/{id}', [QualifierController::class, 'update']);
Route::delete('/qualifiers/{id}', [QualifierController::class, 'destroy']);

Route::get('/users', [UserController::class, 'index']);
Route::post('/users', [UserController::class, 'store']);
Route::put('/users/{id}', [UserController::class, 'update']);
Route::delete('/users/{id}', [UserController::class, 'destroy']);

Route::get('/pr-locations', [PRLocationController::class, 'index']);
Route::post('/pr-locations', [PRLocationController::class, 'store']);
Route::put('/pr-locations/{id}', [PRLocationController::class, 'update']);
Route::delete('/pr-locations/{id}', [PRLocationController::class, 'destroy']);

Route::get('/prs', [PRController::class, 'index']);
Route::get('/api/companies', [PRController::class, 'getCompaniesWithLocations']);
Route::get('/api/departments', [PRController::class, 'getDepartments']);
Route::get('/api/employees', [PRController::class, 'getEmployees']);
Route::get('/urgencies', [PRController::class, 'getUrgencies']);
Route::get('/purposes', [PRController::class, 'getPurposes']);
Route::get('/enduses', [PRController::class, 'getEnduses']);
Route::get('/items-search', [PRController::class, 'searchItems']);
Route::get('/items/{item}', [PRController::class, 'show']);
Route::get('/next-pr-series', [PRController::class, 'getNextPRSeries']);
Route::middleware('auth:sanctum')->post('/pr/save', [PRController::class, 'savePR']);
Route::get('/pr/{id}', [PRController::class, 'get_pr']);



