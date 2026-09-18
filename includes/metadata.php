<?php

declare(strict_types=1);

function page_metadata(array $overrides = []): array
{
    $defaults = [
        'title' => 'MH Websites — Custom Software and Digital Systems',
        'description' => 'Custom software, websites and digital systems built around how your organisation works.',
        'canonical' => canonical_url(),
        'image' => canonical_url('/assets/images/hero-software-3d.webp'),
        'type' => 'website',
        'robots' => 'index,follow',
    ];

    return array_replace($defaults, array_filter(
        $overrides,
        static fn (mixed $value): bool => $value !== null && $value !== ''
    ));
}

function render_metadata(array $overrides = []): void
{
    $meta = page_metadata($overrides);
    ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($meta['title']) ?></title>
    <meta name="description" content="<?= e($meta['description']) ?>">
    <meta name="robots" content="<?= e($meta['robots']) ?>">
    <link rel="canonical" href="<?= e($meta['canonical']) ?>">
    <meta property="og:locale" content="en_ZA">
    <meta property="og:type" content="<?= e($meta['type']) ?>">
    <meta property="og:site_name" content="MH Websites">
    <meta property="og:title" content="<?= e($meta['title']) ?>">
    <meta property="og:description" content="<?= e($meta['description']) ?>">
    <meta property="og:url" content="<?= e($meta['canonical']) ?>">
    <meta property="og:image" content="<?= e($meta['image']) ?>">
    <meta name="twitter:card" content="summary_large_image">
    <link rel="stylesheet" href="<?= e(asset_url('css/tokens.css')) ?>">
    <link rel="stylesheet" href="<?= e(asset_url('css/base.css')) ?>">
    <link rel="stylesheet" href="<?= e(asset_url('css/components.css')) ?>">
    <link rel="stylesheet" href="<?= e(asset_url('css/utilities.css')) ?>">
    <?php
}
