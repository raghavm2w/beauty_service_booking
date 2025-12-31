const bookingModal = document.getElementById("bookingModal");
const closeModal = document.getElementById("closeModal");
const slotsContainer = document.getElementById("slotsContainer");
const slotsGrid = document.getElementById("slotsGrid");
const confirmBtn = document.getElementById("confirmBooking");

let selectedProviderId = null;
let selectedServiceId = null;
let datePickerInstance = null;

let selectedSlot = null;
function openBookNow(btn) {
  bookingModal.classList.add("active");

  selectedProviderId = btn.dataset.providerId;
  selectedServiceId = btn.dataset.serviceId;

  slotsGrid.innerHTML = "";
  slotsContainer.classList.add("hidden");
  confirmBtn.disabled = true;
  selectedSlot = null;

  fetch(`/user/weekly-availability?provider_id=${selectedProviderId}`)
    .then(res => res.json())
    .then(res => {
      if (res.status !== "success") {
        alert("Failed to load availability");
        return;
      }

      const workingDays = res.data.workingDays;

      if (datePickerInstance) {
        datePickerInstance.destroy();
      }

      datePickerInstance = flatpickr("#datePicker", {
        minDate: "today",
        disable: [
          date => !workingDays.includes(date.getDay())
        ],
        onChange: function (selectedDates, dateStr) {
          fetchAvailableSlots(dateStr);
        }
      });
    })
    .catch((err) => {
      console.log("error in loading avialability", err.message);
      showAlert("Error loading availability");
    });
}
closeModal.onclick = () => bookingModal.classList.remove("active");
bookingModal.addEventListener("click", (e) => {
  if (e.target === bookingModal) {
    bookingModal.classList.remove("active");
  }
});

function fetchAvailableSlots(date) {
  slotsGrid.innerHTML = "";
  slotsContainer.classList.remove("hidden");
  confirmBtn.disabled = true;
  selectedSlot = null;

  fetch(
    `/user/service-slots?provider_id=${selectedProviderId}&service_id=${selectedServiceId}&date=${date}`
  )
    .then(res => res.json())
    .then(data => {
      if (data.status === "error") {
        slotsGrid.innerHTML = `<p class="no-slots">${data.message}</p>`;
        return;
      }
      renderSlots(data.data);
    })
    .catch((err) => {
      slotsGrid.innerHTML = `<p class="no-slots">Failed to load slots</p>`;
      console.error(err);
    });
}

/* Render slots */
function renderSlots(slots) {
  if (slots.length === 0) {
    slotsGrid.innerHTML = `<p class="no-slots">No slots available</p>`;

  }
  slots.forEach(slot => {
    const btn = document.createElement("button");
    btn.className = "slot-btn";
    btn.textContent = `${slot.start_time} - ${slot.end_time}`;

    if (slot.status === 'booked') {
      btn.classList.add("disabled");
      btn.disabled = true;
    }

    btn.onclick = () => selectSlot(btn, slot);

    slotsGrid.appendChild(btn);
  });
}

/* Select slot */
function selectSlot(button, slot) {
  document
    .querySelectorAll(".slot-btn")
    .forEach(b => b.classList.remove("active"));

  button.classList.add("active");
  selectedSlot = slot;
  confirmBtn.disabled = false;
}

/* Confirm booking */


confirmBtn.onclick = () => {
  if (!selectedSlot) return;
  if (!window.IS_LOGGED_IN) {
    showAlert("You must be logged in to book service");

    setTimeout(() => {
      window.location.href = '/login';
    }, 1500);
    return;
  }

  const payload = {
    provider_id: selectedProviderId,
    service_id: selectedServiceId,
    date: datePickerInstance.input.value, 
    start_time: selectedSlot.start_time,
    end_time: selectedSlot.end_time
  };

  fetch('/user/book-slot', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify(payload)
  })
    .then(res => res.json())
    .then(data => {
      if (data.status === 'success') {
        showAlert("Booking confirmed! Redirecting to payment...", "success");
        bookingModal.classList.remove("active");
        setTimeout(() => {
          window.location.href = `/payments?booking_id=${data.data.booking_id}`;
        }, 1500);
      } else {
        showAlert(data.message || "Booking failed", "error");
      }
    })
    .catch(err => {
      console.error("Booking error:", err);
      showAlert("An error occurred while booking", "error");
    });
};
