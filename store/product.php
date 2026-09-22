<?php

declare(strict_types=1);

require dirname(__DIR__) . '/config/bootstrap.php';

$productId = mb_substr(trim((string) ($_GET['id'] ?? '')), 0, 100);
$product = catalogue_product($productId);

if ($product === null) {
    http_response_code(404);
}

render_header([
    'active' => 'store',
    'body_class' => 'page-store page-product',
    'metadata' => [
        'title' => $product === null ? 'Product Not Found — MH Websites' : $product['name'] . ' — Technology Catalogue',
        'description' => $product === null ? 'The requested catalogue product could not be found.' : $product['summary'],
        'canonical' => canonical_url('/store/product.php' . ($product === null ? '' : '?id=' . rawurlencode($product['id']))),
        'robots' => $product === null ? 'noindex,nofollow' : 'index,follow',
        'stylesheets' => ['pages/store.css'],
    ],
]);
render_store_navigation('catalogue');
?>
<main id="main-content">
    <?php if ($product === null): ?>
        <?php render_store_breadcrumbs([['label' => 'Product not found']]); ?>
        <section class="section">
            <div class="container content-column empty-page-state">
                <span class="eyebrow">Catalogue</span>
                <h1>We could not find that product.</h1>
                <p>The product link may be incomplete or the catalogue may have changed.</p>
                <a class="button button--primary" href="<?= e(url('store/index.php')) ?>">Browse the Catalogue</a>
            </div>
        </section>
    <?php else: ?>
        <?php render_store_breadcrumbs([['label' => (string) $product['name']]]); ?>
        <section class="section product-detail" aria-labelledby="product-title">
            <div class="container product-detail__layout">
                <?php render_product_media($product, 'product-detail__media'); ?>
                <div class="product-detail__content">
                    <div class="product-detail__meta">
                        <span class="status-badge"><?= e(catalogue_category_label((string) $product['category'])) ?></span>
                        <span><?= e((string) $product['code']) ?></span>
                    </div>
                    <h1 id="product-title"><?= e((string) $product['name']) ?></h1>
                    <p class="product-detail__brand"><?= e((string) $product['brand']) ?> · <?= e((string) $product['type']) ?></p>
                    <p class="product-detail__lead"><?= e((string) $product['description']) ?></p>

                    <section aria-labelledby="specifications-title">
                        <h2 id="specifications-title">Key information</h2>
                        <dl class="specification-list">
                            <?php foreach ($product['specifications'] as $label => $value): ?>
                                <div><dt><?= e((string) $label) ?></dt><dd><?= e((string) $value) ?></dd></div>
                            <?php endforeach; ?>
                        </dl>
                    </section>

                    <?php if ((bool) $product['compatibility_required']): ?>
                        <div class="compatibility-panel">
                            <h2>Confirm compatibility first</h2>
                            <p>This product type may be device-specific. MH Websites will manually confirm suitability before issuing a final quotation.</p>
                            <a class="text-link" href="<?= e(url('store/compatibility-help.php?product=' . rawurlencode((string) $product['id']))) ?>">Request Compatibility Help <span aria-hidden="true">&rarr;</span></a>
                        </div>
                    <?php endif; ?>

                    <aside class="product-quote-panel" aria-label="Quotation action">
                        <div>
                            <strong>Price and availability confirmed on quotation</strong>
                            <p>No payment is taken through this catalogue.</p>
                        </div>
                        <button class="button button--accent" type="button" data-add-to-quote="<?= e((string) $product['id']) ?>">Add to Quote Basket</button>
                    </aside>
                    <p class="store-action-status" role="status" aria-live="polite" data-store-status></p>
                </div>
            </div>
        </section>
    <?php endif; ?>
</main>
<?php
if ($product !== null) {
    render_catalogue_payload();
}
render_footer(['scripts' => [$product === null ? 'js/store/store-shell.js' : 'js/store/product.js']]);
?>
