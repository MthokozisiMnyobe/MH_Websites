<?php

declare(strict_types=1);

function flash_set(string $key, string $message): void
{
    ensure_session_started();
    $_SESSION['_flash'][$key] = $message;
}

function flash_get(string $key): ?string
{
    ensure_session_started();
    $message = $_SESSION['_flash'][$key] ?? null;
    unset($_SESSION['_flash'][$key]);
    return is_string($message) ? $message : null;
}

function flash_peek(string $key): ?string
{
    ensure_session_started();
    $message = $_SESSION['_flash'][$key] ?? null;
    return is_string($message) ? $message : null;
}
