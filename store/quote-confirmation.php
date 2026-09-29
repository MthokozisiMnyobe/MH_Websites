<?php
declare(strict_types=1);
require dirname(__DIR__) . '/config/bootstrap.php';
send_application_security_headers(true);
$quotation = null;
$pdo = database_connection();
if ($pdo instanceof PDO) {
    try { $quotation = load_authorised_quotation($pdo); }
    catch (Throwable $exception) { safe_log('Quotation confirmation unavailable.', ['failure_category' => $exception::class]); }
}
render_header(['active' => 'store', 'body_class' => 'page-store page-quote-confirmation', 'metadata' => ['title' => 'Quotation Request Confirmation â€” MH Websites', 'description' => 'Quotation request confirmation.', 'canonical' => canonical_url('/store/quote-confirmation.php'), 'robots' => 'noindex,nofollow', 'stylesheets' => ['pages/store.css']]]);
render_store_navigation();
render_store_breadcrumbs([['label' => 'Quotation Confirmation']]);
?>
<main id="main-content"><section class="section"><div class="container content-column confirmation-placeholder">
<?php if ($quotation === null): ?>
<span class="confirmation-placeholder__icon" aria-hidden="true">i</span><h1>Quotation request unavailable</h1><p>This confirmation is unavailable or has expired. No customer information is exposed from this page.</p><a class="button button--primary" href="<?= e(url('store/request-quote.php')) ?>">Return to Request Quotation</a>
<?php else: ?>
<span class="eyebrow">Request received</span><h1>Your quotation request has been submitted.</h1><p>Reference: <strong><?= e($quotation['quotation_reference']) ?></strong></p><p>MH Websites will review the requested products and contact you using your selected contact method. This acknowledgement is not a priced quotation.</p><h2>Submitted products</h2><ul><?php foreach ($quotation['items'] as $item): ?><li><?= e($item['product_name']) ?> &ndash; quantity <?= e($item['quantity']) ?></li><?php endforeach; ?></ul><div class="form-actions"><a class="button button--primary" href="<?= e(url('store/print-request.php')) ?>">Print submitted request</a><a class="button button--secondary" href="<?= e(url('store/index.php')) ?>">Return to Catalogue</a></div>
<?php endif; ?>
</div></section></main>
<?php render_footer(['scripts' => ['js/store/store-shell.js']]); ?>
