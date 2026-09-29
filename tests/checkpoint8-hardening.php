<?php

declare(strict_types=1);

error_reporting(E_ALL);
ini_set('display_errors', '1');

$assertions = 0;

function hardening_check(bool $condition, string $label): void
{
    global $assertions;
    $assertions++;
    if (!$condition) {
        throw new RuntimeException($label . ' failed.');
    }
}

function localhost_head_status(string $path): int
{
    $socket = @fsockopen('127.0.0.1', 80, $errorCode, $errorMessage, 5.0);
    if (!is_resource($socket)) {
        throw new RuntimeException('Local Apache is unavailable for the hardening check.');
    }

    stream_set_timeout($socket, 5);
    fwrite(
        $socket,
        "HEAD {$path} HTTP/1.1\r\n"
        . "Host: 127.0.0.1\r\n"
        . "Connection: close\r\n\r\n"
    );
    $statusLine = fgets($socket);
    fclose($socket);

    if (!is_string($statusLine) || preg_match('/^HTTP\/\d(?:\.\d)?\s+(\d{3})\b/', trim($statusLine), $matches) !== 1) {
        throw new RuntimeException('Local Apache returned an invalid HTTP status line.');
    }

    return (int) $matches[1];
}

function report_http_check(string $category, string $path, int $status, bool $passed, string $result): void
{
    echo implode('|', [$category, $path, (string) $status, $result, $passed ? 'PASS' : 'FAIL']) . PHP_EOL;
    hardening_check($passed, $category . ' ' . $path);
}

$root = dirname(__DIR__);
$rules = (string) file_get_contents($root . '/.htaccess');
$acmePosition = strpos($rules, 'RewriteRule ^\.well-known/acme-challenge');
$dotPosition = strpos($rules, 'RewriteRule (^|/)\.');

hardening_check(str_contains($rules, 'Options -Indexes'), 'Directory indexes disabled');
hardening_check($acmePosition !== false && $dotPosition !== false && $acmePosition < $dotPosition, 'ACME exception precedes dot-path denial');
hardening_check(str_contains($rules, 'config|database|documentation|includes|storage|tests|vendor'), 'Internal directories denied');
hardening_check(str_contains($rules, 'composer\.(?:json|lock)'), 'Composer metadata denied');
hardening_check(str_contains($rules, 'README'), 'README files denied');
hardening_check(str_contains($rules, '[^/]+\.md'), 'Root Markdown documentation denied');

$basePath = '/MH_Websites';
$sensitivePaths = [
    '/.env',
    '/.env.example',
    '/.git/HEAD',
    '/.git/config',
    '/.gitignore',
    '/composer.json',
    '/composer.lock',
    '/documentation/',
    '/tests/',
    '/database/',
    '/database/migrations/',
    '/database/migrations/20260922_phase1e_submissions.sql',
    '/config/',
    '/config/bootstrap.php',
    '/includes/',
    '/includes/mailer.php',
    '/storage/',
    '/vendor/',
    '/vendor/autoload.php',
    '/README.txt',
    '/MH_WEBSITES_CODEX_PHASE1_MASTER_PROMPT.md',
    '/AGENTS.md',
    '/AI_PROJECT_MANAGER.md',
];

foreach ($sensitivePaths as $path) {
    $status = localhost_head_status($basePath . $path);
    report_http_check('SENSITIVE', $path, $status, $status === 403 || $status === 404, $status === 403 ? 'BLOCKED' : 'NOT_FOUND');
}

foreach (['/assets/', '/assets/images/', '/assets/js/', '/legal/'] as $path) {
    $status = localhost_head_status($basePath . $path);
    report_http_check('DIRECTORY', $path, $status, $status === 403, 'INDEX_DISABLED');
}

$acmePath = '/.well-known/acme-challenge/phase1f-c1-reservation-probe';
$acmeStatus = localhost_head_status($basePath . $acmePath);
report_http_check('ACME', $acmePath, $acmeStatus, $acmeStatus === 404, 'RESERVED_NOT_CONFIGURED');

$publicRoutes = [
    '/',
    '/index.php',
    '/services.php',
    '/about.php',
    '/contact.php',
    '/legal/privacy.php',
    '/legal/website-terms.php',
    '/legal/quotation-terms.php',
    '/legal/delivery-information.php',
    '/store/index.php',
    '/store/product.php?id=business-multifunction-printer',
    '/store/compatibility-help.php',
    '/store/quote-basket.php',
    '/store/request-quote.php',
    '/store/quote-confirmation.php',
    '/store/print-request.php',
];

foreach ($publicRoutes as $path) {
    $status = localhost_head_status($basePath . $path);
    report_http_check('PUBLIC', $path, $status, $status === 200, 'AVAILABLE');
}

$publicAssets = [
    '/assets/css/base.css',
    '/assets/css/pages/corporate.css',
    '/assets/js/navigation.js',
    '/assets/js/store/catalogue.js',
    '/assets/images/hero-software-environment.webp',
];

foreach ($publicAssets as $path) {
    $status = localhost_head_status($basePath . $path);
    report_http_check('ASSET', $path, $status, $status === 200, 'AVAILABLE');
}

require_once $root . '/vendor/autoload.php';
hardening_check(class_exists(PHPMailer\PHPMailer\PHPMailer::class), 'Composer autoload remains available internally');
echo 'RUNTIME|Composer autoload|INTERNAL|AVAILABLE|PASS' . PHP_EOL;

echo "Checkpoint 8 hardening tests passed ({$assertions} assertions)." . PHP_EOL;
