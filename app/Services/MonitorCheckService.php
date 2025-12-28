<?php

namespace App\Services;

use App\Models\Monitor;
use App\Models\MonitorCheck;
use Illuminate\Database\Eloquent\Collection;

class MonitorCheckService
{
    /**
     * List recent checks for a specific monitor, enforcing company ownership.
     */
    public function listForMonitor(int $monitorId, int $companyId, int $limit = 50): Collection
    {
        // Verify ownership path: Monitor -> Project -> Company
        $monitor = Monitor::whereHas('project', function ($query) use ($companyId) {
            $query->where('company_id', $companyId);
        })
        ->where('id', $monitorId)
        ->first();

        if (!$monitor) {
            return new Collection();
        }

        return $monitor->checks()
            ->orderBy('checked_at', 'desc')
            ->limit($limit)
            ->get();
    }
}
