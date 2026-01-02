<?php include "partials/header.php";?>


    <div class="booking-container">
        <h1>My Bookings</h1>
        
        <div class="tabs">
            <button class="tab-btn active" data-filter="upcoming">Upcoming</button>
            <button class="tab-btn" data-filter="cancelled">Cancelled</button>
            <button class="tab-btn" data-filter="completed">Completed</button>

        </div>

        <div id="bookings-list" class="bookings-grid">
            <!-- Bookings will be loaded here -->
            <div class="loading">Loading bookings...</div>
        </div>
    </div>


<?php include "partials/footer.php"; ?>
