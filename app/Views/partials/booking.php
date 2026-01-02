
  <!-- Booking Modal -->
  <div class="booking-modal" id="bookingModal">
    <div class="booking-card">
  <div class="booking-header">
      <h2>Select Date & Time</h2>
      <button class="close-btn" id="closeModal">&times;</button>
    </div>
      <!-- Date Picker -->
      <input
        type="text"
        id="datePicker"
        placeholder="Select a date"
        readonly
      />

      <!-- Slots -->
      <div id="slotsContainer" class="slots hidden">
        <!-- <h3>Available Time Slots</h3> -->
        <div class="slots-grid" id="slotsGrid"></div>
      </div>

      <div class="actions">
        <button class="confirm" id="confirmBooking" disabled>
          Confirm Booking
        </button>
      </div>

    </div>
  </div>
<script>
  window.IS_LOGGED_IN = <?= $auth['loggedIn'] ? 'true' : 'false' ?>;
</script>
 