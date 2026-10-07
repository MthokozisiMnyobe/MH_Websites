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
check($founder['image'] === 'images/director-mthokozisi.png', 'Approved professional founder portrait selected');
check($founder['image_width'] === 1122 && $founder['image_height'] === 1402, 'Founder portrait dimensions are accurate');
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
check(str_contains($homeSource, 'Collaboration ready'), 'Homepage uses truthful collaboration-ready heading');
check(str_contains($homeSource, 'Representative identities are shown for demonstration.'), 'Demo identities are truthfully framed');
foreach ([
    'ApexTech Glossy 3D Logo.png',
    'SkyNex Cloud Solutions Logo.png',
    'EduNova_ Shaping Brighter Futures.png',
    'GlobalLink Neon Network Logo.png',
] as $logoFile) {
    check(substr_count($homeSource, $logoFile) === 1, 'Unique collaboration asset configured once: ' . $logoFile);
    check(is_file($root . '/assets/images/Collaboration/' . $logoFile), 'Collaboration asset exists: ' . $logoFile);
}
check(substr_count($homeSource, "'image' => 'images/Collaboration/") === 4, 'Homepage configures exactly four collaboration images');
check(substr_count($homeSource, 'class="collaboration-moving-group"') === 1, 'Homepage has one collaboration moving group');
check(!str_contains($homeSource, 'collaboration-marquee__group'), 'Duplicate marquee groups are removed');
check(!str_contains($homeSource, '$repeat'), 'Collaboration identities are not repeated in the DOM');
check(substr_count($homeSource, 'class="collaboration-logo-image"') === 1, 'Collaboration loop assigns the dedicated image class');

$aboutSource = (string) file_get_contents($root . '/about.php');
check(is_file($root . '/assets/images/Letterhead.png'), 'Approved letterhead asset exists');
check(!str_contains($aboutSource, 'Letterhead.png'), 'Letterhead is absent from About company-story content');
check(!str_contains($aboutSource, 'about-letterhead-image'), 'Obsolete About letterhead markup is removed');
check(!str_contains($aboutSource, 'Organisations We Collaborate With'), 'Old About collaboration section is removed');
check(!str_contains($aboutSource, '$collaborators'), 'About page no longer loads collaborator data');
check(!str_contains($aboutSource, 'Basic Blue Trading 773 CC'), 'Basic Blue Trading card is absent from About');
check(!str_contains($aboutSource, 'Halisi Group (Pty) Ltd'), 'Halisi Group card is absent from About');

$headerSource = (string) file_get_contents($root . '/includes/header.php');
$footerSource = (string) file_get_contents($root . '/includes/footer.php');
check(substr_count($headerSource, "asset_url('images/mh-websites-logo-transparent.png')") === 1, 'Shared header uses transparent MH logo once');
check(substr_count($footerSource, "asset_url('images/mh-websites-logo-transparent.png')") === 1, 'Shared footer uses transparent MH logo once');
check(is_file($root . '/assets/images/mh-websites-logo-transparent.png'), 'Transparent MH logo asset exists');
check(!str_contains($headerSource, "asset_url('images/Letterhead.png')"), 'Header no longer uses opaque Letterhead asset');
check(!str_contains($footerSource, "asset_url('images/Letterhead.png')"), 'Footer no longer uses opaque Letterhead asset');
check(str_contains($headerSource, 'class="site-brand-logo"'), 'Header Letterhead has its dedicated image class');
check(str_contains($headerSource, 'width="44"') && str_contains($headerSource, 'height="44"'), 'Header markup has a safe 44px fallback size');
check(str_contains($footerSource, 'class="footer-brand-logo"'), 'Footer Letterhead has its dedicated image class');
check(str_contains($footerSource, 'width="72"') && str_contains($footerSource, 'height="72"'), 'Footer markup has a safe 72px fallback size');
check(!str_contains($headerSource, 'brand__mark'), 'Header placeholder mark is removed');
check(!str_contains($footerSource, 'brand__mark'), 'Footer placeholder mark is removed');

$corporateCss = (string) file_get_contents($root . '/assets/css/pages/corporate.css');
$componentsCss = (string) file_get_contents($root . '/assets/css/components.css');
check(!str_contains($corporateCss, '.service-summary:first-child'), 'First service card is not oversized');
check(str_contains($corporateCss, 'grid-template-columns: repeat(5, minmax(0, 1fr))'), 'Five-card desktop service layout defined');
check(str_contains($corporateCss, 'animation: collaboration-move-right 22s linear infinite'), 'Four-logo group uses the requested traversal speed');
check(str_contains($corporateCss, 'transform: translateX(calc(-100% - var(--space-4)))'), 'Moving group starts beyond the left edge');
check(str_contains($corporateCss, 'transform: translateX(var(--space-4))'), 'Moving group ends beyond the right edge');
check(str_contains($corporateCss, 'left: 100%'), 'Moving group traverses the full viewport width');
check(str_contains($corporateCss, '@media (prefers-reduced-motion: reduce)'), 'Reduced-motion styling remains available');
$marqueeCssStart = strpos($corporateCss, '.collaboration-marquee {');
$marqueeCssEnd = strpos($corporateCss, '/* Approved software-first homepage direction */');
$marqueeCss = substr($corporateCss, $marqueeCssStart, $marqueeCssEnd - $marqueeCssStart);
check(substr_count($marqueeCss, 'flex-direction: row') === 1, 'Single moving group explicitly uses a horizontal flex row');
check(substr_count($marqueeCss, 'flex-wrap: nowrap') === 1, 'Single moving group explicitly prevents wrapping');
check(str_contains($marqueeCss, 'width: max-content'), 'Moving group uses max-content width');
check(str_contains($marqueeCss, 'flex: 0 0 auto'), 'Logo slots prevent shrinking');
check(!str_contains($marqueeCss, 'display: grid'), 'No marquee media query changes the layout to a grid');
check(!str_contains($marqueeCss, 'flex-direction: column'), 'No marquee rule changes the layout to a column');
check(!str_contains($marqueeCss, 'translateX(-50%)'), 'Duplicate-track translation is removed');
check(!str_contains($marqueeCss, 'animation-direction'), 'Animation has no reverse or alternate direction');
check(str_contains($marqueeCss, 'width: 136px'), 'Collaboration images are directly constrained to 136px on desktop');
check(str_contains($marqueeCss, 'max-width: 136px'), 'Collaboration images have a direct 136px desktop maximum');
check(str_contains($marqueeCss, 'width: 116px'), 'Collaboration images are directly constrained to 116px on tablet');
check(str_contains($marqueeCss, 'width: 92px'), 'Collaboration images are directly constrained to 92px on mobile');
check(str_contains($marqueeCss, 'flex-wrap: wrap'), 'Reduced-motion layout wraps all four identities responsively');
check(!str_contains($corporateCss, '.about-letterhead-image'), 'Obsolete About letterhead CSS is removed');
check(str_contains($componentsCss, '.site-brand-logo'), 'Header brand image has dedicated sizing');
check(str_contains($componentsCss, 'width: 44px') && str_contains($componentsCss, 'max-width: 44px'), 'Header brand image is directly constrained to 44px on desktop');
check(str_contains($componentsCss, 'width: 40px') && str_contains($componentsCss, 'max-width: 40px'), 'Header brand image is directly constrained to 40px on tablet');
check(str_contains($componentsCss, 'width: 36px') && str_contains($componentsCss, 'max-width: 36px'), 'Header brand image is directly constrained to 36px on mobile');
check(str_contains($componentsCss, '.footer-brand-logo'), 'Footer brand image has dedicated sizing');
check(str_contains($componentsCss, 'width: 72px') && str_contains($componentsCss, 'max-width: 72px'), 'Footer brand image is directly constrained to 72px from tablet upward');
check(str_contains($componentsCss, 'width: 60px') && str_contains($componentsCss, 'max-width: 60px'), 'Footer brand image is directly constrained to 60px on mobile');
check(!str_contains($corporateCss, '.collaboration-card'), 'Obsolete collaboration-card CSS is removed');
check(!str_contains($corporateCss, '.collaboration-grid'), 'Obsolete collaboration-grid CSS is removed');

restore_error_handler();
echo "Checkpoint 2 content tests passed ({$assertions} assertions)." . PHP_EOL;
