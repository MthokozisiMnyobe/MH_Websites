<?php

declare(strict_types=1);

define('APP_ROOT', dirname(__DIR__));

require_once APP_ROOT . '/includes/functions.php';

$composerAutoload = APP_ROOT . '/vendor/autoload.php';
if (is_file($composerAutoload)) {
    require_once $composerAutoload;
}

load_env_file(APP_ROOT . '/.env');

$debug = (bool) env_value('APP_DEBUG', false);
$basePath = env_value('APP_BASE_PATH', infer_base_path(APP_ROOT));

$GLOBALS['app_config'] = [
    'app' => [
        'name' => 'MH Websites',
        'legal_name' => 'MH WEBSITES (Pty) Ltd',
        'environment' => (string) env_value('APP_ENV', 'production'),
        'debug' => $debug,
        'url' => rtrim((string) env_value('APP_URL', 'http://localhost' . normalise_path((string) $basePath)), '/'),
        'base_path' => normalise_path((string) $basePath),
        'canonical_url' => rtrim((string) env_value('APP_CANONICAL_URL', 'https://mhwebsites.co.za'), '/'),
        'timezone' => (string) env_value('APP_TIMEZONE', 'Africa/Johannesburg'),
        'session_name' => (string) env_value('APP_SESSION_NAME', 'mh_websites_session'),
        'session_save_path' => (string) env_value('SESSION_SAVE_PATH', APP_ROOT . '/storage/sessions'),
        'key' => (string) env_value('APP_KEY', ''),
        'trusted_proxies' => array_values(array_filter(array_map('trim', explode(',', (string) env_value('TRUSTED_PROXIES', ''))))),
    ],
    'database' => [
        'host' => (string) env_value('DB_HOST', ''),
        'port' => (int) env_value('DB_PORT', 3306),
        'name' => (string) env_value('DB_NAME', ''),
        'user' => (string) env_value('DB_USER', ''),
        'password' => (string) env_value('DB_PASSWORD', ''),
        'charset' => (string) env_value('DB_CHARSET', 'utf8mb4'),
    ],
    'mail' => [
        'enabled' => (bool) env_value('MAIL_ENABLED', false),
        'transport' => (string) env_value('MAIL_TRANSPORT', 'null'),
        'host' => (string) env_value('MAIL_HOST', ''),
        'port' => (int) env_value('MAIL_PORT', 587),
        'username' => (string) env_value('MAIL_USERNAME', ''),
        'password' => (string) env_value('MAIL_PASSWORD', ''),
        'encryption' => (string) env_value('MAIL_ENCRYPTION', 'tls'),
        'from_address' => (string) env_value('MAIL_FROM_ADDRESS', ''),
        'from_name' => (string) env_value('MAIL_FROM_NAME', 'MH Websites'),
        'notification_address' => (string) env_value('MAIL_NOTIFICATION_ADDRESS', ''),
        'timeout_seconds' => (int) env_value('MAIL_TIMEOUT_SECONDS', 10),
    ],
    'rate_limit' => [
        'attempts' => (int) env_value('RATE_LIMIT_ATTEMPTS', 6),
        'window_seconds' => (int) env_value('RATE_LIMIT_WINDOW_SECONDS', 900),
    ],
    'submissions' => [
        'max_body_bytes' => (int) env_value('SUBMISSION_MAX_BODY_BYTES', 65536),
        'max_basket_lines' => (int) env_value('SUBMISSION_MAX_BASKET_LINES', 50),
        'idempotency_ttl' => (int) env_value('IDEMPOTENCY_TTL_SECONDS', 3600),
        'confirmation_ttl' => (int) env_value('CONFIRMATION_TTL_SECONDS', 1800),
    ],
    'contact' => [
        'whatsapp_number' => (string) env_value('WHATSAPP_NUMBER', ''),
    ],
];

date_default_timezone_set((string) config('app.timezone'));
error_reporting(E_ALL);
ini_set('display_errors', $debug ? '1' : '0');
ini_set('log_errors', '1');

require_once APP_ROOT . '/includes/security.php';
require_once APP_ROOT . '/includes/flash.php';
require_once APP_ROOT . '/includes/metadata.php';
require_once APP_ROOT . '/includes/catalogue.php';
require_once APP_ROOT . '/includes/submission-validation.php';
require_once APP_ROOT . '/includes/submission-reference.php';
require_once APP_ROOT . '/includes/idempotency.php';
require_once APP_ROOT . '/includes/rate-limit.php';
require_once APP_ROOT . '/includes/submission-access.php';
require_once APP_ROOT . '/includes/mailer.php';
require_once APP_ROOT . '/includes/quotation-service.php';
require_once APP_ROOT . '/includes/enquiry-service.php';
require_once APP_ROOT . '/includes/store-components.php';
require_once APP_ROOT . '/includes/systems-data.php';
require_once APP_ROOT . '/includes/services-data.php';
require_once APP_ROOT . '/includes/company-data.php';
require_once APP_ROOT . '/includes/header.php';
require_once APP_ROOT . '/includes/footer.php';
require_once APP_ROOT . '/config/database.php';

register_application_exception_handler();
send_application_security_headers();
