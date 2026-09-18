<?php

declare(strict_types=1);

require __DIR__ . '/config/bootstrap.php';

$founder = founder_profile();
$collaborators = collaborating_organisations();

render_header([
    'active' => 'about',
    'body_class' => 'page-about',
    'metadata' => [
        'title' => 'About MH Websites — Practical Digital Problem Solving',
        'description' => 'Learn about MH Websites, its practical approach to software and web development, and founder Mthokozisi Hlomela Mnyobe.',
        'canonical' => canonical_url('/about.php'),
        'stylesheets' => ['pages/corporate.css'],
    ],
]);
?>
<main id="main-content">
    <header class="page-hero">
        <div class="container">
            <span class="eyebrow">About MH Websites</span>
            <h1>A growing software company focused on useful technology.</h1>
            <p>MH Websites brings together software development, web platforms and practical technology support to help organisations improve the way work gets done.</p>
        </div>
    </header>

    <section class="section" aria-labelledby="company-story-title">
        <div class="container story-grid">
            <div class="flow">
                <span class="eyebrow">Our direction</span>
                <h2 id="company-story-title">Build around the problem. Keep the solution understandable.</h2>
                <p>MH Websites approaches digital work by first understanding the people, information and decisions involved. The goal is not to add technology for its own sake, but to create something that makes the work clearer and more manageable.</p>
                <p>The company develops custom systems, professional websites, learning platforms and focused data tools. ICT support and technology-product guidance extend that capability where clients need practical assistance around the solution.</p>
                <p>As the systems portfolio grows, each product is presented with an honest status and developed responsibly. Concepts, prototypes and in-development products are not presented as completed deployments.</p>
            </div>
            <aside class="story-panel">
                <span class="eyebrow">What matters</span>
                <h2>Responsible growth through practical delivery.</h2>
                <p>Clear communication, appropriate scope, careful implementation and ongoing improvement guide the way MH Websites approaches its work.</p>
            </aside>
        </div>
    </section>

    <section class="section why-section" aria-labelledby="principles-title">
        <div class="container">
            <div class="section-heading"><span class="eyebrow">Company principles</span><h2 id="principles-title">A grounded approach to digital work.</h2></div>
            <div class="principles-grid">
                <article class="principle"><h3>Understand before building</h3><p>Useful solutions begin with the organisation’s actual workflow and priorities.</p></article>
                <article class="principle"><h3>Design for people</h3><p>Interfaces should be clear, responsive and appropriate for their users.</p></article>
                <article class="principle"><h3>Communicate honestly</h3><p>Project scope, product status and technical decisions should remain understandable.</p></article>
                <article class="principle"><h3>Protect what matters</h3><p>Security-conscious choices and responsible data handling belong in the foundation.</p></article>
                <article class="principle"><h3>Build for change</h3><p>Modular thinking gives a solution room to adapt as an organisation develops.</p></article>
                <article class="principle"><h3>Support the outcome</h3><p>Technology has value when it remains usable and supported beyond launch.</p></article>
            </div>
        </div>
    </section>

    <section class="section" aria-labelledby="founder-title">
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
                    <h2 id="founder-title"><?= e($founder['name']) ?></h2>
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

    <section class="section why-section" aria-labelledby="about-collaborations-title">
        <div class="container">
            <div class="section-heading">
                <span class="eyebrow">Collaboration context</span>
                <h2 id="about-collaborations-title">Organisations We Collaborate With</h2>
                <p>Names are shown for context only, without implying endorsement, certification or a legal partnership.</p>
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

    <section class="final-cta" aria-labelledby="about-cta-title">
        <div class="container final-cta__inner">
            <div><span class="eyebrow">Work with MH Websites</span><h2 id="about-cta-title">Bring us the operational problem you want to solve.</h2></div>
            <div class="final-cta__actions"><a class="button button--accent" href="<?= e(url('contact.php')) ?>">Discuss Your Project</a></div>
        </div>
    </section>
</main>
<?php render_footer(); ?>
