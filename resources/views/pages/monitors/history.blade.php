@extends('layouts.app')

@section('content')
<div class="container mx-auto px-6 py-12 space-y-12">
    <!-- Header Section -->
    <div class="flex items-center gap-6">
        <button onclick="history.back()" class="p-3 bg-zinc-900/50 hover:bg-zinc-800 rounded-2xl text-zinc-400 hover:text-white transition-all border border-zinc-800">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
        </button>
        <div>
            <h1 class="text-4xl font-bold text-zinc-200 tracking-tight">{{ $monitor->name }}</h1>
            <p class="text-zinc-500 font-mono text-sm mt-1">{{ $monitor->url }}</p>
        </div>
    </div>

    <!-- Metrics Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="card bg-zinc-900/40">
            <p class="text-xs font-semibold text-zinc-600 uppercase tracking-widest mb-2">Uptime (7 Days)</p>
            <p id="metric-uptime" class="text-4xl font-bold text-emerald-400">--%</p>
        </div>
        <div class="card bg-zinc-900/40">
            <p class="text-xs font-semibold text-zinc-600 uppercase tracking-widest mb-2">Average Latency (24h)</p>
            <p id="metric-latency" class="text-4xl font-bold text-amber-400">--ms</p>
        </div>
    </div>

    <!-- Chart Section -->
    <div class="card-glass overflow-hidden">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h3 class="text-2xl font-bold text-zinc-200">Latency Dynamics</h3>
                <p class="text-zinc-500 text-sm">Real-time response time monitoring (Last 50 checks)</p>
            </div>
        </div>
        <div class="h-64 sm:h-80 w-full relative">
            <canvas id="latency-chart"></canvas>
        </div>
    </div>

    <div class="card-glass">
        <div class="mb-8">
            <h3 class="text-2xl font-bold text-zinc-200 mb-2">Execution History</h3>
            <p class="text-zinc-500 text-sm">Complete audit log of service availability</p>
        </div>

        <div id="history-full-list" data-monitor-id="{{ $monitor->id }}" class="space-y-4">
            <div class="py-12 text-center text-zinc-500 font-bold uppercase tracking-widest text-xs">Retrieving logs...</div>
        </div>
    </div>
</div>

<script>
    window.current_monitor_id = {{ $monitor->id }};
    window.current_view_context = 'history';
</script>
@endsection
