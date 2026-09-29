<?php

declare(strict_types=1);

require dirname(__DIR__) . '/config/bootstrap.php';

$errors = [];
if (strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET')) === 'POST') {
    try {
        require_post_request();
        if (!request_body_within_limit()) {
            http_response_code(413);
            throw new SubmissionValidationException(['submission' => 'The request is too large.']);
        }
        validate_csrf_or_fail($_POST['csrf_token'] ?? null);
        $customer = validate_quotation_customer($_POST);
        if (!$customer['privacy_consent']) {
            throw new SubmissionValidationException(['privacy_consent' => 'Consent is required.']);
        }
        $items = validate_basket_payload($_POST['basket_payload'] ?? null);
        $compatibility = $_SESSION['compatibility_draft'] ?? null;
        $idempotencyHash = consume_idempotency_token('quotation', $_POST['idempotency_token'] ?? null);
        $pdo = database_connection();
        if (!$pdo instanceof PDO) {
            throw new RuntimeException('Submission storage is unavailable.');
        }
        enforce_submission_rate_limit($pdo, 'quotation');
        $created = create_quotation_request($pdo, $customer, $items, is_array($compatibility) ? $compatibility : null, $idempotencyHash);
        $reference = $created['reference'];
        establish_submission_grant('quotation', $reference, $created['confirmation_token']);
        unset($_SESSION['compatibility_draft']);
        session_regenerate_id(true);
        header('Location: ' . url('store/quote-confirmation.php'), true, 303);
        attempt_submission_notifications(
            configured_mailer(),
            'quotation',
            $reference,
            static fn(): array => quotation_owner_notification_context(
                $customer,
                $items,
                is_array($compatibility) ? $compatibility : null
            )
        );
        exit;
    } catch (SubmissionValidationException $exception) {
        $errors = $exception->errors;
    } catch (RateLimitExceededException $exception) {
        http_response_code(429);
        header('Retry-After: ' . (string) config('rate_limit.window_seconds', 900));
        $errors = ['submission' => $exception->getMessage()];
    } catch (Throwable $exception) {
        safe_log('Quotation submission failed.', ['failure_category' => $exception::class]);
        $errors = ['submission' => 'We could not securely store your request. Please try again later or contact MH Websites directly.'];
    }
}
$idempotencyToken = issue_idempotency_token('quotation');
$compatibilityDraft = is_array($_SESSION['compatibility_draft'] ?? null) ? $_SESSION['compatibility_draft'] : null;

render_header([
    'active' => 'store',
    'body_class' => 'page-store page-request-quote',
    'metadata' => [
        'title' => 'Request a Quotation — MH Websites',
        'description' => 'Prepare contact details and review selected technology products before the secure quotation workflow is activated.',
        'canonical' => canonical_url('/store/request-quote.php'),
        'robots' => 'noindex,follow',
        'stylesheets' => ['pages/store.css'],
    ],
]);
render_store_navigation();
render_store_breadcrumbs([['label' => 'Quote Basket', 'path' => 'store/quote-basket.php'], ['label' => 'Request Quotation']]);
?>
<main id="main-content">
    <section class="section request-quote-section" aria-labelledby="request-quote-title">
        <div class="container">
            <div class="section-heading section-heading--split">
                <div>
                    <span class="eyebrow">Quotation request</span>
                    <h1 id="request-quote-title">Tell us who the quotation is for.</h1>
                </div>
                <p>Review your selected items and prepare the details MH Websites will need to respond.</p>
            </div>

            <?php if ($errors !== []): ?>
                <div class="alert alert--danger" role="alert">
                    <strong>Your quotation request was not submitted.</strong>
                    <p><?= e((string) reset($errors)) ?></p>
                </div>
            <?php endif; ?>

            <div class="request-quote-layout">
                <form class="store-form request-quote-form" method="post" action="<?= e(url('store/request-quote.php')) ?>" data-quote-request-form>
                    <?= csrf_field() ?>
                    <input type="hidden" name="idempotency_token" value="<?= e($idempotencyToken) ?>">
                    <input type="hidden" name="basket_payload" value="" data-basket-payload>
                    <h2>Contact details</h2>
                    <div class="form-grid form-grid--2">
                        <div class="field">
                            <label for="quote-name">Full name</label>
                            <input id="quote-name" name="full_name" type="text" maxlength="120" autocomplete="name" required>
                        </div>
                        <div class="field">
                            <label for="quote-organisation">Organisation or company <span class="field-optional">(optional)</span></label>
                            <input id="quote-organisation" name="organisation" type="text" maxlength="160" autocomplete="organization">
                        </div>
                        <div class="field">
                            <label for="quote-email">Email address</label>
                            <input id="quote-email" name="email" type="email" maxlength="254" autocomplete="email" required>
                        </div>
                        <div class="field">
                            <label for="quote-phone">Phone number</label>
                            <input id="quote-phone" name="phone" type="tel" maxlength="40" autocomplete="tel" inputmode="tel" required>
                        </div>
                        <div class="field">
                            <label for="preferred-contact">Preferred contact method</label>
                            <select id="preferred-contact" name="preferred_contact" required>
                                <option value="">Select a method</option>
                                <option value="email">Email</option>
                                <option value="phone">Phone</option>
                            </select>
                        </div>
                    </div>
                    <div class="field">
                        <label for="quote-notes">Quotation notes <span class="field-optional">(optional)</span></label>
                        <textarea id="quote-notes" name="quotation_notes" rows="5" maxlength="2000" placeholder="Tell us about quantities, delivery context or any related requirements."></textarea>
                    </div>
                    <label class="checkbox-field">
                        <input type="checkbox" name="privacy_consent" value="1" required>
                        <span>I agree that MH Websites may use these details to respond to my quotation request, as explained in the <a href="<?= e(url('legal/privacy.php')) ?>">Privacy &amp; POPIA Notice</a>.</span>
                    </label>
                    <p class="privacy-note" id="quote-submission-note">Your request is validated securely. Pricing and availability are supplied only in the quotation prepared by MH Websites.</p>
                    <button class="button button--accent button--block" type="submit" aria-describedby="quote-submission-note">Submit Quotation Request</button>
                </form>

                <aside class="quote-review-panel" aria-labelledby="quote-review-title">
                    <h2 id="quote-review-title">Selected products</h2>
                    <div data-request-items></div>
                    <div class="quote-review-empty" data-request-empty hidden>
                        <p>Your Quote Basket is empty.</p>
                        <a class="text-link" href="<?= e(url('store/index.php')) ?>">Browse products</a>
                    </div>
                    <div class="compatibility-draft" data-compatibility-draft<?= $compatibilityDraft === null ? ' hidden' : '' ?>>
                        <h3>Compatibility details prepared</h3>
                        <dl data-compatibility-summary><?php if ($compatibilityDraft !== null): foreach ($compatibilityDraft as $key => $value): if ($value === '') continue; ?><div><dt><?= e(ucwords(str_replace('_', ' ', $key))) ?></dt><dd><?= e($key === 'product_id' ? (catalogue_product((string) $value)['name'] ?? $value) : $value) ?></dd></div><?php endforeach; endif; ?></dl>
                    </div>
                    <div class="quote-review-actions">
                        <a class="text-link" href="<?= e(url('store/quote-basket.php')) ?>">Edit Quote Basket</a>
                        <a class="text-link" href="<?= e(url('store/compatibility-help.php')) ?>">Add compatibility details</a>
                    </div>
                </aside>
            </div>
        </div>
    </section>
</main>
<?php
render_catalogue_payload();
render_footer(['scripts' => ['js/store/request-quote.js']]);
?>
