<?php

namespace App\Traits;

use Illuminate\Support\Facades\Crypt;

trait HasEncryptedId
{
    /**
     * Get the encrypted ID for the model.
     *
     * @return string
     */
    public function getEncryptedIdAttribute(): string
    {
        return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode(Crypt::encryptString((string)$this->id)));
    }

    /**
     * Decrypt an ID from a slug.
     *
     * @param string $slug
     * @return int|null
     */
    public static function decryptId(string $slug): ?int
    {
        try {
            // Restore base64 padding if necessary
            $base64 = str_replace(['-', '_'], ['+', '/'], $slug);
            $padding = strlen($base64) % 4;
            if ($padding > 0) {
                $base64 .= str_repeat('=', 4 - $padding);
            }
            
            $decrypted = Crypt::decryptString(base64_decode($base64));
            return (int)$decrypted;
        } catch (\Exception $e) {
            return null;
        }
    }
}
