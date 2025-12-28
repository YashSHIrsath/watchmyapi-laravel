import { $ } from '../utils/dom';
import { authApi } from '../utils/auth';

/**
 * Handle session management UI in the dashboard - BENTO CARD VERSION
 */
export const initSessions = async () => {
    const listContainer = $('#sessions-table-body'); // This is now a div for cards
    const revokeOthersBtn = $('#revoke-others-btn');
    
    if (!listContainer) return;

    const renderSessions = async () => {
        try {
            const refreshToken = localStorage.getItem('refresh_token');
            const { status, data } = await authApi.getSessions(refreshToken);
            
            if (status === 401) {
                localStorage.removeItem('access_token');
                window.location.href = '/login';
                return;
            }

            if (status !== 200 || !data.success) {
                listContainer.innerHTML = `<div class="p-8 text-center text-red-400/50 font-black uppercase tracking-widest text-[10px]">Failed to scan nodes</div>`;
                return;
            }

            const sessions = data.data;
            if (sessions.length === 0) {
                listContainer.innerHTML = `<div class="p-8 text-center text-zinc-700 font-black uppercase tracking-widest text-[10px]">No active terminals</div>`;
                return;
            }

            listContainer.innerHTML = sessions.map(session => `
                <div class="p-6 bg-zinc-900/40 border ${session.is_current ? 'border-indigo-500/30 bg-indigo-500/[0.02]' : 'border-zinc-800/50'} rounded-3xl group hover:border-zinc-700 transition-all">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-xl ${session.is_current ? 'bg-indigo-500/10 text-indigo-400' : 'bg-zinc-800 text-zinc-600'} flex items-center justify-center border border-zinc-800">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            </div>
                            <div>
                                <h4 class="text-xs font-black text-zinc-200 uppercase tracking-wider">${session.device}</h4>
                                <p class="text-[9px] text-zinc-600 font-mono">${session.ip}</p>
                            </div>
                        </div>
                        ${session.is_current ? 
                            '<span class="w-1.5 h-1.5 rounded-full bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)] animate-pulse"></span>' : 
                            `<button class="revoke-specific-btn p-1.5 hover:bg-red-500/10 text-zinc-700 hover:text-red-400 rounded-lg transition-all" data-id="${session.id}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                             </button>`
                        }
                    </div>
                    <div class="flex items-center justify-between text-[9px] font-black uppercase tracking-[0.2em] text-zinc-700">
                        <span>Last Active</span>
                        <span class="text-zinc-500">${session.last_active}</span>
                    </div>
                </div>
            `).join('');

            // Attach event listeners
            document.querySelectorAll('.revoke-specific-btn').forEach(btn => {
                btn.addEventListener('click', async (e) => {
                    const id = e.currentTarget.getAttribute('data-id');
                    if (confirm('Revoke this access terminal?')) {
                        btn.disabled = true;
                        const { status: rStatus } = await authApi.revokeSession(id);
                        if (rStatus === 200) {
                            renderSessions();
                        } else {
                            btn.disabled = false;
                        }
                    }
                });
            });
        } catch (error) {
            console.error('[Sessions View Error]', error);
            listContainer.innerHTML = `<div class="p-8 text-center text-red-400/50 font-black uppercase tracking-widest text-[10px]">Security offline</div>`;
        }
    };

    renderSessions();

    if (revokeOthersBtn) {
        revokeOthersBtn.addEventListener('click', async () => {
            if (confirm('Deauthorize all other terminals?')) {
                revokeOthersBtn.disabled = true;
                try {
                    const { status } = await authApi.revokeOtherSessions();
                    if (status === 200) {
                        renderSessions();
                    } else if (status === 401) {
                        window.location.href = '/login';
                    }
                } catch (e) {
                    console.error(e);
                }
                revokeOthersBtn.disabled = false;
            }
        });
    }
};
