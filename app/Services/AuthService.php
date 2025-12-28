<?php

namespace App\Services;

use App\Models\User;
use App\Services\RefreshTokenService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use Carbon\Carbon;

class AuthService
{
    protected $refreshTokenService;

    public function __construct(RefreshTokenService $refreshTokenService)
    {
        $this->refreshTokenService = $refreshTokenService;
    }

    /**
     * Handle user login.
     *
     * @param array $credentials
     * @return array|null
     */
    public function login(array $credentials): ?array
    {
        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return null;
        }

        if (!$user->is_active) {
            throw new \Exception("Account is inactive.");
        }

        $token = Auth::guard('api')->login($user);
        Auth::guard('web')->login($user);

        if (!$token) {
            return null;
        }

        // Audit update
        $user->update([
            'last_login_at' => Carbon::now(),
        ]);

        $refreshToken = $this->refreshTokenService->createToken(
            $user,
            request()->userAgent(),
            request()->ip()
        );

        return $this->respondWithToken($token, $refreshToken);
    }

    /**
     * Get all active sessions for the current user.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getActiveSessions()
    {
        $user = Auth::guard('api')->user();
        if (!$user) return collect();

        return $this->refreshTokenService->getActiveSessions($user->id);
    }

    /**
     * Revoke all other sessions for the current user.
     *
     * @param string $currentRefreshToken
     * @return void
     */
    public function revokeOtherSessions(string $currentRefreshToken): void
    {
        $user = Auth::guard('api')->user();
        if ($user) {
            $this->refreshTokenService->revokeOthers($user->id, $currentRefreshToken);
        }
    }

    /**
     * Revoke a specific session for the current user.
     *
     * @param int $sessionId
     * @return bool
     */
    public function revokeSpecificSession(int $sessionId): bool
    {
        $user = Auth::guard('api')->user();
        if (!$user) return false;

        return $this->refreshTokenService->revokeSpecific($user->id, $sessionId);
    }

    /**
     * Refresh the current token using rotation.
     *
     * @param string $refreshToken
     * @return array|null
     */
    public function refresh(string $refreshToken): ?array
    {
        $rotation = $this->refreshTokenService->rotateToken(
            $refreshToken,
            request()->userAgent(),
            request()->ip()
        );

        if (!$rotation) {
            return null;
        }

        $user = $rotation['user'];
        $newRefreshToken = $rotation['refresh_token'];

        // Generate new JWT
        $accessToken = Auth::guard('api')->login($user);

        return $this->respondWithToken($accessToken, $newRefreshToken);
    }

    /**
     * Log the user out (acknowledgement).
     *
     * @return void
     */
    public function logout(): void
    {
        $user = Auth::guard('api')->user();
        if ($user) {
            $this->refreshTokenService->revokeAllForUser($user->id);
        }
        Auth::guard('api')->logout();
    }

    /**
     * Get the authenticated user.
     *
     * @return User|null
     */
    public function me(): ?User
    {
        $user = Auth::guard('api')->user();

        if ($user && !$user->is_active) {
            return null;
        }

        return $user;
    }

    /**
     * Format the response with the token.
     *
     * @param  string $token
     * @param  string|null $refreshToken
     * @return array
     */
    protected function respondWithToken(string $token, ?string $refreshToken = null): array
    {
        $data = [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::guard('api')->factory()->getTTL() * 60,
        ];

        if ($refreshToken) {
            $data['refresh_token'] = $refreshToken;
        }

        return $data;
    }
}
