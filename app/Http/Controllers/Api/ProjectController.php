<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ProjectController extends Controller
{
    /**
     * List projects belonging to the authenticated user's company (paginated).
     */
    public function index(Request $request): JsonResponse
    {
        $user = Auth::user();

        if (!$user->company_id) {
            return response()->json([
                'success' => true,
                'data' => [],
                'message' => 'No projects found.',
            ]);
        }

        $projects = Project::where('company_id', $user->company_id)
            ->withCount('monitors')
            ->orderBy('is_default', 'desc')
            ->orderBy('name', 'asc')
            ->paginate(6); // 6 projects per page as requested

        return response()->json([
            'success' => true,
            'data' => $projects->items(),
            'meta' => [
                'current_page' => $projects->currentPage(),
                'last_page' => $projects->lastPage(),
                'total' => $projects->total(),
            ],
            'message' => 'Projects retrieved successfully.',
        ]);
    }

    /**
     * Create a new project (name only).
     */
    public function store(Request $request): JsonResponse
    {
        $user = Auth::user();

        if (!$user->company_id) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot create project without company association.',
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
                'message' => 'Validation failed.',
            ], 422);
        }

        $project = Project::create([
            'company_id' => $user->company_id,
            'name' => $request->name,
            'is_default' => false,
        ]);

        return response()->json([
            'success' => true,
            'data' => $project,
            'message' => 'Project created successfully.',
        ], 201);
    }

    /**
     * Update an existing project.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $user = Auth::user();
        $project = Project::where('company_id', $user->company_id)->where('id', $id)->first();

        if (!$project) {
            return response()->json(['success' => false, 'message' => 'Project not found.'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $project->update(['name' => $request->name]);

        return response()->json([
            'success' => true,
            'data' => $project,
            'message' => 'Project updated successfully.',
        ]);
    }

    /**
     * Delete a project.
     */
    public function destroy(int $id): JsonResponse
    {
        $user = Auth::user();
        $project = Project::where('company_id', $user->company_id)->where('id', $id)->first();

        if (!$project) {
            return response()->json(['success' => false, 'message' => 'Project not found.'], 404);
        }

        if ($project->is_default) {
            return response()->json(['success' => false, 'message' => 'Default project cannot be deleted.'], 400);
        }

        // Reassign monitors to default project before deleting? 
        // Or let cascade delete them (currently migration says onDelete('cascade') for project_id in monitors)
        // User said "move monitors to default project safely" earlier for migration, 
        // but for manual deletion, let's just delete them if cascading is set, 
        // OR better, move them to default project to be safe.
        
        $defaultProject = Project::where('company_id', $user->company_id)->where('is_default', true)->first();
        if ($defaultProject) {
            $project->monitors()->update(['project_id' => $defaultProject->id]);
        }

        $project->delete();

        return response()->json(['success' => true, 'message' => 'Project deleted successfully.']);
    }
}
