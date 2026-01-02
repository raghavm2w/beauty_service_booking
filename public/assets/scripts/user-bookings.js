document.addEventListener('DOMContentLoaded', () => {
    const tabs = document.querySelectorAll('.tab-btn');
    const bookingsList = document.getElementById('bookings-list');
    let currentFilter = 'upcoming';
    let currentPage = 1;
    let hasMore = true;
    let isLoading = false;
    const LIMIT = 9;

    // Initial load
    loadBookings(currentFilter, true);

    // Tab switching
    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            if (tab.classList.contains('active')) return;
            tabs.forEach(t => t.classList.remove('active'));
            tab.classList.add('active');
            currentFilter = tab.dataset.filter;
            loadBookings(currentFilter, true);
        });
    });

    // Infinite scroll listener
    window.addEventListener('scroll', handleInfiniteScroll);

    function handleInfiniteScroll() {
        const scrollPosition = window.innerHeight + window.scrollY;
        const threshold = document.body.offsetHeight - 200;

        if (scrollPosition >= threshold) {
            loadBookings(currentFilter);
        }
    }

    function loadBookings(filter, reset = false) {
        if (isLoading || (!hasMore && !reset)) return;

        if (reset) {
            currentPage = 1;
            hasMore = true;
            bookingsList.innerHTML = '<div class="loading">Loading...</div>';
        } else {
            const loader = document.createElement('div');
            loader.className = 'scroll-loader';
            loader.innerHTML = 'Loading more...';
            bookingsList.appendChild(loader);
        }

        isLoading = true;

        fetch(`/user/my-bookings?filter=${filter}&page=${currentPage}&limit=${LIMIT}`)
            .then(res => res.json())
            .then(res => {
                // Remove scroll loader if exists
                const loader = document.querySelector('.scroll-loader');
                if (loader) loader.remove();

                if (res.status === "success") {
                    if (reset) bookingsList.innerHTML = '';
                    renderBookings(res.data.bookings, filter);
                    hasMore = res.data.hasMore;
                    currentPage++;
                } else {
                    if (reset) {
                        bookingsList.innerHTML = `<div class="no-bookings">${res.message || 'Failed to load bookings'}</div>`;
                    } else {
                        showAlert(res.message || 'Failed to load more bookings', 'error');
                    }
                }
            })
            .catch(err => {
                console.error(err);
                if (reset) {
                    bookingsList.innerHTML = '<div class="no-bookings">An error occurred</div>';
                }
                const loader = document.querySelector('.scroll-loader');
                if (loader) loader.remove();
            })
            .finally(() => {
                isLoading = false;
            });
    }

    function renderBookings(bookings, filter) {
        if (!bookings || bookings.length === 0) {
            if (currentPage === 1) {
                bookingsList.innerHTML = '<div class="no-bookings">No bookings found in this category.</div>';
            }
            return;
        }

        const html = bookings.map(booking => `
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
                    <div class="price">₹${booking.price}</div>

                    ${filter === 'upcoming' ? `
                        <button class="btn-cancel" onclick="cancelBooking(${booking.id})">
                            Cancel Booking
                        </button>
                    ` : ''}
                </div>
            </div>
        `).join('');

        if (currentPage === 1) {
            bookingsList.innerHTML = html;
        } else {
            bookingsList.insertAdjacentHTML('beforeend', html);
        }
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
                    loadBookings(currentFilter, true);
                    showAlert("Booking cancellation success", "success");
                } else {
                    showAlert(res.message || "Failed to cancel", "error");
                }
            })
            .catch(err => {
                console.error(err);
                alert("An error occurred");
            });
    };
});
