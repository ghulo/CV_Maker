<?php
// cv_maker/form.php
session_start();

// --- Error Reporting (Development) ---
error_reporting(E_ALL);
ini_set('display_errors', 1); // Set to 0 for production
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/php_errors.log');

$user_id_session = $_SESSION['user_id'] ?? null;

// --- Authentication: Redirect to login if user is not logged in ---
if (!$user_id_session) {
    $_SESSION['redirect_after_login'] = 'form.php';
    // Preserve GET parameters for redirect after login
    if (isset($_GET['template'])) {
        $_SESSION['redirect_after_login'] .= '?template=' . urlencode($_GET['template']);
    } elseif (isset($_GET['edit_id'])) {
         $_SESSION['redirect_after_login'] .= '?edit_id=' . urlencode($_GET['edit_id']);
    }
    $_SESSION['info_message'] = "Ju lutemi kyçuni për të plotësuar informacionin personal.";
    header("Location: login.php"); // Redirect to login page
    exit();
}
$logged_in_user_id = (int)$user_id_session;

// --- Initialize variables ---
$form_data = [];
$page_title_action = "Plotëso";
$header_subtitle = "Hapi 1: Informacioni Personal";
$current_cv_id = null; // This will hold the ID from the 'cvs' table
$is_editing = false;

// --- Database Connection ---
$pdo = null;
try {
    require_once 'connect.php';
} catch (PDOException $e) {
    error_log("Form.php DB Connection Error: " . $e->getMessage());
    $_SESSION['error_message'] = "Gabim kritik në lidhjen me bazën e të dhënave.";
}

// --- Determine Mode (Editing Existing, Starting New with Template, Resuming Pending, Continuing Session) ---

// 1. Editing an existing CV? (edit_id is the ID from 'cvs' table)
if (isset($_GET['edit_id']) && is_numeric($_GET['edit_id'])) {
    $current_cv_id = (int)$_GET['edit_id'];
    if ($pdo) {
        try {
            // Load CV data from 'cvs' table for the logged-in user
            $stmt_load = $pdo->prepare("SELECT * FROM cvs WHERE id = :cv_id AND user_id = :user_id");
            $stmt_load->execute([':cv_id' => $current_cv_id, ':user_id' => $logged_in_user_id]);
            $loaded_data = $stmt_load->fetch(PDO::FETCH_ASSOC);

            if ($loaded_data) {
                $form_data = $loaded_data;
                $_SESSION['cv_id'] = $current_cv_id; // Set/update 'cv_id' in session
                $_SESSION['selected_template'] = $form_data['selected_template'] ?? 'classic'; // Ensure template is in session
                $page_title_action = "Modifiko";
                $header_subtitle = "Modifiko Informacionin Personal";
                $is_editing = true;
            } else {
                // CV not found or does not belong to the user
                $_SESSION['error_message'] = "CV-ja për modifikim nuk u gjet ose nuk ju përket.";
                unset($_SESSION['cv_id']); // Clear potentially invalid CV ID from session
                $current_cv_id = null; // Reset current CV ID
            }
        } catch (PDOException $e) {
            error_log("Form.php DB Error loading CV ID {$current_cv_id} from 'cvs': " . $e->getMessage());
            $_SESSION['error_message'] = "Gabim gjatë ngarkimit të të dhënave të CV-së.";
            $current_cv_id = null;
        }
    }
}
// 2. Starting a new CV with a template choice?
elseif (isset($_GET['template'])) {
    $allowed_templates = ['classic', 'modern', 'professional'];
    if (in_array($_GET['template'], $allowed_templates)) {
        $_SESSION['selected_template'] = $_GET['template'];
        unset($_SESSION['cv_id']); // Clear for a new CV
        $current_cv_id = null;
        $is_editing = false;
        $page_title_action = "Plotëso";
        // Clear local storage for personal and experience forms when starting a new CV
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                if (typeof clearPersonalFormLocalStorage === 'function') {
                    clearPersonalFormLocalStorage();
                }
                if (typeof clearExperienceFormLocalStorage === 'function') {
                    clearExperienceFormLocalStorage();
                }
            });
        </script>";
    } else {
        // Invalid template selected
        $_SESSION['error_message'] = "Model i pavlefshëm i zgjedhur.";
        header("Location: choose_template.php"); // Redirect back to template selection
        exit();
    }
}
// 3. Resuming after login with pending data? (e.g., user filled form, then logged in)
elseif (isset($_GET['action']) && $_GET['action'] === 'process_pending' && isset($_SESSION['pending_cv_data'])) {
    $form_data = $_SESSION['pending_cv_data']; // Load pending data
    $_SESSION['selected_template'] = $form_data['selected_template'] ?? 'classic'; // Ensure template is set
    unset($_SESSION['cv_id']); // Ensure it's treated as a new CV
    $current_cv_id = null;
    $is_editing = false;
    $page_title_action = "Plotëso";
    $_SESSION['info_message'] = "Plotësoni ose konfirmoni të dhënat tuaja për të vazhduar.";
    unset($_SESSION['pending_cv_data']); // Clear pending data from session
}
// 4. Continuing an existing CV from session (using 'cv_id')?
elseif (isset($_SESSION['cv_id']) && is_numeric($_SESSION['cv_id'])) {
    // Load from DB using the session CV ID if not already in editing mode
    if (!$is_editing && $pdo) {
        $current_cv_id = (int)$_SESSION['cv_id'];
         try {
            $stmt_load = $pdo->prepare("SELECT * FROM cvs WHERE id = :cv_id AND user_id = :user_id");
            $stmt_load->execute([':cv_id' => $current_cv_id, ':user_id' => $logged_in_user_id]);
            $loaded_data = $stmt_load->fetch(PDO::FETCH_ASSOC);
            if ($loaded_data) {
                $form_data = $loaded_data;
                $_SESSION['selected_template'] = $form_data['selected_template'] ?? 'classic';
                $page_title_action = "Modifiko";
                $header_subtitle = "Modifiko Informacionin Personal";
                $is_editing = true;
            } else {
                // CV from session not found or does not belong to user
                unset($_SESSION['cv_id']);
                $current_cv_id = null;
                $_SESSION['error_message'] = "CV-ja e mëparshme nuk u gjet. Fillo një të re.";
            }
        } catch (PDOException $e) {
            error_log("Form.php DB Error loading CV ID {$current_cv_id} from session ('cvs'): " . $e->getMessage());
            $_SESSION['error_message'] = "Gabim gjatë ngarkimit të të dhënave të CV-së nga sesioni.";
            unset($_SESSION['cv_id']);
            $current_cv_id = null;
        }
    }
}


// If no template is selected by this point, redirect to choose template page
if (!$is_editing && !isset($_SESSION['selected_template'])) {
    $_SESSION['error_message'] = "Ju lutemi zgjidhni një model fillimisht.";
    header("Location: choose_template.php");
    exit();
}
$template_to_use_for_form = $_SESSION['selected_template'] ?? 'classic';
$header_subtitle .= " (Modeli: " . ucfirst(htmlspecialchars($template_to_use_for_form)) . ")";


// --- Handle Session Messages ---
$error_message_display = $_SESSION['error_message'] ?? null;
$success_message_display = $_SESSION['success_message'] ?? null;
$info_message_display = $_SESSION['info_message'] ?? null;

// If there's temporary form data from a failed submission, merge it
if (isset($_SESSION['form_data_temp'])) {
    $form_data_from_session_temp = $_SESSION['form_data_temp'];
    $form_data = array_merge($form_data, $form_data_from_session_temp);
    // Ensure template is consistent if it was part of the temp data
    $template_to_use_for_form = $form_data_from_session_temp['selected_template'] ?? $template_to_use_for_form;
    $_SESSION['selected_template'] = $template_to_use_for_form;
    unset($_SESSION['form_data_temp']); // Clear temporary data
}

// Clear messages from session after retrieving them for display
unset($_SESSION['error_message']);
unset($_SESSION['success_message']);
unset($_SESSION['info_message']);

// --- Prepare form field values (keys match columns in 'cvs' table) ---
// Use htmlspecialchars to prevent XSS when displaying data
$cv_title_val = htmlspecialchars($form_data['cv_title'] ?? 'My CV');
$emri_val = htmlspecialchars($form_data['emri'] ?? '');
$mbiemri_val = htmlspecialchars($form_data['mbiemri'] ?? '');
$email_form_val = htmlspecialchars($form_data['email'] ?? '');
$telefoni_val = htmlspecialchars($form_data['telefoni'] ?? ''); // Use 'telefoni' to match DB column name
$address_val = htmlspecialchars($form_data['address'] ?? '');
$summary_val = htmlspecialchars($form_data['summary'] ?? '');
$date_of_birth_val = htmlspecialchars($form_data['date_of_birth'] ?? '');
$place_of_birth_val = htmlspecialchars($form_data['place_of_birth'] ?? '');
$gender_val = htmlspecialchars($form_data['gender'] ?? '');
$nationality_val = htmlspecialchars($form_data['nationality'] ?? '');
$zip_code_val = htmlspecialchars($form_data['zip_code'] ?? '');
$marital_status_val = htmlspecialchars($form_data['marital_status'] ?? '');
$driving_license_val = htmlspecialchars($form_data['driving_license'] ?? '');

// Define current page for header active link class
$current_page = basename($_SERVER['PHP_SELF']);

?>
<!DOCTYPE html>
<html lang="sq">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CV Maker - <?php echo htmlspecialchars($page_title_action); ?> Informacionin Personal</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body class="<?php echo $is_editing ? 'editing-cv' : ''; ?>">
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
        <form action="save_personal.php" method="POST" class="cv-form page-container animate-in">
            <input type="hidden" name="selected_template" value="<?php echo htmlspecialchars($template_to_use_for_form); ?>">
            <?php if ($is_editing && $current_cv_id): // Pass CV ID if editing existing CV ?>
                <input type="hidden" name="cv_id_to_edit" value="<?php echo $current_cv_id; ?>">
            <?php endif; ?>

            <h2 class="reveal-on-scroll"><?php echo htmlspecialchars($page_title_action); ?> Detajet Personale</h2>
            <p class="page-intro-text reveal-on-scroll" style="text-align:left; max-width:none; margin-bottom:var(--space-md);" data-reveal-delay="100">Plotësoni informacionin tuaj personal. Fushat me <span class="required">*</span> janë të detyrueshme.</p>

            <div class="message-area">
                <?php if ($error_message_display): ?><p class="message error" role="alert"><?php echo $error_message_display; /* Allow HTML if errors are formatted with <ul> */ ?></p><?php endif; ?>
                <?php if ($success_message_display): ?><p class="message success" role="status"><?php echo htmlspecialchars($success_message_display); ?></p><?php endif; ?>
                <?php if ($info_message_display): ?><p class="message info" role="status"><?php echo htmlspecialchars($info_message_display); ?></p><?php endif; ?>
            </div>

            <div class="form-section reveal-on-scroll" data-reveal-delay="100">
                <h3>Informacioni Kryesor i CV-së</h3>
                <div class="form-grid">
                    <div class="form-group full-width">
                        <label for="cv_title">Titulli i CV-së (opsionale)</label>
                        <input type="text" name="cv_title" id="cv_title" value="<?php echo $cv_title_val; ?>" placeholder="P.sh., CV për Zhvillues Softueri">
                    </div>
                </div>
            </div>

            <div class="form-section reveal-on-scroll" data-reveal-delay="150">
                <h3>Informacioni Bazë Personal</h3>
                <div class="form-grid">
                    <div class="form-group"><label for="emri">Emri <span class="required">*</span></label><input type="text" name="emri" id="emri" value="<?php echo $emri_val; ?>" required></div>
                    <div class="form-group"><label for="mbiemri">Mbiemri <span class="required">*</span></label><input type="text" name="mbiemri" id="mbiemri" value="<?php echo $mbiemri_val; ?>" required></div>
                    <div class="form-group">
                        <label for="email">Email</label>
                         <div class="input-icon-wrapper">
                             <i class="fas fa-envelope input-icon"></i>
                             <input type="email" name="email" id="email" value="<?php echo $email_form_val; ?>">
                         </div>
                    </div>
                    <div class="form-group">
                        <label for="telefoni">Numri i Telefonit</label>
                        <div class="input-icon-wrapper">
                             <i class="fas fa-phone input-icon"></i>
                             <input type="tel" name="telefoni" id="telefoni" value="<?php echo $telefoni_val; ?>" placeholder="p.sh. +38344123456">
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-section reveal-on-scroll" data-reveal-delay="200">
                <h3>Detaje Shtesë Personale (opsionale)</h3>
                <div class="form-grid">
                     <div class="form-group"><label for="date_of_birth">Datëlindja</label><input type="date" name="date_of_birth" id="date_of_birth" value="<?php echo $date_of_birth_val; ?>" max="<?php echo date('Y-m-d'); ?>"></div>
                    <div class="form-group"><label for="place_of_birth">Vendlindja</label><input type="text" name="place_of_birth" id="place_of_birth" value="<?php echo $place_of_birth_val; ?>"></div>
                    <div class="form-group">
                        <label for="gender">Gjinia</label>
                        <select name="gender" id="gender">
                            <option value="" <?php echo ($gender_val == '') ? 'selected' : ''; ?>>Zgjidh...</option>
                            <option value="Mashkull" <?php echo ($gender_val == 'Mashkull') ? 'selected' : ''; ?>>Mashkull</option>
                            <option value="Femër" <?php echo ($gender_val == 'Femër') ? 'selected' : ''; ?>>Femër</option>
                            <option value="Tjetër" <?php echo ($gender_val == 'Tjetër') ? 'selected' : ''; ?>>Tjetër</option>
                            <option value="Preferoj të mos e them" <?php echo ($gender_val == 'Preferoj të mos e them') ? 'selected' : ''; ?>>Preferoj të mos e them</option>
                        </select>
                    </div>
                    <div class="form-group"><label for="nationality">Kombësia</label><input type="text" name="nationality" id="nationality" value="<?php echo $nationality_val; ?>"></div>
                    <div class="form-group">
                        <label for="marital_status">Statusi Martesor</label>
                        <select name="marital_status" id="marital_status">
                            <option value="" <?php echo ($marital_status_val == '') ? 'selected' : ''; ?>>Zgjidh...</option>
                            <option value="Beqar/e" <?php echo ($marital_status_val == 'Beqar/e') ? 'selected' : ''; ?>>Beqar/e</option>
                            <option value="I/E Martuar" <?php echo ($marital_status_val == 'I/E Martuar') ? 'selected' : ''; ?>>I/E Martuar</option>
                            <option value="I/E Divorcuar" <?php echo ($marital_status_val == 'I/E Divorcuar') ? 'selected' : ''; ?>>I/E Divorcuar</option>
                            <option value="I/E Ve" <?php echo ($marital_status_val == 'I/E Ve') ? 'selected' : ''; ?>>I/E Ve</option>
                        </select>
                    </div>
                    <div class="form-group"><label for="driving_license">Patentë Shoferi (p.sh. Kategoria B)</label><input type="text" name="driving_license" id="driving_license" value="<?php echo $driving_license_val; ?>"></div>
                </div>
            </div>

            <div class="form-section reveal-on-scroll" data-reveal-delay="250">
                <h3>Adresa (opsionale)</h3>
                 <div class="form-grid">
                    <div class="form-group full-width"><label for="address">Adresa e Plotë</label><textarea name="address" id="address" rows="3"><?php echo $address_val; ?></textarea></div>
                    <div class="form-group"><label for="zip_code">Kodi Postar</label><input type="text" name="zip_code" id="zip_code" value="<?php echo $zip_code_val; ?>"></div>
                </div>
            </div>

            <div class="form-section reveal-on-scroll" data-reveal-delay="300">
                <h3>Përmbledhja Profesionale / Rreth Meje (opsionale)</h3>
                <div class="form-group full-width">
                    <label for="summary">Përshkrim i shkurtër</label><textarea name="summary" id="summary" rows="5" placeholder="Përshkruani shkurtimisht veten, aftësitë kryesore dhe objektivat e karrierës..."><?php echo $summary_val; ?></textarea>
                    <p class="form-field-description">Ky profil i shkurtër mund të përdoret si titull profesional në disa modele CV-je nëse është konciz, ose si një seksion i dedikuar "Rreth Meje".</p>
                </div>
            </div>

            <div class="page-actions form-actions-sticky reveal-on-scroll" data-reveal-delay="350">
                <a href="choose_template.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kthehu te Modelet</a>
                <button type="submit" class="btn-primary-form btn btn-primary" name="save_personal_info">Ruaj dhe Vazhdo <i class="fas fa-arrow-right icon-right"></i></button>
            </div>
        </form>
    </main>
    <?php include 'footer.php'; ?>
    <script src="script.js"></script>
</body>
</html>
