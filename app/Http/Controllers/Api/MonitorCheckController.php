<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\MonitorCheckService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class MonitorCheckController extends Controller
{
    protected MonitorCheckService $monitorCheckService;

    public function __construct(MonitorCheckService $monitorCheckService)
    {
        $this->monitorCheckService = $monitorCheckService;
    }

    /**
     * List recent checks for a specific monitor.
     */
    public function index(int $monitorId): JsonResponse
    {
        $user = Auth::user();

        if (!$user->company_id) {
            return response()->json([
                'success' => true,
                'data' => [],
                'message' => 'No checks available.',
            ]);
        }

        $checks = $this->monitorCheckService->listForMonitor($monitorId, $user->company_id);

        return response()->json([
            'success' => true,
            'data' => $checks,
            'message' => 'Monitor checks retrieved successfully.',
        ]);
    }
}
