<?php

declare(strict_types=1);

require dirname(__DIR__) . '/config/bootstrap.php';

$categories = catalogue_categories();
$brands = catalogue_brands();
$types = catalogue_product_types();
$query = mb_substr(trim((string) ($_GET['q'] ?? '')), 0, 100);
$category = (string) ($_GET['category'] ?? '');
$brand = mb_substr(trim((string) ($_GET['brand'] ?? '')), 0, 80);
$type = mb_substr(trim((string) ($_GET['type'] ?? '')), 0, 80);
$sort = (string) ($_GET['sort'] ?? ($query !== '' ? 'relevance' : 'name-asc'));

if (!array_key_exists($category, $categories)) {
    $category = '';
}
if (!in_array($brand, $brands, true)) {
    $brand = '';
}
if (!in_array($type, $types, true)) {
    $type = '';
}
if (!in_array($sort, ['relevance', 'name-asc', 'name-desc'], true)) {
    $sort = $query !== '' ? 'relevance' : 'name-asc';
}

$filters = compact('query', 'category', 'brand', 'type', 'sort');
$filters['q'] = $filters['query'];
unset($filters['query']);
$visibleProducts = catalogue_filtered_products($filters);
$visibleProductIds = array_column($visibleProducts, 'id');
$allProducts = catalogue_products();
$activeFilterCount = count(array_filter([$query, $category, $brand, $type], static fn (string $value): bool => $value !== ''));

render_header([
    'active' => 'store',
    'body_class' => 'page-store page-catalogue',
    'metadata' => [
        'title' => 'Technology Quotation Catalogue — MH Websites',
        'description' => 'Browse representative printers, toner, drum units and accessories, then request a tailored quotation from MH Websites.',
        'canonical' => canonical_url('/store/'),
        'stylesheets' => ['pages/store.css'],
    ],
]);
render_store_navigation('catalogue');
?>
<main id="main-content">
    <section class="catalogue-hero" aria-labelledby="catalogue-title">
        <div class="container catalogue-hero__inner">
            <div>
                <span class="eyebrow">Technology quotation catalogue</span>
                <h1 id="catalogue-title">Find the right technology for your organisation.</h1>
                <p>Browse representative product options, compare practical requirements and prepare a Quote Basket for MH Websites to review.</p>
            </div>
            <aside class="quote-notice" aria-label="Quotation information">
                <strong>Pricing is supplied by quotation.</strong>
                <p>Product selection, compatibility, price and availability are confirmed before a final quotation is issued.</p>
                <a class="text-link text-link--light" href="<?= e(url('store/compatibility-help.php')) ?>">Need compatibility help? <span aria-hidden="true">&rarr;</span></a>
            </aside>
        </div>
    </section>

    <section class="section catalogue-section" aria-labelledby="product-results-title" data-catalogue-app>
        <div class="container container--wide">
            <form class="catalogue-controls" action="<?= e(url('store/index.php')) ?>" method="get" data-catalogue-form>
                <div class="catalogue-search">
                    <label for="catalogue-search">Search the catalogue</label>
                    <div class="catalogue-search__field">
                        <input
                            id="catalogue-search"
                            name="q"
                            type="search"
                            value="<?= e($query) ?>"
                            placeholder="Search products, codes or specifications"
                            autocomplete="off"
                            role="combobox"
                            aria-autocomplete="list"
                            aria-controls="catalogue-suggestions"
                            aria-expanded="false"
                            data-catalogue-search
                        >
                        <button class="button button--primary" type="submit">Search</button>
                    </div>
                    <ul class="search-suggestions" id="catalogue-suggestions" role="listbox" data-search-suggestions hidden></ul>
                </div>

                <button class="button button--secondary filter-toggle" type="button" aria-expanded="false" aria-controls="catalogue-filters" data-filter-toggle hidden>
                    Filters<?= $activeFilterCount > 0 ? ' (' . e((string) $activeFilterCount) . ')' : '' ?>
                </button>

                <aside class="catalogue-filters" id="catalogue-filters" aria-label="Catalogue filters" data-filter-panel>
                    <div class="catalogue-filters__header">
                        <h2>Filter products</h2>
                        <button type="button" class="filter-close" data-filter-close aria-label="Close filters">&times;</button>
                    </div>
                    <div class="field">
                        <label for="filter-category">Category</label>
                        <select id="filter-category" name="category" data-filter="category">
                            <option value="">All categories</option>
                            <?php foreach ($categories as $slug => $item): ?>
                                <option value="<?= e($slug) ?>"<?= $category === $slug ? ' selected' : '' ?>><?= e($item['label']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="field">
                        <label for="filter-brand">Brand range</label>
                        <select id="filter-brand" name="brand" data-filter="brand">
                            <option value="">All brand ranges</option>
                            <?php foreach ($brands as $brandOption): ?>
                                <option value="<?= e($brandOption) ?>"<?= $brand === $brandOption ? ' selected' : '' ?>><?= e($brandOption) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="field">
                        <label for="filter-type">Product type</label>
                        <select id="filter-type" name="type" data-filter="type">
                            <option value="">All product types</option>
                            <?php foreach ($types as $typeOption): ?>
                                <option value="<?= e($typeOption) ?>"<?= $type === $typeOption ? ' selected' : '' ?>><?= e($typeOption) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="field">
                        <label for="filter-sort">Sort</label>
                        <select id="filter-sort" name="sort" data-filter="sort">
                            <option value="relevance"<?= $sort === 'relevance' ? ' selected' : '' ?>>Most relevant</option>
                            <option value="name-asc"<?= $sort === 'name-asc' ? ' selected' : '' ?>>Name: A–Z</option>
                            <option value="name-desc"<?= $sort === 'name-desc' ? ' selected' : '' ?>>Name: Z–A</option>
                        </select>
                    </div>
                    <div class="catalogue-filters__actions">
                        <button class="button button--primary" type="submit">Apply Filters</button>
                        <a class="text-link" href="<?= e(url('store/index.php')) ?>" data-reset-filters>Reset Filters</a>
                    </div>
                </aside>
                <button class="filter-backdrop" type="button" aria-label="Close filters" data-filter-backdrop hidden></button>
            </form>

            <nav class="category-chips" aria-label="Product categories">
                <a href="<?= e(url('store/index.php')) ?>" data-category-chip=""<?= $category === '' ? ' aria-current="page"' : '' ?>>All products</a>
                <?php foreach ($categories as $slug => $item): ?>
                    <a href="<?= e(url('store/index.php?category=' . rawurlencode($slug))) ?>" data-category-chip="<?= e($slug) ?>"<?= $category === $slug ? ' aria-current="page"' : '' ?>><?= e($item['label']) ?></a>
                <?php endforeach; ?>
            </nav>

            <div class="catalogue-results-heading">
                <div>
                    <span class="eyebrow">Product results</span>
                    <h2 id="product-results-title">Browse representative products</h2>
                </div>
                <p class="catalogue-results-count" role="status" aria-live="polite" data-results-status><?= e((string) count($visibleProducts)) ?> products shown</p>
            </div>
            <p class="sr-only" role="status" aria-live="polite" data-store-status></p>

            <div class="catalogue-grid" data-product-grid>
                <?php foreach ($allProducts as $product): ?>
                    <?php render_product_card($product, !in_array($product['id'], $visibleProductIds, true)); ?>
                <?php endforeach; ?>
            </div>

            <div class="catalogue-empty" data-no-results<?= $visibleProducts !== [] ? ' hidden' : '' ?>>
                <h3>No matching products</h3>
                <p>Try a broader search, remove a filter or ask MH Websites to help identify what you need.</p>
                <a class="button button--secondary" href="<?= e(url('store/index.php')) ?>">Reset Filters</a>
            </div>
            <noscript><p class="alert">Search and filters work when submitted. JavaScript adds live suggestions and the temporary Quote Basket.</p></noscript>
        </div>
    </section>
</main>
<?php
render_catalogue_payload();
render_footer(['scripts' => ['js/store/catalogue.js']]);
?>
