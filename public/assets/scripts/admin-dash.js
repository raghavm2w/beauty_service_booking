document.addEventListener('DOMContentLoaded', () => {
    fetchTodayBookings();
});

function fetchTodayBookings() {
    fetch('/admin/get-today-bookings')
        .then(response => response.json())
        .then(data => {
            const emptyState = document.getElementById('empty-state');
            const tableContainer = document.getElementById('table-container');

            if (data.data && data.data.length > 0) {
                renderBookings(data.data);
                if (emptyState) emptyState.style.display = 'none';
                if (tableContainer) tableContainer.style.display = 'block';
            } else {
                if (emptyState) emptyState.style.display = 'block';
                if (tableContainer) tableContainer.style.display = 'none';
            }
        })
        .catch(error => console.error('Error fetching bookings:', error));
}

function renderBookings(bookings) {
    const tbody = document.getElementById('booking-list-body');
    tbody.innerHTML = '';
    bookings.forEach(booking => {
        const row = document.createElement('tr');

        // Time Slot
        const timeCell = document.createElement('td');
        const timeSlot = document.createElement('div');
        timeSlot.className = 'time-slot';
        timeSlot.innerHTML = `<i class="fa-regular fa-clock"></i> 
            ${formatTime(booking.start_time)} - ${formatTime(booking.end_time)}`;
        timeCell.appendChild(timeSlot);

        // Service
        const serviceCell = document.createElement('td');
        serviceCell.innerHTML = `<div class="service-info"><span class="service-name">${escapeHtml(booking.service_name)}</span></div>`;

        // Client
        const clientCell = document.createElement('td');
        const clientAvatar = booking.customer_name ? booking.customer_name.charAt(0).toUpperCase() : '?';
        clientCell.innerHTML = `
            <div class="client-info">
                <div class="client-avatar">${clientAvatar}</div>
                <span>${escapeHtml(booking.customer_name)}</span>
            </div>`;

        // Status
        const statusCell = document.createElement('td');
        const statusBadge = document.createElement('span');
        if (booking.status == 1) {
            statusBadge.className = 'status-badge upcoming';
            statusBadge.textContent = 'Upcoming';
        } else if (booking.status == 2) {
            statusBadge.className = 'status-badge completed';
            statusBadge.textContent = 'Completed';
        }
        statusCell.appendChild(statusBadge);

        // Actions
        const actionsCell = document.createElement('td');
        if (booking.status == 1) {
            const actionsDiv = document.createElement('div');
            actionsDiv.className = 'action-buttons';

            const completeBtn = document.createElement('button');
            completeBtn.className = 'btn-icon complete';
            completeBtn.title = 'Complete Booking';
            completeBtn.innerHTML = '<i class="fa-solid fa-check"></i>';
            completeBtn.onclick = () => completeBooking(booking.id);

            const cancelBtn = document.createElement('button');
            cancelBtn.className = 'btn-icon cancel';
            cancelBtn.title = 'Cancel Booking';
            cancelBtn.innerHTML = '<i class="fa-solid fa-xmark"></i>';
            cancelBtn.onclick = () => cancelBooking(booking.id);

            actionsDiv.appendChild(completeBtn);
            actionsDiv.appendChild(cancelBtn);
            actionsCell.appendChild(actionsDiv);
        }

        row.appendChild(timeCell);
        row.appendChild(serviceCell);
        row.appendChild(clientCell);
        row.appendChild(statusCell);
        row.appendChild(actionsCell);

        tbody.appendChild(row);
    });
}

function completeBooking(bookingId) {
    if (!confirm('Mark this booking as completed?')) return;

    fetch('/admin/complete-booking', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ booking_id: bookingId })
    })
        .then(response => response.json())
        .then(data => {
            if (data.status === "success") {
                fetchTodayBookings();
            } else {
                showAlert('Failed to complete booking', "error");
            }
        })
        .catch(error => console.error('Error:', error));
}

function cancelBooking(bookingId) {
    if (!confirm('Are you sure you want to cancel this booking?')) return;

    fetch('/admin/cancel-booking', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ booking_id: bookingId })
    })
        .then(response => response.json())
        .then(data => {
            if (data.status === "success") {
                fetchTodayBookings();
            } else {
                showAlert('Failed to cancel booking');
            }
        })
        .catch(error => console.error('Error:', error));
}

function formatTime(dateString) {
    const date = new Date(dateString);
    let hours = date.getHours();
    const minutes = date.getMinutes();
    const ampm = hours >= 12 ? 'PM' : 'AM';
    hours = hours % 12;
    hours = hours ? hours : 12; // the hour '0' should be '12'
    const strMinutes = minutes < 10 ? '0' + minutes : minutes;
    return hours + ':' + strMinutes + ' ' + ampm;
}

function escapeHtml(text) {
    if (!text) return '';
    return text
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}
