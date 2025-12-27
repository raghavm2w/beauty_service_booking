<?php include __DIR__."/../partials/sidebar.php"; ?>
 <main id="content" class="main">
        <div class="breadcrumb">Home › Availability</div>

<div class="availability-container">

  <div class="quick-setup">
        <div class="calendar-title">
    <h3>Set Working Hours</h3>
    <input type="time" id="defaultStart">
    <input type="time" id="defaultEnd">
    </div>
    <button id="applyAll">Apply to All Days</button>
  </div>

  <!-- Calendar -->
  <div id="calendar"></div>

</div>

<!-- Modal -->
<div id="dayModal" class="avail-modal">
  <div class="modal-content">
    <h3 id="modalDay"></h3>
    <h4 id="modalDate"></h4>
    <label>Start Time</label>
    <input type="time" id="dayStart">

    <label>End Time</label>
    <input type="time" id="dayEnd">

    <div class="modal-actions">
      <button id="saveDay">Save</button>
      <button id="markOff">Day Off</button>
      <button onclick="closeModal()">Cancel</button>
    </div>
  </div>
</div>


 </main>
</div>
         <div id="custom-alert" class="alert-box"></div>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/6.1.19/index.global.min.js"></script>
       <!-- <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.5/main.min.js"> -->
</script>
    <script src="/assets/scripts/sidebar.js"></script>
    <script src="/assets/scripts/avail.js"></script>

</body>
</html>