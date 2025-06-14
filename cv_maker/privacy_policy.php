<?php
session_start();
$user_id = $_SESSION['user_id'] ?? null;
$header_subtitle = "Politika e Privatësisë";
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="sq">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Politika e Privatësisë - CV Maker</title>
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
            <h2 class="reveal-on-scroll">Politika e Privatësisë</h2>
            <p class="page-intro-text reveal-on-scroll" data-reveal-delay="100">Kjo politikë privatësie shpjegon se si CV Maker mbledh, përdor dhe mbron informacionin tuaj personal.</p>

            <div class="content-section reveal-on-scroll" data-reveal-delay="150">
                <h3>1. Informacioni që Mbledhim</h3>
                <p>Ne mbledhim informacionin që ju na jepni direkt, si emri, adresa e emailit, dhe detajet e CV-së. Gjithashtu, mbledhim të dhëna të përdorimit të platformës për të përmirësuar shërbimin tonë.</p>
            </div>

            <div class="content-section reveal-on-scroll" data-reveal-delay="200">
                <h3>2. Si e Përdorim Informacionin Tuaj</h3>
                <p>Informacioni juaj përdoret për të krijuar dhe menaxhuar CV-të tuaja, për t'ju ofruar mbështetje, dhe për të përmirësuar funksionalitetin e platformës. Ne nuk e shesim apo shpërndajmë informacionin tuaj personal me palë të treta pa pëlqimin tuaj, përveç rasteve kur kërkohet me ligj.</p>
            </div>

            <div class="content-section reveal-on-scroll" data-reveal-delay="250">
                <h3>3. Siguria e të Dhënave</h3>
                <p>Ne marrim masa të arsyeshme për të mbrojtur informacionin tuaj personal nga aksesi, përdorimi ose zbulimi i paautorizuar. Megjithatë, asnjë metodë transmetimi në internet ose ruajtje elektronike nuk është 100% e sigurt.</p>
            </div>

            <div class="content-section reveal-on-scroll" data-reveal-delay="300">
                <h3>4. Ndryshimet në Politikën e Privatësisë</h3>
                <p>Ne mund të përditësojmë Politikën tonë të Privatësisë herë pas here. Ne do t'ju njoftojmë për çdo ndryshim duke postuar Politikën e re të Privatësisë në këtë faqe.</p>
            </div>

            <div class="content-section reveal-on-scroll" data-reveal-delay="350">
                <h3>5. Na Kontaktoni</h3>
                <p>Nëse keni pyetje rreth kësaj Politike Privatësie, ju lutemi na kontaktoni përmes faqes sonë të kontaktit.</p>
            </div>
        </div>
    </main>

    <?php include 'footer.php'; ?>
    <div class="page-transition-overlay"></div>
    <script src="script.js"></script>
</body>
</html>
