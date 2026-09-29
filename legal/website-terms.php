<?php

declare(strict_types=1);

require dirname(__DIR__) . '/config/bootstrap.php';

$company = company_legal_information();

render_header([
    'body_class' => 'page-legal',
    'metadata' => [
        'title' => 'Website Terms — MH Websites',
        'description' => 'Terms governing use of the MH Websites website and quotation-based technology catalogue.',
        'canonical' => canonical_url('/legal/website-terms.php'),
        'stylesheets' => ['pages/corporate.css'],
    ],
]);
?>
<main id="main-content">
    <header class="page-hero">
        <div class="container">
            <span class="eyebrow">Legal &amp; Information</span>
            <h1>Website Terms</h1>
            <p>These terms explain the basis on which you may use the MH Websites website and quotation catalogue.</p>
        </div>
    </header>

    <section class="section">
        <div class="container content-column legal-content">
            <div class="legal-summary"><p><strong>Last updated:</strong> 29 September 2026</p></div>

            <section class="legal-section" aria-labelledby="terms-business">
                <h2 id="terms-business">Business identity and contact</h2>
                <p>This website is operated by <?= e($company['legal_name']) ?>, a CIPC-registered company with registration number <?= e($company['registration_number']) ?>, established in <?= e($company['established']) ?> and serving organisations and customers across South Africa.</p>
                <p>Contact: <a href="tel:<?= e($company['phone_uri']) ?>"><?= e($company['phone_display']) ?></a> or <a href="mailto:<?= e($company['email']) ?>"><?= e($company['email']) ?></a>.</p>
            </section>

            <section class="legal-section" aria-labelledby="terms-use">
                <h2 id="terms-use">Informational and lawful use</h2>
                <p>The website provides information about software, web, learning, data, ICT and technology-product services. You may use it to learn about those services, prepare a Quote Basket, request a quotation or submit an enquiry.</p>
                <p>You must not misuse the website, interfere with its operation, attempt unauthorised access, submit unlawful or malicious material, impersonate another person, or use automated activity that places an unreasonable load on the service.</p>
            </section>

            <section class="legal-section" aria-labelledby="terms-intellectual-property">
                <h2 id="terms-intellectual-property">Intellectual property</h2>
                <p>Unless stated otherwise, the website design, original text, code and MH Websites branding are owned by or licensed to MH Websites. Product and manufacturer names, marks and information may belong to their respective owners. Website access does not transfer ownership or grant permission to reproduce protected material beyond normal personal or business evaluation of the services.</p>
            </section>

            <section class="legal-section" aria-labelledby="terms-catalogue">
                <h2 id="terms-catalogue">Catalogue and product information</h2>
                <p>The technology catalogue is quotation-based. It does not publish product prices or public stock quantities and does not provide checkout or online payment.</p>
                <p>Product images may be representative. Final product or model, specifications, compatibility, availability, pricing and delivery arrangements are confirmed during the quotation process. MH Websites may correct an error or update information when it becomes aware that website material is incomplete or inaccurate.</p>
            </section>

            <section class="legal-section" aria-labelledby="terms-quotation">
                <h2 id="terms-quotation">Quotation requests are not orders</h2>
                <p>Submitting a website form or receiving an MHQ reference records a <strong>request for quotation</strong>. It does not create an accepted order or require MH Websites to supply a product or service.</p>
                <p>An order or project is confirmed only after MH Websites has reviewed the request, issued a priced quotation or written agreement, the customer has accepted the applicable terms, and any required payment or deposit arrangements have been met.</p>
                <p>See the <a href="<?= e(url('legal/quotation-terms.php')) ?>">Product Quotation Terms</a> and <a href="<?= e(url('legal/delivery-information.php')) ?>">Delivery Information</a> for further details.</p>
            </section>

            <section class="legal-section" aria-labelledby="terms-third-parties">
                <h2 id="terms-third-parties">Third-party services and links</h2>
                <p>The website may rely on service providers for hosting, email or other operational functions and may sometimes refer to third-party products or websites. A reference or link does not by itself imply endorsement, ownership or control of that third party. Third-party services remain subject to their own terms and availability.</p>
            </section>

            <section class="legal-section" aria-labelledby="terms-availability">
                <h2 id="terms-availability">Availability and responsibility</h2>
                <p>MH Websites aims to keep the website useful, secure and accurate, but does not promise uninterrupted or error-free availability. The website may be changed, corrected or temporarily unavailable for maintenance or circumstances outside reasonable control.</p>
                <p>To the extent permitted by applicable law, MH Websites is not responsible for loss caused solely by reliance on preliminary website information that is expressly subject to quotation and confirmation. Nothing in these terms excludes or limits a right or remedy that cannot lawfully be excluded.</p>
            </section>

            <section class="legal-section" aria-labelledby="terms-privacy-law">
                <h2 id="terms-privacy-law">Privacy and South African law</h2>
                <p>Personal information submitted through the website is handled as described in the <a href="<?= e(url('legal/privacy.php')) ?>">Privacy &amp; POPIA Notice</a>.</p>
                <p>These website terms are considered in the context of the laws of the Republic of South Africa to the extent applicable. Any issue should first be raised with MH Websites using the contact details above so that it can be addressed reasonably.</p>
            </section>
        </div>
    </section>
</main>
<?php render_footer(); ?>
