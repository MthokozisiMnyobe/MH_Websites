<?php

declare(strict_types=1);

function ensure_session_started(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }

    if (headers_sent($file, $line)) {
        throw new RuntimeException("Cannot start a secure session after output at {$file}:{$line}.");
    }

    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    ini_set('session.cookie_httponly', '1');
    ini_set('session.cookie_samesite', 'Lax');
    $sessionSavePath = (string) config('app.session_save_path', '');
    if ($sessionSavePath === '' || !is_dir($sessionSavePath) || !is_writable($sessionSavePath)) {
        throw new RuntimeException('The configured session storage path is unavailable.');
    }
    session_save_path($sessionSavePath);
    session_name((string) config('app.session_name', 'mh_websites_session'));
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => app_base_path() === '' ? '/' : app_base_path() . '/',
        'secure' => request_is_secure(),
        'httponly' => true,
        'samesite' => 'Lax',
    ]);

    if (!session_start()) {
        throw new RuntimeException('Unable to start a secure session.');
    }
}

function csrf_token(): string
{
    ensure_session_started();
    if (!isset($_SESSION['csrf_token']) || !is_string($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

function csrf_is_valid(?string $submittedToken): bool
{
    ensure_session_started();
    $knownToken = $_SESSION['csrf_token'] ?? null;
    return is_string($knownToken)
        && is_string($submittedToken)
        && $submittedToken !== ''
        && hash_equals($knownToken, $submittedToken);
}

function require_post_request(): void
{
    if (strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET')) !== 'POST') {
        http_response_code(405);
        header('Allow: POST');
        throw new RuntimeException('This endpoint accepts POST requests only.');
    }
}

function validate_csrf_or_fail(?string $submittedToken): void
{
    if (!csrf_is_valid($submittedToken)) {
        http_response_code(419);
        throw new RuntimeException('The form session has expired. Please refresh and try again.');
    }
}
