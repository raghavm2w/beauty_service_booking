document.addEventListener('DOMContentLoaded', () => {
    const urlParams = new URLSearchParams(window.location.search);
    const bookingId = urlParams.get('booking_id');
    const loadingMessage = document.getElementById('loadingMessage');
    const paymentDetails = document.getElementById('paymentDetails');
    const errorMessage = document.getElementById('errorMessage');
    const payBtn = document.getElementById('payBtn');
    const cancelPayBtn = document.getElementById('cancelPayBtn');

    if (!bookingId) {
        showAlert("Invalid Booking ID", "error");
        return;
    }

    // Fetch booking details
    fetch(`/user/booking-details?booking_id=${bookingId}`)
        .then(res => res.json())
        .then(res => {
            if (res.status === 'success') {
                const data = res.data;
                document.getElementById('serviceName').textContent = data.service_name;
                document.getElementById('providerName').textContent = data.provider_name;
                document.getElementById('date').textContent = `${data.start_time.slice(0, 10)}`;
                document.getElementById('time').textContent = `${data.start_time.slice(11, 19)} - ${data.end_time.slice(11, 19)}`;
                document.getElementById('duration').textContent = `${data.duration} min`;
                document.getElementById('price').textContent = `$${data.price}`;

                // Timer Logic
                const createdAt = new Date(data.created_at.replace(" ", "T"));
                const expireTime = createdAt.getTime() + 5 * 60 * 1000;

                const updateTimer = () => {
                    const now = new Date().getTime();
                    const diff = expireTime - now;

                    if (diff <= 0) {
                        clearInterval(timerInterval);
                        document.getElementById('paymentTimer').textContent = "EXPIRED";

                        // Automatically cancel booking
                        fetch('/user/cancel-booking', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({ booking_id: bookingId })
                        }).then(() => {
                            showAlert("Booking expired. Redirecting...", "error");
                            payBtn.disabled = true;
                            payBtn.textContent = "Expired";
                            setTimeout(() => window.location.href = '/', 2000);
                        });
                        return;
                    }

                    const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                    const seconds = Math.floor((diff % (1000 * 60)) / 1000);
                    document.getElementById('paymentTimer').textContent =
                        `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
                };

                const timerInterval = setInterval(updateTimer, 1000);
                updateTimer();

                loadingMessage.classList.add('hidden');
                paymentDetails.classList.remove('hidden');
            } else {
                showAlert(res.message || "Failed to load booking details", "error");
            }
        })
        .catch(err => {
            console.error(err);
            showAlert("An error occurred loading details", "error");
        });


    // Handle Payment
    payBtn.addEventListener('click', () => {
        payBtn.disabled = true;
        payBtn.textContent = "Processing...";

        fetch('/user/confirm-payment', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ booking_id: bookingId })
        })
            .then(res => res.json())
            .then(res => {
                if (res.status === 'success') {
                    showAlert("Payment Successful! your slot is confirmed", "success");
                    setTimeout(() => {
                        window.location.href = "/";
                    }, 1500);
                } else {
                    showAlert(res.message || "Payment failed", "error");
                    payBtn.disabled = false;
                    payBtn.textContent = "Confirm and Pay";
                }
            })
            .catch(err => {
                console.error(err);
                showAlert("An error occurred via payment", "error");
                payBtn.disabled = false;
                payBtn.textContent = "Confirm and Pay";
            });
    });

cancelPayBtn.addEventListener('click', () => {
      fetch('/user/cancel-booking', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({ booking_id: bookingId })
                        }).then(() => {
                         window.location.href = '/services';
                        return;
                    });
                });
            });

function showAlert(message, type) {
    const alertBox = document.getElementById("custom-alert");
    alertBox.innerHTML = message;
    alertBox.className = "alert-box alert-" + type;
    alertBox.style.display = "block";

    setTimeout(() => {
        alertBox.style.display = "none";
    }, 3500);
}  
