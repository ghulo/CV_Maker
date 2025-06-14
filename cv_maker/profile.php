<?php
// cv_maker/profile.php
session_start();
ob_start(); // Start output buffering

// --- Error Reporting (Development) ---
error_reporting(E_ALL);
ini_set('display_errors', 1); // Set to 0 in production
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/php_errors.log'); // Ensure this path is writable

$user_id_session = $_SESSION['user_id'] ?? null;
$user_email_session = $_SESSION['user_email'] ?? 'Përdorues'; // Default email display
$header_subtitle = "Profili Im & Cilësimet";

// --- Authentication ---
if (!$user_id_session) {
    $_SESSION['redirect_after_login'] = 'profile.php'; // Set redirect to this page after login
    $_SESSION['info_message'] = "Ju lutemi kyçuni për të parë profilin tuaj.";
    header("Location: login.php"); // Redirect to login page
    ob_end_flush(); // Flush buffer and exit
    exit();
}
$logged_in_user_id = (int)$user_id_session;

// --- Database Connection ---
$db_error_message = null;
$user_details = null;
$pdo = null;
try {
    require_once 'connect.php'; // Establishes $pdo
    // Fetch user details from the database
    $stmt_user = $pdo->prepare("SELECT email, created_at FROM users WHERE id = :user_id");
    $stmt_user->bindParam(':user_id', $logged_in_user_id, PDO::PARAM_INT);
    $stmt_user->execute();
    $user_details = $stmt_user->fetch(PDO::FETCH_ASSOC);

    if (!$user_details) {
        // If user details not found (shouldn't happen with valid session)
        $db_error_message = "Të dhënat e përdoruesit nuk u gjetën.";
        error_log("Profile page: User details not found for user_id: " . $logged_in_user_id);
    } else {
        $user_email_session = $user_details['email']; // Update email display from DB
    }

} catch (PDOException $e) {
    // Catch and log database exceptions
    error_log("DB Connection/Query FAULT in profile.php: " . $e->getMessage());
    $db_error_message = "Problem me lidhjen e databazës ose ngarkimin e të dhënave.";
}

// --- Message Handling (from Session or GET) ---
$success_message_display = null;
// Check session for success message and clear it
if (isset($_SESSION['success_message_profile'])) {
    $success_message_display = htmlspecialchars(urldecode($_SESSION['success_message_profile']));
    unset($_SESSION['success_message_profile']);
} elseif (isset($_GET['success'])) { // Check GET for success message
    $success_message_display = htmlspecialchars(urldecode($_GET['success']));
}

$error_message_display = null;
// Check session for error message and clear it
if (isset($_SESSION['error_message_profile'])) {
    $error_message_display = htmlspecialchars(urldecode($_SESSION['error_message_profile']));
    unset($_SESSION['error_message_profile']);
} elseif (isset($_GET['error'])) { // Check GET for error message (e.g., from redirect)
    $error_message_display = htmlspecialchars(urldecode($_GET['error']));
}

// Define current page for header active link class
$current_page = basename($_SERVER['PHP_SELF']);

?>
<!DOCTYPE html>
<html lang="sq">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profili Im - CV Maker</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
        <div class="profile-container page-container animate-in">
            <h2 class="reveal-on-scroll">Profili dhe Cilësimet Tuaja</h2>

            <div class="message-area">
                <?php if ($success_message_display): ?><p class="message success" role="alert"><?php echo $success_message_display; ?></p><?php endif; ?>
                <?php if ($error_message_display): ?><p class="message error" role="alert"><?php echo $error_message_display; ?></p><?php endif; ?>
                <?php if ($db_error_message && !$success_message_display && !$error_message_display): ?><p class="message error" role="alert"><?php echo htmlspecialchars($db_error_message); ?></p><?php endif; ?>
            </div>

            <?php if ($user_details): // Display profile details if user data was fetched ?>

            <div class="profile-layout-grid">

                <div class="profile-section reveal-on-scroll" data-reveal-delay="100">
                    <h3><i class="fas fa-id-card"></i>Detajet e Llogarisë</h3>
                    <div class="profile-detail">
                        <strong>Email:</strong> <?php echo htmlspecialchars($user_details['email']); ?>
                    </div>
                    <div class="profile-detail">
                        <strong>Regjistruar më:</strong> <?php echo htmlspecialchars(date("d M Y, H:i", strtotime($user_details['created_at']))); ?>
                    </div>
                    <div class="profile-detail" style="margin-top: var(--space-md);">
                        <a href="view_cvs.php" class="btn btn-secondary"><i class="fas fa-file-alt"></i> Shiko CV-të e Mia</a>
                    </div>
                </div>

                <div class="profile-section reveal-on-scroll" data-reveal-delay="150">
                    <h3><i class="fas fa-palette"></i>Cilësimet e Pamjes</h3>
                    <div class="form-group">
                        <label style="font-weight: 500; display:block; margin-bottom: var(--space-xs);">Zgjidh Temën e Preferuar:</label>
                        <div class="theme-selector-group">
                            <label for="theme-light">
                                <input type="radio" id="theme-light" name="theme_preference_profile" value="light">
                                <i class="fas fa-sun"></i> Dritë
                            </label>
                            <label for="theme-dark">
                                <input type="radio" id="theme-dark" name="theme_preference_profile" value="dark">
                                <i class="fas fa-moon"></i> Errët
                            </label>
                        </div>
                    </div>
                </div>

                <div class="profile-section reveal-on-scroll" data-reveal-delay="200">
                    <h3><i class="fas fa-key"></i>Ndrysho Fjalëkalimin</h3>
                    <form action="process_change_password.php" method="POST" id="changePasswordForm">
                        <div class="form-group">
                            <label for="current_password">Fjalëkalimi Aktual <span class="required">*</span></label>
                            <div class="input-icon-wrapper">
                                <i class="fas fa-unlock-alt input-icon"></i>
                                <input type="password" name="current_password" id="current_password" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="new_password">Fjalëkalimi i Ri <span class="required">*</span></label>
                             <div class="input-icon-wrapper">
                                <i class="fas fa-lock input-icon"></i>
                                <input type="password" name="new_password" id="new_password" required minlength="6" placeholder="Minimumi 6 karaktere">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="confirm_new_password">Konfirmo Fjalëkalimin e Ri <span class="required">*</span></label>
                            <div class="input-icon-wrapper">
                                <i class="fas fa-check-circle input-icon"></i>
                                <input type="password" name="confirm_new_password" id="confirm_new_password" required minlength="6">
                            </div>
                        </div>
                        <button type="submit" class="btn-primary-form btn btn-primary" style="margin-top: var(--space-md);">Ndrysho Fjalëkalimin <i class="fas fa-save icon-right"></i></button>
                    </form>
                </div>

                <div class="profile-section danger-zone reveal-on-scroll" data-reveal-delay="300">
                    <h3><i class="fas fa-exclamation-triangle"></i>Zonë e Rrezikshme</h3>
                    <p>Kujdes: Fshirja e llogarisë është veprim permanent dhe do të fshijë të gjitha CV-të tuaja dhe të dhënat e lidhura.</p>
                    <a href="delete_profile.php" class="btn btn-danger" style="margin-top: var(--space-sm);">
                        <i class="fas fa-trash-alt"></i> Fshi Llogarinë Time
                    </a>
                </div>

            </div>

            <?php else: // Display error if user details couldn't be fetched ?>
                <?php if (!$db_error_message): ?>
                <p class="message error">Nuk mund të ngarkoheshin detajet e profilit.</p>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </main>
    
    <?php include 'footer.php'; ?>
    <div class="page-transition-overlay"></div>
    <script src="script.js"></script>
</body>
</html>
<?php ob_end_flush(); ?>