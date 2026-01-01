<?php
use App\Middlewares\AuthMiddleware;
$auth = AuthMiddleware::checkAuth();
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" />
  <link rel="stylesheet" href="/assets/styles/app.css">
</head>

<body>
  <header class="header">
    <div class="header-inner">
      <a href="/" class="header-logo">
        <span class="logo-badge">BQ</span>
        <span class="logo-text">Beauty Queen</span>
      </a>

      <nav class="header-actions">
        <a href="/services" class="header-link">Services</a>

        <?php if ($auth['loggedIn']): ?>
          <a href="/bookings" class="header-link">Bookings</a>
          <div class="header-dropdown">
            <button class="header-link icon-btn" id="menuBtn">
              <i class="fa fa-bars"></i>
            </button>
            <div class="dropdown-menu" id="dropdownMenu">
              <a href="/user/profile/edit" class="dropdown-item">Edit Profile</a>
              <a href="#" class="dropdown-item" onclick="logout()">Log out</a>
            </div>
          </div>

        <?php else: ?>
          <a href="/login" class="header-link">Login</a>
        <?php endif; ?>


      </nav>
    </div>
  </header>
  <script src="/assets/scripts/components/header.js"></script>