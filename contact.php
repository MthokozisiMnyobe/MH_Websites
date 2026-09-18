<?php

declare(strict_types=1);

require __DIR__ . '/config/bootstrap.php';

$contact = company_contact();
$interestOptions = [
    'custom-software' => 'Custom Software Development',
    'web-development' => 'Website & E-commerce Development',
    'learning-platforms' => 'Learning Platforms & Moodle',
    'data-automation' => 'Dashboards, Data & Automation',
    'ict-support' => 'ICT Support & Systems Integration',
    'technology-products' => 'Technology Products / Accessories',
    'general' => 'General Enquiry',
];
$selectedInterest = (string) ($_GET['enquiry'] ?? '');
if (!array_key_exists($selectedInterest, $interestOptions)) {
    $selectedInterest = '';
}

$systemSlug = (string) ($_GET['system'] ?? '');
$systemInterest = system_record($systemSlug);
$unexpectedSubmission = ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';

render_header([
    'active' => 'contact',
    'body_class' => 'page-contact',
    'metadata' => [
        'title' => 'Discuss Your Project — MH Websites',
        'description' => 'Contact MH Websites about custom software, websites, Moodle, dashboards, ICT support or technology products.',
        'canonical' => canonical_url('/contact.php'),
        'stylesheets' => ['pages/corporate.css'],
    ],
]);
?>
<main id="main-content">
    <header class="page-hero">
        <div class="container">
            <span class="eyebrow">Discuss Your Project</span>
            <h1>Tell us what your organisation needs to improve.</h1>
            <p>Start with the challenge, workflow or idea. You do not need to prepare a technical specification before getting in touch.</p>
        </div>
    </header>

    <section class="section" aria-labelledby="contact-section-title">
        <div class="container contact-layout">
            <div class="contact-form-panel" id="contact-form">
                <span class="eyebrow">Project enquiry</span>
                <h2 id="contact-section-title">How can we help?</h2>
                <div class="alert alert--warning" id="form-availability-note">
                    <strong>Online submission is not active yet.</strong>
                    <p class="margin-0">The secure enquiry workflow will be connected in Checkpoint 5. Nothing entered here is currently transmitted or stored. Please use the phone or email contact options for an immediate enquiry.</p>
                </div>

                <?php if ($unexpectedSubmission): ?>
                    <div class="alert alert--danger margin-top-4" role="alert">
                        This form is not connected yet. Your details were not stored or sent. Please contact MH Websites by phone or email.
                    </div>
                <?php endif; ?>

                <?php if ($systemInterest !== null): ?>
                    <p class="alert margin-top-4">System interest: <strong><?= e($systemInterest['name']) ?></strong> — <?= e($systemInterest['description']) ?>.</p>
                <?php endif; ?>

                <form class="form-grid margin-top-8" method="post" action="<?= e(url('contact.php#contact-form')) ?>" aria-describedby="form-availability-note">
                    <div class="form-grid form-grid--2">
                        <div class="field">
                            <label class="field__label" for="full-name">Full name</label>
                            <input class="input" id="full-name" name="full_name" type="text" autocomplete="name" required>
                        </div>
                        <div class="field">
                            <label class="field__label" for="organisation">Organisation <span class="text-muted">(optional)</span></label>
                            <input class="input" id="organisation" name="organisation" type="text" autocomplete="organization">
                        </div>
                        <div class="field">
                            <label class="field__label" for="email">Email address</label>
                            <input class="input" id="email" name="email" type="email" autocomplete="email" inputmode="email" required>
                        </div>
                        <div class="field">
                            <label class="field__label" for="phone">Phone number <span class="text-muted">(optional)</span></label>
                            <input class="input" id="phone" name="phone" type="tel" autocomplete="tel" inputmode="tel">
                        </div>
                    </div>
                    <div class="field">
                        <label class="field__label" for="enquiry-type">What would you like to discuss?</label>
                        <select class="select" id="enquiry-type" name="enquiry_type" required>
                            <option value="">Choose an enquiry type</option>
                            <?php foreach ($interestOptions as $value => $label): ?>
                                <option value="<?= e($value) ?>" <?= $selectedInterest === $value ? 'selected' : '' ?>><?= e($label) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="field">
                        <label class="field__label" for="preferred-contact">Preferred contact method</label>
                        <select class="select" id="preferred-contact" name="preferred_contact" required>
                            <option value="">Choose a contact method</option>
                            <option value="email">Email</option>
                            <option value="phone">Phone</option>
                        </select>
                    </div>
                    <div class="field">
                        <label class="field__label" for="project-summary">Project or enquiry details</label>
                        <textarea class="textarea" id="project-summary" name="project_summary" rows="7" minlength="20" maxlength="3000" aria-describedby="project-summary-hint" required></textarea>
                        <p class="field__hint" id="project-summary-hint">Describe the challenge, who will use the solution and what you would like to improve.</p>
                    </div>
                    <div class="checkbox-field">
                        <input id="privacy-consent" name="privacy_consent" type="checkbox" value="yes" required>
                        <label for="privacy-consent">I agree that MH Websites may use these details to respond to my enquiry once secure submission is enabled.</label>
                    </div>
                    <div>
                        <button class="button button--primary" type="submit" disabled aria-describedby="form-availability-note">Secure Submission Coming in Checkpoint 5</button>
                    </div>
                </form>
            </div>

            <aside class="contact-details-panel" aria-labelledby="direct-contact-title">
                <span class="eyebrow">Direct contact</span>
                <h2 id="direct-contact-title">MH Websites</h2>
                <p>Based in East London and available to discuss digital projects and technology needs.</p>
                <ul class="contact-list">
                    <li><span>Phone</span><a href="tel:<?= e($contact['phone_uri']) ?>"><?= e($contact['phone_display']) ?></a></li>
                    <li><span>Email</span><a href="mailto:<?= e($contact['email']) ?>"><?= e($contact['email']) ?></a></li>
                    <li><span>Location</span><strong><?= e($contact['location']) ?></strong></li>
                    <li><span>Hours</span><strong><?= e($contact['hours']) ?></strong></li>
                </ul>
            </aside>
        </div>
    </section>
</main>
<?php render_footer(); ?>
