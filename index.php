<?php

declare(strict_types=1);

require __DIR__ . '/config/bootstrap.php';

$systems = systems_portfolio();
$services = service_categories();
$founder = founder_profile();
$collaborators = collaborating_organisations();

render_header([
    'active' => 'home',
    'body_class' => 'page-home',
    'metadata' => [
        'title' => 'MH Websites — Custom Software and Digital Solutions',
        'description' => 'MH Websites develops custom software, websites, learning platforms and practical digital systems for South African organisations.',
        'canonical' => canonical_url('/'),
        'stylesheets' => ['pages/corporate.css'],
    ],
]);
?>
<main id="main-content">
    <section class="corporate-hero" aria-labelledby="home-hero-title">
        <div class="container corporate-hero__inner">
            <div class="corporate-hero__copy">
                <span class="eyebrow">Digital solutions · East London, South Africa</span>
                <h1 id="home-hero-title">Digital systems built around real operational problems.</h1>
                <p class="corporate-hero__lead">
                    MH Websites designs custom software, web platforms and practical technology solutions that help organisations work with greater clarity and control.
                </p>
                <div class="corporate-hero__actions">
                    <a class="button button--accent" href="#systems-preview">Explore Our Systems</a>
                    <a class="button button--light-outline" href="<?= e(url('contact.php')) ?>">Discuss Your Project</a>
                </div>
            </div>
            <div class="solution-map" role="group" aria-label="MH Websites solution areas">
                <div class="solution-map__core">MH</div>
                <div class="solution-map__items">
                    <div class="solution-map__item">Custom systems</div>
                    <div class="solution-map__item">Web platforms</div>
                    <div class="solution-map__item">Learning technology</div>
                    <div class="solution-map__item">Data &amp; automation</div>
                </div>
            </div>
        </div>
    </section>

    <section class="section" id="systems-preview" aria-labelledby="systems-preview-title">
        <div class="container">
            <div class="section-heading section-heading--split">
                <div>
                    <span class="eyebrow">Systems portfolio</span>
                    <h2 id="systems-preview-title">Focused products for important work.</h2>
                </div>
                <p>Our portfolio is growing responsibly. Each product is shown with its current, honest development status.</p>
            </div>
            <div class="systems-grid">
                <?php foreach ($systems as $slug => $system): ?>
                    <?php
                    $statusClass = match ($system['status']) {
                        'Pilot' => 'status-badge--success',
                        'Prototype' => 'status-badge--warning',
                        default => '',
                    };
                    ?>
                    <article class="card card--interactive system-card" id="<?= e($slug) ?>">
                        <div class="system-card__top">
                            <span class="system-card__mark" aria-hidden="true"><?= e(strtoupper(substr($system['name'], 0, 2))) ?></span>
                            <span class="status-badge <?= e($statusClass) ?>"><?= e($system['status']) ?></span>
                        </div>
                        <div class="system-card__body">
                            <h3><?= e($system['name']) ?></h3>
                            <p><?= e($system['description']) ?></p>
                            <a class="text-link" href="<?= e(url('contact.php?enquiry=custom-software&system=' . rawurlencode($slug) . '#contact-form')) ?>">
                                Request a similar solution <span aria-hidden="true">→</span>
                            </a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="section why-section" aria-labelledby="services-preview-title">
        <div class="container">
            <div class="section-heading section-heading--split">
                <div>
                    <span class="eyebrow">What we provide</span>
                    <h2 id="services-preview-title">Software leads. Technology support completes the picture.</h2>
                </div>
                <p>Choose a focused service or bring us an operational challenge. We start by understanding what needs to work better.</p>
            </div>
            <div class="services-grid">
                <?php foreach ($services as $slug => $service): ?>
                    <article class="card service-summary">
                        <div class="service-summary__top">
                            <span class="service-summary__number"><?= e($service['number']) ?></span>
                        </div>
                        <div>
                            <h3><?= e($service['title']) ?></h3>
                            <p><?= e($service['summary']) ?></p>
                        </div>
                        <a class="text-link" href="<?= e(url('services.php#' . $slug)) ?>">Explore this service <span aria-hidden="true">→</span></a>
                    </article>
                <?php endforeach; ?>
            </div>
            <div class="product-division margin-top-8">
                <div>
                    <span class="eyebrow">Technology products</span>
                    <h3>Printers, toner, drum units and accessories by quotation.</h3>
                    <p class="margin-0">Our technology-products division supports organisations that need practical product guidance alongside technical services. Price and availability are confirmed on quotation.</p>
                </div>
                <a class="button button--secondary" href="<?= e(url('contact.php?enquiry=technology-products#contact-form')) ?>">Ask About Products</a>
            </div>
        </div>
    </section>

    <section class="section" aria-labelledby="why-title">
        <div class="container">
            <div class="section-heading">
                <span class="eyebrow">Why MH Websites</span>
                <h2 id="why-title">Technology should fit the organisation—not the other way around.</h2>
            </div>
            <div class="principles-grid">
                <article class="principle"><h3>Workflow-led thinking</h3><p>We begin with the people, decisions and steps involved in the work.</p></article>
                <article class="principle"><h3>Modular foundations</h3><p>Solutions are structured so useful capabilities can grow over time.</p></article>
                <article class="principle"><h3>Responsive interfaces</h3><p>Experiences are planned for the devices people actually use.</p></article>
                <article class="principle"><h3>Security-conscious delivery</h3><p>Data handling, access and safe defaults are considered from the start.</p></article>
                <article class="principle"><h3>Local support</h3><p>Clients can discuss technical needs with a South African business.</p></article>
                <article class="principle"><h3>Clear communication</h3><p>We explain the work in practical language and set honest expectations.</p></article>
            </div>
        </div>
    </section>

    <section class="section why-section" aria-labelledby="process-title">
        <div class="container">
            <div class="section-heading section-heading--split">
                <div><span class="eyebrow">How we work</span><h2 id="process-title">A clear path from problem to working solution.</h2></div>
                <p>Each engagement is shaped to the project, but the underlying journey remains straightforward.</p>
            </div>
            <ol class="process-list">
                <li><span class="process-list__number">01</span><div><h3>Discover</h3><p>Understand the challenge, users and priorities.</p></div></li>
                <li><span class="process-list__number">02</span><div><h3>Design</h3><p>Plan the experience, structure and delivery approach.</p></div></li>
                <li><span class="process-list__number">03</span><div><h3>Develop</h3><p>Build the agreed solution in focused stages.</p></div></li>
                <li><span class="process-list__number">04</span><div><h3>Test</h3><p>Check usability, behavior and readiness with care.</p></div></li>
                <li><span class="process-list__number">05</span><div><h3>Launch &amp; Support</h3><p>Release responsibly and support continued improvement.</p></div></li>
            </ol>
        </div>
    </section>

    <section class="section" aria-labelledby="founder-preview-title">
        <div class="container">
            <article class="founder-panel">
                <div class="founder-panel__image">
                    <img
                        src="<?= e(asset_url($founder['image'])) ?>"
                        alt="Mthokozisi Hlomela Mnyobe, Founder and Managing Director of MH Websites"
                        width="<?= e($founder['image_width']) ?>"
                        height="<?= e($founder['image_height']) ?>"
                        loading="lazy"
                    >
                </div>
                <div class="founder-panel__content">
                    <span class="eyebrow">Meet the Founder</span>
                    <h2 id="founder-preview-title"><?= e($founder['name']) ?></h2>
                    <p><strong><?= e($founder['role']) ?></strong></p>
                    <ul class="credential-list">
                        <?php foreach ($founder['credentials'] as $credential): ?>
                            <li><?= e($credential) ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <a class="button button--accent" href="<?= e(url('contact.php')) ?>">Discuss Your Project</a>
                </div>
            </article>
        </div>
    </section>

    <section class="section why-section" aria-labelledby="collaborations-title">
        <div class="container">
            <div class="section-heading">
                <span class="eyebrow">Collaboration context</span>
                <h2 id="collaborations-title">Organisations We Collaborate With</h2>
            </div>
            <div class="collaboration-grid">
                <?php foreach ($collaborators as $organisation): ?>
                    <article class="collaboration-card">
                        <span class="collaboration-card__mark" aria-hidden="true"><?= e($organisation['monogram']) ?></span>
                        <h3><?= e($organisation['name']) ?></h3>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="final-cta" aria-labelledby="home-cta-title">
        <div class="container final-cta__inner">
            <div>
                <span class="eyebrow">Start with the problem</span>
                <h2 id="home-cta-title">Let’s shape a practical digital solution for your organisation.</h2>
            </div>
            <div class="final-cta__actions">
                <a class="button button--accent" href="<?= e(url('contact.php')) ?>">Discuss Your Project</a>
                <a class="button button--light-outline" href="#systems-preview">Explore Our Systems</a>
            </div>
        </div>
    </section>
</main>
<?php render_footer(); ?>
