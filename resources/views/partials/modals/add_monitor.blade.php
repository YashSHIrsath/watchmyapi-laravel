<div id="add-monitor-modal" class="hidden fixed inset-0 bg-black/80 backdrop-blur-sm z-50 items-center justify-center p-4">
    <div class="card-glass max-w-lg w-full">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h2 class="text-2xl font-bold text-zinc-200">New Monitor</h2>
                <p class="text-zinc-500 text-sm mt-1">Register a new infrastructure endpoint</p>
            </div>
            <button class="close-modal p-2 hover:bg-zinc-800 rounded-lg text-zinc-600 hover:text-zinc-300 transition-all">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <form id="add-monitor-form" class="space-y-6">
            <div>
                <label for="monitor-name" class="block text-sm font-medium text-zinc-400 mb-2">Node Identifier</label>
                <input type="text" id="monitor-name" name="name" required class="input" placeholder="e.g., Enterprise Gateway">
            </div>
            <div>
                <label for="monitor-url" class="block text-sm font-medium text-zinc-400 mb-2">Endpoint URL</label>
                <input type="url" id="monitor-url" name="url" required class="input" placeholder="https://api.internal.com">
            </div>
            <div>
                <label for="monitor-project" class="block text-sm font-medium text-zinc-400 mb-2">Project Assignment</label>
                <select id="monitor-project" name="project_id" required class="input">
                    <!-- Projects populated via JS -->
                </select>
            </div>
            <div class="flex gap-4 pt-4">
                <button type="submit" class="flex-1 px-6 py-3 bg-purple-600 hover:bg-purple-500 text-zinc-200 rounded-full font-semibold transition-all duration-200">
                    Register Monitor
                </button>
                <button type="button" class="cancel-modal flex-1 px-6 py-3 bg-zinc-800 hover:bg-zinc-700 text-zinc-300 rounded-full font-semibold transition-all duration-200 border border-zinc-700">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>
