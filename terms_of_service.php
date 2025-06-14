<?php
session_start();
$user_id = $_SESSION['user_id'] ?? null;
$header_subtitle = "Termat e Shërbimit";
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="sq">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Termat e Shërbimit - CV Maker</title>
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
            <h2 class="reveal-on-scroll">Termat dhe Kushtet e Shërbimit</h2>
            <p class="page-intro-text reveal-on-scroll" data-reveal-delay="100">Ju lutemi lexoni me kujdes këto terma dhe kushte përpara se të përdorni shërbimin tonë.</p>

            <div class="content-section reveal-on-scroll" data-reveal-delay="150">
                <h3>1. Pranimi i Termave</h3>
                <p>Duke hyrë ose përdorur shërbimin tonë, ju pranoni të jeni të detyruar nga këto Terma. Nëse nuk pajtoheni me ndonjë pjesë të termave, atëherë nuk mund të përdorni shërbimin.</p>
            </div>

            <div class="content-section reveal-on-scroll" data-reveal-delay="200">
                <h3>2. Llogaritë e Përdoruesit</h3>
                <p>Kur krijoni një llogari me ne, duhet të na jepni informacion që është i saktë, i plotë dhe aktual në çdo kohë. Dështimi për ta bërë këtë përbën shkelje të Termave, e cila mund të rezultojë në ndërprerjen e menjëhershme të llogarisë tuaj në shërbimin tonë.</p>
            </div>

            <div class="content-section reveal-on-scroll" data-reveal-delay="250">
                <h3>3. Pronësia Intelektuale</h3>
                <p>Shërbimi dhe përmbajtja e tij origjinale (duke përjashtuar përmbajtjen e ofruar nga përdoruesit), veçoritë dhe funksionaliteti janë dhe do të mbeten pronë ekskluzive e CV Maker dhe licencuesve të tij.</p>
            </div>

            <div class="content-section reveal-on-scroll" data-reveal-delay="300">
                <h3>4. Ndërprerja</h3>
                <p>Ne mund të ndërpresim ose pezullojmë menjëherë aksesin në shërbimin tonë, pa njoftim paraprak ose përgjegjësi, për çdo arsye, duke përfshirë pa kufizim nëse shkelni Termat.</p>
            </div>

            <div class="content-section reveal-on-scroll" data-reveal-delay="350">
                <h3>5. Ligji Qeverisës</h3>
                <p>Këto Terma do të rregullohen dhe interpretohen në përputhje me ligjet e juridiksionit ku operon CV Maker, pa marrë parasysh konfliktin e dispozitave ligjore.</p>
            </div>
        </div>
    </main>

       <?php include 'footer.php'; ?>
    <div class="page-transition-overlay"></div>
    <script src="script.js"></script>
</body>
</html>
