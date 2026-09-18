<?php

declare(strict_types=1);

function database_is_configured(): bool
{
    return config('database.host', '') !== ''
        && config('database.name', '') !== ''
        && config('database.user', '') !== '';
}

function database_connection(): ?PDO
{
    static $connection = null;
    static $attempted = false;

    if ($connection instanceof PDO) {
        return $connection;
    }
    if ($attempted || !database_is_configured()) {
        return null;
    }

    $attempted = true;
    $host = (string) config('database.host');
    $port = (int) config('database.port', 3306);
    $name = (string) config('database.name');
    $charset = (string) config('database.charset', 'utf8mb4');
    $dsn = "mysql:host={$host};port={$port};dbname={$name};charset={$charset}";

    try {
        $connection = new PDO(
            $dsn,
            (string) config('database.user'),
            (string) config('database.password'),
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]
        );
    } catch (PDOException $exception) {
        safe_log('Database connection unavailable.', ['exception' => $exception::class]);
        return null;
    }

    return $connection;
}
