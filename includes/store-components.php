<?php

declare(strict_types=1);

function render_store_navigation(string $active = ''): void
{
    $items = [
        ['key' => 'catalogue', 'label' => 'Browse Products', 'path' => 'store/index.php'],
        ['key' => 'compatibility', 'label' => 'Compatibility Help', 'path' => 'store/compatibility-help.php'],
        ['key' => 'basket', 'label' => 'Quote Basket', 'path' => 'store/quote-basket.php'],
    ];
    ?>
    <nav class="store-navigation" aria-label="Technology catalogue navigation">
        <div class="container store-navigation__inner">
            <ul>
                <?php foreach ($items as $item): ?>
                    <li>
                        <a
                            href="<?= e(url($item['path'])) ?>"
                            <?= $active === $item['key'] ? 'aria-current="page"' : '' ?>
                            <?= $item['key'] === 'basket' ? 'data-basket-link aria-label="Quote Basket, 0 items"' : '' ?>
                        >
                            <?= e($item['label']) ?>
                            <?php if ($item['key'] === 'basket'): ?>
                                <span class="basket-count" data-basket-count aria-hidden="true">0</span>
                            <?php endif; ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </nav>
    <?php
}

function render_store_breadcrumbs(array $items): void
{
    ?>
    <nav class="breadcrumbs container" aria-label="Breadcrumb">
        <ol>
            <li><a href="<?= e(url('index.php')) ?>">Home</a></li>
            <li><a href="<?= e(url('store/index.php')) ?>">Technology Catalogue</a></li>
            <?php foreach ($items as $index => $item): ?>
                <li<?= $index === array_key_last($items) ? ' aria-current="page"' : '' ?>>
                    <?php if (isset($item['path']) && $index !== array_key_last($items)): ?>
                        <a href="<?= e(url((string) $item['path'])) ?>"><?= e((string) $item['label']) ?></a>
                    <?php else: ?>
                        <?= e((string) $item['label']) ?>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ol>
    </nav>
    <?php
}

function render_product_media(array $product, string $className = ''): void
{
    $image = $product['image'] ?? null;
    $classes = trim('product-media ' . $className);
    ?>
    <div class="<?= e($classes) ?>">
        <?php if (is_string($image) && $image !== ''): ?>
            <img
                src="<?= e(asset_url($image)) ?>"
                alt="<?= e((string) ($product['image_alt'] ?? '')) ?>"
                width="1200"
                height="1200"
                loading="lazy"
            >
            <span class="product-media__note">Representative product image</span>
        <?php else: ?>
            <div class="product-media__fallback" aria-hidden="true">
                <span><?= e(strtoupper(substr((string) ($product['type'] ?? 'Product'), 0, 2))) ?></span>
            </div>
            <span class="product-media__note">Product selection confirmed on quotation</span>
        <?php endif; ?>
    </div>
    <?php
}

function render_product_card(array $product, bool $hidden = false): void
{
    $categoryLabel = catalogue_category_label((string) $product['category']);
    ?>
    <article
        class="catalogue-card"
        data-product-card
        data-product-id="<?= e((string) $product['id']) ?>"
        data-product-name="<?= e((string) $product['name']) ?>"
        data-product-code="<?= e((string) $product['code']) ?>"
        data-product-category="<?= e((string) $product['category']) ?>"
        data-product-category-label="<?= e($categoryLabel) ?>"
        data-product-brand="<?= e((string) $product['brand']) ?>"
        data-product-type="<?= e((string) $product['type']) ?>"
        data-product-search="<?= e(catalogue_search_text($product)) ?>"
        <?= $hidden ? 'hidden' : '' ?>
    >
        <?php render_product_media($product, 'catalogue-card__media'); ?>
        <div class="catalogue-card__body">
            <div class="catalogue-card__meta">
                <span><?= e($categoryLabel) ?></span>
                <span><?= e((string) $product['code']) ?></span>
            </div>
            <h3><a href="<?= e(url('store/product.php?id=' . rawurlencode((string) $product['id']))) ?>"><?= e((string) $product['name']) ?></a></h3>
            <p class="catalogue-card__brand"><?= e((string) $product['brand']) ?></p>
            <p><?= e((string) $product['summary']) ?></p>
            <?php if ((bool) $product['compatibility_required']): ?>
                <span class="compatibility-label">Compatibility check recommended</span>
            <?php endif; ?>
        </div>
        <div class="catalogue-card__actions">
            <a class="button button--secondary" href="<?= e(url('store/product.php?id=' . rawurlencode((string) $product['id']))) ?>">View Details</a>
            <button class="button button--primary" type="button" data-add-to-quote="<?= e((string) $product['id']) ?>">Add to Quote</button>
        </div>
    </article>
    <?php
}

function render_catalogue_payload(): void
{
    $json = json_encode(
        catalogue_public_payload(),
        JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR
    );
    ?>
    <script type="application/json" id="catalogue-data"><?= $json ?></script>
    <?php
}
