@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-zinc-900 via-zinc-800 to-zinc-900">
    <!-- Hero Section -->
    <section class="relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-r from-purple-600/10 to-violet-600/10"></div>
        <div class="container mx-auto px-6 py-24 relative">
            <div class="max-w-4xl mx-auto text-center">
                <div id="authenticated-view" class="hidden">
                    <div class="inline-flex items-center px-4 py-2 bg-emerald-500/10 border border-emerald-500/20 rounded-full text-emerald-400 text-sm font-medium mb-8">
                        <span class="relative flex h-2 w-2 mr-3">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                        </span>
                        Authenticated Session Active
                    </div>
                    <h1 class="text-6xl md:text-7xl font-bold text-zinc-200 mb-6 leading-tight">
                        Welcome to your <br><span class="gradient-text">Command Center</span>
                    </h1>
                    <p class="text-xl text-zinc-400 max-w-2xl mx-auto mb-10 leading-relaxed">
                        You are successfully signed in. Your API monitors and security events are being tracked in real-time.
                    </p>
                    <a href="/dashboard" class="inline-flex items-center px-8 py-4 bg-purple-600 hover:bg-purple-500 text-zinc-200 rounded-full font-semibold transition-all duration-200 shadow-lg">
                        Open Dashboard
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                        </svg>
                    </a>
                </div>

                <div id="guest-view">
                    <div class="inline-flex items-center px-4 py-2 bg-purple-500/10 border border-purple-500/20 rounded-full text-purple-400 text-sm font-medium mb-8">
                        ✨ Enterprise-Grade API Monitoring
                    </div>
                    <h1 class="text-6xl md:text-7xl font-bold text-zinc-200 mb-6 leading-tight">
                        Monitor Your APIs with <br><span class="gradient-text">Total Confidence</span>
                    </h1>
                    <p class="text-xl text-zinc-400 max-w-2xl mx-auto mb-10 leading-relaxed">
                        Real-time observability, automated security audits, and global performance tracking for your mission-critical endpoints.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <a href="/login" class="px-8 py-4 bg-purple-600 hover:bg-purple-500 text-zinc-200 rounded-full font-semibold transition-all duration-200 shadow-lg flex items-center gap-3">
                            Get Started Now
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                            </svg>
                        </a>
                        <a href="#" class="px-8 py-4 bg-zinc-800 hover:bg-zinc-700 text-zinc-300 rounded-full font-semibold transition-all duration-200 border border-zinc-700">
                            View Demo
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-24">
        <div class="container mx-auto px-6">
            <div class="max-w-6xl mx-auto">
                <div class="text-center mb-16">
                    <h2 class="text-4xl font-bold text-zinc-200 mb-4">Built for Modern Infrastructure</h2>
                    <p class="text-xl text-zinc-400">Enterprise-level monitoring with developer-first experience</p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="group">
                        <div class="p-8 bg-zinc-900/50 border border-zinc-800 rounded-3xl hover:border-purple-500/30 transition-all duration-300">
                            <div class="w-16 h-16 bg-purple-500/10 border border-purple-500/20 rounded-2xl flex items-center justify-center text-purple-400 mb-6 group-hover:bg-purple-500/20 transition-all duration-300">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                </svg>
                            </div>
                            <h3 class="text-2xl font-bold text-zinc-200 mb-4">Stateless Authentication</h3>
                            <p class="text-zinc-400 leading-relaxed">Industry-standard JWT implementation with automatic refreshing and secure session management across all devices.</p>
                        </div>
                    </div>
                    
                    <div class="group">
                        <div class="p-8 bg-zinc-900/50 border border-zinc-800 rounded-3xl hover:border-emerald-500/30 transition-all duration-300">
                            <div class="w-16 h-16 bg-emerald-500/10 border border-emerald-500/20 rounded-2xl flex items-center justify-center text-emerald-400 mb-6 group-hover:bg-emerald-500/20 transition-all duration-300">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                            </div>
                            <h3 class="text-2xl font-bold text-zinc-200 mb-4">High Performance</h3>
                            <p class="text-zinc-400 leading-relaxed">Optimized asset delivery via Vite with ultra-fast backend response times and intelligent caching strategies.</p>
                        </div>
                    </div>
                    
                    <div class="group">
                        <div class="p-8 bg-zinc-900/50 border border-zinc-800 rounded-3xl hover:border-violet-500/30 transition-all duration-300">
                            <div class="w-16 h-16 bg-violet-500/10 border border-violet-500/20 rounded-2xl flex items-center justify-center text-violet-400 mb-6 group-hover:bg-violet-500/20 transition-all duration-300">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"></path>
                                </svg>
                            </div>
                            <h3 class="text-2xl font-bold text-zinc-200 mb-4">Modular Architecture</h3>
                            <p class="text-zinc-400 leading-relaxed">Enterprise-level Blade components and structured JavaScript for maximum maintainability and scalability.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
