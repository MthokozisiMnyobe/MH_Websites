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
 * Checkpoint 4 will populate and validate quotation-catalogue records here.
 */
function catalogue_products(): array
{
    return [];
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
