<?php
// cv_maker/choose_template.php
session_start();
// --- Error Reporting ---
error_reporting(E_ALL);
ini_set('display_errors', 1); // Set to 0 for production
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/php_errors.log');

$user_id = $_SESSION['user_id'] ?? null;
$header_subtitle = "Hapi 1: Zgjidh Modelin";

// If a template is selected via GET parameter
if (isset($_GET['template'])) {
    $selected_template_from_get = $_GET['template'];
    $allowed_templates = ['classic', 'modern', 'professional'];

    if (in_array($selected_template_from_get, $allowed_templates)) {
        if (!$user_id) {
            // User not logged in, redirect to login, pass template choice and return URL
            $redirect_url = 'form.php?template=' . urlencode($selected_template_from_get); // After login, go to form with template
            $_SESSION['redirect_after_login'] = $redirect_url;
            $_SESSION['info_message'] = "Ju lutemi kyçuni ose regjistrohuni për të vazhduar me modelin e zgjedhur.";
            header("Location: login.php"); // Redirect to login.php
            exit();
        }
        // User is logged in, proceed to set template and go to form
        $_SESSION['selected_template'] = $selected_template_from_get;
        unset($_SESSION['cv_id']); // Ensure a fresh start for a new CV with the new template
        header("Location: form.php");
        exit();
    } else {
        $_SESSION['error_message_template_choice'] = "Model i pavlefshëm i zgjedhur.";
        header("Location: choose_template.php"); // Redirect back to choose_template to show error
        exit();
    }
}

// If user lands on choose_template.php without a GET param, but is not logged in,
// we don't strictly need to redirect them yet, they can browse.
// The actual gating will happen when they click a template.

$error_message_display = null;
if (isset($_SESSION['error_message_template_choice'])) {
    $error_message_display = htmlspecialchars($_SESSION['error_message_template_choice']);
    unset($_SESSION['error_message_template_choice']);
} elseif (isset($_GET['error'])) {
    $error_message_display = htmlspecialchars(urldecode($_GET['error']));
}

$info_message_display = null;
if(isset($_SESSION['info_message'])){ // Display info message from login redirect
    $info_message_display = htmlspecialchars($_SESSION['info_message']);
    unset($_SESSION['info_message']);
}

// Define current page for header active link class
$current_page = basename($_SERVER['PHP_SELF']);

?>
<!DOCTYPE html>
<html lang="sq">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CV Maker - Zgjidh Modelin</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body>
    <div class="background-cvs-container" aria-hidden="true">
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
                            </div>
                      <div class="header-actions-group">
                <button id="theme-toggle-button" aria-label="Toggle theme">
                    <i class="fas fa-moon"></i>
                </button>
            </div>
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
        <div class="template-selection-page-container page-container animate-in">
            <h2 class="reveal-on-scroll">Zgjidhni Modelin për CV-në Tuaj</h2>
            <p class="page-intro-text reveal-on-scroll" data-reveal-delay="100">Çdo model ofron një pamje unike. Klikoni mbi një model për të filluar plotësimin e të dhënave tuaja.</p>

            <div class="message-area">
                <?php if ($error_message_display): ?><p class="message error" role="alert"><?php echo $error_message_display; ?></p><?php endif; ?>
                <?php if ($info_message_display): ?><p class="message info" role="status"><?php echo $info_message_display; ?></p><?php endif; ?>
            </div>

            <div class="template-options-grid">
                <a href="choose_template.php?template=classic" class="template-option-card reveal-on-scroll" data-reveal-delay="150" data-template="classic">
                    <div class="template-thumbnail">
                        <div class="thumb-header">EMRI MBIEMRI</div>
                        <div class="thumb-line bg-muted"></div>
                        <div class="thumb-line bg-primary w-80"></div>
                        <div class="thumb-line bg-muted w-90"></div>
                        <div class="thumb-line bg-primary w-70"></div>
                        <div class="thumb-line bg-muted w-full"></div>
                    </div>
                    <span class="template-name">Modeli Klasik</span>
                    <p class="template-description">Një dizajn tradicional dhe i qartë, ideal për shumicën e profesioneve.</p>
                </a>

                <a href="choose_template.php?template=modern" class="template-option-card reveal-on-scroll" data-reveal-delay="200" data-template="modern">
                    <div class="template-thumbnail">
                        <div class="thumb-modern-layout">
                            <div class="thumb-sidebar">
                                <div class="thumb-avatar"></div>
                                <div class="thumb-line bg-primary w-70 mt-sm"></div>
                                <div class="thumb-line bg-muted w-90"></div>
                            </div>
                            <div class="thumb-main-content">
                                <div class="thumb-header-sm">EMRI MBIEMRI</div>
                                <div class="thumb-line bg-muted w-60"></div>
                                <div class="thumb-line bg-primary w-full mt-sm"></div>
                                <div class="thumb-line bg-muted w-80"></div>
                            </div>
                        </div>
                    </div>
                    <span class="template-name">Modeli Modern</span>
                    <p class="template-description">Një pamje bashkëkohore me fokus në dizajn dhe lexueshmëri.</p>
                </a>

                <a href="choose_template.php?template=professional" class="template-option-card reveal-on-scroll" data-reveal-delay="250" data-template="professional">
                    <div class="template-thumbnail">
                         <div class="thumb-header-alt">EMRI MBIEMRI</div>
                         <div class="thumb-line bg-primary w-50 mx-auto mb-sm"></div>
                         <div class="thumb-modern-layout">
                            <div class="thumb-sidebar-alt">
                                <div class="thumb-line bg-muted w-full"></div>
                                <div class="thumb-line bg-muted w-80"></div>
                            </div>
                            <div class="thumb-main-content-alt">
                                <div class="thumb-line bg-primary w-full"></div>
                                <div class="thumb-line bg-muted w-90"></div>
                                <div class="thumb-line bg-muted w-full"></div>
                            </div>
                        </div>
                    </div>
                    <span class="template-name">Modeli Profesional</span>
                    <p class="template-description">Elegant dhe i strukturuar, perfekt për role ekzekutive dhe korporative.</p>
                </a>
            </div>

            <div class="page-actions" style="margin-top: var(--space-xl);">
                <?php if ($user_id): // Show link to My CVs if logged in ?>
                    <a href="view_cvs.php" class="btn btn-secondary reveal-on-scroll" data-reveal-delay="300"><i class="fas fa-arrow-left"></i> Kthehu te CV-të e Mia</a>
                <?php else: // Show login/signup prompt if not logged in ?>
                     <p class="form-alternate-action reveal-on-scroll" data-reveal-delay="300">Keni një llogari? <a href="login.php">Kyçu</a> për të ruajtur dhe menaxhuar CV-të.</p>
                <?php endif; ?>
            </div>
        </div>
    </main>
    <?php include 'footer.php'; ?>

    <div class="page-transition-overlay"></div>
    <script src="script.js"></script>
</body>
</html>
