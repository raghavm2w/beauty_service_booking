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
                <option value="">All</option>
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
                    <th data-sort="customer_name">Customer <i class="fa-solid fa-sort"></i></th>
                    <th data-sort="service_name">Service <i class="fa-solid fa-sort"></i></th>
                    <th data-sort="start_time">Date <i class="fa-solid fa-sort"></i></th>
                    <th>Time</th>
                    <th data-sort="duration">Duration <i class="fa-solid fa-sort"></i></th>
                    <th data-sort="price">Price <i class="fa-solid fa-sort"></i></th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="bookingsTableBody">
            </tbody>
        </table>
        
        <div class="pagination">
            <span id="paginationInfo">Showing 0-0 of 0</span>
            <div class="pagination-controls">
                <button id="prevBookingsBtn" disabled>Previous</button>
                <button id="nextBookingsBtn" disabled>Next</button>
            </div>
        </div>
    </div>

</main>

<div id="custom-alert" class="alert-box"></div>
<script src="/assets/scripts/sidebar.js"></script>
<script src="/assets/scripts/admin-bookings.js"></script>
</body>
</html>