@extends('layouts.app')

@section('content')
<div class="container mx-auto px-6 py-12 space-y-12">
    <!-- Header Section -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-8">
        <div class="space-y-2">
            <div class="flex items-center gap-4">
                <h1 id="dashboard-title" class="text-4xl lg:text-5xl font-bold text-zinc-200 tracking-tight">Platform Management Console</h1>
                <span id="dashboard-role-badge" class="status-online text-xs font-medium">SUPER ADMIN</span>
            </div>
            <p id="dashboard-subtitle" class="text-xl text-zinc-500 font-medium">Global surveillance and platform stability monitoring</p>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="card group hover:border-indigo-500/30">
            <div class="flex items-center justify-between mb-4">
                <p class="text-xs font-semibold text-zinc-600 uppercase tracking-wider">Total Companies</p>
                <div class="w-12 h-12 bg-indigo-500/10 border border-indigo-500/20 rounded-xl flex items-center justify-center text-indigo-400 group-hover:bg-indigo-500/20 transition-all duration-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
            </div>
            <p id="stat-total-companies" class="text-3xl font-bold text-zinc-200 mb-1">Loading...</p>
            <p class="text-sm text-zinc-500">Real-time status</p>
        </div>
    </div>

    <!-- Registered Companies Section -->
    <div class="card-glass">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h3 class="text-2xl font-bold text-zinc-200 mb-2">Registered Companies</h3>
                <p class="text-zinc-500">Monitor and manage platform organizations</p>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-zinc-800">
                        <th class="text-left py-4 px-2 text-xs font-semibold text-zinc-600 uppercase tracking-wider">Company Name</th>
                        <th class="text-left py-4 px-2 text-xs font-semibold text-zinc-600 uppercase tracking-wider">Status</th>
                        <th class="text-left py-4 px-2 text-xs font-semibold text-zinc-600 uppercase tracking-wider">Users Count</th>
                        <th class="text-left py-4 px-2 text-xs font-semibold text-zinc-600 uppercase tracking-wider">Created At</th>
                        <th class="text-right py-4 px-2 text-xs font-semibold text-zinc-600 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody id="companies-table-body" class="divide-y divide-zinc-800/50">
                    <tr>
                        <td colspan="5" class="py-16 text-center">
                            <div class="flex flex-col items-center gap-4 text-zinc-600">
                                <svg class="w-8 h-8 animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span class="text-sm font-medium">Fetching platform data...</span>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
