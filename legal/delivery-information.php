<?php

declare(strict_types=1);

require dirname(__DIR__) . '/config/bootstrap.php';

$company = company_legal_information();

render_header([
    'body_class' => 'page-legal',
    'metadata' => [
        'title' => 'Delivery Information — MH Websites',
        'description' => 'Delivery areas, methods, timing and product-receipt information for physical products quoted by MH Websites.',
        'canonical' => canonical_url('/legal/delivery-information.php'),
        'stylesheets' => ['pages/corporate.css'],
    ],
]);
?>
<main id="main-content">
    <header class="page-hero">
        <div class="container">
            <span class="eyebrow">Legal &amp; Information</span>
            <h1>Delivery Information</h1>
            <p>Physical-product delivery arrangements are confirmed as part of the quotation process.</p>
        </div>
    </header>

    <section class="section">
        <div class="container content-column legal-content">
            <section class="legal-section" aria-labelledby="delivery-area">
                <h2 id="delivery-area">Service and delivery area</h2>
                <p>MH Websites serves South Africa nationwide. Digital and software services may be delivered remotely. Physical products may be couriered or delivered nationally, subject to the issued quotation, product availability and agreed delivery arrangements.</p>
            </section>

            <section class="legal-section" aria-labelledby="delivery-methods">
                <h2 id="delivery-methods">Methods and charges</h2>
                <p>Physical products may be supplied by courier or delivery. Delivery or courier charges are quoted separately where applicable. The website does not promise free delivery.</p>
            </section>

            <section class="legal-section" aria-labelledby="delivery-timing">
                <h2 id="delivery-timing">Estimated timing</h2>
                <p>Estimated delivery timing is confirmed when the quotation is issued. Timing may depend on product availability, destination, supplier arrangements, courier arrangements and circumstances outside reasonable control. No fixed universal delivery period is promised by the website.</p>
                <p>MH Websites will communicate material information it receives about a delay and, where appropriate, discuss an updated arrangement with the customer.</p>
            </section>

            <section class="legal-section" aria-labelledby="delivery-details">
                <h2 id="delivery-details">Accurate delivery and contact details</h2>
                <p>The customer must provide an accurate recipient name, delivery address and contact number through the agreed ordering process, and should check those details before accepting the quotation. MH Websites may request clarification where information is incomplete.</p>
                <p>Sensitive payment information should not be entered into the website quotation or enquiry forms.</p>
            </section>

            <section class="legal-section" aria-labelledby="delivery-compatibility">
                <h2 id="delivery-compatibility">Product and compatibility confirmation</h2>
                <p>Product images may be representative. Final product or model, specifications, compatibility and availability are confirmed during quotation. Where compatibility matters, accurate brand, model and device information is required before the customer approves the quoted product.</p>
            </section>

            <section class="legal-section" aria-labelledby="delivery-inspection">
                <h2 id="delivery-inspection">Incorrect, damaged or defective goods</h2>
                <p>Customers should inspect delivered goods as soon as reasonably possible and contact MH Websites promptly if an item appears incorrect, damaged or defective. Please retain the product, packaging and relevant delivery information while the matter is assessed.</p>
                <p>Contact <a href="tel:<?= e($company['phone_uri']) ?>"><?= e($company['phone_display']) ?></a> or <a href="mailto:<?= e($company['email']) ?>"><?= e($company['email']) ?></a>. Any return, replacement, repair or refund will be considered according to the transaction, the accepted quotation, applicable supplier or manufacturer processes, and rights that apply under South African law.</p>
            </section>

            <section class="legal-section" aria-labelledby="delivery-warranty">
                <h2 id="delivery-warranty">Warranty context</h2>
                <p>Applicable physical products generally rely on supplier or manufacturer warranties. No universal warranty period is stated on this website. A supplier or manufacturer warranty does not replace a right that may otherwise apply under South African law.</p>
            </section>
        </div>
    </section>
</main>
<?php render_footer(); ?>
