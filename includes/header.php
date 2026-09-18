<?php

declare(strict_types=1);

function render_header(array $options = []): void
{
    $active = (string) ($options['active'] ?? '');
    $bodyClass = trim((string) ($options['body_class'] ?? ''));
    $metadata = is_array($options['metadata'] ?? null) ? $options['metadata'] : [];
    ?>
    <!doctype html>
    <html lang="en-ZA">
    <head>
        <?php render_metadata($metadata); ?>
    </head>
    <body<?= $bodyClass !== '' ? ' class="' . e($bodyClass) . '"' : '' ?>>
        <a class="skip-link" href="#main-content">Skip to main content</a>
        <header class="site-header" data-site-header>
            <div class="site-header__bar container">
                <a class="brand" href="<?= e(url('index.php')) ?>" aria-label="MH Websites home">
                    <span class="brand__mark" aria-hidden="true">MH</span>
                    <span class="brand__text">MH Websites<small>Software &middot; Web &middot; Technology</small></span>
                </a>

                <button
                    class="site-nav__toggle"
                    type="button"
                    data-nav-toggle
                    aria-expanded="false"
                    aria-controls="primary-navigation"
                    hidden
                >
                    <span class="site-nav__toggle-icon" aria-hidden="true"></span>
                    <span data-nav-label>Menu</span>
                </button>

                <nav class="site-nav__panel" id="primary-navigation" aria-label="Primary navigation" data-nav-panel>
                    <ul class="site-nav__list">
                        <?php foreach (navigation_items() as $item): ?>
                            <li>
                                <a
                                    href="<?= e(url($item['path'])) ?>"
                                    <?= $active === $item['key'] ? 'aria-current="page"' : '' ?>
                                ><?= e($item['label']) ?></a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <a class="button button--accent site-nav__cta" href="<?= e(url('contact.php')) ?>">
                        Discuss Your Project <span aria-hidden="true">&rarr;</span>
                    </a>
                </nav>
            </div>
            <button class="site-nav__backdrop" type="button" data-nav-backdrop hidden tabindex="-1" aria-label="Close menu"></button>
        </header>
    <?php
}
