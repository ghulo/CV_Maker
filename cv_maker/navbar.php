<?php
// navbar.php - This file defines the navigation bar for the CV Maker application.
// It includes links to various pages and dynamically adjusts based on user login status.

// Start the session if it's not already started. This is crucial for accessing session variables
// like 'user_id' to determine if a user is logged in.
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Define current page for header active link class
// This variable is typically set in the individual page files (e.g., homepage.php, about_us.php)
// before including navbar.php, to highlight the active link.
$current_page = basename($_SERVER['PHP_SELF']);

// Determine the target for "Create CV" based on homepage.php's logic.
// This ensures that non-logged-in users are directed to login first, then to choose_template.php.
$create_cv_base_link = 'choose_template.php';
$create_cv_link = $create_cv_base_link;
if (!isset($_SESSION['user_id'])) {
    $create_cv_link = 'login.php?redirect_after_login=' . urlencode($create_cv_base_link);
}
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">


<header class="header">
    <div class="header-content-wrapper">
        <a href="homepage.php" class="logo-link">
            <span class="gemini-icon">CV</span>
            <div class="logo-text">
                <h1>CV Maker</h1>
                <p class="header-subtitle">Build your future</p>
            </div>
        </a>

        <button id="mobile-menu-toggle" aria-label="Toggle mobile menu">
            <i class="fas fa-bars"></i>
        </button>

        <nav class="header-nav" id="desktop-nav-menu-id">
            <a href="homepage.php" class="<?php echo ($current_page == 'index.php' || $current_page == 'homepage.php') ? 'active' : ''; ?>">Home</a>
            <a href="about_us.php" class="<?php echo ($current_page == 'about_us.php') ? 'active' : ''; ?>">About Us</a>
            <a href="contact.php" class="<?php echo ($current_page == 'contact.php') ? 'active' : ''; ?>">Contact</a>
            <a href="faq.php" class="<?php echo ($current_page == 'faq.php') ? 'active' : ''; ?>">FAQ</a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="<?php echo $create_cv_link; ?>" class="btn btn-primary">Create CV</a>
                <a href="view_cvs.php" class="<?php echo ($current_page == 'view_cvs.php') ? 'active' : ''; ?>">My CVs</a>
            <?php endif; ?>
        </nav>

        <div class="header-actions-group">
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="profile.php" class="profile-icon-btn" aria-label="Profile">
                    <i class="fas fa-user-circle"></i>
                </a>
                <a href="logout.php" class="auth-link logout-link btn btn-secondary">Logout</a>
            <?php else: ?>
                <a href="login.php" class="auth-link login-link <?php echo ($current_page == 'login.php') ? 'active' : ''; ?>">Login</a>
                <a href="signup.php" class="auth-link signup-link btn <?php echo ($current_page == 'signup.php') ? 'active' : ''; ?>">Sign Up</a>
            <?php endif; ?>
            <button id="theme-toggle-button" aria-label="Toggle theme">
                <i class="fas fa-moon" id="theme-icon"></i>
            </button>
        </div>
    </div>

    <nav class="mobile-nav-menu" id="mobile-nav-menu-id">
        <a href="homepage.php" class="<?php echo ($current_page == 'index.php' || $current_page == 'homepage.php') ? 'active' : ''; ?>">Home</a>
        <a href="about_us.php" class="<?php echo ($current_page == 'about_us.php') ? 'active' : ''; ?>">About Us</a>
        <a href="contact.php" class="<?php echo ($current_page == 'contact.php') ? 'active' : ''; ?>">Contact</a>
        <a href="faq.php" class="<?php echo ($current_page == 'faq.php') ? 'active' : ''; ?>">FAQ</a>
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="choose_template.php">Create CV</a>
            <a href="view_cvs.php" class="<?php echo ($current_page == 'view_cvs.php') ? 'active' : ''; ?>">My CVs</a>
            <a href="profile.php" class="<?php echo ($current_page == 'profile.php') ? 'active' : ''; ?>">My Profile</a>
            <a href="logout.php">Logout</a>
        <?php else: ?>
            <a href="login.php" class="<?php echo ($current_page == 'login.php') ? 'active' : ''; ?>">Login</a>
            <a href="signup.php" class="<?php echo ($current_page == 'signup.php') ? 'active' : ''; ?>">Sign Up</a>
        <?php endif; ?>
    </nav>
</header>
