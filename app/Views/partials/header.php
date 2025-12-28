<?php
 use App\Middlewares\AuthMiddleware;
$auth = AuthMiddleware::checkAuth();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  
  <link rel="stylesheet" href="/assets/styles/app.css">
</head>
<body>
<header class="header">
  <div class="header-inner">
    <div class="header-logo">
      <span class="logo-badge">BQ</span>
      <span class="logo-text">Beauty Queen</span>
    </div>

    <nav class="header-actions">
    <?php if ($auth['loggedIn']): ?>
      <a href="#" class="header-link" onclick="logout()">Log out</a>
    <?php else: ?>
            <a href="/login" class="header-link">Login</a>
    <?php endif; ?>

    </nav>
  </div>
</header>