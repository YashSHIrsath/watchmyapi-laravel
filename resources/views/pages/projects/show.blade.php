@extends('layouts.app')

@section('content')
<div class="container mx-auto px-6 py-12 space-y-12">
    <!-- Header Section -->
    <div class="flex items-center gap-6">
        <a href="/dashboard" class="p-3 bg-zinc-900/50 hover:bg-zinc-800 rounded-2xl text-zinc-400 hover:text-white transition-all border border-zinc-800">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
        </a>
        <div>
            <h1 class="text-4xl font-bold text-zinc-200 tracking-tight">{{ $project->name }}</h1>
            <p class="text-zinc-500 font-medium">Project Workspace &bull; Managed Infrastructure</p>
        </div>
    </div>

    <!-- Monitors Section -->
    <div class="card-glass">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h3 class="text-2xl font-bold text-zinc-200 mb-2">Active Monitors</h3>
                <p class="text-zinc-500">Real-time health monitoring for this project's endpoints</p>
            </div>
            <button id="add-api-btn" data-project-id="{{ $project->id }}" class="px-6 py-3 bg-purple-600 hover:bg-purple-500 text-zinc-200 rounded-full font-semibold transition-all">
                Add New API
            </button>
        </div>

        <!-- This container is picked up by monitors.js -->
        <div id="project-monitor-list" data-project-id="{{ $project->id }}">
            <div class="animate-pulse flex space-x-4">
                <div class="flex-1 space-y-4 py-1">
                    <div class="h-4 bg-zinc-800 rounded w-3/4"></div>
                    <div class="space-y-2">
                        <div class="h-4 bg-zinc-800 rounded"></div>
                        <div class="h-4 bg-zinc-800 rounded w-5/6"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Monitor Modal (needed here too) -->
@include('partials.modals.add_monitor')
@include('partials.modals.move_monitor')

<script>
    // Inject raw project ID for JS loading
    window.current_project_id = {{ $project->id }};
    window.current_view_context = 'project';
</script>
@endsection
