import { $, $$ } from '../utils/dom';
import { authApi } from '../utils/auth';

/**
 * Monitor management UI logic - WITH DYNAMIC METRICS
 */
export const initMonitors = async () => {
    console.log('[initMonitors] Starting initialization...');
    
    // UI Elements
    const addApiBtn = $('#add-api-btn');
    const modal = $('#add-monitor-modal'); 
    const addMonitorForm = $('#add-monitor-form');
    const monitorListContainer = $('#monitor-list-container'); 
    const projectMonitorList = $('#project-monitor-list'); 
    const historyFullList = $('#history-full-list');
    
    const projectFilter = $('#project-filter');
    const projectSelect = $('#monitor-project');
    const projectsTableBody = $('#projects-table-body');
    const projectPagination = $('#project-pagination');
    const monitorPagination = $('#monitor-pagination');
    
    // Modals
    const addProjectBtn = $('#add-project-btn');
    const addProjectModal = $('#add-project-modal');
    const addProjectForm = $('#add-project-form');
    const editProjectModal = $('#edit-project-modal');
    const editProjectForm = $('#edit-project-form');
    const moveMonitorModal = $('#move-monitor-modal');
    const moveMonitorForm = $('#move-monitor-form');
    const moveProjectSelect = $('#move-project-selection');

    let currentMonitorPage = 1;
    let currentProjectPage = 1;

    // --- Helpers ---

    const showAlert = (message, type = 'error') => {
        const container = $('#alerts-container') || document.body;
        const alertEl = document.createElement('div');
        alertEl.className = `p-4 mb-4 rounded-xl text-sm font-bold border transition-all duration-500 animate-slide-in ${
            type === 'success' ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20' : 'bg-red-500/10 text-red-400 border-red-500/20'
        }`;
        alertEl.textContent = message;
        container.prepend(alertEl);
        setTimeout(() => alertEl.remove(), 5000);
    };

    const apiRequest = async (url, method = 'GET', body = null) => {
        const token = localStorage.getItem('access_token');
        const options = {
            method,
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${token}`
            }
        };
        if (body) options.body = JSON.stringify(body);
        if (method !== 'GET') {
            const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            if (csrf) options.headers['X-CSRF-TOKEN'] = csrf;
        }
        
        try {
            const response = await fetch(url, options);
            if (response.status === 401) {
                localStorage.removeItem('access_token');
                window.location.href = '/login';
                return { success: false, message: 'Session expired.' };
            }
            
            const data = await response.json();
            if (!response.ok) {
                return { success: false, message: data.message || 'Operation failed.' };
            }
            return data;
        } catch (error) {
            console.error(`[API Fetch Error]`, error);
            return { success: false, message: 'System error or network failure.' };
        }
    };

    const closeAllModals = () => {
        [modal, addProjectModal, editProjectModal, moveMonitorModal].forEach(m => {
            if (m) {
                m.classList.add('hidden');
                m.style.setProperty('display', 'none', 'important');
            }
        });
        if (addMonitorForm) addMonitorForm.reset();
        if (addProjectForm) addProjectForm.reset();
        if (editProjectForm) editProjectForm.reset();
        if (moveMonitorForm) moveMonitorForm.reset();
    };

    const escapeHtml = (text) => {
        if (text === null || text === undefined) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    };

    // --- Metrics Loading ---

    const loadDashboardStats = async () => {
        console.log('[loadDashboardStats] Fetching dashboard statistics...');
        try {
            const res = await apiRequest('/api/stats/dashboard');
            console.log('[loadDashboardStats] API Response:', res);
            
            if (res.success && res.data) {
                const s = res.data;
                console.log('[loadDashboardStats] Stats data:', s);
                
                if ($('#stats-total-projects')) {
                    $('#stats-total-projects').textContent = s.total_projects.toString().padStart(2, '0');
                    console.log('[loadDashboardStats] Updated total_projects:', s.total_projects);
                }
                if ($('#stats-total-monitors')) {
                    $('#stats-total-monitors').textContent = s.total_monitors.toString().padStart(2, '0');
                    console.log('[loadDashboardStats] Updated total_monitors:', s.total_monitors);
                }
                if ($('#stats-avg-response')) {
                    $('#stats-avg-response').textContent = s.avg_response;
                }
                if ($('#stats-active-alerts')) {
                    $('#stats-active-alerts').textContent = s.active_alerts;
                }
            } else {
                console.error('[loadDashboardStats] Failed to load stats:', res);
            }
        } catch (error) {
            console.error('[loadDashboardStats] Error:', error);
        }
    };

    const renderLatencyChart = (data) => {
        const canvas = $('#latency-chart');
        if (!canvas) return;

        const ctx = canvas.getContext('2d');
        const labels = data.map(d => d.time);
        const values = data.map(d => d.latency);
        const colors = data.map(d => d.is_success ? '#6366f1' : '#ef4444');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Latency (ms)',
                    data: values,
                    borderColor: '#6366f1',
                    backgroundColor: 'rgba(99, 102, 241, 0.1)',
                    borderWidth: 3,
                    pointBackgroundColor: colors,
                    pointBorderColor: colors,
                    pointRadius: 4,
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { ticks: { color: '#52525b', font: { family: 'JetBrains Mono', size: 10 } }, grid: { display: false } },
                    y: { ticks: { color: '#52525b', font: { family: 'JetBrains Mono', size: 10 } }, grid: { color: '#1e1e1e' } }
                }
            }
        });
    };

    const loadMonitorMetrics = async () => {
        if (!window.current_monitor_id) return;
        const res = await apiRequest(`/api/stats/monitor/${window.current_monitor_id}`);
        if (res.success) {
            const m = res.data;
            if ($('#metric-uptime')) $('#metric-uptime').textContent = m.uptime_7d;
            if ($('#metric-latency')) $('#metric-latency').textContent = m.avg_latency_24h;
            renderLatencyChart(m.history);
        }
    };

    // --- Loading Logic ---

    const loadProjects = async (page = 1) => {
        if (!projectsTableBody) return;
        currentProjectPage = page;
        const result = await apiRequest(`/api/projects?page=${page}`);
        if (result.success) {
            renderProjectsTable(result.data);
            renderPagination(projectPagination, result.meta, (p) => loadProjects(p));
            
            if (page === 1 && projectFilter) {
                const currentVal = projectFilter.value;
                projectFilter.innerHTML = '<option value="all">All Projects</option>';
                result.data.forEach(p => {
                    projectFilter.innerHTML += `<option value="${p.id}">${escapeHtml(p.name)}</option>`;
                });
                projectFilter.value = currentVal;
            }
        }
    };

    const loadProjectsForSelect = async () => {
        const result = await apiRequest('/api/projects?page=1');
        if (result.success && result.data) {
            if (projectSelect) {
                projectSelect.innerHTML = result.data.map(p => `
                    <option value="${p.id}" ${p.id == window.current_project_id ? 'selected' : (p.is_default && !window.current_project_id ? 'selected' : '')}>
                        ${escapeHtml(p.name)} ${p.is_default ? '(Default)' : ''}
                    </option>
                `).join('');
            }
            if (moveProjectSelect) {
                moveProjectSelect.innerHTML = result.data.map(p => `<option value="${p.id}">${escapeHtml(p.name)}</option>`).join('');
            }
        }
    };

    const loadMonitors = async (page = 1) => {
        if (!monitorListContainer) return;
        currentMonitorPage = page;
        let url = `/api/monitors?page=${page}`;
        if (projectFilter && projectFilter.value !== 'all') {
            url += `&project_id=${projectFilter.value}`;
        }
        const result = await apiRequest(url);
        if (result.success) {
            renderMonitors(result.data, '#monitor-list-container');
            renderPagination(monitorPagination, result.meta, (p) => loadMonitors(p));
        }
    };

    const loadProjectSpecificMonitors = async () => {
        if (!projectMonitorList || !window.current_project_id) return;
        const result = await apiRequest(`/api/monitors?project_id=${window.current_project_id}`);
        if (result.success) {
            renderMonitors(result.data, '#project-monitor-list');
        }
    };

    const loadHistoryFull = async () => {
        if (!historyFullList || !window.current_monitor_id) return;
        const result = await apiRequest(`/api/monitors/${window.current_monitor_id}/checks`);
        if (result.success) {
            if (result.data.length === 0) {
                historyFullList.innerHTML = '<div class="p-12 text-center text-zinc-500 font-bold uppercase tracking-widest text-xs border border-dashed border-zinc-800 rounded-2xl">No access logs found</div>';
                return;
            }
            historyFullList.innerHTML = result.data.map(c => `
                <div class="flex items-center justify-between p-4 bg-zinc-800/20 border border-zinc-800 rounded-xl hover:border-zinc-700 transition-all">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-lg ${c.is_success ? 'bg-emerald-500/10 text-emerald-400' : 'bg-red-500/10 text-red-400'} flex items-center justify-center font-bold font-mono text-xs">
                            ${c.http_status_code || '---'}
                        </div>
                        <div>
                            <p class="text-zinc-200 text-sm font-bold">${c.is_success ? 'Request Success' : 'Request Failure'}</p>
                            <p class="text-[10px] text-zinc-500 font-mono">${new Date(c.checked_at).toLocaleString()}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-lg font-black text-zinc-300 font-mono">${c.response_time_ms || '--'}ms</p>
                    </div>
                </div>
            `).join('');
        }
    };

    // --- Rendering Logic ---

    const renderPagination = (container, meta, onPageClick) => {
        if (!container || !meta || meta.last_page <= 1) {
            if (container) container.innerHTML = '';
            return;
        }

        let html = '';
        for (let i = 1; i <= meta.last_page; i++) {
            const isActive = i === meta.current_page;
            html += `
                <button onclick="window.handlePageClick(${i}, '${container.id}')" 
                    class="px-4 py-2 rounded-lg border ${isActive ? 'bg-purple-600 text-white border-purple-500' : 'bg-zinc-800 text-zinc-400 border-zinc-700 hover:bg-zinc-700'} transition-all text-sm font-semibold">
                    ${i}
                </button>
            `;
        }
        container.innerHTML = html;

        window.handlePageClick = (page, containerId) => {
            if (containerId === 'project-pagination') loadProjects(page);
            if (containerId === 'monitor-pagination') loadMonitors(page);
        };
    };

    const renderProjectsTable = (projects) => {
        if (!projectsTableBody) return;
        projectsTableBody.innerHTML = projects.map(project => `
            <tr class="group hover:bg-zinc-800/30 transition-all cursor-pointer" onclick="if(event.target.tagName !== 'BUTTON' && !event.target.closest('button')) window.location.href='/projects/${project.encrypted_id}'">
                <td class="py-4 px-2">
                    <span class="text-zinc-200 font-semibold group-hover:text-indigo-400 transition-colors">${escapeHtml(project.name)}</span>
                </td>
                <td class="py-4 px-2 text-zinc-400">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-zinc-800 text-xs font-medium text-zinc-300">
                        ${project.monitors_count} Monitors
                    </span>
                </td>
                <td class="py-4 px-2">
                    ${project.is_default ? 
                        '<span class="text-[10px] font-black uppercase text-purple-400 tracking-widest bg-purple-500/10 px-2 py-1 rounded">Default</span>' : 
                        '<span class="text-[10px] font-black uppercase text-zinc-500 tracking-widest px-2 py-1">Custom</span>'}
                </td>
                <td class="py-4 px-2 text-right">
                    <div class="flex justify-end gap-2 text-zinc-400">
                        <button onclick="window.editProject(${project.id}, '${escapeHtml(project.name).replace(/'/g, "\\'")}')" class="p-2 hover:text-indigo-400 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        </button>
                    </div>
                </td>
            </tr>
        `).join('') || '<tr><td colspan="4" class="py-12 text-center text-zinc-500">No projects found.</td></tr>';
    };

    const renderMonitors = (monitors, containerSelector) => {
        const container = $(containerSelector);
        if (!container) return;

        if (monitors.length === 0) {
            container.innerHTML = `<div class="text-center text-zinc-500 py-16 bg-zinc-900/30 rounded-2xl border border-dashed border-zinc-700 font-bold uppercase tracking-widest text-xs">No monitors found</div>`;
            return;
        }

        container.innerHTML = `<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            ${monitors.map(monitor => {
                const isMaint = monitor.status === 'maintenance';
                return `
                <div class="p-8 bg-[#1a1a1a] border border-[#262626] rounded-[2rem] hover:border-indigo-500/30 transition-all duration-500 group relative overflow-hidden shadow-xl">
                    <div class="absolute inset-x-0 top-0 h-[2px] bg-gradient-to-r from-transparent ${monitor.status === 'active' ? 'via-indigo-500/20' : isMaint ? 'via-amber-500/20' : 'via-neutral-700/20'} to-transparent"></div>
                    
                    <!-- Action Menu -->
                    <div class="absolute top-6 right-6 z-10">
                        <div class="relative group-actions inline-block text-left">
                            <button onclick="this.nextElementSibling.classList.toggle('hidden')" class="p-2 hover:bg-zinc-800 rounded-lg text-zinc-600 hover:text-zinc-300 transition-all">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"></path></svg>
                            </button>
                            <div class="hidden absolute right-0 mt-2 w-48 bg-zinc-900 border border-zinc-800 rounded-xl shadow-2xl py-2 z-50">
                                <button onclick="window.openMoveModal(${monitor.id}, ${monitor.project_id})" class="w-full text-left px-4 py-2 text-xs font-bold text-zinc-400 hover:text-white hover:bg-zinc-800 transition-all">MOVE PROJECT</button>
                                <button onclick="window.setMaintenance(${monitor.id}, '${monitor.status}')" class="w-full text-left px-4 py-2 text-xs font-bold text-zinc-400 hover:text-white hover:bg-zinc-800 transition-all uppercase">${isMaint ? 'RESUME MONITOR' : 'MAINTENANCE MODE'}</button>
                                <div class="h-[1px] bg-zinc-800 my-1"></div>
                                <button onclick="window.deleteMonitor(${monitor.id})" class="w-full text-left px-4 py-2 text-xs font-bold text-red-500/70 hover:text-red-400 hover:bg-red-500/10 transition-all">DELETE API</button>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col h-full">
                        <div class="flex-grow">
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-2.5 h-2.5 rounded-full ${monitor.status === 'active' ? 'bg-indigo-500 shadow-[0_0_12px_rgba(99,102,241,0.6)]' : isMaint ? 'bg-amber-500 shadow-[0_0_12px_rgba(245,158,11,0.6)]' : 'bg-neutral-700 animate-pulse'}"></div>
                                    <h4 class="font-extrabold text-white text-lg tracking-tight">${escapeHtml(monitor.name)}</h4>
                                </div>
                            </div>
                            <div class="bg-[#0a0a0a]/50 border border-[#262626] rounded-xl px-4 py-3 mb-6 font-mono text-zinc-300">
                                <p class="text-[10px] break-all leading-relaxed">${escapeHtml(monitor.url)}</p>
                            </div>

                            <div id="checks-container-${monitor.id}" class="hidden mt-4 pt-4 border-t border-[#262626]/50">
                                <h5 class="text-[10px] uppercase font-black text-zinc-500 tracking-[0.2em] mb-3">Recent Execution Summary</h5>
                                <div class="checks-list space-y-2"></div>
                            </div>
                        </div>

                        <div class="mt-6">
                            <div class="flex items-center justify-between pt-4 border-t border-[#262626]/50 mb-6 font-bold uppercase text-[9px] tracking-widest text-zinc-500">
                                <span>${monitor.status}</span>
                                <span>${escapeHtml(monitor.project?.name || 'Unknown')}</span>
                            </div>
                            <div class="flex gap-2">
                                <button onclick="window.viewChecks(${monitor.id}, 5)" class="flex-1 py-3 px-4 rounded-xl bg-zinc-800/50 hover:bg-zinc-800 border border-zinc-700/50 text-[9px] font-black uppercase tracking-widest text-zinc-400 transition-all">Summary</button>
                                <button onclick="window.location.href='/history/${monitor.encrypted_id}'" class="flex-1 py-3 px-4 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-[9px] font-black uppercase tracking-widest text-white transition-all">History</button>
                            </div>
                        </div>
                    </div>
                </div>`;
            }).join('')}
        </div>`;
    };

    // --- Window Actions ---

    window.viewChecks = async (id, limit = 5) => {
        const container = $(`#checks-container-${id}`);
        if (!container) return;
        if (!container.classList.contains('hidden')) { container.classList.add('hidden'); return; }
        container.classList.remove('hidden');
        const list = container.querySelector('.checks-list');
        list.innerHTML = '<div class="h-4 bg-zinc-800/50 rounded animate-pulse"></div>';
        const res = await apiRequest(`/api/monitors/${id}/checks`);
        if (res.success) {
            const data = res.data.slice(0, limit);
            if (data.length === 0) { list.innerHTML = '<p class="text-[10px] text-zinc-500 text-center">No checks.</p>'; return; }
            list.innerHTML = data.map(c => `
                <div class="flex items-center justify-between p-1.5 rounded-lg bg-zinc-900/50">
                    <span class="text-[10px] font-bold ${c.is_success ? 'text-emerald-400' : 'text-red-400'}">${c.http_status_code || '---'}</span>
                    <span class="text-[9px] text-zinc-500 italic">${c.response_time_ms || '--'}ms</span>
                </div>
            `).join('');
        }
    };

    window.openMoveModal = (id, currentProjectId) => {
        $('#move-monitor-id').value = id;
        loadProjectsForSelect();
        moveMonitorModal.classList.remove('hidden');
        moveMonitorModal.style.display = 'flex';
    };

    window.deleteMonitor = async (id) => {
        if (!confirm('Delete this API?')) return;
        const res = await apiRequest(`/api/monitors/${id}`, 'DELETE');
        if (res.success) {
            showAlert('Monitor deleted.', 'success');
            loadDashboardStats();
            if (monitorListContainer) loadMonitors();
            if (projectMonitorList) loadProjectSpecificMonitors();
        } else {
            showAlert(res.message);
        }
    };

    window.setMaintenance = async (id, status) => {
        const newStatus = status === 'maintenance' ? 'active' : 'maintenance';
        const res = await apiRequest(`/api/monitors/${id}`, 'PUT', { status: newStatus });
        if (res.success) {
            showAlert(`Monitor is now ${newStatus}.`, 'success');
            if (monitorListContainer) loadMonitors();
            if (projectMonitorList) loadProjectSpecificMonitors();
        } else {
            showAlert(res.message);
        }
    };

    window.editProject = (id, name) => {
        $('#edit-project-id').value = id;
        $('#edit-project-name').value = name;
        editProjectModal.classList.remove('hidden');
        editProjectModal.style.display = 'flex';
    };

    window.deleteProject = async (id) => {
        if (!confirm('Delete project? Monitors will move to default.')) return;
        const res = await apiRequest(`/api/projects/${id}`, 'DELETE');
        if (res.success) {
            showAlert('Project deleted.', 'success');
            loadDashboardStats();
            loadProjects(); loadMonitors(); 
        } else {
            showAlert(res.message);
        }
    };

    // --- Init Event Listeners ---

    $$('.close-modal, .cancel-modal').forEach(btn => btn.addEventListener('click', closeAllModals));
    
    addApiBtn?.addEventListener('click', () => {
        modal?.classList.remove('hidden');
        modal.style.display = 'flex';
        loadProjectsForSelect();
    });

    addProjectBtn?.addEventListener('click', () => {
        addProjectModal?.classList.remove('hidden');
        addProjectModal.style.display = 'flex';
    });

    addMonitorForm?.addEventListener('submit', async (e) => {
        e.preventDefault();
        const body = {
            name: $('#monitor-name').value,
            url: $('#monitor-url').value,
            project_id: $('#monitor-project').value
        };
        const res = await apiRequest('/api/monitors', 'POST', body);
        if (res.success) { 
            showAlert('API created successfully.', 'success');
            closeAllModals(); 
            loadDashboardStats();
            if (monitorListContainer) loadMonitors();
            if (projectMonitorList) loadProjectSpecificMonitors(); 
        } else {
            showAlert(res.message);
        }
    });

    addProjectForm?.addEventListener('submit', async (e) => {
        e.preventDefault();
        const res = await apiRequest('/api/projects', 'POST', { name: $('#project-name').value });
        if (res.success) { 
            showAlert('Project created successfully.', 'success');
            closeAllModals(); 
            loadProjects(); 
            loadProjectsForSelect(); 
        } else {
            showAlert(res.message);
        }
    });

    editProjectForm?.addEventListener('submit', async (e) => {
        e.preventDefault();
        const res = await apiRequest(`/api/projects/${$('#edit-project-id').value}`, 'PUT', { name: $('#edit-project-name').value });
        if (res.success) { 
            showAlert('Project updated.', 'success');
            closeAllModals(); 
            loadProjects(); 
            if (projectMonitorList) loadProjectSpecificMonitors(); 
        } else {
            showAlert(res.message);
        }
    });

    moveMonitorForm?.addEventListener('submit', async (e) => {
        e.preventDefault();
        const res = await apiRequest(`/api/monitors/${$('#move-monitor-id').value}`, 'PUT', { project_id: moveProjectSelect.value });
        if (res.success) {
            showAlert('Monitor moved successfully.', 'success');
            closeAllModals();
            if (monitorListContainer) loadMonitors();
            if (projectMonitorList) loadProjectSpecificMonitors();
        } else {
            showAlert(res.message);
        }
    });

    projectFilter?.addEventListener('change', () => loadMonitors(1));

    // Initial Load
    try {
        await loadDashboardStats();
        await loadProjects();
        await loadMonitors();
        await loadProjectSpecificMonitors();
        await loadHistoryFull();
        await loadMonitorMetrics();
    } catch (e) {
        console.error('[initMonitors] Critical initialization error:', e);
    }
};
