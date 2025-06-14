<?php
session_start();
$user_id = $_SESSION['user_id'] ?? null;
$header_subtitle = "Rreth Nesh";
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="sq">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rreth Nesh - CV Maker</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body>
    <div class="background-cvs-container" aria-hidden="true"></div>

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
                <a href="index.php" class="<?php echo ($current_page == 'index.php' || $current_page == 'homepage.php') ? 'active' : ''; ?>">Home</a>
                <a href="choose_template.php" class="<?php echo $current_page == 'choose_template.php' ? 'active' : ''; ?>">Templates</a>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="view_cvs.php" class="<?php echo $current_page == 'view_cvs.php' ? 'active' : ''; ?>">My CVs</a>
                    <a href="form.php" class="<?php echo $current_page == 'form.php' ? 'active' : ''; ?>">Create CV</a>
                <?php endif; ?>
            </nav>

            <div class="header-actions-group">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="profile.php" class="profile-icon-btn" aria-label="View Profile">
                        <i class="fas fa-user-circle"></i>
                    </a>
                    <a href="logout.php" class="auth-link logout-link btn btn-secondary">Logout</a>
                <?php else: ?>
                    <a href="login.php" class="auth-link login-link <?php echo $current_page == 'login.php' ? 'active' : ''; ?>">Login</a>
                    <a href="signup.php" class="auth-link signup-link btn <?php echo $current_page == 'signup.php' ? 'active' : ''; ?>">Sign Up</a>
                <?php endif; ?>
                <button id="theme-toggle-button" aria-label="Toggle theme">
                    <i class="fas fa-moon"></i>
                </button>
            </div>
        </div>
    </header>

    <nav class="mobile-nav-menu" id="mobile-nav-menu-id">
        <a href="index.php" class="<?php echo ($current_page == 'index.php' || $current_page == 'homepage.php') ? 'active' : ''; ?>">Home</a>
        <a href="choose_template.php" class="<?php echo $current_page == 'choose_template.php' ? 'active' : ''; ?>">Templates</a>
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="view_cvs.php" class="<?php echo $current_page == 'view_cvs.php' ? 'active' : ''; ?>">My CVs</a>
            <a href="form.php" class="<?php echo $current_page == 'form.php' ? 'active' : ''; ?>">Create CV</a>
            <a href="profile.php" class="<?php echo $current_page == 'profile.php' ? 'active' : ''; ?>">Profile</a>
            <a href="logout.php" class="auth-link logout-link">Logout</a>
        <?php else: ?>
            <a href="login.php" class="auth-link login-link <?php echo $current_page == 'login.php' ? 'active' : ''; ?>">Login</a>
            <a href="signup.php" class="auth-link signup-link <?php echo $current_page == 'signup.php' ? 'active' : ''; ?>">Sign Up</a>
        <?php endif; ?>
    </nav>

    <main class="main">
        <div class="page-container animate-in">
            <section class="homepage-hero about-us-hero">
                <div class="hero-bg-decoration" aria-hidden="true"></div>
                <h2 class="reveal-on-scroll">Ndërtojmë Të Ardhmen Tuaj, Një CV në Një Kohë.</h2>
                <p class="reveal-on-scroll" data-reveal-delay="100">CV Maker është krijuar me pasion për të fuqizuar individët në rrugëtimin e tyre profesional. Besojmë se një CV e mirë mund të hapë dyer dhe të krijojë mundësi.</p>
            </section>

            <section class="homepage-value-prop about-us-mission">
                <div class="value-prop-container reveal-on-scroll" data-reveal-delay="100">
                    <div class="value-prop-grid">
                        <div class="value-prop-headline">
                            <h3 class="reveal-on-scroll" data-reveal-delay="200">Misioni dhe Vizioni Ynë.</h3>
                        </div>
                        <div class="value-prop-text">
                            <p class="reveal-on-scroll" data-reveal-delay="300">Misioni ynë është të ofrojmë një platformë intuitive dhe të fuqishme që thjeshton procesin e krijimit të CV-ve profesionale. Ne synojmë të bëjmë dizajnin e CV-së të aksesueshëm për të gjithë, duke ju ndihmuar të prezantoni aftësitë dhe përvojën tuaj në mënyrën më të mirë të mundshme.</p>
                            <p class="reveal-on-scroll" data-reveal-delay="400">Vizioni ynë është të jemi zgjedhja kryesore për individët që kërkojnë të lënë një përshtypje të fortë në tregun e punës, duke ofruar mjete inovative dhe mbështetje të vazhdueshme.</p>
                        </div>
                    </div>
                </div>
            </section>

            <section class="homepage-how-it-works about-us-story">
                <h3 class="reveal-on-scroll">Historia Jonë</h3>
                <p class="page-intro-text reveal-on-scroll" data-reveal-delay="100">CV Maker filloi si një projekt i vogël me një ide të madhe: të thjeshtojë krijimin e CV-ve. Kemi evoluar duke u bazuar në feedback-un e përdoruesve dhe tendencat e industrisë, gjithmonë me fokusin te ju.</p>
                <div class="how-it-works-steps">
                     <div class="step-item reveal-on-scroll" data-reveal-delay="200">
                         <div class="step-icon-wrapper"><i class="fas fa-lightbulb"></i></div>
                         <h4>Ideja</h4>
                         <p>Gjithçka nisi me një ide të thjeshtë për të ndihmuar njerëzit të krijojnë CV pa mundim.</p>
                     </div>
                      <div class="step-item reveal-on-scroll" data-reveal-delay="300">
                         <div class="step-icon-wrapper"><i class="fas fa-code"></i></div>
                         <h4>Zhvillimi</h4>
                         <p>Kemi punuar me zell për të ndërtuar një platformë të fuqishme dhe të lehtë në përdorim.</p>
                     </div>
                      <div class="step-item reveal-on-scroll" data-reveal-delay="400">
                         <div class="step-icon-wrapper"><i class="fas fa-users"></i></div>
                         <h4>Komuniteti</h4>
                         <p>Me mbështetjen e përdoruesve tanë, ne vazhdojmë të rritemi dhe të përmirësohemi.</p>
                     </div>
                 </div>
            </section>

            <section class="homepage-final-cta about-us-cta">
                <div class="cta-content">
                    <h4 class="reveal-on-scroll">Bashkohuni me Komunitetin Tonë!</h4>
                    <p class="reveal-on-scroll" data-reveal-delay="100">Jemi këtu për t'ju mbështetur në çdo hap të rrugëtimit tuaj profesional.</p>
                    <a href="contact.php" class="btn-create btn-large btn btn-primary reveal-on-scroll" data-reveal-delay="200"><i class="fas fa-envelope"></i> Na Kontaktoni</a>
                </div>
            </section>
        </div>
    </main>

    <?php include 'footer.php'; ?>
    <div class="page-transition-overlay"></div>
    <script src="script.js"></script>
</body>
</html>