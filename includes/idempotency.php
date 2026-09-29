<?php

declare(strict_types=1);

function issue_idempotency_token(string $scope): string
{
    ensure_session_started();
    $now = time();
    $ttl = (int) config('submissions.idempotency_ttl', 3600);
    $_SESSION['_idempotency'][$scope] = array_filter(
        (array) ($_SESSION['_idempotency'][$scope] ?? []),
        static fn (int $expires): bool => $expires >= $now
    );
    if (count($_SESSION['_idempotency'][$scope]) > 9) {
        $_SESSION['_idempotency'][$scope] = array_slice($_SESSION['_idempotency'][$scope], -9, null, true);
    }
    $token = bin2hex(random_bytes(32));
    $_SESSION['_idempotency'][$scope][hash('sha256', $token)] = $now + $ttl;
    return $token;
}

function consume_idempotency_token(string $scope, ?string $token): string
{
    ensure_session_started();
    if (!is_string($token) || strlen($token) !== 64 || !ctype_xdigit($token)) {
        throw new SubmissionValidationException(['submission' => 'This form has expired. Please refresh and try again.']);
    }
    $hash = hash('sha256', $token);
    $expires = $_SESSION['_idempotency'][$scope][$hash] ?? 0;
    if (!is_int($expires) || $expires < time()) {
        throw new SubmissionValidationException(['submission' => 'This form has already been used or has expired.']);
    }
    unset($_SESSION['_idempotency'][$scope][$hash]);
    return $hash;
}
