<?php

namespace App\Services;

use App\Models\Monitor;
use App\Models\Project;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class MonitorService
{
    /**
     * List all monitors for a specific company (across all projects).
     */
    public function listForCompany(int $companyId): Collection
    {
        return Monitor::with('project')->whereHas('project', function ($query) use ($companyId) {
            $query->where('company_id', $companyId);
        })
        ->orderBy('created_at', 'desc')
        ->get();
    }

    /**
     * List all monitors for a specific company (across all projects, paginated).
     */
    public function listForCompanyPaginated(int $companyId, int $perPage = 6, ?int $projectId = null): LengthAwarePaginator
    {
        $query = Monitor::with('project')->whereHas('project', function ($q) use ($companyId) {
            $q->where('company_id', $companyId);
        });

        if ($projectId) {
            $query->where('project_id', $projectId);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    /**
     * Create a new monitor associated with a project.
     * If project_id is missing, use the company's default project.
     */
    public function create(int $companyId, array $data): Monitor
    {
        $projectId = $data['project_id'] ?? null;

        if (!$projectId) {
            $defaultProject = \App\Models\Project::where('company_id', $companyId)
                ->where('is_default', true)
                ->first();
            $projectId = $defaultProject->id;
        }

        return Monitor::create([
            'project_id' => $projectId,
            'name' => $data['name'],
            'url' => $data['url'],
            'status' => $data['status'] ?? 'active',
        ]);
    }

    /**
     * Fetch a single monitor with explicit project/company scoping.
     */
    public function getForCompany(int $companyId, int $monitorId): ?Monitor
    {
        return Monitor::whereHas('project', function ($query) use ($companyId) {
            $query->where('company_id', $companyId);
        })
        ->where('id', $monitorId)
        ->first();
    }
    /**
     * Update an existing monitor.
     */
    public function update(int $companyId, int $monitorId, array $data): ?Monitor
    {
        $monitor = $this->getForCompany($companyId, $monitorId);
        
        if (!$monitor) {
            return null;
        }

        $monitor->update($data);
        return $monitor;
    }
}
