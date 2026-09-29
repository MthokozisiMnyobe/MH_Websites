<?php

declare(strict_types=1);

final class RateLimitExceededException extends RuntimeException
{
}

function rate_limit_key(string $scope, string $ip): string
{
    $key = (string) config('app.key', '');
    if ($key === '') {
        throw new RuntimeException('Application key is not configured.');
    }
    return hash_hmac('sha256', $scope . "\0" . $ip, $key);
}

function enforce_submission_rate_limit(PDO $pdo, string $scope, ?DateTimeImmutable $now = null): void
{
    if (!in_array($scope, ['quotation', 'enquiry'], true)) {
        throw new InvalidArgumentException('Invalid rate-limit scope.');
    }
    $now ??= new DateTimeImmutable();
    $window = max(60, (int) config('rate_limit.window_seconds', 900));
    $limit = max(1, (int) config('rate_limit.attempts', 6));
    $bucket = intdiv($now->getTimestamp(), $window) * $window;
    $key = rate_limit_key($scope, client_ip_address());
    $driver = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
    if ($driver === 'sqlite') {
        $sql = 'INSERT INTO submission_rate_limits (scope, key_hash, window_started_at, attempt_count, updated_at) VALUES (:scope, :key_hash, :window, 1, :updated) '
            . 'ON CONFLICT(scope, key_hash, window_started_at) DO UPDATE SET attempt_count = attempt_count + 1, updated_at = excluded.updated_at';
    } else {
        $sql = 'INSERT INTO submission_rate_limits (scope, key_hash, window_started_at, attempt_count, updated_at) VALUES (:scope, :key_hash, :window, 1, :updated) '
            . 'ON DUPLICATE KEY UPDATE attempt_count = attempt_count + 1, updated_at = VALUES(updated_at)';
    }
    $stamp = gmdate('Y-m-d H:i:s', $bucket);
    $statement = $pdo->prepare($sql);
    $statement->execute(['scope' => $scope, 'key_hash' => $key, 'window' => $stamp, 'updated' => $now->format('Y-m-d H:i:s')]);
    $check = $pdo->prepare('SELECT attempt_count FROM submission_rate_limits WHERE scope = :scope AND key_hash = :key_hash AND window_started_at = :window');
    $check->execute(['scope' => $scope, 'key_hash' => $key, 'window' => $stamp]);
    if ((int) $check->fetchColumn() > $limit) {
        throw new RateLimitExceededException('Too many submission attempts. Please wait and try again.');
    }
}
