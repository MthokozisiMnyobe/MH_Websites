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

function check(bool $condition, string $label): void
{
    global $assertions;
    $assertions++;
    if (!$condition) {
        throw new RuntimeException($label . ' failed.');
    }
}

$root = dirname(__DIR__);
$pages = ['index.php', 'services.php', 'about.php', 'contact.php'];

foreach ($pages as $page) {
    $path = $root . '/' . $page;
    check(is_file($path), $page . ' exists');
    $source = (string) file_get_contents($path);
    check(str_contains($source, "require __DIR__ . '/config/bootstrap.php'"), $page . ' uses shared bootstrap');
    check(str_contains($source, 'render_header(['), $page . ' uses shared header');
    check(str_contains($source, '<main id="main-content">'), $page . ' exposes main landmark target');
    check(str_contains($source, 'render_footer();'), $page . ' uses shared footer');
    check(preg_match('/lorem ipsum|placeholder logo|fake statistic/i', $source) !== 1, $page . ' has no placeholder copy');
}

$services = service_categories();
check(count($services) === 5, 'Five approved service categories');
check(array_key_first($services) === 'custom-software', 'Custom software leads services');
check(($services['learning-platforms']['title'] ?? '') === 'Learning Platforms & Moodle', 'Approved Moodle service title');

$systems = systems_portfolio();
check(count($systems) === 6, 'Six approved systems in preview');
check(($systems['eduflow']['status'] ?? '') === 'Pilot', 'EduFlow status is Pilot');
check(($systems['clinicflow']['status'] ?? '') === 'In Development', 'ClinicFlow status is honest');
check(($systems['peopleflow']['status'] ?? '') === 'Prototype', 'PeopleFlow status is honest');

$founder = founder_profile();
check($founder['name'] === 'Mthokozisi Hlomela Mnyobe', 'Approved founder name');
check($founder['role'] === 'Founder and Managing Director', 'Approved founder role');
check($founder['credentials'] === ['IT Graduate', 'CompTIA Network+ certified'], 'Only approved founder credentials');
check(is_file($root . '/assets/' . $founder['image']), 'Founder image exists');

$collaborators = collaborating_organisations();
check(count($collaborators) === 3, 'Three approved collaboration names');
check(array_column($collaborators, 'monogram') === ['MH', 'BB', 'HG'], 'Approved collaboration monograms');

$navigation = navigation_items();
$systemsNavigation = array_values(array_filter(
    $navigation,
    static fn (array $item): bool => $item['key'] === 'systems'
));
check(($systemsNavigation[0]['path'] ?? '') === 'index.php#systems-preview', 'Systems navigation uses non-broken Checkpoint 2 preview route');
$storeNavigation = array_values(array_filter(
    $navigation,
    static fn (array $item): bool => $item['key'] === 'store'
));
check(
    ($storeNavigation[0]['path'] ?? '') === 'contact.php?enquiry=technology-products#contact-form',
    'Store navigation uses non-broken Checkpoint 2 enquiry route'
);

$contactSource = (string) file_get_contents($root . '/contact.php');
foreach (['full_name', 'organisation', 'email', 'phone', 'enquiry_type', 'preferred_contact', 'project_summary', 'privacy_consent'] as $fieldName) {
    check(str_contains($contactSource, 'name="' . $fieldName . '"'), 'Contact field has name: ' . $fieldName);
}
check(str_contains($contactSource, 'disabled aria-describedby="form-availability-note"'), 'Interim contact submission is disabled');
check(str_contains($contactSource, 'Your details were not stored or sent'), 'Unexpected POST response is truthful');

restore_error_handler();
echo "Checkpoint 2 content tests passed ({$assertions} assertions)." . PHP_EOL;
