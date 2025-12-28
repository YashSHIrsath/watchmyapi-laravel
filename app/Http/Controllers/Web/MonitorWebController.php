<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Monitor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MonitorWebController extends Controller
{
    /**
     * Show the monitor history page.
     */
    public function history(string $slug)
    {
        $id = Monitor::decryptId($slug);
        
        if (!$id) {
            abort(404, 'Invalid Monitor identifier.');
        }

        $monitor = Monitor::where('id', $id)
            ->whereHas('project', function ($query) {
                $query->where('company_id', Auth::user()->company_id);
            })
            ->firstOrFail();

        return view('pages.monitors.history', compact('monitor'));
    }
}
