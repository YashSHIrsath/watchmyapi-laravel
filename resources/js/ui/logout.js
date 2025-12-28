import { $, on } from '../utils/dom';
import { authApi } from '../utils/auth';

/**
 * Handle logout logic and UI state.
 */
export const initLogout = () => {
    const logoutBtn = $('#logout-btn');
    const loginLink = $('#login-link');
    if (!logoutBtn || !loginLink) return;

    // Toggle visibility based on token presence
    const token = localStorage.getItem('access_token');
    if (token) {
        logoutBtn.classList.remove('hidden');
        loginLink.classList.add('hidden');
    } else {
        logoutBtn.classList.add('hidden');
        loginLink.classList.remove('hidden');
        return;
    }

    on(logoutBtn, 'click', async (e) => {
        e.preventDefault();
        
        const originalText = logoutBtn.innerText;
        logoutBtn.innerText = 'Signing out...';
        logoutBtn.style.opacity = '0.5';
        logoutBtn.style.pointerEvents = 'none';

        try {
            // 1. Server acknowledgement (Logical Logout)
            await authApi.logout();

            // 2. Client-side token disposal
            localStorage.removeItem('access_token');
            localStorage.removeItem('refresh_token');

            // 3. UX Treatment
            window.location.href = '/login';
        } catch (err) {
            console.error('Logout failed:', err);
            // Even if API fails, clear client state for security
            localStorage.removeItem('access_token');
            localStorage.removeItem('refresh_token');
            window.location.href = '/login';
        }
    });
};
