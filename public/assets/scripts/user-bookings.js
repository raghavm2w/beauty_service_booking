document.addEventListener('DOMContentLoaded', () => {
    const tabs = document.querySelectorAll('.tab-btn');
    const bookingsList = document.getElementById('bookings-list');
    let currentFilter = 'upcoming';

    // Initial load
    loadBookings(currentFilter);

    // Tab switching
    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            tabs.forEach(t => t.classList.remove('active'));
            tab.classList.add('active');
            currentFilter = tab.dataset.filter;
            loadBookings(currentFilter);
        });
    });

    function loadBookings(filter) {
        bookingsList.innerHTML = '<div class="loading">Loading...</div>';

        fetch(`/user/my-bookings?filter=${filter}`)
            .then(res => res.json())
            .then(res => {
                if (res.status === "success") {
                    renderBookings(res.data, filter);
                } else {
                    bookingsList.innerHTML = `<div class="no-bookings">${res.message || 'Failed to load bookings'}</div>`;
                }
            })
            .catch(err => {
                console.error(err);
                bookingsList.innerHTML = '<div class="no-bookings">An error occurred</div>';
            });
    }

    function renderBookings(bookings, filter) {
        if (!bookings || bookings.length === 0) {
            bookingsList.innerHTML = '<div class="no-bookings">No bookings found in this category.</div>';
            return;
        }

        bookingsList.innerHTML = bookings.map(booking => `
            <div class="booking-card">
                <div class="card-header">
                    <div class="service-name">${booking.service_name}</div>
                     <div class="status-badge status-${filter}">
                        ${filter.charAt(0).toUpperCase() + filter.slice(1)}
                    </div>
                </div>
                <div class="card-body">
                    <p><strong>Provider:</strong> ${booking.provider_name}</p>
                    <p><strong>Date:</strong> ${formatDate(booking.start_time)}</p>
                    <p><strong>Time:</strong> ${formatTime(booking.start_time)} - ${formatTime(booking.end_time)}</p>
                    <p><strong>Duration:</strong> ${booking.duration} min</p>
                    <div class="price">$${booking.price}</div>

                    
                    ${filter === 'upcoming' ? `
                        <button class="btn-cancel" onclick="cancelBooking(${booking.id})">
                            Cancel Booking
                        </button>
                    ` : ''}
                </div>
            </div>
        `).join('');
    }

    function formatDate(dateString) {
        const options = { year: 'numeric', month: 'long', day: 'numeric' };
        return new Date(dateString.replace(" ", "T")).toLocaleDateString('en-US', options);
    }

    function formatTime(dateString) {
        const options = { hour: '2-digit', minute: '2-digit' };
        return new Date(dateString.replace(" ", "T")).toLocaleTimeString('en-US', options);
    }

    window.cancelBooking = function (id) {
        if (!confirm('Are you sure you want to cancel this booking?')) return;

        fetch('/user/cancel-booking', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ booking_id: id })
        })
            .then(res => res.json())
            .then(res => {
                if (res.status === "success") {
                    loadBookings(currentFilter);
                    showAlert("Booking cancellation success","success");
                } else {
                    showAlert(res.message || "Failed to cancel","error");
                }
            })
            .catch(err => {
                console.error(err);
                alert("An error occurred");
            });
    };
});
