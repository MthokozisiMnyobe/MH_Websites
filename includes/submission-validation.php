<?php

declare(strict_types=1);

function enquiry_type_registry(): array
{
    return [
        'custom-software' => ['form_label' => 'Custom Software Development', 'email_label' => 'Custom Software Development'],
        'web-development' => ['form_label' => 'Website & E-commerce Development', 'email_label' => 'Web Development'],
        'learning-platforms' => ['form_label' => 'Learning Platforms & Moodle', 'email_label' => 'Learning Platforms & Moodle'],
        'data-automation' => ['form_label' => 'Dashboards, Data & Automation', 'email_label' => 'Dashboards, Data & Automation'],
        'ict-support' => ['form_label' => 'ICT Support & Systems Integration', 'email_label' => 'ICT Support & Systems Integration'],
        'technology-products' => ['form_label' => 'Technology Products / Accessories', 'email_label' => 'Technology Products / Accessories'],
        'general' => ['form_label' => 'General Enquiry', 'email_label' => 'General Enquiry'],
    ];
}

function enquiry_type_options(): array
{
    $options = [];
    foreach (enquiry_type_registry() as $key => $record) {
        $options[$key] = $record['form_label'];
    }
    return $options;
}

function enquiry_type_email_label(string $type): ?string
{
    $record = enquiry_type_registry()[$type] ?? null;
    return is_array($record) ? (string) $record['email_label'] : null;
}

final class SubmissionValidationException extends RuntimeException
{
    public function __construct(public readonly array $errors)
    {
        parent::__construct('Submission validation failed.');
    }
}

function submission_text(mixed $value, string $field, int $min, int $max, bool $required = true): string
{
    $value = trim(is_string($value) ? $value : '');
    $length = mb_strlen($value);
    if (($required && $length < $min) || $length > $max || preg_match('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', $value)) {
        throw new SubmissionValidationException([$field => 'Please enter a valid value.']);
    }
    return $value;
}

function submission_email(mixed $value): string
{
    $email = submission_text($value, 'email', 3, 254);
    if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
        throw new SubmissionValidationException(['email' => 'Please enter a valid email address.']);
    }
    return $email;
}

function submission_phone(mixed $value, bool $required): string
{
    $phone = submission_text($value, 'phone', $required ? 7 : 0, 40, $required);
    if ($phone !== '' && (preg_match('/^\+?[0-9][0-9\s().-]{5,38}[0-9]$/', $phone) !== 1 || strlen(preg_replace('/\D/', '', $phone)) < 7)) {
        throw new SubmissionValidationException(['phone' => 'Please enter a valid phone number.']);
    }
    return $phone;
}

function submission_choice(mixed $value, string $field, array $allowed): string
{
    $value = is_string($value) ? $value : '';
    if (!in_array($value, $allowed, true)) {
        throw new SubmissionValidationException([$field => 'Please choose a valid option.']);
    }
    return $value;
}

function validate_quotation_customer(array $input): array
{
    $preferred = submission_choice($input['preferred_contact'] ?? null, 'preferred_contact', ['email', 'phone']);
    $consent = in_array($input['privacy_consent'] ?? null, ['1', 1, true], true);
    if (!$consent) {
        throw new SubmissionValidationException(['privacy_consent' => 'Consent is required.']);
    }
    return [
        'customer_name' => submission_text($input['full_name'] ?? null, 'full_name', 2, 120),
        'organisation' => submission_text($input['organisation'] ?? '', 'organisation', 0, 160, false),
        'email' => submission_email($input['email'] ?? null),
        'phone' => submission_phone($input['phone'] ?? null, true),
        'preferred_contact_method' => $preferred,
        'notes' => submission_text($input['quotation_notes'] ?? '', 'quotation_notes', 0, 2000, false),
        'privacy_consent' => true,
    ];
}

function validate_basket_payload(mixed $json): array
{
    if (!is_string($json) || strlen($json) > (int) config('submissions.max_body_bytes', 65536)) {
        throw new SubmissionValidationException(['basket' => 'The basket data is invalid.']);
    }
    try {
        $lines = json_decode($json, true, 16, JSON_THROW_ON_ERROR);
    } catch (JsonException) {
        throw new SubmissionValidationException(['basket' => 'The basket data is invalid.']);
    }
    if (!is_array($lines) || $lines === [] || count($lines) > (int) config('submissions.max_basket_lines', 50) || !array_is_list($lines)) {
        throw new SubmissionValidationException(['basket' => 'Select at least one valid product.']);
    }
    $seen = [];
    $validated = [];
    foreach ($lines as $line) {
        if (!is_array($line) || array_diff(array_keys($line), ['id', 'quantity']) !== [] || !isset($line['id'], $line['quantity'])) {
            throw new SubmissionValidationException(['basket' => 'The basket contains untrusted fields.']);
        }
        $id = is_string($line['id']) ? $line['id'] : '';
        $quantity = filter_var($line['quantity'], FILTER_VALIDATE_INT);
        $product = catalogue_product($id);
        if ($id === '' || isset($seen[$id]) || $quantity === false || $quantity < 1 || $quantity > 99 || $product === null) {
            throw new SubmissionValidationException(['basket' => 'The basket contains an invalid product or quantity.']);
        }
        $seen[$id] = true;
        $validated[] = ['product' => $product, 'quantity' => $quantity];
    }
    return $validated;
}

function validate_compatibility_draft(array $input): ?array
{
    $hasContent = false;
    foreach (['device_type', 'manufacturer', 'model', 'current_component', 'product_id', 'notes'] as $key) {
        $hasContent = $hasContent || trim((string) ($input[$key] ?? '')) !== '';
    }
    if (!$hasContent) {
        return null;
    }
    $productId = submission_text($input['product_id'] ?? '', 'product_id', 0, 100, false);
    if ($productId !== '' && catalogue_product($productId) === null) {
        throw new SubmissionValidationException(['product_id' => 'Please choose a valid catalogue product.']);
    }
    return [
        'device_type' => submission_choice($input['device_type'] ?? null, 'device_type', ['Printer', 'Laptop or computer', 'Other technology device']),
        'manufacturer' => submission_text($input['manufacturer'] ?? null, 'manufacturer', 2, 80),
        'model' => submission_text($input['model'] ?? null, 'model', 1, 120),
        'current_component' => submission_text($input['current_component'] ?? '', 'current_component', 0, 120, false),
        'product_id' => $productId,
        'notes' => submission_text($input['notes'] ?? '', 'notes', 0, 1200, false),
    ];
}

function validate_enquiry(array $input, array $interestOptions): array
{
    $preferred = submission_choice($input['preferred_contact'] ?? null, 'preferred_contact', ['email', 'phone']);
    $consent = in_array($input['privacy_consent'] ?? null, ['yes', '1', 1, true], true);
    if (!$consent) {
        throw new SubmissionValidationException(['privacy_consent' => 'Consent is required.']);
    }
    return [
        'customer_name' => submission_text($input['full_name'] ?? null, 'full_name', 2, 120),
        'organisation' => submission_text($input['organisation'] ?? '', 'organisation', 0, 160, false),
        'email' => submission_email($input['email'] ?? null),
        'phone' => submission_phone($input['phone'] ?? '', $preferred === 'phone'),
        'enquiry_type' => submission_choice($input['enquiry_type'] ?? null, 'enquiry_type', array_keys($interestOptions)),
        'preferred_contact_method' => $preferred,
        'message' => submission_text($input['project_summary'] ?? null, 'project_summary', 20, 3000),
        'privacy_consent' => true,
    ];
}
