import { $, on } from '../utils/dom';
import { authApi } from '../utils/auth';

/**
 * Handle login form UI states and submission.
 */
export const initLogin = () => {
    const loginForm = $('#login-form');
    if (!loginForm) return;

    const errorContainer = $('#login-error-container');
    const submitBtn = $('#login-submit');

    on(loginForm, 'submit', async (e) => {
        e.preventDefault();
        console.log('[Login] Form submitted');
        
        // Reset state
        errorContainer.innerHTML = '';
        submitBtn.disabled = true;
        submitBtn.innerText = 'Authenticating...';

        try {
            const email = $('#email').value;
            const password = $('#password').value;

            console.log('[Login] Calling authApi.login...');
            const { status, data } = await authApi.login(email, password);
            console.log('[Login] authApi.login result:', { status, success: data?.success });

            if (status === 200 && data.success) {
                console.log('[Login] Success! Saving tokens...');
                localStorage.setItem('access_token', data.data.access_token);
                localStorage.setItem('refresh_token', data.data.refresh_token);
                
                showSuccess('Welcome back! Redirecting...');
                
                setTimeout(() => {
                    const userType = data.data.user_type;
                    const target = userType === 'super_admin' ? '/admin/dashboard' : '/dashboard';
                    console.log(`[Login] Redirecting to ${target}`);
                    window.location.href = target; 
                }, 800);
            } else {
                console.warn('[Login] Authentication failed:', data.message);
                showError(data.message || 'Invalid credentials.');
            }
        } catch (err) {
            console.error('[Login] Unexpected system error:', err);
            showError('A system error occurred.');
        } finally {
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerText = 'Sign In';
            }
        }
    });

    const showError = (message) => {
        errorContainer.innerHTML = `
            <div class="mb-6 p-4 bg-red-900/40 border border-red-800 text-red-400 rounded-xl flex items-center justify-between animate-pulse">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 001.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                    </svg>
                    ${message}
                </div>
            </div>
        `;
    };

    const showSuccess = (message) => {
        errorContainer.innerHTML = `
            <div class="mb-6 p-4 bg-green-900/40 border border-green-800 text-green-400 rounded-xl flex items-center justify-between">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    ${message}
                </div>
            </div>
        `;
    };
};
