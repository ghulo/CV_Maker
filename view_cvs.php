<?php
// cv_maker/view_cvs.php
ob_start(); // Start output buffering
session_start(); // Start session at the very beginning

// --- Error Reporting (Development) ---
error_reporting(E_ALL);
ini_set('display_errors', 1); // Set to 0 for production
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/php_errors.log');

$user_id_session = $_SESSION['user_id'] ?? null;
$header_subtitle = "CV-të e Mia";

// --- Authentication ---
if (!$user_id_session) {
    $_SESSION['redirect_after_login'] = 'view_cvs.php';
    $_SESSION['info_message'] = "Ju lutemi kyçuni për të parë CV-të tuaja.";
    header("Location: login.php"); // Redirect to login page
    ob_end_flush();
    exit();
}
$logged_in_user_id = (int)$user_id_session;

// --- Database Connection ---
$db_error_message = null;
$pdo = null;
try {
    require_once 'connect.php'; // Establishes $pdo
} catch (PDOException $e) {
    error_log("CRITICAL DB Connection FAULT in view_cvs.php (initial include): " . $e->getMessage());
    $db_error_message = "Problem kritik me lidhjen e databazës. Ju lutemi provoni më vonë.";
}

$cv_list = [];

// --- Fetch CVs from the 'cvs' table ---
if ($pdo && !$db_error_message) {
    try {
        // Select necessary columns from the 'cvs' table
        $sql = "SELECT id, user_id, cv_title, emri, mbiemri, selected_template, summary, created_at, updated_at
                FROM cvs
                WHERE user_id = :user_id
                ORDER BY updated_at DESC, id DESC"; // Order by last updated date
        $stmt_cv_list = $pdo->prepare($sql);
        if ($stmt_cv_list === false) {
            // Log detailed error if statement preparation fails
            $errorInfo = $pdo->errorInfo();
            throw new PDOException("Failed to prepare statement. SQLSTATE[{$errorInfo[0]}] Driver Code[{$errorInfo[1]}] Message: {$errorInfo[2]}", (int)$errorInfo[0]);
        }
        $stmt_cv_list->bindParam(':user_id', $logged_in_user_id, PDO::PARAM_INT);
        if ($stmt_cv_list->execute()) {
            $cv_list = $stmt_cv_list->fetchAll(PDO::FETCH_ASSOC);
        } else {
            // Log detailed error if statement execution fails
            $errorInfo = $stmt_cv_list->errorInfo();
            if (!$db_error_message) {
                $db_error_message = "Ekzekutimi i kërkesës dështoi: SQLSTATE[{$errorInfo[0]}] - {$errorInfo[2]}";
            }
            error_log("DB Error (view_cvs.php execute failed for user {$logged_in_user_id}): " . ($pdo->errorInfo()[2]) . ". Query: {$sql}");
        }
    } catch (PDOException $e) {
        // Catch and log PDO exceptions during data fetching
        if (!$db_error_message) {
            $db_error_message = "Gabim në databazë gjatë marrjes së CV-ve: " . htmlspecialchars($e->getMessage());
        }
        error_log("PDOException (view_cvs.php data fetch for user {$logged_in_user_id}): " . $e->getMessage() . ". Query: " . (isset($sql) ? $sql : "SQL not prepared"));
    } catch (Exception $e) {
        // Catch and log any other general exceptions
        if (!$db_error_message) {
            $db_error_message = "Gabim i përgjithshëm: " . htmlspecialchars($e->getMessage());
        }
        error_log("General Exception (view_cvs.php for user {$logged_in_user_id}): " . $e->getMessage());
    }
} elseif (!$db_error_message && !$pdo) {
    // Handle case where DB connection failed initially
    $db_error_message = "Lidhja me databazën nuk është e disponueshme.";
}

// --- Message Handling (from GET parameters or Session) ---
$success_message_from_get = isset($_GET['success']) ? htmlspecialchars(urldecode($_GET['success'])) : '';
$error_param_message_from_get = isset($_GET['error']) ? htmlspecialchars(urldecode($_GET['error'])) : '';
$info_message_from_get = isset($_GET['message']) ? htmlspecialchars(urldecode($_GET['message'])) : '';
// Check session for messages and clear them after retrieving
$success_message_from_session = $_SESSION['success_message'] ?? null;
if ($success_message_from_session) unset($_SESSION['success_message']);
$error_message_from_session = $_SESSION['error_message'] ?? null;
if ($error_message_from_session) unset($_SESSION['error_message']);
$info_message_from_session = $_SESSION['info_message'] ?? null;
if ($info_message_from_session) unset($_SESSION['info_message']);

// Determine which message to display (GET takes precedence over Session)
$success_message_display = $success_message_from_get ?: $success_message_from_session;
$error_message_display = $error_param_message_from_get ?: $error_message_from_session;
$info_message_display = $info_message_from_get ?: $info_message_from_session;

// Define current page for header active link class
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="sq">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CV-të e Mia - CV Maker</title>
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
        <div class="cv-list-container page-container animate-in">
             <h2 class="reveal-on-scroll">CV-të e Mia të Ruajtura</h2>
             <p class="page-intro-text reveal-on-scroll" data-reveal-delay="100">Këtu mund të shikoni, modifikoni, ose fshini CV-të që keni krijuar.</p>

            <div class="message-area">
                <?php if ($success_message_display): ?><p class="message success" role="alert"><?php echo $success_message_display; ?></p><?php endif; ?>
                <?php if ($error_message_display): ?><p class="message error" role="alert"><?php echo $error_message_display; ?></p><?php endif; ?>
                <?php if ($info_message_display): ?><p class="message info" role="status"><?php echo $info_message_display; ?></p><?php endif; ?>
            </div>

            <?php if ($db_error_message): // Display database error if any ?>
                <div class="message error" role="alert">
                    <p><?php echo $db_error_message; ?></p>
                </div>
            <?php elseif (empty($cv_list)): // Display empty state if no CVs found ?>
                <div class="empty-state-container reveal-on-scroll" data-reveal-delay="150">
                    <i class="fas fa-folder-open empty-state-icon"></i>
                    <h3>Nuk Keni CV Ende</h3>
                    <p>Duket sikur nuk keni krijuar asnjë CV. Filloni tani duke zgjedhur një model profesional.</p>
                    <a href="choose_template.php" class="btn-create btn-large btn btn-primary" style="margin-top: var(--space-md);"><i class="fas fa-plus-circle"></i> Krijo CV të Re Tani</a>
                </div>
            <?php else: // Display the list of CVs ?>
                <div class="list-controls-header reveal-on-scroll" data-reveal-delay="150">
                    <div class="filter-sort-area">
                        <div class="form-group">
                            <label for="search-cvs"><i class="fas fa-search"></i> Kërko CV:</label>
                            <input type="text" id="search-cvs" placeholder="Titulli, Emri, Modeli...">
                        </div>
                        <div class="form-group">
                            <label for="sort-cvs"><i class="fas fa-sort-amount-down"></i> Rendit sipas:</label>
                            <select id="sort-cvs">
                                <option value="date_desc">Më të rejat (Modifikuar)</option>
                                <option value="date_asc">Më të vjetrat (Modifikuar)</option>
                                <option value="title_asc">Titullit (A-Z)</option>
                                <option value="title_desc">Titullit (Z-A)</option>
                                <option value="name_asc">Emrit (A-Z)</option>
                                <option value="name_desc">Emrit (Z-A)</option>
                            </select>
                        </div>
                    </div>
                     <a href="choose_template.php" class="btn btn-primary btn-create-list-header"><i class="fas fa-plus"></i> Krijo të Re</a>
                </div>

                <div class="loading-spinner" id="cv-list-loading" style="display: none;"></div>

                <ul class="cv-list" id="cv-list">
                    <?php
                    $delay_increment = 50; // Delay increment for staggered animation
                    $initial_delay = 200; // Initial delay for the first item
                    $item_count = 0;
                    foreach ($cv_list as $cv_entry):
                        $item_count++;
                        $current_delay = $initial_delay + ($item_count * $delay_increment);
                        // Determine the display title for the CV
                        $display_title = !empty($cv_entry['cv_title']) ? htmlspecialchars($cv_entry['cv_title']) : (trim(($cv_entry['emri'] ?? '') . ' ' . ($cv_entry['mbiemri'] ?? '')) ?: 'CV e Paemërtuar');
                    ?>
                        <li class="cv-item reveal-on-scroll"
                            data-reveal-delay="<?php echo $current_delay; ?>"
                            data-cv-id="<?php echo htmlspecialchars($cv_entry['id']); ?>"
                            data-title="<?php echo htmlspecialchars($display_title); ?>"
                            data-name="<?php echo htmlspecialchars(trim(($cv_entry['emri'] ?? '') . ' ' . ($cv_entry['mbiemri'] ?? ''))); ?>"
                            data-date="<?php echo htmlspecialchars($cv_entry['updated_at'] ?? $cv_entry['created_at'] ?? ''); ?>"
                            data-template="<?php echo htmlspecialchars($cv_entry['selected_template'] ?? 'classic'); ?>"
                            data-summary-keywords="<?php echo htmlspecialchars(substr($cv_entry['summary'] ?? '', 0, 70)); ?>">

                            <div class="cv-item-main-info">
                                <div class="cv-item-icon">
                                    <i class="fas <?php
                                        // Choose icon based on template
                                        switch (strtolower($cv_entry['selected_template'] ?? 'classic')) {
                                            case 'modern': echo 'fa-drafting-compass'; break;
                                            case 'professional': echo 'fa-briefcase'; break;
                                            case 'classic': default: echo 'fa-file-alt'; break;
                                        }
                                    ?>"></i>
                                </div>
                                <div class="cv-info">
                                    <strong><?php echo $display_title; ?></strong>
                                    <?php if (!empty($cv_entry['cv_title']) && (!empty($cv_entry['emri']) || !empty($cv_entry['mbiemri']))): // Show name if title is set ?>
                                        <span class="cv-name-secondary">(<?php echo htmlspecialchars(trim($cv_entry['emri'] . ' ' . $cv_entry['mbiemri'])); ?>)</span>
                                    <?php endif; ?>
                                    <span class="cv-template">Modeli: <?php echo ucfirst(htmlspecialchars($cv_entry['selected_template'] ?? 'Klasik')); ?></span>
                                    <span class="cv-last-updated">Modifikuar:
                                        <?php
                                        $date_to_display = $cv_entry['updated_at'] ?? $cv_entry['created_at'] ?? null;
                                        echo htmlspecialchars($date_to_display ? date("d M Y, H:i", strtotime($date_to_display)) : 'Data nuk dihet');
                                        ?>
                                    </span>
                                </div>
                            </div>
                            <div class="cv-actions">
                                <button class="btn btn-icon quick-preview-btn" data-cv-id="<?php echo htmlspecialchars($cv_entry['id']); ?>" aria-label="Parapamje e shpejtë" title="Parapamje e shpejtë">
                                     <i class="fas fa-search-plus"></i>
                                </button>
                                <a href="preview_loader.php?id=<?php echo htmlspecialchars($cv_entry['id']); ?>" class="btn btn-icon view-edit" title="Shiko dhe Menaxho CV-në">
                                    <i class="fas fa-eye"></i>
                                </a>
                                 <a href="form.php?edit_id=<?php echo htmlspecialchars($cv_entry['id']); ?>" class="btn btn-icon edit-cv-btn" title="Modifiko CV-në">
                                    <i class="fas fa-pencil-alt"></i>
                                </a>
                                <a href="delete_cv.php?id=<?php echo htmlspecialchars($cv_entry['id']); ?>" class="btn btn-icon delete"
                                   onclick="return confirm('Jeni absolutisht të sigurt që dëshironi ta fshini këtë CV? Ky veprim nuk mund të kthehet.');"
                                   aria-label="Fshi CV-në" title="Fshi CV-në">
                                    <i class="fas fa-trash-alt"></i>
                                </a>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
                 <p class="no-cvs-message" id="no-search-results-message" style="display:none; margin-top:20px; text-align:center;">Asnjë CV nuk përputhet me kërkimin tuaj.</p>
            <?php endif; ?>
        </div>
    </main>

    <footer class="footer"><p>© <span id="current-year"><?php echo date("Y"); ?></span> CV Maker - Të gjitha të drejtat e rezervuara</p></footer>

    <div class="modal-overlay" id="preview-modal-overlay" aria-hidden="true" role="dialog" aria-modal="true">
        <div class="modal-content" id="preview-modal-content" aria-labelledby="modal-title-h">
            <button class="modal-close-button" id="modal-close-button" aria-label="Mbyll dritaren"><i class="fas fa-times"></i></button>
            <h2 id="modal-title-h" class="modal-title">Parapamje e Shpejtë e CV-së</h2>
            <div id="modal-body-content">
                <div class="loading-spinner" id="modal-loading-spinner" style="display: none;"></div>
                <div id="cv-preview-area"></div> </div>
        </div>
    </div>

    <script src="script.js"></script>
</body>
</html>
<?php
ob_end_flush(); // Flush the output buffer
?>
