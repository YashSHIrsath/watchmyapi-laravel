<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectWebController extends Controller
{
    /**
     * Show the project workspace.
     */
    public function show(string $slug)
    {
        $id = Project::decryptId($slug);
        
        if (!$id) {
            abort(404, 'Invalid Project identifier.');
        }

        $project = Project::where('id', $id)
            ->where('company_id', Auth::user()->company_id)
            ->firstOrFail();

        return view('pages.projects.show', compact('project'));
    }
}
