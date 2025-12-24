<?php
function redirect(int $code =200,string $path)
{
      // AJAX / fetch request
    if (
        isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
        $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest'
    ) {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode([
            'redirect' => $path
        ]);
        exit;
    }
    http_response_code($code);
    header("Location: {$path}");
    exit;
}

function back()
{
    header("Location: " . ($_SERVER['HTTP_REFERER'] ?? '/'));
    exit;
}