<?php

declare(strict_types=1);

require dirname(__DIR__) . '/config/bootstrap.php';

render_header([
    'active' => 'store',
    'body_class' => 'page-store page-quote-confirmation',
    'metadata' => [
        'title' => 'Quotation Confirmation — MH Websites',
        'description' => 'Secure quotation confirmation will be activated with the server-side workflow.',
        'canonical' => canonical_url('/store/quote-confirmation.php'),
        'robots' => 'noindex,nofollow',
        'stylesheets' => ['pages/store.css'],
    ],
]);
render_store_navigation();
render_store_breadcrumbs([['label' => 'Quotation Confirmation']]);
?>
<main id="main-content">
    <section class="section">
        <div class="container content-column confirmation-placeholder">
            <span class="confirmation-placeholder__icon" aria-hidden="true">i</span>
            <span class="eyebrow">Checkpoint 1D preview</span>
            <h1>No quotation request has been submitted.</h1>
            <p>This route is prepared for the secure Checkpoint 1E workflow. It does not generate quotation references or claim database or email delivery.</p>
            <div class="alert alert--warning">
                Authoritative confirmation will appear here only after server validation and successful quotation persistence.
            </div>
            <div class="form-actions">
                <a class="button button--primary" href="<?= e(url('store/request-quote.php')) ?>">Return to Request Quotation</a>
                <a class="button button--secondary" href="<?= e(url('store/index.php')) ?>">Browse Products</a>
            </div>
        </div>
    </section>
</main>
<?php render_footer(['scripts' => ['js/store/store-shell.js']]); ?>
