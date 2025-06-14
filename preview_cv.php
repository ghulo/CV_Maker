<?php
// cv_maker/preview_cv.php
session_start();

// --- Error Reporting (Development) ---
error_reporting(E_ALL);
ini_set('display_errors', 1); // Set to 0 for production
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/php_errors.log');

require_once 'template_renderers.php'; // For rendering HTML based on template

// Define current page for active navigation links
$current_page = basename($_SERVER['PHP_SELF']); // Gets 'preview_cv.php'

$user_id_session = $_SESSION['user_id'] ?? null;
$header_subtitle = "Parapamja e CV-së";
$page_error_message = null;
$pdo = null;

// --- Authentication and CV ID Check ---
if (!$user_id_session) {
    // If there's a cv_id in session from a previous attempt, preserve it for after login
    if (isset($_SESSION['cv_id'])) { // Check for 'cv_id'
        $_SESSION['load_cv_id_after_login'] = $_SESSION['cv_id'];
    }
    $_SESSION['redirect_after_login'] = 'preview_cv.php'; // Redirect back to this page
    header("Location: login.html?error=" . urlencode("Ju lutemi kyçuni për të parë CV-në."));
    exit();
}
$logged_in_user_id = (int)$user_id_session;

// Use 'cv_id' from session, which should be set by preview_loader.php or save_experience.php
if (!isset($_SESSION['cv_id']) || !is_numeric($_SESSION['cv_id']) || $_SESSION['cv_id'] <= 0) {
    // This error message is one you saw in the screenshot.
    // It means $_SESSION['cv_id'] was not properly set before redirecting here.
    $_SESSION['error_message'] = "Nuk ka asnjë CV të zgjedhur për parapamje. Ju lutemi zgjidhni një CV nga lista.";
    header("Location: view_cvs.php");
    exit();
}
$cv_id_to_preview = (int)$_SESSION['cv_id'];


// --- Database Connection ---
try {
    require_once 'connect.php'; // Establishes $pdo
} catch (PDOException $e) {
    error_log("CRITICAL Database Connection Failed in preview_cv.php: " . $e->getMessage());
    $page_error_message = "Problem kritik me lidhjen e databazës. Ju lutemi provoni më vonë.";
    // $pdo remains null
}

// --- Data Fetching ---
$cv_details = null; // Changed from $personal_info to be more generic for 'cvs' table
$work_experiences = [];
$educations = [];
$interests_text = '';
$selected_template_from_db = 'classic'; // Default

if ($pdo && !$page_error_message) {
    try {
        // Fetch from 'cvs' table using 'id' as primary key
        $stmt_cv = $pdo->prepare("SELECT * FROM cvs WHERE id = :cv_id AND user_id = :user_id");
        $stmt_cv->execute([':cv_id' => $cv_id_to_preview, ':user_id' => $logged_in_user_id]);
        $cv_details = $stmt_cv->fetch(PDO::FETCH_ASSOC);

        if (!$cv_details) {
            unset($_SESSION['cv_id']); // Clear invalid cv_id from session
            if (!$page_error_message) {
                 $page_error_message = "CV nuk u gjet ose nuk keni qasje për ta parë atë.";
            }
            error_log("Access Denied/Not Found (preview_cv.php): User {$logged_in_user_id} tried to access CV ID {$cv_id_to_preview} from 'cvs' table.");
        } else {
            $selected_template_from_db = $cv_details['selected_template'] ?? 'classic';
            $_SESSION['selected_template'] = $selected_template_from_db; // Keep session template consistent
            $header_subtitle = "Parapamja: " . htmlspecialchars(($cv_details['cv_title'] ?? ($cv_details['emri'] . ' ' . $cv_details['mbiemri'])));

            // Fetch related data using cv_id (which is $cv_details['id'])
            $stmt_work = $pdo->prepare("SELECT * FROM work_experiences WHERE cv_id = :cv_id ORDER BY start_date DESC, id DESC");
            $stmt_work->execute([':cv_id' => $cv_details['id']]);
            $work_experiences = $stmt_work->fetchAll(PDO::FETCH_ASSOC);

            $stmt_edu = $pdo->prepare("SELECT * FROM educations WHERE cv_id = :cv_id ORDER BY graduation_year DESC, id DESC");
            $stmt_edu->execute([':cv_id' => $cv_details['id']]);
            $educations = $stmt_edu->fetchAll(PDO::FETCH_ASSOC);

            $stmt_interests = $pdo->prepare("SELECT description FROM interests WHERE cv_id = :cv_id LIMIT 1");
            $stmt_interests->execute([':cv_id' => $cv_details['id']]);
            $interests_data = $stmt_interests->fetch(PDO::FETCH_ASSOC);
            if ($interests_data && isset($interests_data['description'])) {
                $interests_text = $interests_data['description'];
            }
            // Skills would be fetched similarly if they were part of the preview page's direct display
            // $stmt_skills = $pdo->prepare("SELECT * FROM skills WHERE cv_id = :cv_id ORDER BY category, skill_name");
            // $stmt_skills->execute([':cv_id' => $cv_details['id']]);
            // $skills = $stmt_skills->fetchAll(PDO::FETCH_ASSOC);
        }
    } catch (PDOException $e) {
        error_log("DB Error (preview_cv.php data fetching for CV ID {$cv_id_to_preview}, User {$logged_in_user_id}): " . $e->getMessage());
        if (!$page_error_message) {
            $page_error_message = "Gabim serveri gjatë ngarkimit të të dhënave të CV-së. Ju lutemi provoni më tepër.";
        }
    }
} elseif (!$page_error_message) { // If $pdo was null and no specific error
    $page_error_message = "Lidhja me bazën e të dhënave nuk është e disponueshme.";
}

// --- Message Handling from Session ---
$success_message_display = null;
if (isset($_SESSION['success_message_exp'])) { // Typically from save_experience.php
    $success_message_display = $_SESSION['success_message_exp'];
    unset($_SESSION['success_message_exp']);
} elseif (isset($_SESSION['success_message'])) { // Typically from save_personal.php (though it redirects to experience)
    $success_message_display = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}

$page_title_main = "Parapamja e CV-së";
if ($cv_details && (isset($cv_details['cv_title']) || isset($cv_details['emri']))) { // Use $cv_details
    $page_title_main .= " - " . htmlspecialchars($cv_details['cv_title'] ?? ($cv_details['emri'] . ' ' . $cv_details['mbiemri']));
}

?>
<!DOCTYPE html>
<html lang="sq">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title_main); ?></title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <?php if ($success_message_display): // Clear local storage if CV was just successfully saved/updated ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof clearPersonalFormLocalStorage === 'function') {
                clearPersonalFormLocalStorage();
            }
            if (typeof clearExperienceFormLocalStorage === 'function') {
                clearExperienceFormLocalStorage();
            }
        });
    </script>
    <?php endif; ?>
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

            <nav class="header-nav" id="desktop-nav-menu-id"> <a href="index.php" class="<?php echo $current_page == 'index.php' ? 'active' : ''; ?>">Home</a>
                <a href="choose_template.php" class="<?php echo $current_page == 'choose_template.php' ? 'active' : ''; ?>">Templates</a>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="view_cvs.php" class="<?php echo $current_page == 'view_cvs.php' ? 'active' : ''; ?>">My CVs</a>
                    <a href="form.php" class="<?php echo $current_page == 'form.php' ? 'active' : ''; ?>">Create CV</a>
                <?php endif; ?>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="logout.php" class="auth-link logout-link btn btn-secondary">Logout</a>
                <?php else: ?>
                    <a href="login.php" class="auth-link login-link <?php echo $current_page == 'login.php' ? 'active' : ''; ?>">Login</a>
                    <a href="signup.php" class="auth-link signup-link btn <?php echo $current_page == 'signup.php' ? 'active' : ''; ?>">Sign Up</a>
                <?php endif; ?>
            </nav>

            <div class="header-actions-group">
                <button id="theme-toggle-button" aria-label="Toggle theme">
                    <i class="fas fa-moon"></i>
                </button>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="profile.php" class="profile-icon-btn" aria-label="View Profile">
                        <i class="fas fa-user-circle"></i> </a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <nav class="mobile-nav-menu" id="mobile-nav-menu-id">
        <a href="index.php" class="<?php echo $current_page == 'index.php' ? 'active' : ''; ?>">Home</a>
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
        <div class="cv-preview-page-wrapper page-container animate-in">
             <h2 class="reveal-on-scroll"><?php echo htmlspecialchars($page_title_main); ?></h2>

            <?php if ($success_message_display): // Display success message if any ?>
                <div class="message-area" style="margin-bottom: var(--space-md);">
                    <p class="message success" role="alert"><?php echo htmlspecialchars($success_message_display); ?></p>
                </div>
            <?php endif; ?>

            <?php if (isset($page_error_message)): // Display page error message if any ?>
                <div class="message-area" style="text-align:center;">
                    <p class="message error" role="alert"><?php echo $page_error_message; ?></p>
                    <div class="page-actions" style="margin-top: var(--space-md);">
                        <a href="view_cvs.php" class="btn btn-secondary"><i class="fas fa-list-ul"></i> Kthehu te CV-të e Mia</a>
                    </div>
                </div>
            <?php elseif ($cv_details): // Check $cv_details instead of $personal_info ?>
                <div class="cv-preview-actions reveal-on-scroll" data-reveal-delay="100">
                    <a href="form.php?edit_id=<?php echo $cv_id_to_preview; ?>" class="btn btn-secondary"><i class="fas fa-edit"></i> Modifiko Këtë CV</a>
                    <a href="download_cv.php?id=<?php echo $cv_id_to_preview; ?>" class="btn btn-primary" target="_blank"><i class="fas fa-file-pdf"></i> Shkarko si PDF</a>
                    <a href="view_cvs.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kthehu te Lista</a>
                </div>

                <div class="cv-preview-content-area template-<?php echo htmlspecialchars($selected_template_from_db); ?> reveal-on-scroll" data-reveal-delay="150">
                    <?php
                        // Render the CV preview HTML based on the selected template
                        // Pass $cv_details (which contains all columns from 'cvs' table)
                        // The render functions expect the first parameter to be the main CV data array.
                        switch ($selected_template_from_db) {
                            case 'modern':
                                echo render_modern_preview_html($cv_details, $work_experiences, $educations, $interests_text);
                                break;
                            case 'professional':
                                echo render_professional_preview_html($cv_details, $work_experiences, $educations, $interests_text);
                                break;
                            case 'classic':
                            default:
                                echo render_classic_preview_html($cv_details, $work_experiences, $educations, $interests_text);
                                break;
                        }
                    ?>
                </div>
            <?php else: // Display generic error if CV details couldn't be loaded ?>
                <?php if (!isset($page_error_message)): // If no specific DB error, show generic load fail ?>
                <div class="message-area" style="text-align:center;">
                    <p class="message error" role="alert">Nuk mund të ngarkoheshin të dhënat e CV-së. Provoni të ktheheni te lista e CV-ve.</p>
                    <div class="page-actions" style="margin-top: var(--space-md);">
                        <a href="view_cvs.php" class="btn btn-secondary"><i class="fas fa-list-ul"></i> Kthehu te CV-të e Mia</a>
                    </div>
                </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </main>

    <?php include 'footer.php'; ?>
    <div class="page-transition-overlay"></div>
    <script src="script.js"></script>
</body>
</html>