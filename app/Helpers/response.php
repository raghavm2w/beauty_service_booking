<?php

function jsonResponse(
    int $code = 200,
    string $status,
    string $message = '',
    array $data = [],
    array $errors = [],
) {
    http_response_code($code);
    header('Content-Type: application/json');

    echo json_encode([
        'status'  => $status,
        'message' => $message,
        'data'    => $data,
        'errors'  => $errors,
    ]);

    exit;
}

function success(
        int $code = 200,
    string $message = '',
    array $data = []
) {
    jsonResponse($code,'success', $message, $data, []);
}

function error(
        int $code = 400,
    string $message = '',
    array $errors = []
) {
    jsonResponse($code,'error', $message, [], $errors);
}
