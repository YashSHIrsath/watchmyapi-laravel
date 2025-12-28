<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Authenticate user and return token.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $result = $this->authService->login($request->only(['email', 'password']));

            if (!$result) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid credentials.',
                ], 401);
            }

            return response()->json([
                'success' => true,
                'data' => array_merge($result, ['user_type' => Auth::guard('api')->user()->user_type]),
                'message' => 'Login successful.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Get authenticated user profile.
     */
    public function me(): JsonResponse
    {
        $user = $this->authService->me();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized or account inactive.',
            ], 401);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'uuid' => $user->uuid,
                'name' => $user->name,
                'email' => $user->email,
                'user_type' => $user->user_type,
                'company_id' => $user->company_id,
            ],
            'message' => 'Authenticated user context retrieved.',
        ]);
    }

    /**
     * Refresh current JWT token.
     */
    public function refresh(Request $request): JsonResponse
    {
        $refreshToken = $request->input('refresh_token');

        if (!$refreshToken) {
            return response()->json([
                'success' => false,
                'message' => 'Refresh token required.',
            ], 400);
        }

        try {
            $result = $this->authService->refresh($refreshToken);

            if (!$result) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid or expired refresh token.',
                ], 401);
            }

            return response()->json([
                'success' => true,
                'data' => $result,
                'message' => 'Token refreshed successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Could not refresh token.',
            ], 401);
        }
    }

    /**
     * List active sessions for the user.
     */
    public function sessions(Request $request): JsonResponse
    {
        $currentRefreshToken = $request->query('refresh_token');
        $currentHash = $currentRefreshToken ? hash('sha256', $currentRefreshToken) : null;

        $sessions = $this->authService->getActiveSessions();

        $data = $sessions->map(function ($session) use ($currentHash) {
            return [
                'id' => $session->id,
                'device' => $this->parseUserAgent($session->user_agent),
                'ip' => $this->maskIp($session->ip_address),
                'last_active' => $session->last_used_at ? $session->last_used_at->diffForHumans() : $session->created_at->diffForHumans(),
                'is_current' => $currentHash && $session->token_hash === $currentHash,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data,
            'message' => 'Active sessions retrieved successfully.',
        ]);
    }

    /**
     * Revoke a specific session.
     */
    public function revokeSpecific(Request $request, int $id): JsonResponse
    {
        // Safety: Client should ideally identify itself, but for now we trust the Service
        // to handle user ownership. The controller handles the "not self" rule if possible.
        
        $success = $this->authService->revokeSpecificSession($id);

        if (!$success) {
            return response()->json([
                'success' => false,
                'message' => 'Session not found or already revoked.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Session revoked successfully.',
        ]);
    }

    /**
     * Revoke all other sessions.
     */
    public function revokeOthers(Request $request): JsonResponse
    {
        $currentRefreshToken = $request->input('refresh_token');

        if (!$currentRefreshToken) {
            return response()->json([
                'success' => false,
                'message' => 'Current refresh token required to preserve session.',
            ], 400);
        }

        $this->authService->revokeOtherSessions($currentRefreshToken);

        return response()->json([
            'success' => true,
            'message' => 'All other sessions revoked successfully.',
        ]);
    }

    /**
     * Logout user (client-side discard).
     */
    public function logout(): JsonResponse
    {
        $this->authService->logout();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully.',
        ]);
    }

    /**
     * Basic UA parser.
     */
    protected function parseUserAgent(?string $ua): string
    {
        if (!$ua) return 'Unknown Device';
        
        if (str_contains($ua, 'Mobi')) return 'Mobile Device';
        if (str_contains($ua, 'Windows')) return 'Windows PC';
        if (str_contains($ua, 'Macintosh')) return 'Mac';
        if (str_contains($ua, 'Linux')) return 'Linux System';
        
        return 'Desktop Device';
    }

    /**
     * Mask IP address.
     */
    protected function maskIp(?string $ip): string
    {
        if (!$ip) return 'Unknown';
        $parts = explode('.', $ip);
        if (count($parts) === 4) {
            return "{$parts[0]}.{$parts[1]}.xxx.xxx";
        }
        return 'Protected IP';
    }
}
