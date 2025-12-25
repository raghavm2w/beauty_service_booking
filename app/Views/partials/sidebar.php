<?php $currentPage = $_SERVER['REQUEST_URI']; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="/assets/styles/app.css">
</head>
<body>

<div class="app">

    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="profile">
            <img src="https://i.pravatar.cc/60" alt="Avatar">
            <div>
                <h4>ServiceAdmin</h4>
                <span>Business Manager</span>
            </div>
        </div>

        <nav class="menu">
            <a class="<?= str_contains($currentPage, 'dash') ? 'active' : '' ?> " href="/admin/dash"  >Dashboard</a>
            <a  class="<?= str_contains($currentPage, 'services') ? 'active' : '' ?> " href="/admin/services">My Services</a>
            <a class="<?= str_contains($currentPage, 'avail') ? 'active' : '' ?> " href="/admin/avail" >Availability</a>
            <a oclass="<?= str_contains($currentPage, 'bookings') ? 'active' : '' ?> "href="#" >Bookings</a>
            <a  class="<?= str_contains($currentPage, 'settings') ? 'active' : '' ?> " href="#" >Settings</a>
        </nav>

        <div class="logout" >
            <a href="#" onclick="logout()">Log Out</a>
        </div>
    </aside>
