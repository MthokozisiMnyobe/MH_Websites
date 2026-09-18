<?php

declare(strict_types=1);

require __DIR__ . '/config/bootstrap.php';

$services = service_categories();

render_header([
    'active' => 'services',
    'body_class' => 'page-services',
    'metadata' => [
        'title' => 'Software, Web and ICT Services — MH Websites',
        'description' => 'Explore custom software, web development, Moodle, dashboards, automation and ICT support services from MH Websites.',
        'canonical' => canonical_url('/services.php'),
        'stylesheets' => ['pages/corporate.css'],
    ],
]);
?>
<main id="main-content">
    <header class="page-hero">
        <div class="container">
            <span class="eyebrow">Services</span>
            <h1>Practical digital capability for growing organisations.</h1>
            <p>MH Websites combines software development, web delivery, learning technology, data tools and ICT support around the needs of each organisation.</p>
        </div>
    </header>

    <section class="section" aria-labelledby="services-intro-title">
        <div class="container intro-grid">
            <div class="content-column">
                <span class="eyebrow">Start with what needs to improve</span>
                <h2 id="services-intro-title">A service can be focused. A solution can combine several capabilities.</h2>
                <p class="intro-grid__lead">You do not need to arrive with a technical specification. Explain the work, the obstacle and the result you need; we can help define a sensible next step.</p>
            </div>
            <nav class="cluster" aria-label="Service sections">
                <?php foreach ($services as $slug => $service): ?>
                    <a class="button button--secondary" href="#<?= e($slug) ?>"><?= e($service['title']) ?></a>
                <?php endforeach; ?>
            </nav>
        </div>
    </section>

    <section class="section why-section" aria-label="Service details">
        <div class="container service-detail-list">
            <?php foreach ($services as $slug => $service): ?>
                <article class="card service-detail" id="<?= e($slug) ?>">
                    <span class="service-detail__number"><?= e($service['number']) ?></span>
                    <div>
                        <h2><?= e($service['title']) ?></h2>
                        <p><?= e($service['summary']) ?></p>
                        <a class="text-link" href="<?= e(url('contact.php?enquiry=' . rawurlencode($slug) . '#contact-form')) ?>">
                            Discuss this service <span aria-hidden="true">→</span>
                        </a>
                    </div>
                    <aside class="service-detail__aside">
                        <h3>Who it helps</h3>
                        <p><?= e($service['helps']) ?></p>
                        <h3>Typical outcomes</h3>
                        <ul class="check-list">
                            <?php foreach ($service['outcomes'] as $outcome): ?>
                                <li><?= e($outcome) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </aside>
                </article>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="section" aria-labelledby="products-support-title">
        <div class="container product-division">
            <div>
                <span class="eyebrow">Separate technology-products division</span>
                <h2 id="products-support-title">Need printers, toner, drum units or accessories?</h2>
                <p class="margin-0">Products are supplied through a quotation process. Price, availability and compatibility are confirmed before a quotation is issued.</p>
            </div>
            <a class="button button--primary" href="<?= e(url('contact.php?enquiry=technology-products#contact-form')) ?>">Request Product Help</a>
        </div>
    </section>

    <section class="final-cta" aria-labelledby="services-cta-title">
        <div class="container final-cta__inner">
            <div><span class="eyebrow">Not sure where to begin?</span><h2 id="services-cta-title">Tell us what is slowing the work down.</h2></div>
            <div class="final-cta__actions"><a class="button button--accent" href="<?= e(url('contact.php')) ?>">Discuss Your Project</a></div>
        </div>
    </section>
</main>
<?php render_footer(); ?>
