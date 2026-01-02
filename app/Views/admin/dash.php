<?php include __DIR__."/../partials/sidebar.php"; ?>
 <link rel="stylesheet" href="/assets/styles/pages/dashboard.css">
 <main id="content" class="main">
        <div class="breadcrumb">Home › Dashboard</div>

        <div class="page-header">
            <div>
                <h1>Dashboard</h1>
                <p>Welcome back, <?= $user['name'] ?? 'admin'?>! Here's an overview of your business performance.</p>
            </div>
        </div>

        <div class="dashboard-stats">
            <!-- Today's Bookings -->
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon blue">
                        <i class="fa-solid fa-calendar-day"></i>
                    </div>
                    <div class="stat-title">Today's Bookings</div>
                </div>
                <div class="stat-value"><?= $stats['today_bookings'] ?? 0 ?></div>
                <div class="stat-trend">Scheduled for today</div>
            </div>

            <!-- Upcoming Bookings -->
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon purple">
                        <i class="fa-solid fa-calendar-check"></i>
                    </div>
                    <div class="stat-title">Upcoming</div>
                </div>
                <div class="stat-value"><?= $stats['upcoming_bookings'] ?? 0 ?></div>
                <div class="stat-trend">Future confirmed bookings</div>
            </div>

            <!-- Total Revenue -->
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon green">
                        <i class="fa-solid fa-indian-rupee-sign"></i>
                    </div>
                    <div class="stat-title">Monthly Revenue</div>
                </div>
                <div class="stat-value">₹<?= number_format($stats['month_revenue'] ?? 0, 2) ?></div>
                <div class="stat-trend">For this month</div>
            </div>

            <!-- Total Services -->
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon orange">
                        <i class="fa-solid fa-scissors"></i>
                    </div>
                    <div class="stat-title">Total Services</div>
                </div>
                <div class="stat-value"><?= $totalServices ?? 0 ?></div>
                <div class="stat-trend">Active services listed</div>
            </div>
        </div>
        
        <!-- Today's Bookings List -->
        <div class="booking-list-container">
            <div class="list-header">
                <h2>Today's Schedule</h2>
                <div class="date-badge">
                    <i class="fa-regular fa-calendar"></i>
                    <?= date('F j, Y') ?>
                </div>
            </div>

            <div id="empty-state" class="empty-state" style="display: none;">
                <div class="empty-icon">
                    <i class="fa-solid fa-mug-hot"></i>
                </div>
                <h3>No bookings for today</h3>
                <p>Enjoy your free time or manage your services.</p>
            </div>

            <div id="table-container" class="table-responsive" style="display: none;">
                <table class="booking-table">
                    <thead>
                        <tr>
                            <th>Time Slot</th>
                            <th>Service</th>
                            <th>Client</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="booking-list-body">
                        <!-- Populated by JS -->
                    </tbody>
                </table>
            </div>
        </div>
        
        </main>

</div>
    <div id="custom-alert" class="alert-box"></div>

    <script src="/assets/scripts/sidebar.js"></script>
    <script src="/assets/scripts/admin-dash.js"></script>

</body>
</html>