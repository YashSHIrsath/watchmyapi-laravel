<!-- Move Monitor Modal -->
<div id="move-monitor-modal" class="hidden fixed inset-0 bg-black/80 backdrop-blur-sm z-50 items-center justify-center p-4">
    <div class="card-glass max-w-md w-full">
        <div class="flex items-center justify-between mb-8">
            <h2 class="text-2xl font-bold text-zinc-200">Reassign Monitor</h2>
            <button class="close-modal p-2 hover:bg-zinc-800 rounded-lg text-zinc-600 hover:text-zinc-300">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        <form id="move-monitor-form" class="space-y-6">
            <input type="hidden" id="move-monitor-id" name="id">
            <div>
                <label for="move-project-selection" class="block text-sm font-medium text-zinc-400 mb-2">Select New Project</label>
                <select id="move-project-selection" name="project_id" required class="input">
                    <!-- Projects populated via JS -->
                </select>
            </div>
            <div class="flex gap-4">
                <button type="submit" class="flex-1 px-6 py-3 bg-purple-600 hover:bg-purple-500 text-zinc-200 rounded-full font-semibold">Move Monitor</button>
                <button type="button" class="cancel-modal flex-1 px-6 py-3 bg-zinc-800 text-zinc-300 rounded-full font-semibold border border-zinc-700">Cancel</button>
            </div>
        </form>
    </div>
</div>
