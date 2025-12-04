<?php

function generateEmployeeId(string $email, ?string $uuid = null, int $length = 10): string
{
    // 1. Generate UUID v4
    if ($uuid === null) {
        $uuid = bin2hex(random_bytes(16));
        $uuid = substr($uuid, 0, 8) . '-' . substr($uuid, 8, 4) . '-' . substr($uuid, 12, 4) . '-' . substr($uuid, 16, 4) . '-' . substr($uuid, 20);
    }
    
    echo $uuid . PHP_EOL;

    // 2. Hash UUID (SHA1 = 20 bytes)
    $hash = hash('sha256', $uuid . $email, true);

    // 3. Base32 encode (RFC4648, uppercase, remove padding)
    $base32 = rtrim(strtoupper(base32_encode($hash)), '=');

    // 4. Shorten to desired length
    return substr($base32, 0, $length);
}

// Helper: Base32 encoding (RFC4648)
function base32_encode(string $data): string
{
    $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
    $binaryString = '';

    foreach (str_split($data) as $char) {
        $binaryString .= str_pad(decbin(ord($char)), 8, '0', STR_PAD_LEFT);
    }

    $fiveBitGroups = str_split($binaryString, 5);
    $base32 = '';

    foreach ($fiveBitGroups as $group) {
        if (strlen($group) < 5) {
            $group = str_pad($group, 5, '0', STR_PAD_RIGHT);
        }
        $index = bindec($group);
        $base32 .= $alphabet[$index];
    }

    // Pad to a multiple of 8 chars
    while (strlen($base32) % 8 !== 0) {
        $base32 .= '=';
    }

    return $base32;
}

// Example usage:
# echo generateEmployeeId(email: "you@email.tld") . PHP_EOL;
