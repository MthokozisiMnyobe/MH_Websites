<?php

declare(strict_types=1);

error_reporting(E_ALL);
ini_set('display_errors', '1');

$assertions = 0;

function security_header_check(bool $condition, string $label): void
{
    global $assertions;
    $assertions++;
    if (!$condition) {
        throw new RuntimeException($label . ' failed.');
    }
}

/**
 * @return array{status: int, headers: array<string, list<string>>}
 */
function localhost_head_response(string $path): array
{
    $socket = @fsockopen('127.0.0.1', 80, $errorCode, $errorMessage, 5.0);
    if (!is_resource($socket)) {
        throw new RuntimeException('Local Apache is unavailable for the security-header check.');
    }

    stream_set_timeout($socket, 5);
    fwrite(
        $socket,
        "HEAD {$path} HTTP/1.1\r\n"
        . "Host: 127.0.0.1\r\n"
        . "Connection: close\r\n\r\n"
    );

    $statusLine = fgets($socket);
    if (!is_string($statusLine) || preg_match('/^HTTP\/\d(?:\.\d)?\s+(\d{3})\b/', trim($statusLine), $matches) !== 1) {
        fclose($socket);
        throw new RuntimeException('Local Apache returned an invalid HTTP status line.');
    }

    $headers = [];
    while (($line = fgets($socket)) !== false) {
        $line = rtrim($line, "\r\n");
        if ($line === '') {
            break;
        }
        $separator = strpos($line, ':');
        if ($separator === false) {
            continue;
        }
        $name = strtolower(trim(substr($line, 0, $separator)));
        $value = trim(substr($line, $separator + 1));
        $headers[$name] ??= [];
        $headers[$name][] = $value;
    }
    fclose($socket);

    return ['status' => (int) $matches[1], 'headers' => $headers];
}

/**
 * @param array<string, list<string>> $headers
 */
function assert_single_header(array $headers, string $name, string $expected, string $route): void
{
    $values = $headers[strtolower($name)] ?? [];
    security_header_check(count($values) === 1, "{$route} has one {$name} header");
    security_header_check(($values[0] ?? null) === $expected, "{$route} {$name} value");
}

$basePath = '/MH_Websites';
$permissionsPolicy = 'camera=(), microphone=(), geolocation=(), payment=(), usb=(), accelerometer=(), gyroscope=(), magnetometer=()';
$phpRoutes = [
    '/index.php',
    '/contact.php',
    '/legal/privacy.php',
    '/store/index.php',
    '/store/request-quote.php',
    '/store/quote-confirmation.php',
    '/store/print-request.php',
];

foreach ($phpRoutes as $route) {
    $response = localhost_head_response($basePath . $route);
    security_header_check($response['status'] === 200, "{$route} is available");
    assert_single_header($response['headers'], 'X-Content-Type-Options', 'nosniff', $route);
    assert_single_header($response['headers'], 'Referrer-Policy', 'strict-origin-when-cross-origin', $route);
    assert_single_header($response['headers'], 'X-Frame-Options', 'DENY', $route);
    assert_single_header($response['headers'], 'Permissions-Policy', $permissionsPolicy, $route);
    security_header_check(!isset($response['headers']['content-security-policy']), "{$route} has no enforced CSP");
    security_header_check(!isset($response['headers']['content-security-policy-report-only']), "{$route} has no report-only CSP");
    security_header_check(!isset($response['headers']['strict-transport-security']), "{$route} has no HSTS on localhost");
    security_header_check(!isset($response['headers']['x-powered-by']), "{$route} suppresses X-Powered-By");
}

$sensitiveRoutes = [
    '/contact.php',
    '/store/compatibility-help.php',
    '/store/request-quote.php',
    '/store/quote-confirmation.php',
    '/store/print-request.php',
];

foreach ($sensitiveRoutes as $route) {
    $response = localhost_head_response($basePath . $route);
    $cacheValues = $response['headers']['cache-control'] ?? [];
    security_header_check(count($cacheValues) === 1, "{$route} has one Cache-Control header");
    $cacheControl = strtolower($cacheValues[0] ?? '');
    security_header_check(str_contains($cacheControl, 'no-store'), "{$route} retains no-store");
    security_header_check(str_contains($cacheControl, 'no-cache'), "{$route} retains no-cache");
}

$assets = [
    '/assets/css/base.css' => 'text/css',
    '/assets/js/navigation.js' => 'text/javascript',
    '/assets/images/hero-software-environment.webp' => 'image/webp',
    '/assets/logos/logo-mh-websites.svg' => 'image/svg+xml',
];

foreach ($assets as $route => $expectedType) {
    $response = localhost_head_response($basePath . $route);
    security_header_check($response['status'] === 200, "{$route} is available");
    assert_single_header($response['headers'], 'X-Content-Type-Options', 'nosniff', $route);
    $contentTypes = $response['headers']['content-type'] ?? [];
    security_header_check(count($contentTypes) === 1, "{$route} has one Content-Type header");
    security_header_check(strtolower(trim(explode(';', $contentTypes[0] ?? '')[0])) === $expectedType, "{$route} MIME type");
}

echo "Checkpoint 9 security-header tests passed ({$assertions} assertions)." . PHP_EOL;
