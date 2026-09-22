<?php

declare(strict_types=1);

require dirname(__DIR__) . '/config/bootstrap.php';

render_header([
    'active' => 'quote-basket',
    'body_class' => 'page-store page-quote-basket',
    'metadata' => [
        'title' => 'Quote Basket — MH Websites Technology Catalogue',
        'description' => 'Review technology products and quantities before preparing a quotation request.',
        'canonical' => canonical_url('/store/quote-basket.php'),
        'robots' => 'noindex,follow',
        'stylesheets' => ['pages/store.css'],
    ],
]);
render_store_navigation('basket');
render_store_breadcrumbs([['label' => 'Quote Basket']]);
?>
<main id="main-content">
    <section class="section quote-basket-section" aria-labelledby="quote-basket-title" data-quote-basket-page>
        <div class="container">
            <div class="section-heading section-heading--split">
                <div>
                    <span class="eyebrow">Quotation preparation</span>
                    <h1 id="quote-basket-title">Your Quote Basket</h1>
                </div>
                <p>Review the products and quantities you would like MH Websites to consider. No prices or payments are processed here.</p>
            </div>

            <p class="store-action-status" role="status" aria-live="polite" data-basket-status></p>

            <div class="quote-basket-empty" data-basket-empty>
                <div class="quote-basket-empty__icon" aria-hidden="true">QB</div>
                <h2>Your Quote Basket is empty</h2>
                <p>Browse the catalogue and add products you would like included in a quotation.</p>
                <a class="button button--primary" href="<?= e(url('store/index.php')) ?>">Browse Products</a>
            </div>

            <div data-basket-content hidden>
                <div class="quote-basket-list" data-basket-lines aria-label="Quote Basket items"></div>
                <div class="quote-basket-summary">
                    <div>
                        <span class="eyebrow">Basket summary</span>
                        <h2>Quotation request overview</h2>
                        <dl class="quote-basket-summary__totals">
                            <div><dt>Products</dt><dd data-basket-product-count>0</dd></div>
                            <div><dt>Total units</dt><dd data-basket-total-quantity>0</dd></div>
                            <div><dt>Pricing</dt><dd>Supplied by quotation</dd></div>
                        </dl>
                        <p>Final product selection, compatibility and availability will be confirmed by MH Websites.</p>
                    </div>
                    <div class="quote-basket-summary__actions">
                        <button class="button button--secondary" type="button" data-clear-basket>Clear Basket</button>
                        <a class="button button--accent" href="<?= e(url('store/request-quote.php')) ?>">Request Quotation <span aria-hidden="true">&rarr;</span></a>
                    </div>
                </div>
                <a class="text-link quote-basket-continue" href="<?= e(url('store/index.php')) ?>"><span aria-hidden="true">&larr;</span> Continue browsing</a>
            </div>
            <noscript><p class="alert alert--warning">JavaScript is required for the temporary browser-based Quote Basket in this checkpoint.</p></noscript>
        </div>
    </section>
</main>
<?php
render_catalogue_payload();
render_footer(['scripts' => ['js/store/quote-basket.js']]);
?>
