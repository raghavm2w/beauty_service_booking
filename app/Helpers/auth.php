<?php

function auth(): ?array
{
    return $_SESSION['user'] ?? null;
}

function isLoggedIn(): bool
{
    return isset($_SESSION['user']);
}

function requireAuth(): void
{
    if (!isLoggedIn()) {
        redirect('/login');
    }
}