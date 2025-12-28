import { initAlerts } from './ui/alerts';
import { initLogin } from './ui/login';
import { initLogout } from './ui/logout';
import { initHome } from './ui/home';
import { initSessions } from './ui/sessions';
import { initBranding } from './ui/branding';
import { initMonitors } from './ui/monitors';
import { initAdmin } from './ui/admin';
import { initEnhancements } from './ui/enhancements';

// Initialize UI components
document.addEventListener('DOMContentLoaded', () => {
    const inits = [
        ['Alerts', initAlerts],
        ['Login', initLogin],
        ['Logout', initLogout],
        ['Home', initHome],
        ['Sessions', initSessions],
        ['Branding', initBranding],
        ['Monitors', initMonitors],
        ['Admin', initAdmin],
        ['Enhancements', initEnhancements]
    ];

    inits.forEach(([name, initFunc]) => {
        try {
            console.log(`[Core] Initializing ${name}...`);
            initFunc();
        } catch (error) {
            console.error(`[Core] Failed to initialize ${name}:`, error);
        }
    });
});
