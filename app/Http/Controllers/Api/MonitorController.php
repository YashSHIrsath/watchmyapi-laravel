<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\MonitorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class MonitorController extends Controller
{
    protected MonitorService $monitorService;

    public function __construct(MonitorService $monitorService)
    {
        $this->monitorService = $monitorService;
    }

    /**
     * List all monitors for the authenticated user's company.
     */
    public function index(Request $request): JsonResponse
    {
        $user = Auth::user();
        
        $projectId = $request->query('project_id');
        $query = \App\Models\Monitor::with('project')->whereHas('project', function ($q) use ($user) {
            $q->where('company_id', $user->company_id);
        })->where('status', '!=', '9');

        if ($projectId) {
            $query->where('project_id', $projectId);
        }

        $monitors = $query->orderBy('created_at', 'desc')->paginate(6);

        return response()->json([
            'success' => true,
            'data' => $monitors->items(),
            'meta' => [
                'current_page' => $monitors->currentPage(),
                'last_page' => $monitors->lastPage(),
                'total' => $monitors->total(),
            ],
            'message' => 'Monitors retrieved successfully.',
        ]);
    }

    /**
     * Create a new monitor for the authenticated user's company.
     */
    public function store(Request $request): JsonResponse
    {
        $user = Auth::user();

        // If user has no company_id, they cannot create monitors
        if (!$user->company_id) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot create monitor without company association.',
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'url' => 'required|url|max:255',
            'project_id' => 'nullable|exists:tbl_projects,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
                'message' => 'Validation failed.',
            ], 422);
        }

        $monitor = $this->monitorService->create($user->company_id, $request->only(['name', 'url', 'project_id']));

        return response()->json([
            'success' => true,
            'data' => $monitor,
            'message' => 'Monitor created successfully.',
        ], 201);
    }

    /**
     * Get a single monitor (company-scoped).
     */
    public function show(int $id): JsonResponse
    {
        $user = Auth::user();

        if (!$user->company_id) {
            return response()->json([
                'success' => false,
                'message' => 'Monitor not found.',
            ], 404);
        }

        $monitor = $this->monitorService->getForCompany($user->company_id, $id);

        if (!$monitor) {
            return response()->json([
                'success' => false,
                'message' => 'Monitor not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $monitor,
            'message' => 'Monitor retrieved successfully.',
        ]);
    }

    /**
     * Update an existing monitor.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $user = Auth::user();
        
        $validator = Validator::make($request->all(), [
            'name' => 'nullable|string|max:255',
            'url' => 'nullable|url|max:255',
            'project_id' => 'nullable|exists:tbl_projects,id',
            'status' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
                'message' => 'Validation failed.',
            ], 422);
        }

        $monitor = $this->monitorService->update($user->company_id, $id, $request->only(['name', 'url', 'project_id', 'status']));

        if (!$monitor) {
            return response()->json([
                'success' => false,
                'message' => 'Monitor not found or unauthorized.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $monitor,
            'message' => 'Monitor updated successfully.',
        ]);
    }

    /**
     * Delete a monitor (Logical deletion: status 9).
     */
    public function destroy(int $id): JsonResponse
    {
        $user = Auth::user();
        $monitor = $this->monitorService->update($user->company_id, $id, ['status' => '9']);

        if (!$monitor) {
            return response()->json([
                'success' => false,
                'message' => 'Monitor not found or unauthorized.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Monitor deleted successfully.',
        ]);
    }
}
