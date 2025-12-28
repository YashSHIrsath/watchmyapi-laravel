<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Monitor;
use App\Models\MonitorCheck;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StatsController extends Controller
{
    /**
     * Get high-level stats for the company dashboard.
     */
    public function dashboard(): JsonResponse
    {
        $companyId = Auth::user()->company_id;

        // Total Monitors
        $totalMonitors = Monitor::whereHas('project', function($q) use ($companyId) {
            $q->where('company_id', $companyId);
        })->where('status', '!=', '9')->count();

        // Total Calls (Last 30 Days)
        $totalCalls = MonitorCheck::whereHas('monitor.project', function($q) use ($companyId) {
            $q->where('company_id', $companyId);
        })->where('checked_at', '>=', Carbon::now()->subDays(30))->count();

        // Average Response Time (Last 24 Hours)
        $avgResponse = MonitorCheck::whereHas('monitor.project', function($q) use ($companyId) {
            $q->where('company_id', $companyId);
        })->where('checked_at', '>=', Carbon::now()->subHours(24))
          ->avg('response_time_ms');

        // Active Alerts (Failed checks in last 1 hour)
        $activeAlerts = MonitorCheck::whereHas('monitor.project', function($q) use ($companyId) {
            $q->where('company_id', $companyId);
        })->where('checked_at', '>=', Carbon::now()->subHour())
          ->where('is_success', false)
          ->distinct('monitor_id')
          ->count();

        // Total Projects
        $totalProjects = \App\Models\Project::where('company_id', $companyId)->count();

        return response()->json([
            'success' => true,
            'data' => [
                'total_projects' => $totalProjects,
                'total_monitors' => $totalMonitors,
                'total_calls' => $this->formatLargeNumber($totalCalls),
                'avg_response' => round($avgResponse ?? 0) . 'ms',
                'active_alerts' => $activeAlerts,
                'performance_trend' => '+12%', // Mock trend for now
            ]
        ]);
    }

    /**
     * Get detailed metrics for a specific monitor (for Chart.js).
     */
    public function monitor(int $id): JsonResponse
    {
        $companyId = Auth::user()->company_id;

        $monitor = Monitor::where('id', $id)
            ->whereHas('project', function($q) use ($companyId) {
                $q->where('company_id', $companyId);
            })->firstOrFail();

        // Last 50 checks for the chart
        $checks = MonitorCheck::where('monitor_id', $id)
            ->orderBy('checked_at', 'desc')
            ->limit(50)
            ->get()
            ->reverse()
            ->values();

        // Uptime calculation (Last 7 days)
        $sevenDaysAgo = Carbon::now()->subDays(7);
        $totalChecks = MonitorCheck::where('monitor_id', $id)->where('checked_at', '>=', $sevenDaysAgo)->count();
        $successChecks = MonitorCheck::where('monitor_id', $id)->where('checked_at', '>=', $sevenDaysAgo)->where('is_success', true)->count();
        
        $uptime = $totalChecks > 0 ? round(($successChecks / $totalChecks) * 100, 2) : 100;

        return response()->json([
            'success' => true,
            'data' => [
                'name' => $monitor->name,
                'uptime_7d' => $uptime . '%',
                'avg_latency_24h' => round(MonitorCheck::where('monitor_id', $id)->where('checked_at', '>=', Carbon::now()->subHours(24))->avg('response_time_ms') ?? 0) . 'ms',
                'history' => $checks->map(fn($c) => [
                    'time' => $c->checked_at->format('H:i:s'),
                    'latency' => $c->response_time_ms,
                    'status' => $c->http_status_code,
                    'is_success' => $c->is_success
                ])
            ]
        ]);
    }

    private function formatLargeNumber($num): string
    {
        if ($num >= 1000000) return round($num / 1000000, 1) . 'M';
        if ($num >= 1000) return round($num / 1000, 1) . 'K';
        return (string)$num;
    }
}
