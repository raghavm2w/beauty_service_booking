document.addEventListener('DOMContentLoaded', () => {
    let currentPage = 1;
    const limit = 10;

    // Elements
    const tableBody = document.getElementById('bookingsTableBody');
    const prevBtn = document.getElementById('prevBookingsBtn');
    const nextBtn = document.getElementById('nextBookingsBtn');
    const paginationInfo = document.getElementById('paginationInfo');
    const searchInput = document.getElementById('searchBookings');
    const statusFilter = document.getElementById('statusFilter');

    // Initial Load
    fetchBookings();

    // Event Listeners
    searchInput.addEventListener('input', debounce(() => {
        currentPage = 1;
        fetchBookings();
    }, 500));

    statusFilter.addEventListener('change', () => {
        currentPage = 1;
        fetchBookings();
    });

    prevBtn.addEventListener('click', () => {
        if (currentPage > 1) {
            currentPage--;
            fetchBookings();
        }
    });

    nextBtn.addEventListener('click', () => {
        currentPage++;
        fetchBookings();
    });

    function fetchBookings() {
        const query = searchInput.value;
        const status = statusFilter.value;
        const url = `/admin/fetch-bookings?page=${currentPage}&limit=${limit}&search=${encodeURIComponent(query)}&status=${status}`;

        tableBody.innerHTML = '<tr><td colspan="8" class="text-center">Loading...</td></tr>';

        fetch(url)
            .then(res => res.json())
            .then(res => {
                if (res.status === "success") {
                    renderTable(res.data.bookings);
                    updatePagination(res.data.total);
                } else {
                    showAlert(res.message || 'Failed to fetch bookings', 'error');
                }
            })
            .catch(err => {
                console.error(err);
                showAlert('An error occurred', 'error');
            });
    }

    function renderTable(bookings) {
        if (!bookings || bookings.length === 0) {
            tableBody.innerHTML = '<tr><td colspan="8" class="text-center">No bookings found</td></tr>';
            return;
        }

        tableBody.innerHTML = bookings.map(booking => `
            <tr>
                <td>
                    <div class="user-info">
                        <strong>${booking.customer_name}</strong><br>
                        <small>${booking.customer_email}</small>
                    </div>
                </td>
                <td>${booking.service_name}</td>
                <td>${formatDate(booking.start_time)}</td>
                <td>${formatTime(booking.start_time)} - ${formatTime(booking.end_time)}</td>
                <td>${booking.duration} min</td>
                <td>$${booking.price}</td>
                <td><span class="status-badge status-${getStatusString(booking.status, booking.start_time)}">${getStatusLabel(booking.status, booking.start_time)}</span></td>
                <td>
                   ${(booking.status == 0 || booking.status == 1) ?
                `<button class="btn-cancel-sm" onclick="cancelBooking(${booking.id})">Cancel</button>` :
                ''
            }
            ${(booking.status == 1) ?
                `<button class="btn-complete-sm" onclick="completeBooking(${booking.id})">Complete</button>` :
                ''
            }
                </td>
            </tr>
        `).join('');
    }

    function updatePagination(total) {
        const start = (currentPage - 1) * limit + 1;
        const end = Math.min(currentPage * limit, total);

        if (total === 0) {
            paginationInfo.textContent = 'Showing 0-0 of 0';
            prevBtn.disabled = true;
            nextBtn.disabled = true;
            return;
        }

        paginationInfo.textContent = `Showing ${start}-${end} of ${total}`;
        prevBtn.disabled = currentPage === 1;
        nextBtn.disabled = end >= total;
    }

    // Helper functions
    function formatDate(dateStr) {
        return new Date(dateStr.replace(" ", "T")).toLocaleDateString();
    }

    function formatTime(dateStr) {
        return new Date(dateStr.replace(" ", "T")).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    }

    function getStatusString(status, startTime) {
        // 0: pending, 1: confirmed, 2: completed, 3: cancelled
        if (status == 2) return 'completed';
        if (status == 3) return 'cancelled';
        if (status == 0) return 'pending';
        if (status == 1) {
            // if (new Date(startTime.replace(" ", "T")) < new Date()) return 'completed';
            return 'confirmed';
        }
        return 'unknown';
    }

    function getStatusLabel(status, startTime) {
        const s = getStatusString(status, startTime);
        return s.charAt(0).toUpperCase() + s.slice(1);
    }

    function debounce(func, wait) {
        let timeout;
        return function (...args) {
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(this, args), wait);
        };
    }

    // Global cancel function
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
                    showAlert('Booking cancelled', 'success');
                    fetchBookings();
                } else {
                    showAlert(res.message || 'Failed to cancel', 'error');
                }
            });
    };

    // Global complete function
    window.completeBooking = function (id) {
        if (!confirm('Are you sure you want to mark this booking as completed?')) return;

        fetch('/admin/complete-booking', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ booking_id: id })
        })
            .then(res => res.json())
            .then(res => {
                if (res.status === "success") {
                    showAlert('Booking completed', 'success');
                    fetchBookings();
                } else {
                    showAlert(res.message || 'Failed to complete booking', 'error');
                }
            });
    };

    function showAlert(msg, type) {
        const box = document.getElementById('custom-alert');
        box.textContent = msg;
        box.className = `alert-box ${type}`;
        box.style.display = 'block';
        setTimeout(() => box.style.display = 'none', 3000);
    }
});
