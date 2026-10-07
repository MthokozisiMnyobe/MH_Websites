<?php

declare(strict_types=1);

function render_footer(array $options = []): void
{
    $scripts = is_array($options['scripts'] ?? null) ? $options['scripts'] : [];
    $scripts = array_values(array_unique(['js/store/store-shell.js', ...$scripts]));
    ?>
        <footer class="site-footer">
            <div class="container site-footer__grid">
                <div class="site-footer__about">
                    <a class="brand" href="<?= e(url('index.php')) ?>" aria-label="MH Websites home">
                        <img
                            class="footer-brand-logo"
                            src="<?= e(asset_url('images/mh-websites-logo-transparent.png')) ?>"
                            alt="MH Websites"
                            width="72"
                            height="72"
                            loading="lazy"
                            decoding="async"
                        >
                        <span class="brand__text">MH Websites<small>Software &middot; Web &middot; Technology</small></span>
                    </a>
                    <p>Custom software, websites and digital systems built around how your organisation works.</p>
                </div>
                <nav aria-label="Footer navigation">
                    <h2 class="site-footer__heading">Explore</h2>
                    <ul class="site-footer__links">
                        <?php foreach (footer_navigation_items() as $item): ?>
                            <li><a href="<?= e(url($item['path'])) ?>"><?= e($item['label']) ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </nav>
                <nav aria-label="Legal and information">
                    <h2 class="site-footer__heading">Legal &amp; Information</h2>
                    <ul class="site-footer__links">
                        <?php foreach (legal_navigation_items() as $item): ?>
                            <li><a href="<?= e(url($item['path'])) ?>"><?= e($item['label']) ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </nav>
                <div>
                    <h2 class="site-footer__heading">Start a conversation</h2>
                    <p>Tell us what your organisation needs to improve, simplify or build.</p>
                    <a class="text-link text-link--light" href="<?= e(url('contact.php')) ?>">Discuss your project <span aria-hidden="true">&rarr;</span></a>
                </div>
            </div>
            <div class="container site-footer__bottom">
                <span>&copy; <?= e(date('Y')) ?> MH WEBSITES (Pty) Ltd.</span>
                <span>East London, South Africa</span>
            </div>
        </footer>
        <script type="module" src="<?= e(asset_url('js/navigation.js')) ?>"></script>
        <?php foreach ($scripts as $script): ?>
            <script type="module" src="<?= e(asset_url((string) $script)) ?>"></script>
        <?php endforeach; ?>
    </body>
    </html>
    <?php
}
