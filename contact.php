<?php

declare(strict_types=1);

require __DIR__ . '/config/bootstrap.php';

$contact = company_contact();
$interestOptions = enquiry_type_options();
$selectedInterest = (string) ($_GET['enquiry'] ?? '');
if (!array_key_exists($selectedInterest, $interestOptions)) {
    $selectedInterest = '';
}

$systemSlug = (string) ($_GET['system'] ?? '');
$systemInterest = system_record($systemSlug);
$errors = [];
if (strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET')) === 'POST') {
    try {
        require_post_request();
        if (!request_body_within_limit()) {
            http_response_code(413);
            throw new SubmissionValidationException(['submission' => 'The request is too large.']);
        }
        validate_csrf_or_fail($_POST['csrf_token'] ?? null);
        $enquiry = validate_enquiry($_POST, $interestOptions);
        $idempotencyHash = consume_idempotency_token('enquiry', $_POST['idempotency_token'] ?? null);
        $pdo = database_connection();
        if (!$pdo instanceof PDO) {
            throw new RuntimeException('Submission storage is unavailable.');
        }
        enforce_submission_rate_limit($pdo, 'enquiry');
        $reference = create_enquiry($pdo, $enquiry, $idempotencyHash);
        flash_set('enquiry_success', 'Your enquiry was received. Reference: ' . $reference);
        session_regenerate_id(true);
        header('Location: ' . url('contact.php#contact-form'), true, 303);
        attempt_submission_notifications(
            configured_mailer(),
            'enquiry',
            $reference,
            static fn(): array => enquiry_owner_notification_context($enquiry)
        );
        exit;
    } catch (SubmissionValidationException $exception) {
        $errors = $exception->errors;
    } catch (RateLimitExceededException $exception) {
        http_response_code(429); header('Retry-After: ' . (string) config('rate_limit.window_seconds', 900));
        $errors = ['submission' => $exception->getMessage()];
    } catch (Throwable $exception) {
        safe_log('Enquiry submission failed.', ['failure_category' => $exception::class]);
        $errors = ['submission' => 'We could not securely store your enquiry. Please try again later or contact MH Websites directly.'];
    }
}
$enquirySuccess = flash_get('enquiry_success');
$idempotencyToken = issue_idempotency_token('enquiry');

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
                <?php if ($enquirySuccess !== null): ?><div class="alert margin-top-4" role="status"><?= e($enquirySuccess) ?></div><?php endif; ?>
                <?php if ($errors !== []): ?><div class="alert alert--danger margin-top-4" role="alert"><strong>Your enquiry was not submitted.</strong><p><?= e((string) reset($errors)) ?></p></div><?php endif; ?>

                <?php if ($systemInterest !== null): ?>
                    <p class="alert margin-top-4">System interest: <strong><?= e($systemInterest['name']) ?></strong> — <?= e($systemInterest['description']) ?>.</p>
                <?php endif; ?>

                <form class="form-grid margin-top-8" method="post" action="<?= e(url('contact.php#contact-form')) ?>">
                    <?= csrf_field() ?>
                    <input type="hidden" name="idempotency_token" value="<?= e($idempotencyToken) ?>">
                    <div class="form-grid form-grid--2">
                        <div class="field">
                            <label class="field__label" for="full-name">Full name</label>
                            <input class="input" id="full-name" name="full_name" type="text" minlength="2" maxlength="120" autocomplete="name" required>
                        </div>
                        <div class="field">
                            <label class="field__label" for="organisation">Organisation <span class="text-muted">(optional)</span></label>
                            <input class="input" id="organisation" name="organisation" type="text" maxlength="160" autocomplete="organization">
                        </div>
                        <div class="field">
                            <label class="field__label" for="email">Email address</label>
                            <input class="input" id="email" name="email" type="email" maxlength="254" autocomplete="email" inputmode="email" required>
                        </div>
                        <div class="field">
                            <label class="field__label" for="phone">Phone number <span class="text-muted">(optional)</span></label>
                            <input class="input" id="phone" name="phone" type="tel" maxlength="40" autocomplete="tel" inputmode="tel">
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
                        <label for="privacy-consent">I agree that MH Websites may use these details to respond to my enquiry.</label>
                    </div>
                    <div>
                        <button class="button button--primary" type="submit">Submit Enquiry</button>
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
