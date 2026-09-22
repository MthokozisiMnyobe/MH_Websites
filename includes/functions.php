<?php

declare(strict_types=1);

/**
 * Load simple KEY=VALUE pairs without overriding server-level environment values.
 */
function load_env_file(string $path): void
{
    if (!is_readable($path)) {
        return;
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($lines === false) {
        return;
    }

    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
            continue;
        }

        [$key, $value] = array_map('trim', explode('=', $line, 2));
        if (
            $key === ''
            || preg_match('/^[A-Z_][A-Z0-9_]*$/', $key) !== 1
            || getenv($key) !== false
            || array_key_exists($key, $_ENV)
        ) {
            continue;
        }

        if (
            strlen($value) >= 2
            && (($value[0] === '"' && str_ends_with($value, '"'))
                || ($value[0] === "'" && str_ends_with($value, "'")))
        ) {
            $value = substr($value, 1, -1);
        }

        $_ENV[$key] = $value;
        putenv($key . '=' . $value);
    }
}

function env_value(string $key, mixed $default = null): mixed
{
    $value = $_ENV[$key] ?? getenv($key);
    if ($value === false || $value === null || $value === '') {
        return $default;
    }

    $normalised = strtolower((string) $value);
    return match ($normalised) {
        'true', '(true)' => true,
        'false', '(false)' => false,
        'null', '(null)' => null,
        'empty', '(empty)' => '',
        default => $value,
    };
}

function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function normalise_path(string $path): string
{
    $path = '/' . ltrim(str_replace('\\', '/', trim($path)), '/');
    return $path === '/' ? '' : rtrim($path, '/');
}

function infer_base_path(string $applicationRoot): string
{
    $documentRoot = $_SERVER['DOCUMENT_ROOT'] ?? '';
    $realDocumentRoot = $documentRoot !== '' ? realpath($documentRoot) : false;
    $realApplicationRoot = realpath($applicationRoot);

    if ($realDocumentRoot !== false && $realApplicationRoot !== false) {
        $documentRootPath = rtrim(str_replace('\\', '/', $realDocumentRoot), '/');
        $applicationPath = str_replace('\\', '/', $realApplicationRoot);
        if (str_starts_with(strtolower($applicationPath), strtolower($documentRootPath))) {
            return normalise_path(substr($applicationPath, strlen($documentRootPath)));
        }
    }

    return '/MH_Websites';
}

function config(?string $key = null, mixed $default = null): mixed
{
    $configuration = $GLOBALS['app_config'] ?? [];
    if ($key === null) {
        return $configuration;
    }

    $value = $configuration;
    foreach (explode('.', $key) as $segment) {
        if (!is_array($value) || !array_key_exists($segment, $value)) {
            return $default;
        }
        $value = $value[$segment];
    }

    return $value;
}

function app_base_path(): string
{
    return normalise_path((string) config('app.base_path', '/MH_Websites'));
}

function url(string $path = ''): string
{
    $path = ltrim($path, '/');
    $basePath = app_base_path();
    return $basePath . ($path === '' ? '/' : '/' . $path);
}

function asset_url(string $path): string
{
    return url('assets/' . ltrim($path, '/'));
}

function current_request_path(): string
{
    $requestPath = (string) parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
    $basePath = app_base_path();
    if ($basePath !== '' && str_starts_with($requestPath, $basePath)) {
        $requestPath = substr($requestPath, strlen($basePath));
    }

    return '/' . ltrim($requestPath, '/');
}

function canonical_url(?string $path = null): string
{
    $origin = rtrim((string) config('app.canonical_url', 'https://mhwebsites.co.za'), '/');
    $path ??= current_request_path();
    return $origin . '/' . ltrim($path, '/');
}

function is_local_environment(): bool
{
    return in_array((string) config('app.environment', 'production'), ['local', 'testing'], true);
}

function navigation_items(): array
{
    return [
        ['label' => 'Home', 'path' => 'index.php', 'key' => 'home'],
        ['label' => 'Services', 'path' => 'services.php', 'key' => 'services'],
        ['label' => 'Catalogue', 'path' => 'store/index.php', 'key' => 'store'],
        ['label' => 'About', 'path' => 'about.php', 'key' => 'about'],
        ['label' => 'Contact', 'path' => 'contact.php', 'key' => 'contact'],
        ['label' => 'Quote Basket', 'path' => 'store/quote-basket.php', 'key' => 'quote-basket'],
    ];
}

function footer_navigation_items(): array
{
    return navigation_items();
}

function request_is_secure(): bool
{
    if (!empty($_SERVER['HTTPS']) && strtolower((string) $_SERVER['HTTPS']) !== 'off') {
        return true;
    }

    return (string) ($_SERVER['SERVER_PORT'] ?? '') === '443';
}

function safe_log(string $message, array $context = []): void
{
    $sanitised = [];
    foreach ($context as $key => $value) {
        if (preg_match('/password|secret|token|credential/i', (string) $key)) {
            $sanitised[$key] = '[redacted]';
            continue;
        }
        $sanitised[$key] = is_scalar($value) || $value === null ? $value : get_debug_type($value);
    }

    error_log($message . ($sanitised === [] ? '' : ' ' . json_encode($sanitised, JSON_UNESCAPED_SLASHES)));
}

function register_application_exception_handler(): void
{
    if (PHP_SAPI === 'cli') {
        return;
    }

    set_exception_handler(static function (Throwable $exception): void {
        safe_log('Unhandled application exception.', [
            'exception' => $exception::class,
            'location' => $exception->getFile() . ':' . $exception->getLine(),
        ]);

        if (!headers_sent()) {
            http_response_code(500);
            header('Content-Type: text/html; charset=UTF-8');
        }

        $detail = is_local_environment() && (bool) config('app.debug')
            ? '<p>' . e($exception->getMessage()) . '</p>'
            : '';

        echo '<!doctype html><html lang="en-ZA"><head><meta charset="UTF-8">'
            . '<meta name="viewport" content="width=device-width,initial-scale=1">'
            . '<title>Something went wrong — MH Websites</title></head><body>'
            . '<main><h1>Something went wrong</h1>'
            . '<p>We could not complete this request. Please try again shortly.</p>'
            . $detail
            . '</main></body></html>';
    });
}
