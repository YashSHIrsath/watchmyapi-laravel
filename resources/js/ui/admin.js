import { $ } from '../utils/dom';

/**
 * Handle Admin Dashboard data fetching and UI updates.
 */
export const initAdmin = async () => {
    const tbody = $('#companies-table-body');
    if (!tbody) return;

    const totalCompaniesStat = $('#stat-total-companies');

    try {
        const response = await fetch('/api/admin/companies', {
            headers: {
                'Authorization': `Bearer ${localStorage.getItem('access_token')}`,
                'Accept': 'application/json'
            }
        });
        const data = await response.json();

        if (data.success) {
            const companies = data.data;
            
            // Update Stats
            if (totalCompaniesStat) totalCompaniesStat.textContent = companies.length;
            
            if (companies.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" class="py-16 text-center text-zinc-500">No companies found.</td></tr>';
                return;
            }

            tbody.innerHTML = '';
            companies.forEach(company => {
                const row = `
                    <tr class="group hover:bg-zinc-800/30 transition-all duration-200">
                        <td class="py-4 px-2">
                            <span class="text-zinc-200 font-semibold">${company.name}</span>
                        </td>
                        <td class="py-4 px-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-900/50 text-emerald-400 border border-emerald-800">
                                ${company.status || 'Active'}
                            </span>
                        </td>
                        <td class="py-4 px-2 text-zinc-400">${company.users_count} Users</td>
                        <td class="py-4 px-2 text-zinc-500 font-mono text-xs">
                            ${new Date(company.created_at).toLocaleDateString()}
                        </td>
                        <td class="py-4 px-2 text-right">
                            <button class="p-2 text-zinc-500 hover:text-indigo-400 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </button>
                        </td>
                    </tr>
                `;
                tbody.innerHTML += row;
            });
        }
    } catch (err) {
        console.error('Failed to load admin dashboard:', err);
        tbody.innerHTML = '<tr><td colspan="5" class="py-16 text-center text-red-500">Failed to load platform data.</td></tr>';
    }
};
