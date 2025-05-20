<?php
// footer.php - This file defines the footer section of the CV Maker application.
// It includes copyright information, quick links, support links, and social media links.

// Get the current year for the copyright notice.
$current_year = date("Y");
?>
<footer class="site-footer">
    <div class="footer-container">
        <div class="footer-grid">
            
            <div class="footer-column footer-about">
                <div class="footer-logo-box">
                    <a href="homepage.php" class="footer-logo-link-style">
                        <!-- 
                            Removed the <img> tag for logo.png and replaced it with a <span> element
                            that uses the 'footer-gemini-icon' class defined in style.css.
                            This ensures the footer logo is rendered purely with CSS, matching
                            the design system and removing the image dependency.
                        -->
                        <span class="footer-gemini-icon">CV</span>
                        <div class="footer-logo-text">
                            <h3>CV Maker</h3> 
                            <p class="footer-subtitle">Build your future</p>
                        </div>
                    </a>
                </div>
                <p class="footer-about-text">
                    Craft professional CVs with ease. Our tools and templates help you stand out and land your dream job.
                </p>
            </div>

            <div class="footer-column">
                <h3 class="footer-column-title">Quick Links</h3>
                <ul class="footer-links-list">
                    <li><a href="homepage.php">Home</a></li>
                    <li><a href="about_us.php">About Us</a></li>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li><a href="form.php">Create CV</a></li>
                        <li><a href="profile.php">My Profile</a></li>
                    <?php else: ?>
                        <li><a href="signup.php">Sign Up</a></li>
                        <li><a href="login.php">Login</a></li>
                    <?php endif; ?>
                </ul>
            </div>

            <div class="footer-column">
                <h3 class="footer-column-title">Support & Legal</h3>
                <ul class="footer-links-list">
                    <li><a href="faq.php">FAQ</a></li>
                    <li><a href="contact.php">Contact Us</a></li>
                    <li><a href="privacy_policy.php">Privacy Policy</a></li>
                    <li><a href="terms_of_service.php">Terms of Service</a></li>
                </ul>
            </div>

            <div class="footer-column">
                <h3 class="footer-column-title">Follow Us</h3>
                <div class="footer-social-links">
                    <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                    <a href="#" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                    <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                </div>
            </div>

        </div>

        <div class="footer-bottom">
            <p class="footer-copyright">
                &copy; <?php echo $current_year; ?> CV Maker. All rights reserved.
            </p>
            <p class="footer-tagline">
                Designed to help you succeed.
            </p>
        </div>
    </div>
</footer>
