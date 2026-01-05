<?php include 'partials/header.php'; ?>
<link rel="stylesheet" href="/assets/styles/pages/contact.css">

<main class="contact-main">
    <div class="container">
        <div class="contact-wrapper">
            <div class="contact-info">
                <h1>Get in Touch</h1>
                <p>Have questions? We'd love to hear from you. Send us a message and we'll respond as soon as possible.
                </p>

                <div class="info-item">
                    <i class="fa fa-map-marker-alt"></i>
                    <div>
                        <h3>Our Office</h3>
                        <p>123 Beauty Lane, Glamour City, BC 4500</p>
                    </div>
                </div>

                <div class="info-item">
                    <i class="fa fa-envelope"></i>
                    <div>
                        <h3>Email Us</h3>
                        <p>support@beautyqueen.com</p>
                    </div>
                </div>

                <div class="info-item">
                    <i class="fa fa-phone"></i>
                    <div>
                        <h3>Call Us</h3>
                        <p>+1 (555) 123-4567</p>
                    </div>
                </div>
            </div>

            <div class="contact-form-container">
                <form id="contactForm" class="contact-form">
                    <div class="form-group">
                        <label for="name">Full Name</label>
                        <input type="text" id="name" name="name" placeholder="John ">
                        <span class="error-text" id="nameError"></span>
                    </div>

                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" placeholder="john@example.com">
                        <span class="error-text" id="emailError"></span>
                    </div>

                    <div class="form-group">
                        <label for="message">Message</label>
                        <textarea id="message" name="message" rows="5" placeholder="How can we help you?"></textarea>
                        <span class="error-text" id="messageError"></span>
                    </div>

                    <div id="formMessage" class="form-message"></div>

                    <button type="submit" class="submit-btn" id="submitBtn">Send Message</button>
                </form>
            </div>
        </div>
    </div>
</main>

<script src="/assets/scripts/contact.js"></script>
<?php include 'partials/footer.php'; ?>