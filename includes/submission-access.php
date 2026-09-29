<?php

declare(strict_types=1);

function establish_submission_grant(string $scope, string $reference, string $token): void
{
    ensure_session_started();
    $_SESSION['_submission_grants'][$scope] = [
        'reference' => $reference,
        'token' => $token,
        'expires' => time() + (int) config('submissions.confirmation_ttl', 1800),
    ];
}

function authorised_submission_grant(string $scope): ?array
{
    ensure_session_started();
    $grant = $_SESSION['_submission_grants'][$scope] ?? null;
    if (!is_array($grant) || !is_string($grant['reference'] ?? null) || (int) ($grant['expires'] ?? 0) < time()) {
        unset($_SESSION['_submission_grants'][$scope]);
        return null;
    }
    if (!is_string($grant['token'] ?? null) || strlen($grant['token']) !== 64) {
        return null;
    }
    return ['reference' => $grant['reference'], 'token_hash' => hash('sha256', $grant['token'])];
}

function load_authorised_quotation(PDO $pdo): ?array
{
    $grant = authorised_submission_grant('quotation');
    if ($grant === null) {
        return null;
    }
    $statement = $pdo->prepare('SELECT quotation_reference, compatibility_json, created_at FROM quotation_requests WHERE quotation_reference = :reference AND confirmation_token_hash = :token_hash');
    $statement->execute($grant);
    $request = $statement->fetch();
    if (!is_array($request)) {
        return null;
    }
    $items = $pdo->prepare('SELECT product_code, product_name, product_type, quantity FROM quotation_request_items WHERE quotation_request_id = (SELECT id FROM quotation_requests WHERE quotation_reference = :reference) ORDER BY id');
    $items->execute(['reference' => $grant['reference']]);
    $request['items'] = $items->fetchAll();
    $request['compatibility'] = $request['compatibility_json'] ? json_decode((string) $request['compatibility_json'], true) : null;
    unset($request['compatibility_json']);
    return $request;
}
