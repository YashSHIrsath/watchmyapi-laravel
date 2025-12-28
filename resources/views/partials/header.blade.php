<header class="sticky top-0 z-50 border-b border-zinc-800/50" style="background: rgba(31, 31, 31, 0.8); backdrop-filter: blur(20px);">
    <div class="container mx-auto px-6 py-4">
        <div class="flex items-center justify-between">
            <a href="/" class="flex items-center gap-3 group">
                <div class="w-10 h-10 bg-gradient-to-br from-purple-600 to-violet-600 rounded-full flex items-center justify-center shadow-lg transition-all duration-300">
                    <svg class="w-6 h-6 text-zinc-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
                <span class="text-xl font-bold gradient-text">WatchMyApi</span>
            </a>

            <nav class="hidden md:flex items-center gap-8">
                <a href="/" class="text-zinc-500 hover:text-zinc-300 transition-colors duration-200 font-medium">Home</a>
                
                <a href="/dashboard" id="nav-dashboard-link" class="text-zinc-500 hover:text-zinc-300 transition-colors duration-200 font-medium hidden">Dashboard</a>
                    
                <div id="user-info-container" class="hidden items-center gap-4 pl-6 border-l border-zinc-700">
                    <div class="flex flex-col items-end">
                        <span id="user-name-display" class="text-zinc-300 text-sm font-semibold"></span>
                        <span id="user-role-badge" class="text-xs uppercase tracking-wider font-medium text-purple-400"></span>
                    </div>
                    <button id="logout-btn" class="p-2.5 hover:bg-zinc-800/50 rounded-full text-zinc-500 hover:text-zinc-300 transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                    </button>
                </div>

                <a href="/login" id="login-link" class="px-6 py-2.5 bg-purple-600 hover:bg-purple-500 text-zinc-200 rounded-full font-semibold transition-all duration-200">
                    Sign In
                </a>
            </nav>

            <button class="md:hidden text-zinc-500 hover:text-zinc-300 p-2 rounded-full hover:bg-zinc-800 transition-all">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path>
                </svg>
            </button>
        </div>
    </div>
</header>
