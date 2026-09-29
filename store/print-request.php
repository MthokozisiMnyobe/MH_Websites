<?php
declare(strict_types=1);
require dirname(__DIR__) . '/config/bootstrap.php';
send_application_security_headers(true);
$quotation = null;
$pdo = database_connection();
if ($pdo instanceof PDO) {
    try { $quotation = load_authorised_quotation($pdo); }
    catch (Throwable $exception) { safe_log('Printable quotation request unavailable.', ['failure_category' => $exception::class]); }
}
render_header(['active' => 'store', 'body_class' => 'page-store page-print-request', 'metadata' => ['title' => 'Submitted Quotation Request Summary â€” MH Websites', 'description' => 'Printable submitted quotation request summary.', 'canonical' => canonical_url('/store/print-request.php'), 'robots' => 'noindex,nofollow,noarchive', 'stylesheets' => ['pages/store.css']]]);
render_store_navigation(); render_store_breadcrumbs([['label' => 'Submitted Request Summary']]);
?>
<main id="main-content"><section class="section print-request-section"><div class="container">
<?php if ($quotation === null): ?><h1>Submitted request unavailable</h1><p>This summary is unavailable or has expired.</p>
<?php else: ?><header class="print-request-header"><div><span class="eyebrow">Submitted request</span><h1>SUBMITTED QUOTATION REQUEST SUMMARY</h1><p>This is not an issued or priced quotation.</p></div><button class="button button--primary print-hidden" type="button" onclick="window.print()">Print Summary</button></header><dl><div><dt>Reference</dt><dd><?= e($quotation['quotation_reference']) ?></dd></div><div><dt>Submitted</dt><dd><?= e((new DateTimeImmutable($quotation['created_at']))->format('d F Y, H:i')) ?></dd></div></dl><h2>Submitted products</h2><table><thead><tr><th scope="col">Product</th><th scope="col">Code</th><th scope="col">Quantity</th></tr></thead><tbody><?php foreach ($quotation['items'] as $item): ?><tr><td><?= e($item['product_name']) ?></td><td><?= e($item['product_code']) ?></td><td><?= e($item['quantity']) ?></td></tr><?php endforeach; ?></tbody></table><?php if (is_array($quotation['compatibility'])): ?><section><h2>Compatibility details</h2><dl><?php foreach ($quotation['compatibility'] as $key => $value): if ($value === '') continue; ?><div><dt><?= e(ucwords(str_replace('_', ' ', $key))) ?></dt><dd><?= e($key === 'product_id' ? (catalogue_product((string) $value)['name'] ?? $value) : $value) ?></dd></div><?php endforeach; ?></dl></section><?php endif; ?>
<?php endif; ?></div></section></main>
<?php render_footer(['scripts' => ['js/store/store-shell.js']]); ?>
