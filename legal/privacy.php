<?php

declare(strict_types=1);

require dirname(__DIR__) . '/config/bootstrap.php';

$company = company_legal_information();

render_header([
    'body_class' => 'page-legal',
    'metadata' => [
        'title' => 'Privacy & POPIA Notice — MH Websites',
        'description' => 'How MH Websites collects, uses, protects and retains personal information submitted through its website.',
        'canonical' => canonical_url('/legal/privacy.php'),
        'stylesheets' => ['pages/corporate.css'],
    ],
]);
?>
<main id="main-content">
    <header class="page-hero">
        <div class="container">
            <span class="eyebrow">Legal &amp; Information</span>
            <h1>Privacy &amp; POPIA Notice</h1>
            <p>This notice explains how MH Websites handles personal information submitted through this website and related communications.</p>
        </div>
    </header>

    <section class="section">
        <div class="container content-column legal-content">
            <div class="legal-summary">
                <p><strong>Last updated:</strong> 29 September 2026</p>
                <p>This notice applies to website quotation requests, contact enquiries and the supporting security and notification processes described below.</p>
            </div>

            <section class="legal-section" aria-labelledby="privacy-responsible-party">
                <h2 id="privacy-responsible-party">Responsible business</h2>
                <p><?= e($company['legal_name']) ?>, registration number <?= e($company['registration_number']) ?>, is responsible for the website processing described in this notice.</p>
                <div class="legal-contact-card">
                    <address>
                        <?= e($company['legal_name']) ?><br>
                        <?php foreach ($company['address_lines'] as $line): ?><?= e($line) ?><br><?php endforeach; ?>
                        Phone: <a href="tel:<?= e($company['phone_uri']) ?>"><?= e($company['phone_display']) ?></a><br>
                        Privacy email: <a href="mailto:<?= e($company['email']) ?>"><?= e($company['email']) ?></a>
                    </address>
                </div>
            </section>

            <section class="legal-section" aria-labelledby="privacy-information">
                <h2 id="privacy-information">Information we collect</h2>
                <p>A quotation request may include your full name, organisation, email address, phone number, preferred contact method, quotation notes, selected products and quantities, compatibility details where applicable, and your privacy consent.</p>
                <p>A contact enquiry may include your name, organisation, email address, phone number, enquiry type, preferred contact method, message, and your privacy consent.</p>
                <p>Technical processing may include a keyed hash derived from an IP address for rate limiting, submission and security timestamps, session identifiers and security state, and limited application or security logs where operationally applicable. The application does not store the raw IP address in its rate-limit table.</p>
            </section>

            <section class="legal-section" aria-labelledby="privacy-purposes">
                <h2 id="privacy-purposes">Why we use information</h2>
                <ul>
                    <li>To review and respond to quotation requests and contact enquiries.</li>
                    <li>To reconstruct requested catalogue products from authoritative website information and assess compatibility requirements.</li>
                    <li>To contact you using the method you selected and manage any resulting quotation, project or supply relationship.</li>
                    <li>To prevent duplicate submissions, apply rate limits, protect forms and investigate operational or security problems.</li>
                    <li>To send an owner notification containing the submitted request or enquiry so that MH Websites can respond.</li>
                    <li>To keep business, contractual, accounting, dispute or legal records where the submission develops into a longer-term transaction.</li>
                </ul>
            </section>

            <section class="legal-section" aria-labelledby="privacy-browser-storage">
                <h2 id="privacy-browser-storage">Cookies, sessions and browser storage</h2>
                <p>The website uses a necessary session cookie for form security, duplicate-submission protection and authorised access to a submitted quotation summary. Server-side session records are short-lived and are used only for the related session and security purpose.</p>
                <p>The Quote Basket uses browser local storage to retain catalogue product identifiers and quantities on your device. Compatibility assistance may use browser session storage for a temporary device-information draft and may also retain that draft in the secure server session until the quotation is submitted or the session expires. Browser storage can be cleared through your browser.</p>
                <p>No advertising analytics or behavioural tracking tools are currently included in the active website.</p>
            </section>

            <section class="legal-section" aria-labelledby="privacy-providers">
                <h2 id="privacy-providers">Service providers and disclosures</h2>
                <p>Production hosting is intended to be provided by Afrihost. Google/Gmail SMTP is currently used to deliver owner notifications about website submissions. These providers may process limited information needed to supply their hosting or email services under their own service and privacy arrangements.</p>
                <p>MH Websites may also disclose information where reasonably required to perform an accepted transaction, comply with an applicable legal obligation, protect legitimate rights, or address fraud, security or a dispute. We do not claim a specific processing location for a provider where that location has not been confirmed.</p>
            </section>

            <section class="legal-section" aria-labelledby="privacy-retention">
                <h2 id="privacy-retention">Retention framework</h2>
                <p>Personal information is retained only for as long as reasonably required for its stated purpose and any applicable business or legal obligation. The current policy targets are:</p>
                <ul>
                    <li>Quotation and enquiry records that do not become longer-term business records: up to 12 months from submission.</li>
                    <li>Owner Gmail quotation and enquiry notifications: normally up to 12 months, unless required longer for an active transaction, legal obligation, dispute or legitimate record-keeping purpose.</li>
                    <li>Routine application and security logs: approximately 90 days where operationally applicable.</li>
                    <li>Rate-limit and similar short-term security records: a maximum of 30 days where practical.</li>
                    <li>PHP session records: short-lived and allowed to expire when no longer required for their session or security purpose.</li>
                    <li>Routine database backups: a rolling period of approximately 90 days, subject to final production hosting and backup arrangements.</li>
                </ul>
                <p>Records connected to customers, accepted quotations, contracts, accounting, legal obligations or disputes may require a different retention period appropriate to that purpose.</p>
                <p><strong>Current automation:</strong> not all target periods above are automatically enforced by the application yet. Until the planned retention controls are implemented, deletion and backup rotation may also depend on administrative and hosting procedures.</p>
            </section>

            <section class="legal-section" aria-labelledby="privacy-security">
                <h2 id="privacy-security">Security safeguards</h2>
                <p>MH Websites uses reasonable technical and organisational safeguards appropriate to the website, including input validation, encrypted transport where configured, access controls, least-privilege database access, security tokens, rate limiting and restricted confirmation access. No internet service can promise absolute security.</p>
            </section>

            <section class="legal-section" aria-labelledby="privacy-requests">
                <h2 id="privacy-requests">Access, correction and deletion requests</h2>
                <p>You may contact <a href="mailto:<?= e($company['email']) ?>"><?= e($company['email']) ?></a> to ask about personal information held by MH Websites, request correction, or request deletion where applicable. We may need to verify the requester and may retain information where an applicable obligation, active transaction, security need or dispute requires it.</p>
            </section>

            <section class="legal-section" aria-labelledby="privacy-updates">
                <h2 id="privacy-updates">Updates to this notice</h2>
                <p>This notice may be updated when website processing, providers or legal requirements change. The current version and update date will be published on this page.</p>
            </section>
        </div>
    </section>
</main>
<?php render_footer(); ?>
