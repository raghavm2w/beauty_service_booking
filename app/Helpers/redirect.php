<?php
function redirect(string $path)
{
    header("Location: {$path}");
    exit;
}

function back()
{
    header("Location: " . ($_SERVER['HTTP_REFERER'] ?? '/'));
    exit;
}