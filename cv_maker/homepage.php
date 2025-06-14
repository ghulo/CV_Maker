<?php
// homepage.php - This is the main landing page of the CV Maker application.
// It serves as the entry point for users, displaying a hero section and features.

// Start the session if it's not already started.
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Define current page for header active link class.
// This variable is used to highlight the active link in the navigation.
$current_page = basename($_SERVER['PHP_SELF']);

// Determine the target for CV creation links.
// If the user is not logged in, they will be redirected to the login page first.
$create_cv_base_link = 'choose_template.php';
$create_cv_link = $create_cv_base_link;
if (!isset($_SESSION['user_id'])) {
    $create_cv_link = 'login.php?redirect_after_login=' . urlencode($create_cv_base_link);
}

// Check for any messages passed in the URL (e.g., after logout, signup success, or errors).
$page_message = '';
$message_type = 'info'; // Default message type for messages

if (isset($_GET['message'])) {
    $page_message = htmlspecialchars(urldecode($_GET['message']));
} elseif (isset($_GET['success'])) {
    $page_message = htmlspecialchars(urldecode($_GET['success']));
    $message_type = 'success';
} elseif (isset($_GET['error'])) {
    $page_message = htmlspecialchars(urldecode($_GET['error']));
    $message_type = 'error';
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to CV Maker</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body>
    <div class="background-cvs-container" aria-hidden="true">
        <div class="background-cv">
            <div class="header-placeholder"></div>
            <div class="line-placeholder"></div>
            <div class="line-placeholder w-80"></div>
            <div class="block-placeholder"></div>
            <div class="line-placeholder w-90"></div>
            <div class="line-placeholder w-70"></div>
        </div>
         <div class="background-cv">
            <div class="header-placeholder"></div>
            <div class="line-placeholder"></div>
            <div class="line-placeholder w-80"></div>
            <div class="block-placeholder"></div>
            <div class="line-placeholder w-90"></div>
            <div class="line-placeholder w-70"></div>
        </div>
         <div class="background-cv">
            <div class="header-placeholder"></div>
            <div class="line-placeholder"></div>
            <div class="line-placeholder w-80"></div>
            <div class="block-placeholder"></div>
            <div class="line-placeholder w-90"></div>
            <div class="line-placeholder w-70"></div>
        </div>
    </div>

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

    <main class="main">
        <?php if (!empty($page_message)): // Display page-level messages ?>
        <div class="message-area page-level-message-area" style="max-width: 1200px; margin-left: auto; margin-right: auto; margin-bottom: var(--space-md); padding: 0 var(--space-lg);">
            <p id="autoHidePageMessage" class="message <?php echo $message_type; ?>" role="alert" style="margin-top:0;"><?php echo $page_message; ?></p>
        </div>
        <?php endif; ?>

        <div class="homepage-content-wrapper animate-in">
            <section class="homepage-hero">
                <div class="hero-bg-decoration" aria-hidden="true"></div>
                <h2 class="reveal-on-scroll">Krijo CV-në që të Dallon.<br class="desktop-only">Shpejt dhe Lehtë.</h2>
                <p class="reveal-on-scroll" data-reveal-delay="100">Ndërto një Curriculum Vitae profesional dhe modern në pak minuta. Zgjidh nga modelet tona të kuruara dhe prezanto aftësitë tua në mënyrën më të mirë.</p>
                <div class="hero-cta-container reveal-on-scroll" data-reveal-delay="200">
                    <a href="<?php echo $create_cv_link; ?>" class="btn-create btn btn-primary"><i class="fas fa-plus-circle"></i> Fillo Krijo CV Tënde Tani</a>
                </div>
            </section>

            <section class="homepage-how-it-works">
                 <h3 class="reveal-on-scroll">Si Funksionon?</h3>
                 <p class="page-intro-text reveal-on-scroll" data-reveal-delay="100">Krijimi i CV-së tuaj të ardhshme është i thjeshtë. Ndiqni këto hapa:</p>
                 <div class="how-it-works-steps">
                     <div class="step-item reveal-on-scroll" data-reveal-delay="200">
                         <div class="step-icon-wrapper"><i class="fas fa-file-alt"></i></div>
                         <h4>1. Zgjidh Modelin</h4>
                         <p>Filloni duke zgjedhur një nga modelet tona profesionale që i përshtatet stilit tuaj.</p>
                     </div>
                      <div class="step-item reveal-on-scroll" data-reveal-delay="300">
                         <div class="step-icon-wrapper"><i class="fas fa-keyboard"></i></div>
                         <h4>2. Plotëso Detajet</h4>
                         <p>Shtoni informacionin tuaj personal, eksperiencën e punës, edukimin dhe aftësitë.</p>
                     </div>
                      <div class="step-item reveal-on-scroll" data-reveal-delay="400">
                         <div class="step-icon-wrapper"><i class="fas fa-download"></i></div>
                         <h4>3. Shkarko CV-në</h4>
                         <p>Parapamje dhe shkarkoni CV-në tuaj si një dokument PDF me cilësi të lartë.</p>
                     </div>
                 </div>
            </section>

            <section class="homepage-value-prop">
                <div class="value-prop-container reveal-on-scroll" data-reveal-delay="100">
                    <div class="value-prop-grid">
                        <div class="value-prop-headline">
                            <h3 class="reveal-on-scroll" data-reveal-delay="200">E Dizajnuar për Suksesin Tuaj Profesional.</h3>
                        </div>
                        <div class="value-prop-text">
                            <p class="reveal-on-scroll" data-reveal-delay="300">CV Maker është krijuar me praktikat më të mira në mendje. Ne ofrojmë mjetet dhe modelet që ju ndihmojnë të prezantoni veten në mënyrën më bindëse, duke u fokusuar në qartësi, profesionalizëm dhe rezultate.</p>
                            <p class="reveal-on-scroll" data-reveal-delay="400">Nga fillestarët deri te profesionistët me përvojë, platforma jonë është intuitive dhe e fuqishme, duke ju lejuar të krijoni një CV që vërtet reflekton potencialin tuaj.</p>
                        </div>
                    </div>
                </div>
            </section>

            <section class="homepage-features" id="features">
                <div class="feature-item reveal-on-scroll" data-reveal-delay="100">
                    <div class="feature-icon-wrapper">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <h3>Modele Profesionale</h3>
                    <p>Zgjidhni nga një koleksion i modeleve moderne dhe të dizajnuara profesionalisht që i përshtaten stilit tuaj dhe profesionit.</p>
                </div>
                <div class="feature-item reveal-on-scroll" data-reveal-delay="200">
                    <div class="feature-icon-wrapper">
                        <i class="fas fa-magic"></i>
                    </div>
                    <h3>Redaktim Intuitiv</h3>
                    <p>Shtoni dhe modifikoni informacionin tuaj shpejt me një ndërfaqe të thjeshtë për t'u përdorur, pa pasur nevojë për aftësi teknike.</p>
                </div>
                <div class="feature-item reveal-on-scroll" data-reveal-delay="300">
                    <div class="feature-icon-wrapper">
                        <i class="fas fa-cloud-download-alt"></i>
                    </div>
                    <h3>Shkarko & Ndaj</h3>
                    <p>Shkarkoni CV-në tuaj si PDF me cilësi të lartë, gati për ta ndarë me punëdhënësit potencialë dhe për të lënë përshtypje.</p>
                </div>
            </section>

            <section class="homepage-final-cta">
                <div class="cta-content">
                    <h4 class="reveal-on-scroll">Gati për të krijuar CV-në tuaj të ardhshme?</h4>
                    <p class="reveal-on-scroll" data-reveal-delay="100">Bashkohuni me mijëra përdorues që kanë krijuar CV mbresëlënëse me CV Maker.</p>
                    <a href="signup.php" class="btn-create btn-large btn btn-primary reveal-on-scroll" data-reveal-delay="200"><i class="fas fa-rocket"></i> Fillo Tani, Është Falas!</a>
                </div>
            </section>

        </div>
    </main>

    <?php include 'footer.php'; ?>

    <script src="script.js"></script>
</body>
</html>