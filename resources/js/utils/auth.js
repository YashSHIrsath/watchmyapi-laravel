/**
 * Simple API client for authentication.
 * Separated from UI logic for architectural cleaness.
 */
export const authApi = {
    login: async (email, password) => {
        console.log('[Auth API] Login Attempt:', email);
        try {
            const response = await fetch('/api/auth/login', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ email, password })
            });
            console.log('[Auth API] Login Status:', response.status);
            return {
                status: response.status,
                data: await response.json()
            };
        } catch (error) {
            console.error('[Auth API] Login Error:', error);
            throw error;
        }
    },
    refresh: async () => {
        console.log('[Auth API] Refresh Attempt');
        const refreshToken = localStorage.getItem('refresh_token');
        const response = await fetch('/api/auth/refresh', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ refresh_token: refreshToken })
        });
        console.log('[Auth API] Refresh Status:', response.status);
        return {
            status: response.status,
            data: await response.json()
        };
    },
    getSessions: async (refreshToken = null) => {
        console.log('[Auth API] Get Sessions Attempt');
        const token = localStorage.getItem('access_token');
        let url = '/api/auth/sessions';
        if (refreshToken) {
            url += `?refresh_token=${encodeURIComponent(refreshToken)}`;
        }

        const response = await fetch(url, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'Authorization': `Bearer ${token}`
            }
        });
        console.log('[Auth API] Get Sessions Status:', response.status);
        return {
            status: response.status,
            data: await response.json()
        };
    },
    revokeSession: async (sessionId) => {
        console.log('[Auth API] Revoke Session:', sessionId);
        const token = localStorage.getItem('access_token');
        const response = await fetch(`/api/auth/sessions/${sessionId}`, {
            method: 'DELETE',
            headers: {
                'Accept': 'application/json',
                'Authorization': `Bearer ${token}`,
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        return {
            status: response.status,
            data: await response.json()
        };
    },
    revokeOtherSessions: async () => {
        console.log('[Auth API] Revoke Other Sessions Attempt');
        const token = localStorage.getItem('access_token');
        const refreshToken = localStorage.getItem('refresh_token');
        const response = await fetch('/api/auth/sessions/others', {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'Authorization': `Bearer ${token}`,
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ refresh_token: refreshToken })
        });
        return {
            status: response.status,
            data: await response.json()
        };
    },
    logout: async () => {
        console.log('[Auth API] Logout Attempt');
        const token = localStorage.getItem('access_token');
        const response = await fetch('/api/auth/logout', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'Authorization': `Bearer ${token}`,
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        console.log('[Auth API] Logout Status:', response.status);
        return {
            status: response.status,
            data: await response.json()
        };
    },
    me: async () => {
        console.log('[Auth API] Me Attempt');
        const token = localStorage.getItem('access_token');
        const response = await fetch('/api/auth/me', {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'Authorization': `Bearer ${token}`
            }
        });
        console.log('[Auth API] Me Status:', response.status);
        return {
            status: response.status,
            data: await response.json()
        };
    }
};
