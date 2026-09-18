<?php

declare(strict_types=1);

error_reporting(E_ALL);
ini_set('display_errors', '1');

set_error_handler(
    static function (int $severity, string $message, string $file, int $line): never {
        throw new ErrorException($message, 0, $severity, $file, $line);
    }
);

$_SERVER['DOCUMENT_ROOT'] = dirname(__DIR__, 2);
$_SERVER['REQUEST_URI'] = '/MH_Websites/index.php';
$_SERVER['REQUEST_METHOD'] = 'GET';

require dirname(__DIR__) . '/config/bootstrap.php';

$assertions = 0;

function assert_same(mixed $expected, mixed $actual, string $label): void
{
    global $assertions;
    $assertions++;
    if ($expected !== $actual) {
        throw new RuntimeException(sprintf(
            '%s failed. Expected %s, received %s.',
            $label,
            var_export($expected, true),
            var_export($actual, true)
        ));
    }
}

function assert_contains(string $needle, string $haystack, string $label): void
{
    global $assertions;
    $assertions++;
    if (!str_contains($haystack, $needle)) {
        throw new RuntimeException($label . ' failed. Missing: ' . $needle);
    }
}

assert_same('/MH_Websites', app_base_path(), 'Application base path');
assert_same('/MH_Websites/services.php', url('services.php'), 'Root page URL');
assert_same('/MH_Websites/assets/css/base.css', asset_url('css/base.css'), 'Asset URL');
assert_same('&lt;script&gt;&quot;&amp;', e('<script>"&'), 'HTML escaping');
assert_same(false, database_is_configured(), 'Missing database configuration');
assert_same(null, database_connection(), 'Optional database safe failure');
assert_same(false, config('mail.enabled'), 'Optional mail disabled by default');
assert_same(true, function_exists('register_application_exception_handler'), 'Application error handler');
assert_same(4, count(catalogue_categories()), 'Approved catalogue categories');
assert_same('EduFlow', system_record('eduflow')['name'] ?? null, 'Shared systems registry');

$_SERVER['REQUEST_URI'] = '/MH_Websites/systems/eduflow.php?ref=test';
assert_same('/systems/eduflow.php', current_request_path(), 'Nested request path');
assert_same('/MH_Websites/assets/js/navigation.js', asset_url('js/navigation.js'), 'Nested asset URL');

$token = csrf_token();
assert_same(64, strlen($token), 'CSRF token length');
assert_same(true, csrf_is_valid($token), 'Valid CSRF token');
assert_same(false, csrf_is_valid(str_repeat('0', 64)), 'Invalid CSRF token');

flash_set('notice', 'Saved safely');
assert_same('Saved safely', flash_peek('notice'), 'Flash peek');
assert_same('Saved safely', flash_get('notice'), 'Flash retrieval');
assert_same(null, flash_get('notice'), 'Flash is consumed once');

ob_start();
render_header([
    'active' => 'systems',
    'body_class' => 'foundation-test',
    'metadata' => [
        'title' => 'Architecture & <Test>',
        'description' => 'Checkpoint 1 shared layout test.',
        'robots' => 'noindex,nofollow',
    ],
]);
?>
<main id="main-content"><h1>Foundation test</h1></main>
<?php
render_footer();
$html = (string) ob_get_clean();

assert_contains('<html lang="en-ZA">', $html, 'South African language declaration');
assert_contains('href="#main-content">Skip to main content</a>', $html, 'Skip link');
assert_contains('aria-label="Primary navigation"', $html, 'Primary navigation landmark');
assert_contains('aria-current="page"', $html, 'Active navigation state');
assert_contains('Architecture &amp; &lt;Test&gt;', $html, 'Metadata escaping');
assert_contains('/MH_Websites/assets/css/tokens.css', $html, 'Shared stylesheet path');
assert_contains('/MH_Websites/assets/js/navigation.js', $html, 'Shared JavaScript path');
assert_contains('<footer class="site-footer">', $html, 'Shared footer');

session_write_close();
restore_error_handler();

echo "Checkpoint 1 smoke tests passed ({$assertions} assertions)." . PHP_EOL;
