<?php

declare(strict_types=1);

function create_enquiry(PDO $pdo, array $enquiry, string $idempotencyHash): string
{
    $pdo->beginTransaction();
    try {
        $reference = '';
        for ($attempt = 0; $attempt < 5; $attempt++) {
            $candidate = generate_submission_reference('MHE');
            $check = $pdo->prepare('SELECT COUNT(*) FROM enquiries WHERE enquiry_reference = :reference');
            $check->execute(['reference' => $candidate]);
            if ((int) $check->fetchColumn() === 0) { $reference = $candidate; break; }
        }
        if ($reference === '') { throw new RuntimeException('Unable to allocate a unique enquiry reference.'); }
        $statement = $pdo->prepare('INSERT INTO enquiries (enquiry_reference, idempotency_hash, customer_name, organisation, email, phone, enquiry_type, preferred_contact_method, message, status, created_at, updated_at) VALUES (:reference, :idempotency, :name, :organisation, :email, :phone, :type, :preferred, :message, :status, :created, :updated)');
        $now = gmdate('Y-m-d H:i:s');
        $statement->execute([
            'reference' => $reference, 'idempotency' => $idempotencyHash, 'name' => $enquiry['customer_name'],
            'organisation' => $enquiry['organisation'] ?: null, 'email' => $enquiry['email'], 'phone' => $enquiry['phone'] ?: null,
            'type' => $enquiry['enquiry_type'], 'preferred' => $enquiry['preferred_contact_method'], 'message' => $enquiry['message'],
            'status' => 'received', 'created' => $now, 'updated' => $now,
        ]);
        $pdo->commit();
        return $reference;
    } catch (Throwable $exception) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        throw $exception;
    }
}
