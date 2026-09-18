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
        <div class="container container--wide corporate-hero__inner">
            <div class="corporate-hero__copy">
                <span class="corporate-hero__eyebrow">Software. Websites. Technology.</span>
                <h1 id="home-hero-title">
                    <span>Digital systems</span>
                    <span>that move your</span>
                    <span>organisation <em>forward.</em></span>
                </h1>
                <p class="corporate-hero__lead">
                    We design and develop practical digital systems, web platforms and technology solutions that help schools, healthcare environments and businesses work smarter.
                </p>
                <div class="corporate-hero__actions">
                    <a class="button button--accent" href="#systems-preview">Explore Our Systems <span aria-hidden="true">&rarr;</span></a>
                    <a class="button button--light-outline" href="<?= e(url('contact.php')) ?>">Discuss Your Project</a>
                </div>
            </div>

            <figure class="software-product-visual" aria-labelledby="software-concept-caption">
                <div class="software-device" aria-hidden="true">
                    <div class="software-device__topbar">
                        <span></span><span></span><span></span>
                        <strong>MH Websites</strong>
                    </div>
                    <div class="software-interface">
                        <aside class="software-sidebar">
                            <div class="software-sidebar__identity">
                                <span class="software-sidebar__brand">MH</span>
                                <strong>Systems</strong>
                            </div>
                            <ul>
                                <li class="is-active"><i></i><span>Dashboard</span></li>
                                <li><i></i><span>Students</span></li>
                                <li><i></i><span>Attendance</span></li>
                                <li><i></i><span>Finance</span></li>
                                <li><i></i><span>Reports</span></li>
                                <li><i></i><span>Messages</span></li>
                                <li><i></i><span>Settings</span></li>
                            </ul>
                        </aside>
                        <div class="software-workspace">
                            <div class="software-workspace__header">
                                <div><small>Operations workspace</small><strong>Dashboard overview</strong></div>
                                <span>Concept interface</span>
                            </div>
                            <div class="software-kpis">
                                <div><span>Records</span><b>Structured</b><i></i></div>
                                <div><span>Workflow</span><b>Connected</b><i></i></div>
                                <div><span>Reporting</span><b>Accessible</b><i></i></div>
                            </div>
                            <div class="software-dashboard">
                                <div class="software-chart-panel">
                                    <div class="software-panel-heading"><span>Reporting view</span><i></i></div>
                                    <svg viewBox="0 0 320 120" focusable="false">
                                        <path class="chart-grid" d="M0 95H320M0 60H320M0 25H320"></path>
                                        <path class="chart-area" d="M0 100 C38 91 50 70 85 75 S135 94 166 57 S224 34 252 45 S292 35 320 16 L320 120 L0 120 Z"></path>
                                        <path class="chart-line" d="M0 100 C38 91 50 70 85 75 S135 94 166 57 S224 34 252 45 S292 35 320 16"></path>
                                    </svg>
                                    <div class="software-chart-labels"><span>Plan</span><span>Build</span><span>Review</span></div>
                                </div>
                                <div class="software-workflow-panel">
                                    <div class="software-panel-heading"><span>Recent workflow</span><i></i></div>
                                    <div class="software-workflow-step"><b></b><span>Capture</span><i></i></div>
                                    <div class="software-workflow-step"><b></b><span>Review</span><i></i></div>
                                    <div class="software-workflow-step"><b></b><span>Complete</span><i></i></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="software-laptop-base" aria-hidden="true"></div>
                <figcaption id="software-concept-caption">Illustrative software interface concept</figcaption>
            </figure>
        </div>
    </section>

    <section class="section services-section" aria-labelledby="services-preview-title">
        <div class="container container--wide">
            <div class="section-heading services-section__heading">
                <span class="eyebrow">Our Services</span>
                <h2 id="services-preview-title">Expert solutions for a digital world</h2>
                <p>From custom systems to ongoing support, we provide end-to-end technology solutions for your organisation.</p>
            </div>

            <svg class="service-icon-sprite" aria-hidden="true">
                <symbol id="service-icon-custom-software" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="16" rx="2"></rect><path d="m9 9-3 3 3 3m6-6 3 3-3 3"></path></symbol>
                <symbol id="service-icon-web-development" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="16" rx="2"></rect><path d="M3 9h18M7 6.5h.01M10 6.5h.01"></path></symbol>
                <symbol id="service-icon-learning-platforms" viewBox="0 0 24 24"><path d="m3 7 9-4 9 4-9 4-9-4Z"></path><path d="M6 9.5V15c3.5 2.7 8.5 2.7 12 0V9.5M21 7v8"></path></symbol>
                <symbol id="service-icon-data-automation" viewBox="0 0 24 24"><path d="M4 20V10m5 10V5m6 15v-7m5 7V3"></path><path d="m3 8 6-5 6 8 6-9"></path></symbol>
                <symbol id="service-icon-ict-support" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="13" rx="2"></rect><path d="M8 21h8m-4-4v4M7 8h4m-4 3h7"></path></symbol>
            </svg>

            <div class="services-grid">
                <?php foreach ($services as $slug => $service): ?>
                    <article class="service-summary">
                        <div class="service-summary__top">
                            <span class="service-summary__icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" focusable="false"><use href="#service-icon-<?= e($slug) ?>"></use></svg>
                            </span>
                            <span class="service-summary__number"><?= e($service['number']) ?></span>
                        </div>
                        <div>
                            <h3><?= e($service['title']) ?></h3>
                            <p><?= e($service['summary']) ?></p>
                        </div>
                        <a class="text-link" href="<?= e(url('services.php#' . $slug)) ?>">Explore this service <span aria-hidden="true">&rarr;</span></a>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="founder-section" aria-labelledby="founder-preview-title">
        <div class="container container--wide">
            <article class="founder-panel">
                <div class="founder-panel__image">
                    <img src="<?= e(asset_url($founder['image'])) ?>" alt="Mthokozisi Hlomela Mnyobe, Founder and Managing Director of MH Websites" width="<?= e($founder['image_width']) ?>" height="<?= e($founder['image_height']) ?>" loading="lazy">
                </div>
                <div class="founder-panel__content">
                    <span class="eyebrow">Meet the Founder</span>
                    <h2 id="founder-preview-title"><?= e($founder['name']) ?></h2>
                    <p class="founder-panel__role"><strong><?= e($founder['role']) ?></strong></p>
                    <p class="founder-panel__description">Mthokozisi leads MH Websites with a focus on building practical digital systems, web platforms and technology solutions designed around real organisational needs.</p>
                    <a class="text-link text-link--light" href="<?= e(url('contact.php')) ?>">Discuss Your Project <span aria-hidden="true">&rarr;</span></a>
                </div>
                <ul class="founder-principles" aria-label="Company principles">
                    <li><span class="founder-principles__icon" aria-hidden="true">&#10003;</span><strong>Practical Solutions</strong></li>
                    <li><span class="founder-principles__icon" aria-hidden="true">&#9678;</span><strong>Client Focused</strong></li>
                    <li><span class="founder-principles__icon" aria-hidden="true">&#8635;</span><strong>Long-Term Support</strong></li>
                </ul>
            </article>
        </div>
    </section>

    <section class="section systems-preview-section" id="systems-preview" aria-labelledby="systems-preview-title">
        <div class="container">
            <div class="section-heading section-heading--split">
                <div>
                    <span class="eyebrow">Systems portfolio</span>
                    <h2 id="systems-preview-title">Software leads. Technology support completes the picture.</h2>
                </div>
                <p>Our portfolio is designed to grow. These current systems are presented with their honest development status, while the same modular approach supports what comes next.</p>
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
                            <a class="text-link" href="<?= e(url('contact.php?enquiry=custom-software&system=' . rawurlencode($slug) . '#contact-form')) ?>">Request a similar solution <span aria-hidden="true">&rarr;</span></a>
                        </div>
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

    <section class="section" aria-labelledby="collaborations-title">
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
</main>
<?php render_footer(); ?>
