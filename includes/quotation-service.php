<?php

declare(strict_types=1);

function create_quotation_request(PDO $pdo, array $customer, array $items, ?array $compatibility, string $idempotencyHash): array
{
    if (!($customer['privacy_consent'] ?? false)) {
        throw new SubmissionValidationException(['privacy_consent' => 'Consent is required.']);
    }
    $pdo->beginTransaction();
    try {
        $reference = '';
        for ($attempt = 0; $attempt < 5; $attempt++) {
            $candidate = generate_submission_reference('MHQ');
            $check = $pdo->prepare('SELECT COUNT(*) FROM quotation_requests WHERE quotation_reference = :reference');
            $check->execute(['reference' => $candidate]);
            if ((int) $check->fetchColumn() === 0) {
                $reference = $candidate;
                break;
            }
        }
        if ($reference === '') {
            throw new RuntimeException('Unable to allocate a unique quotation reference.');
        }
        $confirmationToken = bin2hex(random_bytes(32));
        $statement = $pdo->prepare('INSERT INTO quotation_requests (quotation_reference, idempotency_hash, confirmation_token_hash, customer_name, organisation, email, phone, preferred_contact_method, notes, compatibility_json, status, created_at, updated_at) VALUES (:reference, :idempotency, :confirmation_token, :name, :organisation, :email, :phone, :preferred, :notes, :compatibility, :status, :created, :updated)');
        $now = gmdate('Y-m-d H:i:s');
        $statement->execute([
            'reference' => $reference, 'idempotency' => $idempotencyHash, 'confirmation_token' => hash('sha256', $confirmationToken), 'name' => $customer['customer_name'],
            'organisation' => $customer['organisation'] ?: null, 'email' => $customer['email'], 'phone' => $customer['phone'],
            'preferred' => $customer['preferred_contact_method'], 'notes' => $customer['notes'] ?: null,
            'compatibility' => $compatibility === null ? null : json_encode($compatibility, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE),
            'status' => 'received', 'created' => $now, 'updated' => $now,
        ]);
        $requestId = (int) $pdo->lastInsertId();
        $itemStatement = $pdo->prepare('INSERT INTO quotation_request_items (quotation_request_id, product_id, product_code, product_name, brand, category, product_type, specification_json, quantity, created_at) VALUES (:request_id, :product_id, :code, :name, :brand, :category, :type, :specifications, :quantity, :created)');
        foreach ($items as $line) {
            $product = $line['product'];
            $itemStatement->execute([
                'request_id' => $requestId, 'product_id' => $product['id'], 'code' => $product['code'], 'name' => $product['name'],
                'brand' => $product['brand'], 'category' => $product['category'], 'type' => $product['type'],
                'specifications' => json_encode($product['specifications'], JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE),
                'quantity' => $line['quantity'], 'created' => $now,
            ]);
        }
        $pdo->commit();
        return ['reference' => $reference, 'confirmation_token' => $confirmationToken];
    } catch (Throwable $exception) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        throw $exception;
    }
}
