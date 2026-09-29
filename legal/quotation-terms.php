<?php

declare(strict_types=1);

require dirname(__DIR__) . '/config/bootstrap.php';

$company = company_legal_information();

render_header([
    'body_class' => 'page-legal',
    'metadata' => [
        'title' => 'Product Quotation Terms — MH Websites',
        'description' => 'How quotation requests, issued quotations and confirmed orders are handled by MH Websites.',
        'canonical' => canonical_url('/legal/quotation-terms.php'),
        'stylesheets' => ['pages/corporate.css'],
    ],
]);
?>
<main id="main-content">
    <header class="page-hero">
        <div class="container">
            <span class="eyebrow">Legal &amp; Information</span>
            <h1>Product Quotation Terms</h1>
            <p>The catalogue helps you request a quotation. It is not an online shop, checkout or payment service.</p>
        </div>
    </header>

    <section class="section">
        <div class="container content-column legal-content">
            <div class="legal-summary">
                <p><strong>Request for quotation:</strong> the information and products submitted for review.</p>
                <p><strong>Issued quotation:</strong> MH Websites' written offer setting out confirmed pricing and applicable terms.</p>
                <p><strong>Confirmed order or project:</strong> an issued quotation or agreement accepted by the customer, with required payment or deposit arrangements met.</p>
            </div>

            <section class="legal-section" aria-labelledby="quotation-request">
                <h2 id="quotation-request">A website request is not an order</h2>
                <p>Submitting a quotation request and receiving an MHQ reference does not create a binding order, reserve stock, guarantee supply or confirm a project. MH Websites first reviews the request and may seek clarification before issuing a quotation.</p>
            </section>

            <section class="legal-section" aria-labelledby="quotation-validity">
                <h2 id="quotation-validity">Issued quotation and validity</h2>
                <p>An issued quotation is normally valid for <strong>21 days</strong> from its issue date unless that quotation states a different validity period. Acceptance after expiry may require updated pricing, availability or delivery confirmation.</p>
                <p>The issued quotation will show the final price and any applicable tax treatment. This website does not state that catalogue amounts include VAT and does not publish a VAT registration number.</p>
            </section>

            <section class="legal-section" aria-labelledby="quotation-products">
                <h2 id="quotation-products">Products, availability and imagery</h2>
                <p>No public stock quantity or supply guarantee is provided by the website. Product availability, the final product or model, specifications and any alternatives are confirmed in the issued quotation.</p>
                <p>Product images may be representative. Customers should review the product, model, specifications and quantities stated in the issued quotation before accepting it.</p>
            </section>

            <section class="legal-section" aria-labelledby="quotation-compatibility">
                <h2 id="quotation-compatibility">Compatibility information</h2>
                <p>Where compatibility matters, the customer must provide accurate brand, model and device or component information. MH Websites may assist with compatibility confirmation and may request further details before supply.</p>
                <p>Incomplete or incorrect customer-supplied information may prevent accurate confirmation. The customer must review and approve the quoted product before ordering. This does not remove any right that may apply if MH Websites supplies incorrect or defective goods.</p>
            </section>

            <section class="legal-section" aria-labelledby="quotation-acceptance-payment">
                <h2 id="quotation-acceptance-payment">Acceptance and payment</h2>
                <p>The general flow is: customer request, MH Websites review, issued priced quotation, customer acceptance, required payment or deposit, and then order or project confirmation.</p>
                <p>The approved general payment method is EFT or bank transfer. Bank details are supplied through appropriate quotation or payment documentation, not on this website.</p>
                <p>For custom software and web-development projects, the usual structure is 50% upfront and 50% before final handover, unless the applicable written quotation or agreement states different terms.</p>
            </section>

            <section class="legal-section" aria-labelledby="quotation-cancellation">
                <h2 id="quotation-cancellation">Cancellations, returns and refunds</h2>
                <p>Eligibility and consequences depend on the transaction, the accepted quotation or agreement, work already performed, committed third-party costs, goods supplied, the condition of returned goods, whether an item was custom or specially ordered, and applicable law.</p>
                <p>For a software or web project, a cancellation does not automatically make every deposit refundable or non-refundable. Work performed, committed costs and any amount payable or refundable will be handled under the accepted quotation or agreement and applicable law.</p>
                <p>Contact MH Websites promptly at <a href="mailto:<?= e($company['email']) ?>"><?= e($company['email']) ?></a> to discuss a cancellation, incorrect or defective goods, a return or a refund. Nothing in these terms attempts to waive mandatory consumer rights.</p>
            </section>

            <section class="legal-section" aria-labelledby="quotation-warranty">
                <h2 id="quotation-warranty">Warranties</h2>
                <p>Applicable physical products generally rely on the relevant supplier or manufacturer warranty. The issued quotation or product documentation will identify available warranty information where applicable. No warranty period is promised by this website, and supplier or manufacturer terms do not override rights that may apply under South African law.</p>
            </section>

            <section class="legal-section" aria-labelledby="quotation-delivery">
                <h2 id="quotation-delivery">Delivery and website payment</h2>
                <p>Delivery or courier charges and timing are confirmed through the quotation where applicable. See the <a href="<?= e(url('legal/delivery-information.php')) ?>">Delivery Information</a>.</p>
                <p>The current website has no checkout and takes no online payment. Do not submit bank or card details through the quotation or enquiry forms.</p>
            </section>
        </div>
    </section>
</main>
<?php render_footer(); ?>
