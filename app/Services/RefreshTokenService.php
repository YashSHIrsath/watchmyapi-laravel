<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserRefreshToken;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class RefreshTokenService
{
    /**
     * Create a new refresh token for a user.
     *
     * @param User $user
     * @param string|null $userAgent
     * @param string|null $ipAddress
     * @return string
     */
    public function createToken(User $user, ?string $userAgent = null, ?string $ipAddress = null): string
    {
        $plainToken = Str::random(64);
        
        UserRefreshToken::create([
            'user_id' => $user->id,
            'token_hash' => hash('sha256', $plainToken),
            'expires_at' => Carbon::now()->addDays(config('jwt.refresh_ttl', 20160) / 1440), // Default 14 days if not set
            'user_agent' => $userAgent,
            'ip_address' => $ipAddress,
        ]);

        return $plainToken;
    }

    /**
     * Verify and rotate a refresh token.
     *
     * @param string $plainToken
     * @param string|null $userAgent
     * @param string|null $ipAddress
     * @return array|null [User $user, string $newRefreshToken]
     */
    public function rotateToken(string $plainToken, ?string $userAgent = null, ?string $ipAddress = null): ?array
    {
        $hash = hash('sha256', $plainToken);
        $refreshToken = UserRefreshToken::where('token_hash', $hash)->first();

        if (!$refreshToken || $refreshToken->is_revoked || $refreshToken->expires_at->isPast()) {
            if ($refreshToken) {
                // Potential theft: Reusing a revoked or expired token.
                // Revoke everything for this user.
                $this->revokeAllForUser($refreshToken->user_id);
            }
            return null;
        }

        // Revoke the old token and mark last used
        $refreshToken->update([
            'is_revoked' => true,
            'last_used_at' => Carbon::now()
        ]);

        // Issue a new one
        $newPlainToken = $this->createToken($refreshToken->user, $userAgent, $ipAddress);

        return [
            'user' => $refreshToken->user,
            'refresh_token' => $newPlainToken
        ];
    }

    /**
     * Get all active sessions for a user.
     *
     * @param int $userId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getActiveSessions(int $userId)
    {
        return UserRefreshToken::where('user_id', $userId)
            ->where('is_revoked', false)
            ->where('expires_at', '>', Carbon::now())
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Revoke all other refresh tokens for a user except the current one.
     *
     * @param int $userId
     * @param string $currentPlainToken
     * @return void
     */
    public function revokeOthers(int $userId, string $currentPlainToken): void
    {
        $currentHash = hash('sha256', $currentPlainToken);
        
        UserRefreshToken::where('user_id', $userId)
            ->where('token_hash', '!=', $currentHash)
            ->where('is_revoked', false)
            ->update(['is_revoked' => true]);
    }

    /**
     * Revoke a specific refresh token for a user.
     *
     * @param int $userId
     * @param int $sessionId
     * @return bool
     */
    public function revokeSpecific(int $userId, int $sessionId): bool
    {
        $session = UserRefreshToken::where('user_id', $userId)
            ->where('id', $sessionId)
            ->where('is_revoked', false)
            ->first();

        if (!$session) {
            return false;
        }

        return $session->update(['is_revoked' => true]);
    }

    /**
     * Revoke all refresh tokens for a user.
     *
     * @param int $userId
     * @return void
     */
    public function revokeAllForUser(int $userId): void
    {
        UserRefreshToken::where('user_id', $userId)
            ->where('is_revoked', false)
            ->update(['is_revoked' => true]);
    }

    /**
     * Revoke a specific refresh token.
     *
     * @param string $plainToken
     * @return void
     */
    public function revokeToken(string $plainToken): void
    {
        $hash = hash('sha256', $plainToken);
        UserRefreshToken::where('token_hash', $hash)->update(['is_revoked' => true]);
    }
}
