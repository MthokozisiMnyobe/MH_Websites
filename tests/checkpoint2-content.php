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
check(array_column($navigation, 'key') === ['home', 'services', 'store', 'about', 'contact', 'quote-basket'], 'Header navigation includes catalogue and Quote Basket routes');
$footerNavigation = footer_navigation_items();
check(in_array('store', array_column($footerNavigation, 'key'), true), 'Technology catalogue remains available in footer navigation');
check(in_array('quote-basket', array_column($footerNavigation, 'key'), true), 'Quote Basket remains available in footer navigation');
check(!in_array('systems', array_column($footerNavigation, 'key'), true), 'Skipped Systems portfolio is not linked in footer navigation');

$contactSource = (string) file_get_contents($root . '/contact.php');
foreach (['full_name', 'organisation', 'email', 'phone', 'enquiry_type', 'preferred_contact', 'project_summary', 'privacy_consent'] as $fieldName) {
    check(str_contains($contactSource, 'name="' . $fieldName . '"'), 'Contact field has name: ' . $fieldName);
}
check(str_contains($contactSource, 'csrf_field()'), 'Contact submission includes CSRF protection');
check(str_contains($contactSource, 'idempotency_token'), 'Contact submission includes idempotency protection');
check(str_contains($contactSource, 'create_enquiry('), 'Contact submission uses the secure enquiry service');

$homeSource = (string) file_get_contents($root . '/index.php');
check(!str_contains($homeSource, 'Digital solutions · East London, South Africa'), 'Homepage hero location eyebrow removed');
check(!str_contains($homeSource, 'solution-map'), 'Generic category visual removed');
check(str_contains($homeSource, 'hero-environment'), 'Photographic software environment is present');
check(str_contains($homeSource, 'hero-software-environment.webp'), 'Approved photographic hero asset is referenced');
check(is_file($root . '/assets/images/hero-software-environment.webp'), 'Approved photographic hero asset exists');
check(str_contains($homeSource, 'Illustrative MH Websites software dashboard'), 'Concept visual is truthfully labelled');
check(str_contains($homeSource, 'Software. Websites. Technology.'), 'Approved hero eyebrow present');
check(str_contains($homeSource, 'Digital systems'), 'Approved hero headline begins correctly');
check(str_contains($homeSource, 'organisation <em>forward.</em>'), 'Approved hero emphasis is present');
check(str_contains($homeSource, 'Expert solutions for a digital world'), 'Approved services heading is present');
check(str_contains($homeSource, 'founder-principles'), 'Compact founder principles are present');
check(!str_contains($homeSource, 'id="systems-preview"'), 'Homepage systems portfolio section removed');
check(!str_contains($homeSource, '$systems = systems_portfolio()'), 'Homepage no longer loads a fixed systems collection');
check(str_contains($homeSource, 'Software leads. Technology support completes the picture.'), 'Approved software-first positioning remains with services');
check(!str_contains($homeSource, '<ul class="credential-list">'), 'Homepage qualification pills removed');
check(
    str_contains($homeSource, 'Mthokozisi leads MH Websites with a focus on building practical digital systems'),
    'Homepage founder description added'
);
check(!str_contains($homeSource, 'id="home-cta-title"'), 'Large repeated homepage CTA removed');

$corporateCss = (string) file_get_contents($root . '/assets/css/pages/corporate.css');
check(!str_contains($corporateCss, '.service-summary:first-child'), 'First service card is not oversized');
check(str_contains($corporateCss, 'grid-template-columns: repeat(5, minmax(0, 1fr))'), 'Five-card desktop service layout defined');

restore_error_handler();
echo "Checkpoint 2 content tests passed ({$assertions} assertions)." . PHP_EOL;
