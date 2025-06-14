<!DOCTYPE html>
<html lang="sq">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kontakt & Feedback - CV Maker</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body>
    <div class="background-cvs-container">
        </div>
<header class="header">
    <div class="header-content-wrapper">
        <div class="logo-box">
            <a href="index.php" class="logo-link">
                <span class="gemini-icon">CV</span>
                <div class="logo-text">
                    <h1>CV Maker</h1>
                    <p class="header-subtitle">Build your future</p>
                </div>
            </a>
        </div>

        <button id="mobile-menu-toggle" aria-label="Toggle menu" aria-expanded="false">
            <i class="fas fa-bars"></i>
        </button>

        <nav class="header-nav" id="desktop-nav-menu-id">
            <a href="index.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'index.php' || basename($_SERVER['PHP_SELF']) == 'homepage.php' ? 'active' : ''; ?>">Home</a>
            <a href="choose_template.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'choose_template.php' ? 'active' : ''; ?>">Templates</a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="view_cvs.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'view_cvs.php' ? 'active' : ''; ?>">My CVs</a>
                <a href="form.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'form.php' ? 'active' : ''; ?>">Create CV</a>
                <?php endif; ?>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="logout.php" class="auth-link logout-link btn btn-secondary">Logout</a>
            <?php else: ?>
                <a href="login.php" class="auth-link login-link <?php echo basename($_SERVER['PHP_SELF']) == 'login.php' ? 'active' : ''; ?>">Login</a>
                <a href="signup.php" class="auth-link signup-link btn <?php echo basename($_SERVER['PHP_SELF']) == 'signup.php' ? 'active' : ''; ?>">Sign Up</a>
            <?php endif; ?>
        </nav>

        <div class="header-actions-group">
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="profile.php" class="profile-icon-btn" aria-label="View Profile">
                    <i class="fas fa-user-circle"></i> </a>
            <?php endif; ?>
        </div>
    </div>
</header>

<nav class="mobile-nav-menu" id="mobile-nav-menu-id">
    <a href="index.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'index.php' || basename($_SERVER['PHP_SELF']) == 'homepage.php' ? 'active' : ''; ?>">Home</a>
    <a href="choose_template.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'choose_template.php' ? 'active' : ''; ?>">Templates</a>
    <?php if (isset($_SESSION['user_id'])): ?>
        <a href="view_cvs.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'view_cvs.php' ? 'active' : ''; ?>">My CVs</a>
        <a href="form.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'form.php' ? 'active' : ''; ?>">Create CV</a>
        <a href="profile.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'profile.php' ? 'active' : ''; ?>">Profile</a>
        <a href="logout.php" class="auth-link logout-link">Logout</a>
    <?php else: ?>
        <a href="login.php" class="auth-link login-link <?php echo basename($_SERVER['PHP_SELF']) == 'login.php' ? 'active' : ''; ?>">Login</a>
        <a href="signup.php" class="auth-link signup-link <?php echo basename($_SERVER['PHP_SELF']) == 'signup.php' ? 'active' : ''; ?>">Sign Up</a>
    <?php endif; ?>
</nav>

    <main class="main">
        <div class="contact-form-container login-form-container animate-in">
            <h2 class="reveal-on-scroll">Na Kontaktoni ose Dërgoni Feedback</h2>
            <p class="form-intro-text reveal-on-scroll" data-reveal-delay="100">Keni pyetje, sugjerime, apo dëshironi të raportoni një problem? Përdorni formularin më poshtë. Vlerësimi juaj na ndihmon të përmirësohemi!</p>

            <div id="contact-message-area" class="form-message" style="display:none;"></div>

            <form id="contact-feedback-form" action="process_feedback.php" method="POST">
                 <div class="form-group reveal-on-scroll" data-reveal-delay="150">
                    <label for="contact-name">Emri Juaj (opsionale)</label>
                     <div class="input-icon-wrapper">
                         <i class="fas fa-user input-icon"></i>
                         <input type="text" id="contact-name" name="contact_name" placeholder="Emri juaj">
                     </div>
                </div>
                <div class="form-group reveal-on-scroll" data-reveal-delay="200">
                    <label for="contact-email">Email Juaj (opsionale, për përgjigje)</label>
                     <div class="input-icon-wrapper">
                         <i class="fas fa-envelope input-icon"></i>
                         <input type="email" id="contact-email" name="contact_email" placeholder="email@shembull.com">
                     </div>
                </div>
                <div class="form-group reveal-on-scroll" data-reveal-delay="250">
                    <label for="feedback-subject">Subjekti <span class="required">*</span></label>
                     <div class="input-icon-wrapper">
                         <i class="fas fa-tag input-icon"></i>
                         <input type="text" id="feedback-subject" name="feedback_subject" required placeholder="P.sh., Sugjerim për model të ri, Problem teknik">
                     </div>
                </div>
                <div class="form-group full-width reveal-on-scroll" data-reveal-delay="300">
                    <label for="feedback-message">Mesazhi / Feedback-u Juaj <span class="required">*</span></label>
                    <textarea id="feedback-message" name="feedback_message" rows="6" required placeholder="Shkruani detajet këtu..."></textarea>
                </div>
                <button type="submit" class="btn-primary-form btn btn-primary reveal-on-scroll" data-reveal-delay="350">Dërgo Mesazhin <i class="fas fa-paper-plane icon-right"></i></button>
            </form>
             <p style="font-size: 0.8em; text-align: center; margin-top: var(--space-md); color: var(--muted-text);" class="reveal-on-scroll" data-reveal-delay="400">
                Ju lutemi vini re: Ky formular është për demonstrim. Përpunimi aktual i feedback-ut kërkon implementim në server.
            </p>
        </div>
    </main>

    <?php include 'footer.php'; ?>
    <div class="page-transition-overlay"></div>
    <script src="script.js"></script>
    </body>
</html>