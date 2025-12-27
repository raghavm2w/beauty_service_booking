<?php $currentPage = $_SERVER['REQUEST_URI']; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>admin</title>
      <link rel="stylesheet" href="/assets/styles/app.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.5/main.min.css"/> -->
</head>
<body>

<div class="app">

    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="profile">
            <div>
                <h4>ServiceAdmin</h4>
                <span>Business Manager</span>
            </div>
        </div>

        <nav class="menu">
            <a class="<?= str_contains($currentPage, 'dash') ? 'active' : '' ?> " href="/admin/dash"  >Dashboard</a>
            <a  class="<?= str_contains($currentPage, 'services') ? 'active' : '' ?> " href="/admin/services">My Services</a>
            <a class="<?= str_contains($currentPage, 'avail') ? 'active' : '' ?> " href="/admin/avail" >Availability</a>
            <a class="<?= str_contains($currentPage, 'bookings') ? 'active' : '' ?> "href="#" >Bookings</a>
            <a  class="<?= str_contains($currentPage, 'settings') ? 'active' : '' ?> " href="#" onclick="toggleSettings()">Settings</a>
            <div class="settings" >
            <a href="#" onclick="openTime()" >Timezone</a>
            </div>
        </nav>
  <div id="timeOverlay" class="modal-overlay" onclick="closeTimeForm()"> </div>

<div id="timeModal" class="modal">
    <form id="timezoneForm" class="service-form">
     <label for="timezone">Select Timezone</label>

        <select id="timezone" name="timezone">
        <option value="Asia/Kolkata">Asia/Kolkata (IST)</option>
         <option value="Europe/London">Europe/London (GMT)</option>
         <option value="America/New_York">America/New_York (EST)</option>
        <option value="America/Los_Angeles">America/Los_Angeles (PST)</option>
        </select>

        <button type="submit">Save Timezone</button>
    </form>
</div>
        <div class="logout" >
            <a href="#" onclick="logout()">Log Out</a>
        </div>
    </aside>
