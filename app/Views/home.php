<?php include "partials/header.php";
 ?>

<main class="home-main">

    <!-- HERO SECTION -->
    <section class="hero">
        <span class="badge"><i class="fa-solid fa-star"></i> TOP RATED PROFESSIONALS</span>

        <h1>
            Unlock Your True <span>Radiance</span>
        </h1>

        <p class="hero-text">
            Book top-rated beauty professionals in your area instantly.
            Discover premium services for hair, skin, and nails tailored just for you.
        </p>

        <div class="hero-search">
            <input type="text" id="homeSearch" placeholder="Search service" autocomplete="off">
            <button class="clear-btn" id="homeClearBtn" type="submit"><i class="fa-solid fa-xmark"></i></button>
            <button id="homeSearchBtn" class="search-button" type="button"><i class="fa fa-search"></i></button>
            <div id="homeSuggestions" class="search-suggestions hidden"></div>
        </div>

        <div class="hero-trust">
            <div class="avatars">
                <img src="https://i.pravatar.cc/40?img=1">
                <img src="https://i.pravatar.cc/40?img=2">
                <img src="https://i.pravatar.cc/40?img=3">
                <span>+2k</span>
            </div>
            <p>Trusted by over 2,000 happy clients</p>
        </div>
    </section>

    <!-- FEATURES -->
    <section class="features">
        <div class="feature">
            <h4>Easy Booking</h4>
            <p>Book appointments 24/7 with instant confirmation.</p>
        </div>
        <div class="feature">
            <h4>Expert Stylists</h4>
            <p>Access top-tier professionals vetted by experts.</p>
        </div>
        <div class="feature">
            <h4>Secure Payment</h4>
            <p>Pay safely online or at the salon with ease.</p>
        </div>
        <div class="feature">
            <h4>Smart Reminders</h4>
            <p>Never miss an appointment with alerts.</p>
        </div>
    </section>

    <!-- SERVICES -->
    <section class="services">
        <h2>Our Beauty Services</h2>
        <p class="section-subtext">
            Explore our wide range of premium beauty treatments designed to help you look and feel your best.
        </p>

        <div class="service-grid">
            <div class="service-card">
                <img src="/assets/images/hair-cut.jpeg" alt="">
                <h3>Hair Styling <span>From $50</span></h3>
                <p>Expert cuts, coloring, and treatments.</p>
                <button onclick="redirectServices()">Book Service</button>
            </div>

            <div class="service-card">
                <img src="/assets/images/nails.webp" alt="">
                <h3>Nails <span>From $35</span></h3>
                <p>Luxury manicures and pedicures.</p>
                <button onclick="redirectServices()">Book Service</button>
            </div>

            <div class="service-card">
                <img src="/assets/images/skincare.jpg" alt="">
                <h3>Skincare <span>From $80</span></h3>
                <p>Rejuvenating facial treatments.</p>
                <button onclick="redirectServices()">Book Service</button>
            </div>

            <div class="service-card">
                <img src="/assets/images/makeup.jpeg" alt="">
                <h3>Makeup <span>From $60</span></h3>
                <p>Professional makeup artistry.</p>
                <button onclick="redirectServices()">Book Service</button>
            </div>
        </div>
    </section>

    <!-- TESTIMONIALS -->
    <section class="testimonials">
        <h2>What Our Clients Say</h2>

        <div class="testimonial-grid">
            <div class="testimonial-card">
                <p>★★★★★</p>
                <p>
                    “The easiest way to book my monthly facial. The professionals are top-notch.”
                </p>
                <h4>Sarah Jenkins</h4>
                <span>Regular Customer</span>
            </div>

            <div class="testimonial-card">
                <p>★★★★★</p>
                <p>
                    “Found an amazing hairstylist through Glow. Highly recommend!”
                </p>
                <h4>Michael Chen</h4>
                <span>New Client</span>
            </div>

            <div class="testimonial-card">
                <p>★★★★★</p>
                <p>
                    “Booked a spa day with friends. Everything was seamless.”
                </p>
                <h4>Emily Ross</h4>
                <span>Loyal Member</span>
            </div>
        </div>
    </section>

</main>


<?php include "partials/footer.php"; ?>
