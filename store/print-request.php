<?php

declare(strict_types=1);

require dirname(__DIR__) . '/config/bootstrap.php';

render_header([
    'active' => 'store',
    'body_class' => 'page-store page-print-request',
    'metadata' => [
        'title' => 'Printable Quotation Draft — MH Websites',
        'description' => 'Preview the products currently stored in this browser Quote Basket.',
        'canonical' => canonical_url('/store/print-request.php'),
        'robots' => 'noindex,nofollow',
        'stylesheets' => ['pages/store.css'],
    ],
]);
render_store_navigation();
render_store_breadcrumbs([['label' => 'Quote Basket', 'path' => 'store/quote-basket.php'], ['label' => 'Printable Draft']]);
?>
<main id="main-content">
    <section class="section print-request-section" aria-labelledby="print-request-title">
        <div class="container">
            <header class="print-request-header">
                <div>
                    <span class="eyebrow">Browser preview only</span>
                    <h1 id="print-request-title">Quotation Request Draft</h1>
                    <p>This is not an issued quotation and has not been submitted to MH Websites.</p>
                </div>
                <button class="button button--primary print-hidden" type="button" data-print-request>Print Draft</button>
            </header>
            <div class="alert alert--warning print-disclaimer">
                No quotation reference exists yet. Checkpoint 1E will provide authoritative references and printable submitted summaries.
            </div>
            <section aria-labelledby="print-items-title">
                <h2 id="print-items-title">Selected products</h2>
                <div class="print-request-items" data-print-items></div>
                <div class="quote-review-empty" data-print-empty hidden>
                    <p>Your Quote Basket is empty.</p>
                    <a class="text-link print-hidden" href="<?= e(url('store/index.php')) ?>">Browse products</a>
                </div>
            </section>
            <section class="print-compatibility" data-print-compatibility hidden aria-labelledby="print-compatibility-title">
                <h2 id="print-compatibility-title">Compatibility details</h2>
                <dl data-print-compatibility-summary></dl>
            </section>
        </div>
    </section>
</main>
<?php
render_catalogue_payload();
render_footer(['scripts' => ['js/store/print-request.js']]);
?>
