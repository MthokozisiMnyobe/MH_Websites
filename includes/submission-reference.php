<?php

declare(strict_types=1);

function submission_random_code(int $bytes = 10): string
{
    $alphabet = '0123456789ABCDEFGHJKMNPQRSTVWXYZ';
    $bits = '';
    foreach (str_split(random_bytes($bytes)) as $byte) {
        $bits .= str_pad(decbin(ord($byte)), 8, '0', STR_PAD_LEFT);
    }
    $code = '';
    foreach (str_split($bits, 5) as $chunk) {
        $code .= $alphabet[bindec(str_pad($chunk, 5, '0'))];
    }
    return $code;
}

function generate_submission_reference(string $prefix, ?DateTimeImmutable $now = null): string
{
    if (!in_array($prefix, ['MHQ', 'MHE'], true)) {
        throw new InvalidArgumentException('Invalid submission reference prefix.');
    }
    return $prefix . '-' . ($now ?? new DateTimeImmutable())->format('Ymd') . '-' . submission_random_code();
}
