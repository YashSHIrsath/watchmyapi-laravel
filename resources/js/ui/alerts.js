import { $$ } from '../utils/dom';

/**
 * Handle alert dismissal.
 */
export const initAlerts = () => {
    $$('[data-dismiss="alert"]').forEach(button => {
        button.addEventListener('click', () => {
            const alert = button.closest('[id^="alert-"]');
            if (alert) {
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 300);
            }
        });
    });
};
