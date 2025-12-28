import { $ } from '../utils/dom';

/**
 * Handle home page dynamic visibility based on auth state.
 */
export const initHome = () => {
    const authView = $('#authenticated-view');
    const guestView = $('#guest-view');
    
    if (!authView || !guestView) return;

    const token = localStorage.getItem('access_token');
    
    if (token) {
        authView.classList.remove('hidden');
        guestView.classList.add('hidden');
    } else {
        authView.classList.add('hidden');
        guestView.classList.remove('hidden');
    }
};
