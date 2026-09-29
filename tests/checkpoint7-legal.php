<?php

declare(strict_types=1);

error_reporting(E_ALL);
ini_set('display_errors', '1');

$assertions = 0;

function legal_check(bool $condition, string $label): void
{
    global $assertions;
    $assertions++;
    if (!$condition) {
        throw new RuntimeException($label . ' failed.');
    }
}

$root = dirname(__DIR__);
$pages = [
    'privacy' => 'legal/privacy.php',
    'website-terms' => 'legal/website-terms.php',
    'quotation-terms' => 'legal/quotation-terms.php',
    'delivery' => 'legal/delivery-information.php',
];
$sources = [];

foreach ($pages as $key => $route) {
    $path = $root . '/' . $route;
    legal_check(is_file($path), $route . ' exists');
    $source = (string) file_get_contents($path);
    $sources[$key] = $source;
    legal_check(str_contains($source, "require dirname(__DIR__) . '/config/bootstrap.php'"), $route . ' uses shared bootstrap');
    legal_check(str_contains($source, 'render_header(['), $route . ' uses shared header');
    legal_check(str_contains($source, '<main id="main-content">'), $route . ' has main landmark');
    legal_check(str_contains($source, '<h1>'), $route . ' has one visible page heading');
    legal_check(str_contains($source, 'render_footer();'), $route . ' uses shared footer');
    legal_check(str_contains($source, "'stylesheets' => ['pages/corporate.css']"), $route . ' uses corporate design system');
}

$combined = implode("\n", $sources);
legal_check(str_contains($combined, 'MH WEBSITES (Pty) Ltd') || str_contains($combined, "company_legal_information()"), 'Approved company identity is sourced');
legal_check(str_contains($sources['privacy'], 'registration_number'), 'Privacy page displays registration number');
legal_check(str_contains($sources['website-terms'], 'CIPC-registered company'), 'Approved CIPC registration claim appears');
legal_check(str_contains($sources['privacy'], '$company[\'email\']'), 'Privacy contact is displayed from approved company data');
legal_check(str_contains($sources['privacy'], 'up to 12 months'), 'Twelve-month submission retention target stated');
legal_check(str_contains($sources['privacy'], 'approximately 90 days'), 'Routine log and backup targets stated');
legal_check(str_contains($sources['privacy'], 'maximum of 30 days'), 'Rate-limit retention target stated');
legal_check(str_contains($sources['privacy'], 'not all target periods above are automatically enforced'), 'Retention automation limitation is explicit');
legal_check(str_contains($sources['privacy'], 'Afrihost'), 'Intended hosting provider disclosed');
legal_check(str_contains($sources['privacy'], 'Google/Gmail SMTP'), 'Owner notification provider disclosed');
legal_check(str_contains($sources['privacy'], 'mh_quote_basket_v1') === false, 'Implementation storage key is not exposed in legal copy');
legal_check(str_contains($sources['privacy'], 'browser local storage'), 'Browser local storage use is explained');
legal_check(str_contains($sources['privacy'], 'browser session storage'), 'Browser session storage use is explained');
legal_check(str_contains($sources['privacy'], 'No advertising analytics'), 'Absence of advertising trackers is accurately stated');

legal_check(str_contains($sources['quotation-terms'], '<strong>21 days</strong>'), 'Issued quotation validity is 21 days');
legal_check(stripos($sources['quotation-terms'], 'request for quotation') !== false, 'Request for quotation is defined');
legal_check(stripos($sources['quotation-terms'], 'issued quotation') !== false, 'Issued quotation is distinguished');
legal_check(stripos($sources['quotation-terms'], 'confirmed order or project') !== false, 'Confirmed order is distinguished');
legal_check(str_contains($sources['quotation-terms'], 'does not create a binding order'), 'Website submission is not an order');
legal_check(str_contains($sources['quotation-terms'], '50% upfront and 50% before final handover'), 'Approved project payment structure stated');
legal_check(str_contains($sources['quotation-terms'], 'EFT or bank transfer'), 'Approved payment method stated');
legal_check(str_contains($sources['quotation-terms'], 'does not publish a VAT registration number'), 'No VAT number is invented');
legal_check(!preg_match('/VAT number\s*[:#]\s*[A-Z0-9]/i', $combined), 'No VAT registration number value appears');
legal_check(!preg_match('/\bR\s*[0-9]+(?:[.,][0-9]{2})?\b/', $combined), 'No public product price value is introduced');
legal_check(!str_contains($combined, 'stockQty'), 'No public stock quantity field is introduced');
legal_check(!str_contains($combined, 'checkout.html'), 'No checkout route is introduced');
legal_check(!preg_match('/<input[^>]+(?:card|payment)/i', $combined), 'No payment input is introduced');
legal_check(str_contains($sources['quotation-terms'], 'Product images may be representative'), 'Representative-image disclaimer exists');
legal_check(str_contains($sources['quotation-terms'], 'No public stock quantity or supply guarantee'), 'No stock guarantee is made');
legal_check(str_contains($sources['quotation-terms'], 'Nothing in these terms attempts to waive mandatory consumer rights'), 'Mandatory consumer rights preserved');
legal_check(str_contains($sources['quotation-terms'], 'does not automatically make every deposit refundable or non-refundable'), 'Deposit cancellation wording is balanced');
legal_check(str_contains($sources['quotation-terms'], 'supplier or manufacturer warranty'), 'Warranty context is conservative');
legal_check(str_contains($sources['delivery'], 'South Africa nationwide'), 'Nationwide service area stated');
legal_check(str_contains($sources['delivery'], 'courier or delivery'), 'Approved delivery methods stated');
legal_check(str_contains($sources['delivery'], 'quoted separately where applicable'), 'Delivery charge wording stated');
legal_check(str_contains($sources['delivery'], 'does not promise free delivery'), 'Free delivery is explicitly not promised');
legal_check(!preg_match('/collection|collect from/i', $sources['delivery']), 'Unapproved collection option is absent');

require $root . '/config/bootstrap.php';
$legalNavigation = legal_navigation_items();
legal_check(count($legalNavigation) === 4, 'Exactly four legal navigation items');
legal_check(array_column($legalNavigation, 'label') === ['Privacy & POPIA', 'Website Terms', 'Quotation Terms', 'Delivery Information'], 'Approved legal link labels');
legal_check(array_column($legalNavigation, 'path') === array_values($pages), 'Legal routes are correct');
$company = company_legal_information();
legal_check($company['legal_name'] === 'MH WEBSITES (Pty) Ltd', 'Approved legal name');
legal_check($company['registration_number'] === '2024 / 407724 / 07', 'Approved registration number');
legal_check($company['phone_display'] === '067 202 1923', 'Approved public phone');
legal_check($company['email'] === 'mhweb36@gmail.com', 'Approved public and privacy email');
legal_check($company['address_lines'] === ['4 Lancaster Palace', 'Vincent', 'East London', 'Eastern Cape', 'South Africa'], 'Approved address without invented postal code');

$footer = (string) file_get_contents($root . '/includes/footer.php');
legal_check(str_contains($footer, 'legal_navigation_items()'), 'Shared footer renders legal navigation');
$contact = (string) file_get_contents($root . '/contact.php');
$quotation = (string) file_get_contents($root . '/store/request-quote.php');
legal_check(str_contains($contact, "url('legal/privacy.php')"), 'Enquiry consent links privacy notice');
legal_check(str_contains($quotation, "url('legal/privacy.php')"), 'Quotation consent links privacy notice');
legal_check(str_contains($contact, 'validate_csrf_or_fail'), 'Enquiry CSRF behavior retained');
legal_check(str_contains($quotation, 'validate_csrf_or_fail'), 'Quotation CSRF behavior retained');
legal_check(str_contains($contact, 'consume_idempotency_token'), 'Enquiry idempotency retained');
legal_check(str_contains($quotation, 'consume_idempotency_token'), 'Quotation idempotency retained');
legal_check(str_contains($contact, 'create_enquiry'), 'Enquiry persistence retained');
legal_check(str_contains($quotation, 'create_quotation_request'), 'Quotation persistence retained');

echo "Checkpoint 7 legal tests passed ({$assertions} assertions)." . PHP_EOL;
