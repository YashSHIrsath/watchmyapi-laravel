<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('pages.home');
});

Route::get('/login', function () {
    return view('pages.auth.login');
})->name('login');

Route::group(['middleware' => ['auth']], function () {
    Route::get('/dashboard', function () {
        return view('pages.dashboard.index');
    })->middleware('company');

    Route::get('/admin/dashboard', function () {
        return view('pages.admin.dashboard.index');
    })->middleware('admin');

    // Project & Monitor Views (Separate Pages)
    Route::get('/projects/{slug}', [\App\Http\Controllers\Web\ProjectWebController::class, 'show'])->name('projects.show')->middleware('company');
    Route::get('/history/{slug}', [\App\Http\Controllers\Web\MonitorWebController::class, 'history'])->name('monitors.history')->middleware('company');
});

// Temporary verification route
Route::get('/verify-monitor-foundation', function () {
    $authService = app(\App\Services\AuthService::class);
    $monitorService = app(\App\Services\MonitorService::class);
    $results = [];

    // 1. Test Company User - Login
    $userLogin = $authService->login(['email' => 'user@acme.com', 'password' => 'password123']);
    $results['Company User Login'] = $userLogin ? 'PASS' : 'FAIL';

    if (!$userLogin) {
        return response()->json(['success' => false, 'results' => $results]);
    }

    $token = $userLogin['access_token'];
    Auth::guard('api')->setToken($token);
    $user = Auth::guard('api')->user();

    // 2. Create Monitor
    try {
        $monitor = $monitorService->create($user->company_id, [
            'name' => 'Test Monitor',
            'url' => 'https://example.com'
        ]);
        $results['Monitor Creation'] = 'PASS';
        $results['Monitor ID'] = $monitor->id;
    } catch (\Exception $e) {
        $results['Monitor Creation'] = 'FAIL: ' . $e->getMessage();
    }

    // 3. List Monitors for Company
    $monitors = $monitorService->listForCompany($user->company_id);
    $results['Monitor List Count'] = count($monitors);
    $results['Monitor List'] = $monitors->count() > 0 ? 'PASS' : 'FAIL';

    // 4. Verify Company Scoping
    $foundMonitor = $monitors->firstWhere('name', 'Test Monitor');
    $results['Company Scoping'] = ($foundMonitor && $foundMonitor->company_id === $user->company_id) ? 'PASS' : 'FAIL';

    // 5. Test Super Admin - Login
    $adminLogin = $authService->login(['email' => 'admin@watchmyapi.com', 'password' => 'password123']);
    $results['Super Admin Login'] = $adminLogin ? 'PASS' : 'FAIL';

    if ($adminLogin) {
        Auth::guard('api')->setToken($adminLogin['access_token']);
        $admin = Auth::guard('api')->user();

        // 6. Super Admin has no company_id
        $results['Super Admin company_id'] = $admin->company_id === null ? 'NULL (Expected)' : 'NOT NULL (Unexpected)';

        // 7. List monitors for super admin (should be empty due to no company_id)
        if ($admin->company_id) {
            $adminMonitors = $monitorService->listForCompany($admin->company_id);
            $results['Super Admin Monitor Count'] = count($adminMonitors);
        } else {
            $results['Super Admin Monitor Count'] = 'N/A (No company_id)';
        }
    }

    return response()->json(['success' => true, 'results' => $results]);
});
