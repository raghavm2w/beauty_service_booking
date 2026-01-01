<?php include __DIR__."/../partials/sidebar.php"; ?>

<main id="content" class="main">
    <div class="breadcrumb">Home > Bookings</div>

    <div class="page-header">
        <div class="header-info">
            <h1>Bookings Management</h1>
            <p>View and manage all your service appointments.</p>
        </div>
    </div>

    <!-- Filters & Search -->
    <div class="table-header">
        <div class="filters">
            <select id="statusFilter" class="filter-select">
                <option value="">All Statuses</option>
                <option value="pending">Pending</option>
                <option value="confirmed">Confirmed</option>
                <option value="completed">Completed</option>
                <option value="cancelled">Cancelled</option>
            </select>
        </div>
        <input type="text" id="searchBookings" placeholder="Search customer or service...">
    </div>

    <!-- Bookings Table -->
    <div class="table-wrapper">
        <table id="bookingsTable">
            <thead>
                <tr>
                    <th>Customer</th>
                    <th>Service</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Duration</th>
                    <th>Price</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="bookingsTableBody">
                <!-- Rows will be injected by JS -->
            </tbody>
        </table>
        
        <div class="pagination">
            <span id="paginationInfo">Showing 0-0 of 0</span>
            <div class="pagination-controls">
                <button id="prevBtn" disabled>Previous</button>
                <button id="nextBtn" disabled>Next</button>
            </div>
        </div>
    </div>

</main>

<div id="custom-alert" class="alert-box"></div>
<script src="/assets/scripts/sidebar.js"></script>
<script src="/assets/scripts/admin-bookings.js"></script>
</body>
</html>