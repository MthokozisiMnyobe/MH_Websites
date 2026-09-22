<?php

declare(strict_types=1);

function catalogue_categories(): array
{
    return [
        'toner-ink' => ['label' => 'Toner & Ink', 'description' => 'Printer toner and ink products supplied by quotation.'],
        'drum-units' => ['label' => 'Drum Units', 'description' => 'Replacement imaging and drum units supplied by quotation.'],
        'printers' => ['label' => 'Printers', 'description' => 'Business and office printers supplied by quotation.'],
        'accessories' => ['label' => 'Accessories', 'description' => 'Practical computer and printer accessories supplied by quotation.'],
    ];
}

/**
 * Representative catalogue records only. Product selection, compatibility,
 * pricing and availability are confirmed manually when preparing a quotation.
 */
function catalogue_products(): array
{
    return [
        [
            'id' => 'business-multifunction-printer',
            'code' => 'CAT-PRN-001',
            'category' => 'printers',
            'brand' => 'Various manufacturers',
            'type' => 'Multifunction printer',
            'name' => 'Business Multifunction Printer',
            'summary' => 'A representative print, scan and copy solution for offices and organisational workspaces.',
            'description' => 'MH Websites can help identify a suitable multifunction printer based on your workload, connectivity and document-handling requirements.',
            'specifications' => [
                'Functions' => 'Print, scan and copy configurations',
                'Connectivity' => 'Wired and wireless options',
                'Format' => 'Business desktop configurations',
                'Selection' => 'Confirmed from your requirements',
            ],
            'keywords' => ['office', 'mfp', 'scanner', 'copier', 'wireless', 'laser'],
            'image' => 'images/product-printer.webp',
            'image_alt' => 'Representative business multifunction printer',
            'compatibility_required' => false,
        ],
        [
            'id' => 'office-laser-printer',
            'code' => 'CAT-PRN-002',
            'category' => 'printers',
            'brand' => 'Various manufacturers',
            'type' => 'Laser printer',
            'name' => 'Office Laser Printer',
            'summary' => 'A representative laser-printing option for dependable everyday document output.',
            'description' => 'Printer recommendations are prepared around the required functions, expected workload, connectivity and available workspace.',
            'specifications' => [
                'Print options' => 'Mono and colour configurations',
                'Connectivity' => 'USB, network and wireless options',
                'Duplex' => 'Options available where required',
                'Selection' => 'Confirmed from your requirements',
            ],
            'keywords' => ['office', 'document', 'mono', 'colour', 'duplex', 'network'],
            'image' => 'images/product-laser-printer.webp',
            'image_alt' => 'Representative unbranded office laser printer',
            'compatibility_required' => false,
        ],
        [
            'id' => 'black-toner-cartridge',
            'code' => 'CAT-TNR-001',
            'category' => 'toner-ink',
            'brand' => 'Various manufacturers',
            'type' => 'Toner cartridge',
            'name' => 'Black Toner Cartridge',
            'summary' => 'Replacement black toner selected against the exact printer model and cartridge code.',
            'description' => 'Share the printer manufacturer, exact model and current cartridge code so MH Websites can confirm the appropriate toner before quotation.',
            'specifications' => [
                'Colour' => 'Black',
                'Yield options' => 'Standard and high-yield options',
                'Compatibility' => 'Manually confirmed before quotation',
            ],
            'keywords' => ['black', 'laser', 'cartridge', 'replacement', 'high yield'],
            'image' => 'images/product-toner.webp',
            'image_alt' => 'Representative black toner cartridge',
            'compatibility_required' => true,
        ],
        [
            'id' => 'colour-toner-cartridge',
            'code' => 'CAT-TNR-002',
            'category' => 'toner-ink',
            'brand' => 'Various manufacturers',
            'type' => 'Toner cartridge',
            'name' => 'Colour Toner Cartridge',
            'summary' => 'Replacement colour toner matched manually to the printer and required colour.',
            'description' => 'Provide the exact printer model, cartridge code and required colour so compatibility can be checked before a quotation is issued.',
            'specifications' => [
                'Colour options' => 'Cyan, magenta and yellow',
                'Yield options' => 'Model-dependent options',
                'Compatibility' => 'Manually confirmed before quotation',
            ],
            'keywords' => ['cyan', 'magenta', 'yellow', 'laser', 'cartridge', 'replacement'],
            'image' => 'images/product-colour-toner.webp',
            'image_alt' => 'Representative unbranded colour toner cartridges',
            'compatibility_required' => true,
        ],
        [
            'id' => 'printer-ink-supply',
            'code' => 'CAT-INK-001',
            'category' => 'toner-ink',
            'brand' => 'Various manufacturers',
            'type' => 'Ink supply',
            'name' => 'Printer Ink Supply',
            'summary' => 'Cartridge or refill ink options identified from the printer model and current supply code.',
            'description' => 'Ink formats vary by printer. MH Websites will use your device details to identify an appropriate option before quotation.',
            'specifications' => [
                'Formats' => 'Cartridge and refill options',
                'Colours' => 'Model-dependent options',
                'Compatibility' => 'Manually confirmed before quotation',
            ],
            'keywords' => ['inkjet', 'refill', 'bottle', 'cartridge', 'colour', 'black'],
            'image' => 'images/product-ink-supply.webp',
            'image_alt' => 'Representative unbranded printer ink cartridge and refill bottles',
            'compatibility_required' => true,
        ],
        [
            'id' => 'imaging-drum-unit',
            'code' => 'CAT-DRM-001',
            'category' => 'drum-units',
            'brand' => 'Various manufacturers',
            'type' => 'Imaging drum',
            'name' => 'Imaging Drum Unit',
            'summary' => 'A replacement drum unit selected using the printer model and current component code.',
            'description' => 'Drum units are model-specific. Compatibility is checked manually before the final product is included in a quotation.',
            'specifications' => [
                'Component' => 'Imaging or drum unit',
                'Configuration' => 'Printer-model dependent',
                'Compatibility' => 'Manually confirmed before quotation',
            ],
            'keywords' => ['drum', 'imaging', 'laser', 'replacement', 'printer component'],
            'image' => 'images/product-drum.webp',
            'image_alt' => 'Representative printer imaging drum unit',
            'compatibility_required' => true,
        ],
        [
            'id' => 'usb-printer-cable',
            'code' => 'CAT-ACC-001',
            'category' => 'accessories',
            'brand' => 'Universal accessories',
            'type' => 'Printer cable',
            'name' => 'USB Printer Cable',
            'summary' => 'A practical wired connection option for compatible printers and computers.',
            'description' => 'Connection type and required cable length are confirmed from the devices and workspace before quotation.',
            'specifications' => [
                'Connection' => 'USB printer connection options',
                'Length' => 'Confirmed from workspace requirements',
                'Compatibility' => 'Connector type confirmed before quotation',
            ],
            'keywords' => ['usb', 'cable', 'wired', 'connection', 'peripheral'],
            'image' => 'images/product-usb-printer-cable.webp',
            'image_alt' => 'Representative unbranded USB printer cable',
            'compatibility_required' => true,
        ],
        [
            'id' => 'ergonomic-laptop-stand',
            'code' => 'CAT-ACC-002',
            'category' => 'accessories',
            'brand' => 'Universal accessories',
            'type' => 'Laptop stand',
            'name' => 'Ergonomic Laptop Stand',
            'summary' => 'A representative stand for raising a laptop and improving desk organisation.',
            'description' => 'Suitable stand options can be discussed according to laptop size, preferred working position and portability needs.',
            'specifications' => [
                'Use' => 'Desktop laptop positioning',
                'Options' => 'Fixed and adjustable configurations',
                'Selection' => 'Confirmed from device and workspace needs',
            ],
            'keywords' => ['laptop', 'stand', 'desk', 'ergonomic', 'accessory'],
            'image' => 'images/product-laptop-stand.webp',
            'image_alt' => 'Representative unbranded adjustable laptop stand',
            'compatibility_required' => false,
        ],
        [
            'id' => 'surge-protected-power-strip',
            'code' => 'CAT-ACC-003',
            'category' => 'accessories',
            'brand' => 'Universal accessories',
            'type' => 'Power protection',
            'name' => 'Surge-Protected Power Strip',
            'summary' => 'A representative power-distribution accessory for desktop technology equipment.',
            'description' => 'Connection count, cable length and equipment requirements are confirmed before an appropriate option is quoted.',
            'specifications' => [
                'Use' => 'Desktop equipment power distribution',
                'Configuration' => 'Outlet options confirmed by quotation',
                'Selection' => 'Confirmed from equipment requirements',
            ],
            'keywords' => ['power', 'surge', 'strip', 'protection', 'office', 'accessory'],
            'image' => 'images/product-surge-power-strip.webp',
            'image_alt' => 'Representative unbranded South African power strip',
            'compatibility_required' => false,
        ],
    ];
}

function catalogue_product(string $productId): ?array
{
    foreach (catalogue_products() as $product) {
        if (($product['id'] ?? null) === $productId) {
            return $product;
        }
    }

    return null;
}

function catalogue_brands(): array
{
    $brands = array_values(array_unique(array_column(catalogue_products(), 'brand')));
    natcasesort($brands);
    return array_values($brands);
}

function catalogue_product_types(): array
{
    $types = array_values(array_unique(array_column(catalogue_products(), 'type')));
    natcasesort($types);
    return array_values($types);
}

function catalogue_category_label(string $category): string
{
    return (string) (catalogue_categories()[$category]['label'] ?? 'Technology product');
}

function catalogue_search_text(array $product): string
{
    $specifications = is_array($product['specifications'] ?? null)
        ? implode(' ', array_keys($product['specifications'])) . ' ' . implode(' ', $product['specifications'])
        : '';
    $keywords = is_array($product['keywords'] ?? null) ? implode(' ', $product['keywords']) : '';

    return strtolower(implode(' ', [
        (string) ($product['name'] ?? ''),
        (string) ($product['code'] ?? ''),
        (string) ($product['brand'] ?? ''),
        catalogue_category_label((string) ($product['category'] ?? '')),
        (string) ($product['type'] ?? ''),
        (string) ($product['summary'] ?? ''),
        $specifications,
        $keywords,
    ]));
}

function catalogue_relevance_score(array $product, string $query): int
{
    $query = strtolower(trim($query));
    if ($query === '') {
        return 0;
    }

    $tokens = preg_split('/\s+/', $query, -1, PREG_SPLIT_NO_EMPTY) ?: [];
    $name = strtolower((string) ($product['name'] ?? ''));
    $code = strtolower((string) ($product['code'] ?? ''));
    $brand = strtolower((string) ($product['brand'] ?? ''));
    $category = strtolower(catalogue_category_label((string) ($product['category'] ?? '')));
    $type = strtolower((string) ($product['type'] ?? ''));
    $searchText = catalogue_search_text($product);
    $score = str_contains($name, $query) ? 80 : 0;

    foreach ($tokens as $token) {
        if (!str_contains($searchText, $token)) {
            return -1;
        }
        $score += str_contains($name, $token) ? 30 : 0;
        $score += str_contains($code, $token) ? 24 : 0;
        $score += str_contains($brand, $token) ? 18 : 0;
        $score += str_contains($category, $token) ? 14 : 0;
        $score += str_contains($type, $token) ? 12 : 0;
        $score += 4;
    }

    return $score;
}

function catalogue_filtered_products(array $filters): array
{
    $query = trim((string) ($filters['q'] ?? ''));
    $category = (string) ($filters['category'] ?? '');
    $brand = (string) ($filters['brand'] ?? '');
    $type = (string) ($filters['type'] ?? '');
    $sort = (string) ($filters['sort'] ?? ($query !== '' ? 'relevance' : 'name-asc'));
    $results = [];

    foreach (catalogue_products() as $product) {
        if ($category !== '' && ($product['category'] ?? '') !== $category) {
            continue;
        }
        if ($brand !== '' && ($product['brand'] ?? '') !== $brand) {
            continue;
        }
        if ($type !== '' && ($product['type'] ?? '') !== $type) {
            continue;
        }

        $score = catalogue_relevance_score($product, $query);
        if ($query !== '' && $score < 0) {
            continue;
        }
        $product['_score'] = $score;
        $results[] = $product;
    }

    usort($results, static function (array $left, array $right) use ($sort): int {
        return match ($sort) {
            'name-desc' => strcasecmp((string) $right['name'], (string) $left['name']),
            'relevance' => ($right['_score'] <=> $left['_score']) ?: strcasecmp((string) $left['name'], (string) $right['name']),
            default => strcasecmp((string) $left['name'], (string) $right['name']),
        };
    });

    return array_map(static function (array $product): array {
        unset($product['_score']);
        return $product;
    }, $results);
}

function catalogue_public_payload(): array
{
    return array_map(static function (array $product): array {
        return [
            'id' => $product['id'],
            'code' => $product['code'],
            'category' => $product['category'],
            'categoryLabel' => catalogue_category_label($product['category']),
            'brand' => $product['brand'],
            'type' => $product['type'],
            'name' => $product['name'],
            'summary' => $product['summary'],
            'specifications' => $product['specifications'],
            'keywords' => $product['keywords'],
            'image' => $product['image'],
            'imageUrl' => is_string($product['image']) && $product['image'] !== '' ? asset_url($product['image']) : null,
            'imageAlt' => $product['image_alt'],
            'compatibilityRequired' => $product['compatibility_required'],
        ];
    }, catalogue_products());
}
