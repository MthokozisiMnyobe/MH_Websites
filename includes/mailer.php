<?php

declare(strict_types=1);

use PHPMailer\PHPMailer\PHPMailer;

interface MailerInterface
{
    public function send(string $notificationType, string $reference, array $context = []): void;
}

final class NullMailer implements MailerInterface
{
    public function send(string $notificationType, string $reference, array $context = []): void
    {
        // Intentionally no delivery when mail is disabled.
    }
}

final class UnavailableMailer implements MailerInterface
{
    public function send(string $notificationType, string $reference, array $context = []): void
    {
        throw new RuntimeException('The configured mail transport is unavailable.');
    }
}

final class SmtpMailer implements MailerInterface
{
    private array $settings;
    private Closure $clientFactory;

    public function __construct(array $settings, ?callable $clientFactory = null)
    {
        $this->settings = validate_smtp_configuration($settings);
        $this->clientFactory = $clientFactory === null
            ? static fn(): PHPMailer => new PHPMailer(true)
            : Closure::fromCallable($clientFactory);
    }

    public function send(string $notificationType, string $reference, array $context = []): void
    {
        $message = build_owner_notification($notificationType, $reference, $context);
        $mailer = ($this->clientFactory)();
        if (!$mailer instanceof PHPMailer) {
            throw new RuntimeException('The mail client factory is unavailable.');
        }

        $mailer->isSMTP();
        $mailer->Host = $this->settings['host'];
        $mailer->Port = $this->settings['port'];
        $mailer->SMTPAuth = true;
        $mailer->Username = $this->settings['username'];
        $mailer->Password = $this->settings['password'];
        $mailer->SMTPSecure = $this->settings['encryption'] === 'tls'
            ? PHPMailer::ENCRYPTION_STARTTLS
            : PHPMailer::ENCRYPTION_SMTPS;
        $mailer->SMTPAutoTLS = true;
        $mailer->Timeout = $this->settings['timeout_seconds'];
        $mailer->SMTPDebug = 0;
        $mailer->CharSet = PHPMailer::CHARSET_UTF8;
        $mailer->Encoding = PHPMailer::ENCODING_BASE64;

        $mailer->setFrom($this->settings['from_address'], $this->settings['from_name'], false);
        $mailer->addAddress($this->settings['notification_address']);
        if ($message['reply_to'] !== null) {
            $mailer->addReplyTo($message['reply_to']);
        }
        $mailer->isHTML(false);
        $mailer->Subject = $message['subject'];
        $mailer->Body = $message['body'];
        $mailer->send();
    }
}

function current_mail_configuration(): array
{
    return [
        'enabled' => (bool) config('mail.enabled', false),
        'transport' => (string) config('mail.transport', 'smtp'),
        'host' => (string) config('mail.host', ''),
        'port' => (int) config('mail.port', 587),
        'username' => (string) config('mail.username', ''),
        'password' => (string) config('mail.password', ''),
        'encryption' => (string) config('mail.encryption', 'tls'),
        'from_address' => (string) config('mail.from_address', ''),
        'from_name' => (string) config('mail.from_name', 'MH Websites'),
        'notification_address' => (string) config('mail.notification_address', ''),
        'timeout_seconds' => (int) config('mail.timeout_seconds', 10),
    ];
}

function configured_mailer(?array $settings = null, ?callable $clientFactory = null): MailerInterface
{
    $settings ??= current_mail_configuration();
    if (!(bool) ($settings['enabled'] ?? false)) {
        return new NullMailer();
    }

    try {
        if (!class_exists(PHPMailer::class)) {
            throw new RuntimeException('The configured mail dependency is unavailable.');
        }
        return new SmtpMailer($settings, $clientFactory);
    } catch (Throwable $exception) {
        safe_log('Mail transport configuration unavailable.', [
            'failure_category' => $exception::class,
        ]);
        return new UnavailableMailer();
    }
}

function validate_smtp_configuration(array $settings): array
{
    $transport = strtolower(trim((string) ($settings['transport'] ?? '')));
    if ($transport !== 'smtp') {
        throw new InvalidArgumentException('Unsupported mail transport configuration.');
    }

    $encryption = strtolower(trim((string) ($settings['encryption'] ?? '')));
    if (!in_array($encryption, ['tls', 'ssl'], true)) {
        throw new InvalidArgumentException('Unsupported SMTP encryption configuration.');
    }

    $host = trim((string) ($settings['host'] ?? ''));
    if ($host === '' || has_mail_header_break($host) || preg_match('/^[A-Za-z0-9.-]+$/', $host) !== 1) {
        throw new InvalidArgumentException('Invalid SMTP host configuration.');
    }

    $username = trim((string) ($settings['username'] ?? ''));
    $password = (string) ($settings['password'] ?? '');
    if ($username === '' || $password === '' || has_mail_header_break($username)) {
        throw new InvalidArgumentException('Incomplete SMTP authentication configuration.');
    }

    $port = filter_var($settings['port'] ?? null, FILTER_VALIDATE_INT, [
        'options' => ['min_range' => 1, 'max_range' => 65535],
    ]);
    $timeout = filter_var($settings['timeout_seconds'] ?? 10, FILTER_VALIDATE_INT, [
        'options' => ['min_range' => 1, 'max_range' => 30],
    ]);
    if ($port === false || $timeout === false) {
        throw new InvalidArgumentException('Invalid SMTP port or timeout configuration.');
    }

    $fromAddress = validate_mailbox((string) ($settings['from_address'] ?? ''), 'From');
    $notificationAddress = validate_mailbox((string) ($settings['notification_address'] ?? ''), 'Notification');
    $fromName = trim((string) ($settings['from_name'] ?? ''));
    if ($fromName === '' || strlen($fromName) > 160 || has_mail_header_break($fromName)) {
        throw new InvalidArgumentException('Invalid SMTP sender-name configuration.');
    }

    return [
        'transport' => $transport,
        'host' => $host,
        'port' => (int) $port,
        'username' => $username,
        'password' => $password,
        'encryption' => $encryption,
        'from_address' => $fromAddress,
        'from_name' => $fromName,
        'notification_address' => $notificationAddress,
        'timeout_seconds' => (int) $timeout,
    ];
}

function validate_mailbox(string $address, string $label): string
{
    $address = trim($address);
    if ($address === '' || has_mail_header_break($address) || filter_var($address, FILTER_VALIDATE_EMAIL) === false) {
        throw new InvalidArgumentException($label . ' email configuration is invalid.');
    }
    return $address;
}

function has_mail_header_break(string $value): bool
{
    return str_contains($value, "\r") || str_contains($value, "\n");
}

function attempt_submission_notifications(MailerInterface $mailer, string $scope, string $reference, array|callable $context = []): void
{
    try {
        $resolvedContext = is_callable($context) ? $context() : $context;
        if (!is_array($resolvedContext)) {
            throw new RuntimeException('Notification context is unavailable.');
        }
        $mailer->send($scope . '_owner_notification', $reference, $resolvedContext);
    } catch (Throwable $exception) {
        safe_log('Submission owner notification failed.', [
            'reference' => $reference,
            'notification_type' => 'owner_notification',
            'failure_category' => $exception::class,
        ]);
    }
}

function quotation_owner_notification_context(array $customer, array $items, ?array $compatibility): array
{
    $products = [];
    foreach ($items as $line) {
        $product = is_array($line['product'] ?? null) ? $line['product'] : [];
        $products[] = [
            'name' => $product['name'] ?? '',
            'code' => $product['code'] ?? '',
            'quantity' => $line['quantity'] ?? null,
        ];
    }

    return [
        'submitted_at' => gmdate('Y-m-d H:i:s'),
        'customer' => [
            'name' => $customer['customer_name'] ?? '',
            'organisation' => $customer['organisation'] ?? '',
            'email' => $customer['email'] ?? '',
            'phone' => $customer['phone'] ?? '',
            'preferred_contact' => $customer['preferred_contact_method'] ?? '',
            'notes' => $customer['notes'] ?? '',
        ],
        'products' => $products,
        'compatibility' => $compatibility,
    ];
}

function enquiry_owner_notification_context(array $enquiry): array
{
    return [
        'submitted_at' => gmdate('Y-m-d H:i:s'),
        'name' => $enquiry['customer_name'] ?? '',
        'organisation' => $enquiry['organisation'] ?? '',
        'email' => $enquiry['email'] ?? '',
        'phone' => $enquiry['phone'] ?? '',
        'enquiry_type' => $enquiry['enquiry_type'] ?? '',
        'preferred_contact' => $enquiry['preferred_contact_method'] ?? '',
        'message' => $enquiry['message'] ?? '',
    ];
}

function build_owner_notification(string $notificationType, string $reference, array $context): array
{
    return match ($notificationType) {
        'quotation_owner_notification' => build_quotation_owner_notification($reference, $context),
        'enquiry_owner_notification' => build_enquiry_owner_notification($reference, $context),
        default => throw new InvalidArgumentException('Unsupported owner notification type.'),
    };
}

function build_quotation_owner_notification(string $reference, array $context): array
{
    validate_submission_mail_reference($reference, 'MHQ');
    $customer = is_array($context['customer'] ?? null) ? $context['customer'] : [];
    $products = is_array($context['products'] ?? null) ? $context['products'] : [];
    if ($products === []) {
        throw new InvalidArgumentException('Quotation notification products are unavailable.');
    }

    $lines = [
        'QUOTATION REQUEST',
        'NOT AN ISSUED OR PRICED QUOTATION',
        '',
        plain_mail_field('Reference', $reference),
        plain_mail_field('Submitted at', format_owner_notification_datetime($context['submitted_at'] ?? null)),
        plain_mail_field('Customer name', $customer['name'] ?? 'Not supplied'),
        plain_mail_field('Organisation', $customer['organisation'] ?? 'Not supplied'),
        plain_mail_field('Email', $customer['email'] ?? 'Not supplied'),
        plain_mail_field('Phone', $customer['phone'] ?? 'Not supplied'),
        plain_mail_field('Preferred contact', $customer['preferred_contact'] ?? 'Not supplied'),
        '',
        'Requested products:',
    ];

    foreach ($products as $product) {
        if (!is_array($product)) {
            throw new InvalidArgumentException('Invalid quotation product context.');
        }
        $quantity = filter_var($product['quantity'] ?? null, FILTER_VALIDATE_INT, [
            'options' => ['min_range' => 1, 'max_range' => 99],
        ]);
        if ($quantity === false) {
            throw new InvalidArgumentException('Invalid quotation product quantity.');
        }
        $name = normalise_plain_mail_text($product['name'] ?? 'Unknown product');
        $code = normalise_plain_mail_text($product['code'] ?? '');
        $lines[] = '- ' . $name . ($code === '' ? '' : ' [' . $code . ']') . ' | Quantity: ' . $quantity;
    }

    $lines[] = '';
    $lines[] = plain_mail_field('Quotation notes', $customer['notes'] ?? 'Not supplied');
    $compatibility = is_array($context['compatibility'] ?? null) ? $context['compatibility'] : [];
    if ($compatibility !== []) {
        $lines[] = '';
        $lines[] = 'Compatibility details:';
        foreach ([
            'device_type' => 'Device type',
            'manufacturer' => 'Manufacturer',
            'model' => 'Model',
            'current_component' => 'Current component',
            'notes' => 'Compatibility notes',
        ] as $key => $label) {
            $lines[] = plain_mail_field($label, $compatibility[$key] ?? 'Not supplied');
        }
    }

    return [
        'subject' => '[MH Websites] New quotation request ' . $reference,
        'body' => implode("\n", $lines),
        'reply_to' => notification_reply_to($customer['email'] ?? null),
    ];
}

function build_enquiry_owner_notification(string $reference, array $context): array
{
    validate_submission_mail_reference($reference, 'MHE');
    $lines = [
        'NEW CONTACT ENQUIRY',
        '',
        plain_mail_field('Reference', $reference),
        plain_mail_field('Submitted at', format_owner_notification_datetime($context['submitted_at'] ?? null)),
        plain_mail_field('Full name', $context['name'] ?? 'Not supplied'),
        plain_mail_field('Organisation', $context['organisation'] ?? 'Not supplied'),
        plain_mail_field('Email', $context['email'] ?? 'Not supplied'),
        plain_mail_field('Phone', $context['phone'] ?? 'Not supplied'),
        plain_mail_field('Enquiry type', owner_enquiry_type_label($context['enquiry_type'] ?? null)),
        plain_mail_field('Preferred contact', $context['preferred_contact'] ?? 'Not supplied'),
        '',
        plain_mail_field('Enquiry message', $context['message'] ?? 'Not supplied'),
    ];

    return [
        'subject' => '[MH Websites] New enquiry ' . $reference,
        'body' => implode("\n", $lines),
        'reply_to' => notification_reply_to($context['email'] ?? null),
    ];
}

function validate_submission_mail_reference(string $reference, string $prefix): void
{
    if (preg_match('/^' . preg_quote($prefix, '/') . '-[0-9]{8}-[0-9A-HJKMNP-TV-Z]{16}$/', $reference) !== 1) {
        throw new InvalidArgumentException('Invalid submission reference for notification.');
    }
}

function notification_reply_to(mixed $address): ?string
{
    $address = trim((string) $address);
    return $address === '' ? null : validate_mailbox($address, 'Reply-To');
}

function format_owner_notification_datetime(mixed $value): string
{
    $raw = normalise_plain_mail_text($value);
    if ($raw === '') {
        return 'Not supplied';
    }
    if (str_ends_with($raw, ' UTC')) {
        $raw = substr($raw, 0, -4);
    }

    $utc = DateTimeImmutable::createFromFormat(
        '!Y-m-d H:i:s',
        $raw,
        new DateTimeZone('UTC')
    );
    $errors = DateTimeImmutable::getLastErrors();
    if (
        !$utc instanceof DateTimeImmutable
        || (is_array($errors) && ($errors['warning_count'] > 0 || $errors['error_count'] > 0))
        || $utc->format('Y-m-d H:i:s') !== $raw
    ) {
        return 'Unavailable';
    }

    return $utc
        ->setTimezone(new DateTimeZone('Africa/Johannesburg'))
        ->format('j F Y \\a\\t H:i T');
}

function owner_enquiry_type_label(mixed $value): string
{
    $type = normalise_plain_mail_text($value);
    if ($type === '') {
        return 'Not supplied';
    }
    return enquiry_type_email_label($type) ?? 'Unknown enquiry type';
}

function plain_mail_field(string $label, mixed $value): string
{
    $text = normalise_plain_mail_text($value);
    if ($text === '') {
        $text = 'Not supplied';
    }
    return $label . ': ' . str_replace("\n", "\n  ", $text);
}

function normalise_plain_mail_text(mixed $value): string
{
    if (!is_scalar($value) && $value !== null) {
        throw new InvalidArgumentException('Invalid notification text value.');
    }
    $text = str_replace(["\r\n", "\r"], "\n", (string) $value);
    $text = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $text) ?? '';
    return trim($text);
}
