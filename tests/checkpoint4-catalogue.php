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
$_SERVER['REQUEST_URI'] = '/MH_Websites/store/index.php';
$_SERVER['REQUEST_METHOD'] = 'GET';

require dirname(__DIR__) . '/config/bootstrap.php';

$assertions = 0;
$root = dirname(__DIR__);

function catalogue_check(bool $condition, string $label): void
{
    global $assertions;
    $assertions++;
    if (!$condition) {
        throw new RuntimeException($label . ' failed.');
    }
}

$categories = catalogue_categories();
catalogue_check(array_keys($categories) === ['toner-ink', 'drum-units', 'printers', 'accessories'], 'Only approved categories are present');

$products = catalogue_products();
catalogue_check(count($products) === 9, 'Representative catalogue contains nine records');
catalogue_check(count(array_unique(array_column($products, 'id'))) === count($products), 'Product IDs are unique');
catalogue_check(count(array_unique(array_column($products, 'code'))) === count($products), 'Product codes are unique');

$requiredKeys = ['id', 'code', 'category', 'brand', 'type', 'name', 'summary', 'description', 'specifications', 'keywords', 'image', 'image_alt', 'compatibility_required'];
$expectedImages = [
    'business-multifunction-printer' => 'images/product-printer.webp',
    'office-laser-printer' => 'images/product-laser-printer.webp',
    'black-toner-cartridge' => 'images/product-toner.webp',
    'colour-toner-cartridge' => 'images/product-colour-toner.webp',
    'printer-ink-supply' => 'images/product-ink-supply.webp',
    'imaging-drum-unit' => 'images/product-drum.webp',
    'usb-printer-cable' => 'images/product-usb-printer-cable.webp',
    'ergonomic-laptop-stand' => 'images/product-laptop-stand.webp',
    'surge-protected-power-strip' => 'images/product-surge-power-strip.webp',
];
foreach ($products as $product) {
    catalogue_check(array_diff($requiredKeys, array_keys($product)) === [], 'Product has required fields: ' . ($product['id'] ?? 'unknown'));
    catalogue_check(isset($categories[$product['category']]), 'Product category is approved: ' . $product['id']);
    catalogue_check(!array_key_exists('price', $product), 'Product has no public price: ' . $product['id']);
    catalogue_check(!array_key_exists('stock', $product), 'Product has no stock claim: ' . $product['id']);
    catalogue_check(!array_key_exists('discount', $product), 'Product has no discount: ' . $product['id']);
    catalogue_check(($product['image'] ?? null) === ($expectedImages[$product['id']] ?? null), 'Product uses its approved representative image: ' . $product['id']);
    catalogue_check(is_file($root . '/assets/' . $product['image']), 'Product image exists: ' . $product['id']);
    catalogue_check(str_starts_with((string) $product['image_alt'], 'Representative '), 'Product image is labelled as representative: ' . $product['id']);
}

catalogue_check(catalogue_product('imaging-drum-unit')['name'] === 'Imaging Drum Unit', 'Product lookup works');
catalogue_check(catalogue_product('invalid-product') === null, 'Invalid product lookup fails safely');
catalogue_check(catalogue_relevance_score(catalogue_product('black-toner-cartridge'), 'black toner') > 0, 'Token search scores relevant product');
catalogue_check(catalogue_relevance_score(catalogue_product('usb-printer-cable'), 'black toner') === -1, 'Token search excludes unrelated product');

$tonerResults = catalogue_filtered_products(['q' => 'toner', 'category' => '', 'brand' => '', 'type' => '', 'sort' => 'relevance']);
catalogue_check(count($tonerResults) === 3, 'Toner search returns the matching Toner & Ink category products');
catalogue_check($tonerResults[0]['id'] === 'black-toner-cartridge' || $tonerResults[0]['id'] === 'colour-toner-cartridge', 'Relevance search ranks toner products');
$accessoryResults = catalogue_filtered_products(['q' => '', 'category' => 'accessories', 'brand' => '', 'type' => '', 'sort' => 'name-asc']);
catalogue_check(count($accessoryResults) === 3, 'Category filter returns accessories');
$brandResults = catalogue_filtered_products(['q' => '', 'category' => '', 'brand' => 'Universal accessories', 'type' => '', 'sort' => 'name-asc']);
catalogue_check(count($brandResults) === 3, 'Brand filter uses actual catalogue field');

$publicJson = json_encode(catalogue_public_payload(), JSON_THROW_ON_ERROR);
catalogue_check(!str_contains($publicJson, '"price"'), 'Public catalogue payload has no price field');
catalogue_check(!str_contains($publicJson, '"stock"'), 'Public catalogue payload has no stock field');

$routes = [
    'store/index.php',
    'store/product.php',
    'store/compatibility-help.php',
    'store/quote-basket.php',
    'store/request-quote.php',
    'store/quote-confirmation.php',
    'store/print-request.php',
];
foreach ($routes as $route) {
    $source = (string) file_get_contents($root . '/' . $route);
    catalogue_check(str_contains($source, "require dirname(__DIR__) . '/config/bootstrap.php'"), $route . ' uses shared bootstrap');
    catalogue_check(str_contains($source, 'render_header(['), $route . ' uses shared header');
    catalogue_check(str_contains($source, '<main id="main-content">'), $route . ' exposes main landmark');
    catalogue_check(str_contains($source, 'render_footer('), $route . ' uses shared footer');
    catalogue_check(!preg_match('/Add to Cart|Shopping Cart|\bCheckout\b|R\s?0(?:\.00)?/i', $source), $route . ' avoids ecommerce terminology');
}

$catalogueSource = (string) file_get_contents($root . '/store/index.php');
catalogue_check(str_contains($catalogueSource, 'role="combobox"'), 'Search uses combobox semantics');
catalogue_check(str_contains($catalogueSource, 'role="listbox"'), 'Suggestions use listbox semantics');
catalogue_check(str_contains($catalogueSource, 'data-reset-filters'), 'Reset filters control exists');
catalogue_check(str_contains($catalogueSource, 'aria-live="polite"'), 'Catalogue has live announcements');

$basketSource = (string) file_get_contents($root . '/store/quote-basket.php');
catalogue_check(str_contains($basketSource, 'Quote Basket'), 'Quote Basket terminology is used');
catalogue_check(str_contains($basketSource, 'data-clear-basket'), 'Clear basket control exists');
catalogue_check(str_contains($basketSource, 'data-basket-product-count'), 'Basket summary exposes unique product count');
catalogue_check(str_contains($basketSource, 'data-basket-total-quantity'), 'Basket summary exposes total unit count');
catalogue_check(str_contains($basketSource, 'Supplied by quotation'), 'Basket summary states quotation pricing');
catalogue_check(str_contains($basketSource, 'store/request-quote.php'), 'Basket summary provides Request Quotation action');

$headerSource = (string) file_get_contents($root . '/includes/header.php');
catalogue_check(str_contains($headerSource, 'data-basket-link'), 'Shared primary navigation exposes Quote Basket link');
catalogue_check(str_contains($headerSource, 'data-basket-count'), 'Shared primary navigation exposes basket count');
catalogue_check(str_contains($headerSource, 'aria-label="Quote Basket, 0 items"'), 'Shared basket link has an accessible fallback label');

$footerSource = (string) file_get_contents($root . '/includes/footer.php');
catalogue_check(str_contains($footerSource, "'js/store/store-shell.js'"), 'Basket count enhancement loads on corporate and store pages');

$storeShellSource = (string) file_get_contents($root . '/assets/js/store/store-shell.js');
catalogue_check(str_contains($storeShellSource, "querySelectorAll('[data-basket-link]')"), 'Basket count updates shared basket-link labels');
catalogue_check(!str_contains($storeShellSource, 'aria-live'), 'Navigation basket count is not a disruptive live region');

$basketClientSource = (string) file_get_contents($root . '/assets/js/store/quote-basket.js');
catalogue_check(str_contains($basketClientSource, "querySelector('[data-basket-product-count]')"), 'Basket client updates unique product count');
catalogue_check(str_contains($basketClientSource, "querySelector('[data-basket-total-quantity]')"), 'Basket client updates total unit count');

$requestSource = (string) file_get_contents($root . '/store/request-quote.php');
catalogue_check(str_contains($requestSource, 'disabled aria-describedby="quote-submission-note"'), 'Quotation submission remains disabled');
catalogue_check(str_contains($requestSource, 'was not stored or sent'), 'Unexpected POST handling is truthful');

$confirmationSource = (string) file_get_contents($root . '/store/quote-confirmation.php');
catalogue_check(str_contains($confirmationSource, 'No quotation request has been submitted'), 'Confirmation route makes no success claim');
catalogue_check(!str_contains($confirmationSource, 'MHQ-'), 'Confirmation route fabricates no quotation reference');

foreach (['systems.php', 'systems/eduflow.php', 'systems/clinicflow.php', 'systems/peopleflow.php', 'systems/learnhub.php', 'systems/ai-assistant.php', 'systems/insighthub.php'] as $skippedRoute) {
    catalogue_check(!is_file($root . '/' . $skippedRoute), 'Skipped Checkpoint 3 route remains absent: ' . $skippedRoute);
}

restore_error_handler();
echo "Checkpoint 4 catalogue tests passed ({$assertions} assertions)." . PHP_EOL;
