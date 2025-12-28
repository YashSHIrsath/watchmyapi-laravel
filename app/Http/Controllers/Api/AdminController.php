<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    /**
     * List all companies for the admin dashboard.
     */
    public function index(): JsonResponse
    {
        // Admin middleware already checks for super_admin
        $companies = Company::withCount('users')->get();

        return response()->json([
            'success' => true,
            'data' => $companies,
            'message' => 'Companies retrieved successfully.'
        ]);
    }
}
