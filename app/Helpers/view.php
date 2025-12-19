<?php

function view(string $view, array $data = []): void
{
    extract($data);

    $viewPath = __DIR__ . '/../Views/' . str_replace('.', '/', $view) . '.php';

    if (!file_exists($viewPath)) {
        throw new Exception("View not found: {$viewPath}");
    }

    require $viewPath;
}
