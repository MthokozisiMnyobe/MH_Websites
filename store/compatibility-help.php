<?php

declare(strict_types=1);

require dirname(__DIR__) . '/config/bootstrap.php';
ensure_session_started();

$selectedProductId = mb_substr(trim((string) ($_GET['product'] ?? '')), 0, 100);
if ($selectedProductId !== '' && catalogue_product($selectedProductId) === null) {
    $selectedProductId = '';
}
$errors = [];
$saved = false;
if (strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET')) === 'POST') {
    try {
        require_post_request();
        if (!request_body_within_limit()) {
            throw new SubmissionValidationException(['submission' => 'The request is too large.']);
        }
        validate_csrf_or_fail($_POST['csrf_token'] ?? null);
        $draft = validate_compatibility_draft($_POST);
        if ($draft === null) {
            throw new SubmissionValidationException(['submission' => 'Please provide compatibility details.']);
        }
        $_SESSION['compatibility_draft'] = $draft;
        $saved = true;
        $selectedProductId = $draft['product_id'];
    } catch (SubmissionValidationException $exception) {
        $errors = $exception->errors;
    }
}
$existingDraft = is_array($_SESSION['compatibility_draft'] ?? null) ? $_SESSION['compatibility_draft'] : [];

render_header([
    'active' => 'store',
    'body_class' => 'page-store page-compatibility',
    'metadata' => [
        'title' => 'Compatibility Help — MH Websites Technology Catalogue',
        'description' => 'Prepare printer, device and component details so MH Websites can manually confirm product compatibility.',
        'canonical' => canonical_url('/store/compatibility-help.php'),
        'stylesheets' => ['pages/store.css'],
    ],
]);
render_store_navigation('compatibility');
render_store_breadcrumbs([['label' => 'Compatibility Help']]);
?>
<main id="main-content">
    <section class="section store-form-section" aria-labelledby="compatibility-title">
        <div class="container store-form-layout">
            <div class="store-form-intro">
                <span class="eyebrow">Manual compatibility assistance</span>
                <h1 id="compatibility-title">Not sure which product fits your device?</h1>
                <p>Prepare the details below and MH Websites can manually confirm compatibility before the final quotation is issued.</p>
                <div class="trust-panel">
                    <h2>Helpful information</h2>
                    <ul class="check-list">
                        <li>Use the exact manufacturer and model from the device label.</li>
                        <li>Include the existing cartridge or drum code where possible.</li>
                        <li>Do not rely on appearance alone—similar products may use different components.</li>
                    </ul>
                </div>
            </div>

            <form class="store-form" method="post" action="<?= e(url('store/compatibility-help.php')) ?>" data-compatibility-form>
                <?= csrf_field() ?>
                <?php if ($saved): ?><div class="alert" role="status">Compatibility details saved securely to this session draft.</div><?php endif; ?>
                <?php if ($errors !== []): ?><div class="alert alert--danger" role="alert"><?= e((string) reset($errors)) ?></div><?php endif; ?>
                <div class="form-grid form-grid--2">
                    <div class="field">
                        <label for="device-type">Device type</label>
                        <select id="device-type" name="device_type" required>
                            <option value="">Select device type</option>
                            <option>Printer</option>
                            <option>Laptop or computer</option>
                            <option>Other technology device</option>
                        </select>
                    </div>
                    <div class="field">
                        <label for="manufacturer">Manufacturer</label>
                        <input id="manufacturer" name="manufacturer" type="text" maxlength="80" autocomplete="organization" required>
                    </div>
                    <div class="field">
                        <label for="device-model">Exact model</label>
                        <input id="device-model" name="model" type="text" maxlength="120" required>
                    </div>
                    <div class="field">
                        <label for="current-component">Current component or supply code <span class="field-optional">(if known)</span></label>
                        <input id="current-component" name="current_component" type="text" maxlength="120">
                    </div>
                </div>
                <div class="field">
                    <label for="considered-product">Product being considered</label>
                    <select id="considered-product" name="product_id">
                        <option value="">Not selected</option>
                        <?php foreach (catalogue_products() as $product): ?>
                            <option value="<?= e((string) $product['id']) ?>"<?= $selectedProductId === $product['id'] ? ' selected' : '' ?>><?= e((string) $product['name']) ?> — <?= e((string) $product['code']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="field">
                    <label for="compatibility-notes">Notes or question</label>
                    <textarea id="compatibility-notes" name="notes" rows="5" maxlength="1200" placeholder="Describe what you need to replace or connect."></textarea>
                </div>
                <div class="future-upload-note">
                    <strong>Photo upload</strong>
                    <p>A secure device-label or cartridge-photo upload will be added with the server workflow. It is not active yet.</p>
                </div>
                <p class="privacy-note">These details are kept in your secure server session and included only when you submit a quotation request.</p>
                <div class="form-actions">
                    <button class="button button--primary" type="submit">Save to Quotation Draft</button>
                    <a class="button button--secondary" href="<?= e(url('store/quote-basket.php')) ?>">View Quote Basket</a>
                </div>
                <p class="form-status" role="status" aria-live="polite" data-compatibility-status></p>
            </form>
        </div>
    </section>
</main>
<?php render_footer(['scripts' => ['js/store/compatibility-help.js']]); ?>
