@extends('layouts.app')

@section('content')
<div class="container mx-auto px-6 py-12 space-y-12">
    <!-- Header Section -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-8">
        <div class="space-y-2">
            <div class="flex items-center gap-4">
                <h1 id="dashboard-title" class="text-4xl lg:text-5xl font-bold text-zinc-200 tracking-tight">Enterprise Console</h1>
                <span id="dashboard-role-badge" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-black tracking-widest bg-emerald-900/30 text-emerald-400 border border-emerald-800 uppercase">Operational</span>
            </div>
            <p id="dashboard-subtitle" class="text-lg text-zinc-500 font-medium">Organization-wide API observability and infrastructure control</p>
        </div>
        <div class="flex items-center gap-4">
            <button id="add-project-btn" class="px-6 py-4 bg-zinc-800/50 hover:bg-zinc-700 text-zinc-200 rounded-full font-bold transition-all border border-zinc-700">
                New Project
            </button>
            <button id="add-api-btn" class="px-8 py-4 bg-purple-600 hover:bg-purple-500 text-zinc-200 rounded-full font-bold transition-all shadow-lg flex items-center gap-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                Deploy API
            </button>
        </div>
    </div>

    <!-- MAIN BENTO GRID -->
    <div class="grid grid-cols-1 lg:grid-cols-6 gap-6">
        
        <!-- TOP LEFT: CORE STATS (Col-1,2) -->
        <div class="lg:col-span-2 space-y-6">
            <div class="card group bg-indigo-600/[0.03] border-indigo-500/20">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-[10px] font-black text-zinc-600 uppercase tracking-widest">Active Workspace</p>
                    <div class="w-8 h-8 rounded-lg bg-indigo-500/10 flex items-center justify-center text-indigo-400 border border-indigo-500/20">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    </div>
                </div>
                <div class="flex items-baseline gap-4">
                    <h2 id="stats-total-projects" class="text-5xl font-black text-zinc-100 italic">00</h2>
                    <p class="text-zinc-500 text-xs font-bold uppercase tracking-tighter">Total Projects</p>
                </div>
            </div>

            <div class="card group bg-purple-600/[0.03] border-purple-500/20">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-[10px] font-black text-zinc-600 uppercase tracking-widest">Infrastructure Hub</p>
                    <div class="w-8 h-8 rounded-lg bg-purple-500/10 flex items-center justify-center text-purple-400 border border-purple-500/20">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9-9c1.657 0 3 4.03 3 9s-1.343 9-3 9m0-18c-1.657 0-3 4.03-3 9s1.343 9 3 9m-9-9a9 9 0 019-9"></path></svg>
                    </div>
                </div>
                <div class="flex items-baseline gap-4">
                    <h2 id="stats-total-monitors" class="text-5xl font-black text-zinc-100 italic">00</h2>
                    <p class="text-zinc-500 text-xs font-bold uppercase tracking-tighter">Total APIs</p>
                </div>
            </div>

            <!-- Additional Small Metrics -->
            <div class="grid grid-cols-2 gap-4">
                <div class="p-6 bg-zinc-900/30 border border-zinc-800 rounded-3xl">
                    <p class="text-[9px] font-black text-zinc-700 uppercase mb-2 tracking-widest">Global Latency</p>
                    <p id="stats-avg-response" class="text-lg font-bold text-zinc-300">0ms</p>
                </div>
                <div class="p-6 bg-zinc-900/30 border border-zinc-800 rounded-3xl">
                    <p class="text-[9px] font-black text-zinc-700 uppercase mb-2 tracking-widest">Incidents</p>
                    <p id="stats-active-alerts" class="text-lg font-bold text-red-500/70">0</p>
                </div>
            </div>
        </div>

        <!-- TOP RIGHT: API INFRASTRUCTURE (Col-3,4,5,6) -->
        <div id="monitors-section" class="lg:col-span-4 card-glass">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6 mb-8">
                <div>
                    <h3 class="text-2xl font-bold text-zinc-200">API Infrastructure</h3>
                    <p class="text-zinc-500 text-xs font-black uppercase tracking-widest mt-1">Real-time health telemetry</p>
                </div>
                <div class="flex items-center gap-4 bg-zinc-800/30 p-1.5 rounded-2xl border border-zinc-700/50">
                    <label for="project-filter" class="text-[10px] font-black text-zinc-600 uppercase tracking-widest px-3">Filter</label>
                    <select id="project-filter" class="bg-zinc-900/80 border-none text-zinc-200 text-xs font-bold rounded-xl focus:ring-0 block w-full sm:w-40 p-2 outline-none">
                        <option value="all">All Clusters</option>
                    </select>
                </div>
            </div>
            <div id="monitor-list-container">
                <!-- Monitors rendered here -->
            </div>
            <div id="monitor-pagination" class="mt-8 flex justify-center gap-2"></div>
        </div>

        <!-- BOTTOM LEFT: PROJECT MANAGEMENT (Col-1-4) -->
        <div id="projects-section" class="lg:col-span-4 card-glass flex flex-col h-full">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-xl font-bold text-zinc-200">Project Management</h3>
                    <p class="text-xs text-zinc-500 uppercase font-black tracking-widest mt-1">Resource allocation & grouping</p>
                </div>
            </div>
            <div class="overflow-x-auto flex-grow">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-zinc-800/50">
                            <th class="text-left py-4 px-2 text-[10px] font-black text-zinc-600 uppercase tracking-widest">Project Name</th>
                            <th class="text-left py-4 px-2 text-[10px] font-black text-zinc-600 uppercase tracking-widest">Nodes</th>
                            <th class="text-left py-4 px-2 text-[10px] font-black text-zinc-600 uppercase tracking-widest">Status</th>
                            <th class="text-right py-4 px-2 text-[10px] font-black text-zinc-600 uppercase tracking-widest">Action</th>
                        </tr>
                    </thead>
                    <tbody id="projects-table-body" class="divide-y divide-zinc-800/30">
                        <!-- Projects rendered here -->
                    </tbody>
                </table>
            </div>
            <div id="project-pagination" class="mt-4 flex justify-center gap-1"></div>
        </div>

        <!-- BOTTOM RIGHT: ACTIVE SESSIONS (Col-5,6) -->
        <div class="lg:col-span-2 card-glass flex flex-col">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-xl font-bold text-zinc-200">Security Terminals</h3>
                    <p class="text-[9px] text-zinc-500 uppercase font-black tracking-widest mt-1">Verified encrypted handshakes</p>
                </div>
                <button id="revoke-others-btn" class="p-2 bg-red-500/10 text-red-400 hover:bg-red-500/20 rounded-lg transition-all border border-red-500/20" title="Revoke all other sessions">
                   <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-6 0v-1m6-10V7a3 3 0 00-6 0v1"></path></svg>
                </button>
            </div>
            <div class="flex-grow">
                <div class="space-y-4" id="sessions-table-body">
                    <!-- Session cards will be rendered here as list items rather than a table if needed, 
                         but let's keep the table logic for now and just style the container -->
                    <div class="py-12 text-center text-zinc-700 uppercase font-black tracking-widest text-[10px]">Scanning for device nodes...</div>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- All existing modals included at the end -->
<div id="add-project-modal" class="hidden fixed inset-0 bg-black/80 backdrop-blur-sm z-50 items-center justify-center p-4">
    <div class="card-glass max-w-md w-full">
        <div class="flex items-center justify-between mb-8">
            <h2 class="text-2xl font-bold text-zinc-200">New Project</h2>
            <button class="close-modal p-2 hover:bg-zinc-800 rounded-lg text-zinc-600 hover:text-zinc-300">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        <form id="add-project-form" class="space-y-6">
            <div>
                <label for="project-name" class="block text-sm font-medium text-zinc-400 mb-2 font-bold uppercase tracking-widest text-[10px]">Project Name</label>
                <input type="text" id="project-name" name="name" required class="input" placeholder="e.g., Production Cluster">
            </div>
            <div class="flex gap-4">
                <button type="submit" class="flex-1 px-6 py-3 bg-purple-600 hover:bg-purple-500 text-zinc-200 rounded-full font-bold">Initialize</button>
                <button type="button" class="cancel-modal flex-1 px-6 py-3 bg-zinc-800 text-zinc-300 rounded-full font-bold border border-zinc-700">Aborted</button>
            </div>
        </form>
    </div>
</div>

<div id="edit-project-modal" class="hidden fixed inset-0 bg-black/80 backdrop-blur-sm z-50 items-center justify-center p-4">
    <div class="card-glass max-w-md w-full">
        <div class="flex items-center justify-between mb-8">
            <h2 class="text-2xl font-bold text-zinc-200">Rename Project</h2>
            <button class="close-modal p-2 hover:bg-zinc-800 rounded-lg text-zinc-600 hover:text-zinc-300">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        <form id="edit-project-form" class="space-y-6">
            <input type="hidden" id="edit-project-id" name="id">
            <div>
                <label for="edit-project-name" class="block text-sm font-medium text-zinc-400 mb-2 font-bold uppercase tracking-widest text-[10px]">Alias</label>
                <input type="text" id="edit-project-name" name="name" required class="input">
            </div>
            <div class="flex gap-4">
                <button type="submit" class="flex-1 px-6 py-3 bg-purple-600 hover:bg-purple-500 text-zinc-200 rounded-full font-bold">Confirm</button>
                <button type="button" class="cancel-modal flex-1 px-6 py-3 bg-zinc-800 text-zinc-300 rounded-full font-bold border border-zinc-700">Cancel</button>
            </div>
        </form>
    </div>
</div>

@include('partials.modals.add_monitor')
@include('partials.modals.move_monitor')
@endsection
