<?php

declare(strict_types=1);

define('APP_ROOT', dirname(__DIR__));

require_once APP_ROOT . '/includes/functions.php';

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
        'host' => (string) env_value('MAIL_HOST', ''),
        'port' => (int) env_value('MAIL_PORT', 587),
        'username' => (string) env_value('MAIL_USERNAME', ''),
        'password' => (string) env_value('MAIL_PASSWORD', ''),
        'encryption' => (string) env_value('MAIL_ENCRYPTION', 'tls'),
        'from_address' => (string) env_value('MAIL_FROM_ADDRESS', ''),
        'from_name' => (string) env_value('MAIL_FROM_NAME', 'MH Websites'),
        'notification_address' => (string) env_value('MAIL_NOTIFICATION_ADDRESS', ''),
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
require_once APP_ROOT . '/includes/systems-data.php';
require_once APP_ROOT . '/includes/header.php';
require_once APP_ROOT . '/includes/footer.php';
require_once APP_ROOT . '/config/database.php';

register_application_exception_handler();
