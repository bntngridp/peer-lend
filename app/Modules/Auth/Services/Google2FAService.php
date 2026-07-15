<?php

namespace App\Modules\Auth\Services;

use InvalidArgumentException;

class Google2FAService
{
    /**
     * Generate a 16-character secure random Base32 secret.
     */
    public function generateSecret(): string
    {
        $base32Chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $secret = '';
        for ($i = 0; $i < 16; $i++) {
            $secret .= $base32Chars[random_int(0, 31)];
        }
        return $secret;
    }

    /**
     * Verify a 6-digit TOTP code against a secret key with configurable time drift/discrepancy.
     */
    public function verifyCode(string $secret, string $code, int $discrepancy = 1): bool
    {
        $currentTimeSlice = (int) floor(time() / 30);

        for ($i = -$discrepancy; $i <= $discrepancy; $i++) {
            $calculatedCode = $this->calculateCode($secret, $currentTimeSlice + $i);
            if (hash_equals($calculatedCode, trim($code))) {
                return true;
            }
        }

        return false;
    }

    /**
     * Generate Google Authenticator compatible QR Code URL via Google Chart API.
     */
    public function getQRCodeUrl(string $email, string $secret): string
    {
        $issuer = 'Peer-Lend';
        $label = rawurlencode($issuer . ':' . $email);
        $qrUrl = "otpauth://totp/{$label}?secret={$secret}&issuer=" . rawurlencode($issuer);

        return "https://chart.googleapis.com/chart?chs=200x200&chld=M|0&cht=qr&chl=" . urlencode($qrUrl);
    }

    /**
     * Calculate TOTP (Time-based One-time Password) code for a specific time slice.
     */
    private function calculateCode(string $secret, int $timeSlice): string
    {
        $secretKey = $this->base32Decode($secret);

        // Pack the 64-bit counter time slice value to 8 bytes binary
        $timeBytes = pack('N*', 0) . pack('N*', $timeSlice);

        // Compute HMAC-SHA1
        $hmac = hash_hmac('sha1', $timeBytes, $secretKey, true);

        // Dynamic truncation (RFC 4226)
        $offset = ord(substr($hmac, -1)) & 0x0F;
        $hashpart = substr($hmac, $offset, 4);

        // Convert selected bytes to a 31-bit integer
        $value = unpack('N', $hashpart)[1] & 0x7FFFFFFF;

        return str_pad((string) ($value % 1000000), 6, '0', STR_PAD_LEFT);
    }

    /**
     * Decode a Base32 string to binary bytes.
     */
    private function base32Decode(string $secret): string
    {
        $secret = strtoupper(trim($secret));
        if (empty($secret)) {
            return '';
        }

        $base32Alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $base32Lookup = array_flip(str_split($base32Alphabet));

        $binaryString = '';
        foreach (str_split($secret) as $char) {
            if ($char === '=') {
                break;
            }
            if (! isset($base32Lookup[$char])) {
                throw new InvalidArgumentException("Invalid Base32 character: {$char}");
            }
            $binaryString .= str_pad(decbin($base32Lookup[$char]), 5, '0', STR_PAD_LEFT);
        }

        $bytes = '';
        $eightBitGroups = str_split($binaryString, 8);
        foreach ($eightBitGroups as $group) {
            if (strlen($group) < 8) {
                break;
            }
            $bytes .= chr(bindec($group));
        }

        return $bytes;
    }
}
