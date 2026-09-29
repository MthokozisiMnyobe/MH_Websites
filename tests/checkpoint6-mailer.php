<?php

declare(strict_types=1);

error_reporting(E_ALL);
ini_set('display_errors', '1');
putenv('APP_ENV=testing');
putenv('APP_KEY=test-only-key-not-for-production');
putenv('MAIL_ENABLED=false');
$_ENV['APP_ENV'] = 'testing';
$_ENV['APP_KEY'] = 'test-only-key-not-for-production';
$_ENV['MAIL_ENABLED'] = 'false';
$_SERVER['DOCUMENT_ROOT'] = dirname(__DIR__, 2);
$_SERVER['REQUEST_URI'] = '/MH_Websites/tests/checkpoint6-mailer.php';
$_SERVER['REQUEST_METHOD'] = 'GET';

require dirname(__DIR__) . '/config/bootstrap.php';

use PHPMailer\PHPMailer\PHPMailer;

$assertions = 0;

function mailer_check(bool $condition, string $label): void
{
    global $assertions;
    $assertions++;
    if (!$condition) {
        throw new RuntimeException($label . ' failed.');
    }
}

function mailer_rejects(callable $callback, string $label): void
{
    try {
        $callback();
    } catch (Throwable) {
        mailer_check(true, $label);
        return;
    }
    mailer_check(false, $label);
}

final class RecordingPhpMailer extends PHPMailer
{
    public bool $sendCalled = false;

    public function send()
    {
        $this->sendCalled = true;
        return true;
    }
}

$validConfiguration = [
    'enabled' => true,
    'transport' => 'smtp',
    'host' => 'smtp.example.test',
    'port' => 587,
    'username' => 'mailer@example.test',
    'password' => 'synthetic-test-placeholder',
    'encryption' => 'tls',
    'from_address' => 'mailer@example.test',
    'from_name' => 'MH Websites',
    'notification_address' => 'owner@example.test',
    'timeout_seconds' => 10,
];

mailer_check(configured_mailer(['enabled' => false]) instanceof NullMailer, 'Disabled mail selects NullMailer');
mailer_check(configured_mailer($validConfiguration) instanceof SmtpMailer, 'Valid SMTP configuration selects SmtpMailer without sending');
mailer_check(configured_mailer(array_replace($validConfiguration, ['host' => ''])) instanceof UnavailableMailer, 'Incomplete SMTP configuration fails safely');
mailer_check(configured_mailer(array_replace($validConfiguration, ['transport' => 'mail'])) instanceof UnavailableMailer, 'Unsupported transport fails safely');
mailer_check(configured_mailer(array_replace($validConfiguration, ['encryption' => 'none'])) instanceof UnavailableMailer, 'Unsupported encryption fails safely');
mailer_check(validate_smtp_configuration(array_replace($validConfiguration, ['encryption' => 'ssl']))['encryption'] === 'ssl', 'SMTPS encryption is supported');
mailer_check(validate_smtp_configuration($validConfiguration)['timeout_seconds'] === 10, 'Bounded SMTP timeout is retained');
mailer_rejects(fn() => validate_smtp_configuration(array_replace($validConfiguration, ['timeout_seconds' => 31])), 'Excessive SMTP timeout rejected');
mailer_rejects(fn() => validate_smtp_configuration(array_replace($validConfiguration, ['from_name' => "MH Websites\r\nBcc: injected@example.test"])), 'Sender header injection rejected');

$quotationReference = 'MHQ-20260929-ABCDEFGHJKMNPQRS';
$quotationRawTimestamp = '2026-09-29 10:42:46 UTC';
$quotationContext = [
    'submitted_at' => $quotationRawTimestamp,
    'customer' => [
        'name' => 'Synthetic Customer',
        'organisation' => 'Synthetic Organisation',
        'email' => 'customer@example.test',
        'phone' => '+27 10 000 0000',
        'preferred_contact' => 'email',
        'notes' => "Please confirm compatibility.\nThis remains body text.",
    ],
    'products' => [[
        'name' => 'Business Multifunction Printer',
        'code' => 'SYNTH-PRINTER',
        'quantity' => 2,
    ]],
    'compatibility' => [
        'device_type' => 'Printer',
        'manufacturer' => 'Synthetic Manufacturer',
        'model' => 'Model Test',
        'current_component' => 'Synthetic component',
        'notes' => 'Synthetic compatibility note',
    ],
];
$quotationMessage = build_quotation_owner_notification($quotationReference, $quotationContext);
mailer_check($quotationMessage['subject'] === '[MH Websites] New quotation request ' . $quotationReference, 'Quotation subject contains exact MHQ reference');
mailer_check(str_contains($quotationMessage['body'], 'QUOTATION REQUEST'), 'Quotation body is clearly labelled');
mailer_check(str_contains($quotationMessage['body'], 'NOT AN ISSUED OR PRICED QUOTATION'), 'Quotation boundary is explicit');
mailer_check(str_contains($quotationMessage['body'], 'Synthetic Customer'), 'Quotation contains customer details');
mailer_check(str_contains($quotationMessage['body'], 'Business Multifunction Printer [SYNTH-PRINTER] | Quantity: 2'), 'Quotation contains authoritative product and quantity');
mailer_check(str_contains($quotationMessage['body'], 'Compatibility details:'), 'Quotation contains compatibility details');
mailer_check(str_contains($quotationMessage['body'], '29 September 2026 at 12:42 SAST'), 'Quotation timestamp converts UTC to Africa/Johannesburg');
mailer_check($quotationContext['submitted_at'] === $quotationRawTimestamp, 'Raw quotation timestamp remains unchanged');
mailer_check(format_owner_notification_datetime('2026-09-29 10:42:46') === '29 September 2026 at 12:42 SAST', 'Human-readable SAST timestamp is deterministic');
mailer_check(format_owner_notification_datetime('unexpected') === 'Unavailable', 'Unexpected timestamp fails safely');
mailer_check(!preg_match('/^(Price|Stock|Payment|Total):/mi', $quotationMessage['body']), 'Quotation contains no price, stock, payment, or total fields');
mailer_check($quotationMessage['reply_to'] === 'customer@example.test', 'Validated customer email is Reply-To only');
mailer_rejects(fn() => build_quotation_owner_notification($quotationReference, array_replace_recursive($quotationContext, ['customer' => ['email' => "customer@example.test\r\nBcc: injected@example.test"]])), 'Customer email header injection rejected');

$enquiryReference = 'MHE-20260929-ABCDEFGHJKMNPQRT';
$rawEnquiryType = 'web-development';
$enquiryContext = [
    'submitted_at' => '2026-09-29 08:35:00 UTC',
    'name' => 'Synthetic Enquirer',
    'organisation' => 'Synthetic Organisation',
    'email' => 'enquirer@example.test',
    'phone' => '',
    'enquiry_type' => $rawEnquiryType,
    'preferred_contact' => 'email',
    'message' => "Synthetic enquiry message.\nSubject: body text only",
];
$enquiryMessage = build_enquiry_owner_notification($enquiryReference, $enquiryContext);
mailer_check($enquiryMessage['subject'] === '[MH Websites] New enquiry ' . $enquiryReference, 'Enquiry subject contains exact MHE reference');
foreach (['Synthetic Enquirer', 'Synthetic Organisation', 'Web Development', 'Synthetic enquiry message.'] as $expected) {
    mailer_check(str_contains($enquiryMessage['body'], $expected), 'Enquiry body contains approved field: ' . $expected);
}
mailer_check($enquiryContext['enquiry_type'] === $rawEnquiryType, 'Raw enquiry type remains unchanged');
mailer_check(!str_contains($enquiryMessage['body'], 'Enquiry type: web-development'), 'Machine enquiry type is not presented to owner');
mailer_check(owner_enquiry_type_label('unexpected-type') === 'Unknown enquiry type', 'Unknown enquiry type fails to a safe label');
mailer_check(enquiry_type_options()['web-development'] === 'Website & E-commerce Development', 'Contact form retains authoritative option label');
mailer_check(!str_contains($enquiryMessage['subject'], 'Subject: body text only'), 'Customer message cannot alter enquiry subject');

$recordingClient = null;
$smtpMailer = configured_mailer(
    $validConfiguration,
    static function () use (&$recordingClient): PHPMailer {
        $recordingClient = new RecordingPhpMailer(true);
        return $recordingClient;
    }
);
$smtpMailer->send('quotation_owner_notification', $quotationReference, $quotationContext);
mailer_check($recordingClient instanceof RecordingPhpMailer && $recordingClient->sendCalled, 'Injected recording SMTP client sends without network access');
mailer_check($recordingClient->Mailer === 'smtp', 'PHPMailer SMTP transport configured');
mailer_check($recordingClient->Host === 'smtp.example.test' && $recordingClient->Port === 587, 'SMTP host and port configured');
mailer_check($recordingClient->SMTPSecure === PHPMailer::ENCRYPTION_STARTTLS, 'STARTTLS configured');
mailer_check($recordingClient->SMTPDebug === 0 && $recordingClient->Timeout === 10, 'SMTP debugging disabled and timeout bounded');
mailer_check($recordingClient->ContentType === PHPMailer::CONTENT_TYPE_PLAINTEXT, 'Owner email is plain text');
$toAddresses = $recordingClient->getToAddresses();
mailer_check(count($toAddresses) === 1 && $toAddresses[0][0] === 'owner@example.test', 'Only MAIL_NOTIFICATION_ADDRESS receives owner notification');
mailer_check($toAddresses[0][0] !== 'customer@example.test', 'Customer address is not a delivery recipient');
$replyToAddresses = $recordingClient->getReplyToAddresses();
mailer_check(count($replyToAddresses) === 1 && $replyToAddresses[0][0] === 'customer@example.test', 'Validated customer address is configured only as Reply-To');

$recordingMailer = new class implements MailerInterface {
    public array $types = [];
    public function send(string $notificationType, string $reference, array $context = []): void { $this->types[] = $notificationType; }
};
attempt_submission_notifications($recordingMailer, 'quotation', $quotationReference, $quotationContext);
mailer_check($recordingMailer->types === ['quotation_owner_notification'], 'Dispatcher sends owner notification only');
mailer_check(!in_array('quotation_customer_acknowledgement', $recordingMailer->types, true), 'Customer acknowledgement is disabled');

$logPath = tempnam(sys_get_temp_dir(), 'mh-mail-test-');
if ($logPath === false) {
    throw new RuntimeException('Unable to create temporary test log.');
}
$previousLog = ini_get('error_log');
ini_set('error_log', $logPath);
$failingMailer = new class implements MailerInterface {
    public function send(string $notificationType, string $reference, array $context = []): void
    {
        throw new RuntimeException('synthetic-password-marker synthetic-customer-body-marker SMTP transcript');
    }
};
attempt_submission_notifications($failingMailer, 'enquiry', $enquiryReference, $enquiryContext);
$logContents = (string) file_get_contents($logPath);
ini_set('error_log', (string) $previousLog);
unlink($logPath);
mailer_check(str_contains($logContents, $enquiryReference), 'Safe failure log retains reference');
mailer_check(str_contains($logContents, 'RuntimeException'), 'Safe failure log retains exception category');
mailer_check(!str_contains($logContents, 'synthetic-password-marker'), 'Failure log excludes exception secrets');
mailer_check(!str_contains($logContents, 'synthetic-customer-body-marker'), 'Failure log excludes customer message content');
mailer_check(!str_contains($logContents, 'SMTP transcript'), 'Failure log excludes SMTP transcript');

echo "Checkpoint 6 mailer tests passed ({$assertions} assertions)." . PHP_EOL;
