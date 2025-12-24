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
            <a data-page="/admin/dash" class="active" onclick="toggleActive(event,this)">Dashboard</a>
            <a data-page="/admin/services" onclick="toggleActive(event,this)">My Services</a>
            <a href="/admin/avail" onclick="toggleActive(event,this)">Availability</a>
            <a href="#" onclick="toggleActive(event,this)">Bookings</a>
            <a href="#" onclick="toggleActive(event,this)">Settings</a>
        </nav>

        <div class="logout" >
            <a href="#" onclick="logout()">Log Out</a>
        </div>
    </aside>
