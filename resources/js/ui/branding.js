import { $ } from '../utils/dom';
import { authApi } from '../utils/auth';

/**
 * Update UI branding based on user type.
 */
export const initBranding = async () => {
    const { status, data } = await authApi.me();
    
    if (status !== 200 || !data.success) return;

    const user = data.data;
    const isSuperAdmin = user.user_type === 'super_admin';

    // 1. Update Header
    const dashboardLink = $('#nav-dashboard-link');
    const userInfoContainer = $('#user-info-container');
    const userNameDisplay = $('#user-name-display');
    const userRoleBadge = $('#user-role-badge');
    const loginLink = $('#login-link');
    const logoutBtn = $('#logout-btn');

    if (userNameDisplay && userRoleBadge) {
        if (dashboardLink) {
            dashboardLink.classList.remove('hidden');
            dashboardLink.href = isSuperAdmin ? '/admin/dashboard' : '/dashboard';
        }
        if (userInfoContainer) {
            userInfoContainer.classList.remove('hidden');
            userInfoContainer.classList.add('flex');
        }
        if (logoutBtn) logoutBtn.classList.remove('hidden');
        if (loginLink) loginLink.classList.add('hidden');

        userNameDisplay.textContent = user.name;
        
        if (isSuperAdmin) {
            userRoleBadge.textContent = 'Super Admin';
            userRoleBadge.className = 'text-xs uppercase tracking-wider font-medium text-indigo-400';
        } else {
            userRoleBadge.textContent = 'Company User';
            userRoleBadge.className = 'text-xs uppercase tracking-wider font-medium text-emerald-400';
        }
    }

    // 2. Dashboard Branding (Now handled by separate views, but keeping for shared layouts if any)
    const dashboardTitle = $('#dashboard-title');
    const dashboardSubtitle = $('#dashboard-subtitle');
    const dashboardRoleBadge = $('#dashboard-role-badge');

    if (dashboardTitle) {
        if (isSuperAdmin) {
            dashboardTitle.textContent = 'Platform Management Console';
            if (dashboardSubtitle) dashboardSubtitle.textContent = 'Global surveillance and platform stability monitoring';
            if (dashboardRoleBadge) {
                dashboardRoleBadge.textContent = 'SUPER ADMIN';
                dashboardRoleBadge.className = 'status-online text-xs font-medium';
            }
        } else {
            dashboardTitle.textContent = 'Enterprise API Dashboard';
            if (dashboardSubtitle) dashboardSubtitle.textContent = 'Active monitoring for your organization';
            if (dashboardRoleBadge) {
                dashboardRoleBadge.textContent = 'COMPANY USER';
                dashboardRoleBadge.className = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-900/50 text-emerald-400 border border-emerald-800';
            }
        }
    }

    // 3. Update Home Page Views
    const authenticatedView = $('#authenticated-view');
    const guestView = $('#guest-view');
    
    if (authenticatedView && guestView) {
        authenticatedView.classList.remove('hidden');
        guestView.classList.add('hidden');
    }
};
