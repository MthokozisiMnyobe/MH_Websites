<?php

declare(strict_types=1);

require dirname(__DIR__) . '/config/bootstrap.php';

$interimNotice = strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET')) === 'POST';

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

            <?php if ($interimNotice): ?>
                <div class="alert alert--warning" role="alert">
                    Your quotation request was not stored or sent. Secure submission will be activated in Checkpoint 1E.
                </div>
            <?php endif; ?>

            <div class="request-quote-layout">
                <form class="store-form request-quote-form" method="post" action="<?= e(url('store/request-quote.php')) ?>" data-quote-request-form>
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
                            <input id="quote-email" name="email" type="email" maxlength="160" autocomplete="email" required>
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
                        <span>I understand these details will be used to respond to my quotation request when secure submission is activated.</span>
                    </label>
                    <div class="interim-submit-panel" id="quote-submission-note">
                        <strong>Secure submission is not active yet.</strong>
                        <p>This interface does not store, email or submit your personal information during Checkpoint 1D.</p>
                    </div>
                    <button class="button button--accent button--block" type="submit" disabled aria-describedby="quote-submission-note">Submit Quotation Request</button>
                </form>

                <aside class="quote-review-panel" aria-labelledby="quote-review-title">
                    <h2 id="quote-review-title">Selected products</h2>
                    <div data-request-items></div>
                    <div class="quote-review-empty" data-request-empty hidden>
                        <p>Your Quote Basket is empty.</p>
                        <a class="text-link" href="<?= e(url('store/index.php')) ?>">Browse products</a>
                    </div>
                    <div class="compatibility-draft" data-compatibility-draft hidden>
                        <h3>Compatibility details prepared</h3>
                        <dl data-compatibility-summary></dl>
                    </div>
                    <div class="quote-review-actions">
                        <a class="text-link" href="<?= e(url('store/quote-basket.php')) ?>">Edit Quote Basket</a>
                        <a class="text-link" href="<?= e(url('store/compatibility-help.php')) ?>">Add compatibility details</a>
                        <a class="text-link" href="<?= e(url('store/print-request.php')) ?>">Print browser draft</a>
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
