<?php
// cv_maker/experience.php
session_start();

// --- Error Reporting (Development) ---
error_reporting(E_ALL);
ini_set('display_errors', 1); // Set to 0 for production
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/php_errors.log');

$user_id_session = $_SESSION['user_id'] ?? null;

// --- Authentication: Redirect to login if user is not logged in ---
if (!$user_id_session) {
    $_SESSION['redirect_after_login'] = 'experience.php';
    // Preserve cv_id if it was set (using consistent 'cv_id' key)
    if (isset($_SESSION['cv_id'])) { // Check for 'cv_id'
         $_SESSION['redirect_after_login'] .= '?cv_id_resume=' . $_SESSION['cv_id'];
    } elseif (isset($_GET['cv_id_resume'])) { // Or from GET if resuming
         $_SESSION['redirect_after_login'] .= '?cv_id_resume=' . $_GET['cv_id_resume'];
    }
    $_SESSION['info_message'] = "Ju lutemi kyçuni për të shtuar eksperiencën.";
    header("Location: login.html");
    exit();
}
$logged_in_user_id = (int)$user_id_session;

// --- CV ID Check: Ensure cv_id is set from the previous step (form.php or resume) ---
// Check if resuming with cv_id_resume after login
if (isset($_GET['cv_id_resume']) && is_numeric($_GET['cv_id_resume']) && !isset($_SESSION['cv_id'])) {
    try {
        require_once 'connect.php'; // $pdo
        // Verify this cv_id belongs to the user from 'cvs' table
        $stmt_check = $pdo->prepare("SELECT id FROM cvs WHERE id = :cv_id AND user_id = :user_id");
        $stmt_check->execute([':cv_id' => (int)$_GET['cv_id_resume'], ':user_id' => $logged_in_user_id]);
        if ($stmt_check->fetch()) {
            $_SESSION['cv_id'] = (int)$_GET['cv_id_resume']; // Set the session cv_id
             // Also try to load the template if not set
            if (!isset($_SESSION['selected_template'])) {
                 $stmt_template = $pdo->prepare("SELECT selected_template FROM cvs WHERE id = :cv_id");
                 $stmt_template->execute([':cv_id' => $_SESSION['cv_id']]);
                 $template_row = $stmt_template->fetch(PDO::FETCH_ASSOC);
                 if ($template_row) {
                     $_SESSION['selected_template'] = $template_row['selected_template'];
                 }
            }
        } else {
            unset($_SESSION['cv_id']);
             unset($_SESSION['selected_template']);
        }
    } catch (PDOException $e) {
        error_log("experience.php DB error checking cv_id_resume: " . $e->getMessage());
        unset($_SESSION['cv_id']);
        unset($_SESSION['selected_template']);
    }
}


if (!isset($_SESSION['cv_id']) || !is_numeric($_SESSION['cv_id']) || $_SESSION['cv_id'] <= 0) {
    error_log("experience.php [PREREQ_FAIL]: cv_id missing/invalid for user {$logged_in_user_id}. Value: " . ($_SESSION['cv_id'] ?? 'Not Set'));
    $_SESSION['error_message'] = "Ju lutemi plotësoni ose zgjidhni informacionin personal së pari.";
    // Redirect to form.php, it will handle loading or starting new based on session
    header("Location: form.php");
    exit();
}
$current_cv_id = (int)$_SESSION['cv_id']; // This is the ID from 'cvs' table

$current_template_for_header = $_SESSION['selected_template'] ?? 'Pa Model';
$header_subtitle = "Hapi 2: Eksperienca & Aftësitë";
$header_subtitle .= " (Modeli: " . ucfirst(htmlspecialchars($current_template_for_header)) . ")";

// --- Initialize arrays to hold existing data ---
$existing_work_experiences = [];
$existing_educations = [];
$existing_skills = []; // Skills are now in their own table
$existing_interests_description = '';

// --- Form data for pre-filling (from failed save attempt) ---
$form_input_data = $_SESSION['experience_form_data'] ?? [];
unset($_SESSION['experience_form_data']); // Clear temporary form data after retrieving

// --- Database Connection & Data Loading ---
$pdo = null;
try {
    require_once 'connect.php';

    // Verify ownership of the CV before loading data
    $stmt_check_owner = $pdo->prepare("SELECT id FROM cvs WHERE id = :cv_id AND user_id = :user_id");
    $stmt_check_owner->execute([':cv_id' => $current_cv_id, ':user_id' => $logged_in_user_id]);
    if (!$stmt_check_owner->fetch()) {
        error_log("experience.php [AUTH_FAIL]: User {$logged_in_user_id} does not own CV ID {$current_cv_id}.");
        $_SESSION['error_message'] = "Nuk keni leje për të modifikuar këtë CV ose CV-ja nuk ekziston.";
        unset($_SESSION['cv_id']); // Clear invalid cv_id from session
        unset($_SESSION['selected_template']);
        header("Location: view_cvs.php"); // Redirect to CV list
        exit();
    }


    // Load existing work experiences for the current_cv_id
    $stmt_work = $pdo->prepare("SELECT * FROM work_experiences WHERE cv_id = :cv_id ORDER BY start_date DESC, id DESC");
    $stmt_work->execute([':cv_id' => $current_cv_id]);
    $existing_work_experiences = $stmt_work->fetchAll(PDO::FETCH_ASSOC);

    // Load existing educations
    $stmt_edu = $pdo->prepare("SELECT * FROM educations WHERE cv_id = :cv_id ORDER BY graduation_year DESC, id DESC");
    $stmt_edu->execute([':cv_id' => $current_cv_id]);
    $existing_educations = $stmt_edu->fetchAll(PDO::FETCH_ASSOC);

    // Load existing skills
    $stmt_skills = $pdo->prepare("SELECT * FROM skills WHERE cv_id = :cv_id ORDER BY category, skill_name");
    $stmt_skills->execute([':cv_id' => $current_cv_id]);
    $existing_skills = $stmt_skills->fetchAll(PDO::FETCH_ASSOC);

    // Load existing interests description
    $stmt_interests = $pdo->prepare("SELECT description FROM interests WHERE cv_id = :cv_id LIMIT 1");
    $stmt_interests->execute([':cv_id' => $current_cv_id]);
    $interests_row = $stmt_interests->fetch(PDO::FETCH_ASSOC);
    if ($interests_row) {
        $existing_interests_description = $interests_row['description'];
    }

} catch (PDOException $e) {
    error_log("experience.php DB Load Error for CV ID {$current_cv_id}: " . $e->getMessage());
    $_SESSION['error_message_exp_load'] = "Gabim gjatë ngarkimit të të dhënave ekzistuese.";
    // No exit here, allow page to render with error message
}


// --- Handle Session Messages ---
$error_message_display = $_SESSION['error_message_exp_load'] ?? ($_SESSION['experience_form_errors_msg'] ?? null);
// If there are specific validation errors from save_experience.php
if (isset($_SESSION['experience_form_errors']) && is_array($_SESSION['experience_form_errors']) && !empty($_SESSION['experience_form_errors'])) {
    $error_message_display = "Ju lutemi korrigjoni gabimet:<ul><li>" . implode("</li><li>", array_map('htmlspecialchars', $_SESSION['experience_form_errors'])) . "</li></ul>";
}
// Clear messages from session after retrieving them
unset($_SESSION['error_message_exp_load'], $_SESSION['experience_form_errors'], $_SESSION['experience_form_errors_msg']);

$success_message_display = $_SESSION['success_message_exp'] ?? null;
unset($_SESSION['success_message_exp']);

// --- Prepare values for "Add New" form fields (pre-fill from $form_input_data if available) ---
$new_job_title = htmlspecialchars($form_input_data['new_job_title'] ?? '');
$new_company = htmlspecialchars($form_input_data['new_company'] ?? '');
$new_work_city = htmlspecialchars($form_input_data['new_work_city_country'] ?? ''); // Name matches form field
$new_start_date = htmlspecialchars($form_input_data['new_start_date'] ?? '');
$new_end_date = htmlspecialchars($form_input_data['new_end_date'] ?? '');
$new_is_current_job = isset($form_input_data['new_is_current_job']); // Check if checkbox was checked
$new_job_description = htmlspecialchars($form_input_data['new_job_description'] ?? '');

$new_school = htmlspecialchars($form_input_data['new_school'] ?? '');
$new_degree = htmlspecialchars($form_input_data['new_degree'] ?? '');
$new_field_of_study = htmlspecialchars($form_input_data['new_field_of_study'] ?? '');
$new_edu_city = htmlspecialchars($form_input_data['new_edu_city_country'] ?? ''); // Name matches form field
$new_graduation_year = htmlspecialchars($form_input_data['new_graduation_year'] ?? '');
$new_edu_description = htmlspecialchars($form_input_data['new_edu_description'] ?? '');

$new_skill_name = htmlspecialchars($form_input_data['new_skill_name'] ?? '');
$new_skill_level = htmlspecialchars($form_input_data['new_skill_level'] ?? '');
$new_skill_category = htmlspecialchars($form_input_data['new_skill_category'] ?? '');

// For interests, pre-fill from form_input_data if available, otherwise from existing DB data
$interests_textarea_val = htmlspecialchars($form_input_data['interests_description'] ?? $existing_interests_description);

?>
<!DOCTYPE html>
<html lang="sq">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>CV Maker - <?php echo htmlspecialchars($header_subtitle); ?></title>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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

        <nav class="header-nav" id="mobile-nav-menu-id">
            <a href="index.php" class="<?php echo $current_page == 'index.php' ? 'active' : ''; ?>">Home</a>
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
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="profile.php" class="profile-icon-btn" aria-label="View Profile">
                    <i class="fas fa-user-circle"></i> </a>
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
    <form action="save_experience.php" method="POST" class="cv-form animate-in">
      <input type="hidden" name="cv_id" value="<?php echo $current_cv_id; ?>">
      <h2 class="reveal-on-scroll">Eksperienca, Edukimi & Aftësitë</h2>
      <p class="page-intro-text reveal-on-scroll" data-reveal-delay="100">Shtoni detajet për përvojën tuaj të punës, edukimin dhe aftësitë. Mund të shtoni më shumë se një hyrje për punë dhe edukim.</p>

        <div class="message-area" style="margin-bottom: var(--space-md);">
            <?php if ($error_message_display): ?><div class="message error" role="alert"><?php echo $error_message_display; /* Allow HTML if errors are formatted with <ul> */ ?></div><?php endif; ?>
            <?php if ($success_message_display): ?><p class="message success" role="status"><?php echo htmlspecialchars($success_message_display); ?></p><?php endif; ?>
        </div>

      <div class="form-section reveal-on-scroll" data-reveal-delay="150">
        <h3>Eksperienca e Punës</h3>
        <?php if (!empty($existing_work_experiences)): ?>
            <h4>Eksperiencat Ekzistuese:</h4>
            <ul class="existing-items-list">
                <?php foreach ($existing_work_experiences as $work): ?>
                    <li>
                        <strong><?php echo htmlspecialchars($work['job_title'] ?? 'Pa titull'); ?> te <?php echo htmlspecialchars($work['company'] ?? 'Pa kompani'); ?></strong>
                        <p><?php echo htmlspecialchars($work['city_country'] ?? ''); ?>
                           (<?php echo htmlspecialchars($work['start_date'] ? date('M Y', strtotime($work['start_date'])) : 'N/A'); ?> -
                           <?php echo $work['is_current_job'] ? 'Tani' : htmlspecialchars($work['end_date'] ? date('M Y', strtotime($work['end_date'])) : 'N/A'); ?>)
                        </p>
                        <?php if(!empty($work['job_description'])): ?><p style="white-space: pre-wrap;"><?php echo htmlspecialchars($work['job_description']); ?></p><?php endif; ?>
                        </li>
                <?php endforeach; ?>
            </ul>
            <hr style="margin: var(--space-md) 0;">
        <?php endif; ?>
        <h4>Shto Eksperiencë të Re Pune (opsionale):</h4>
        <div class="form-grid">
            <div class="form-group"><label for="new_job_title">Pozita</label><input type="text" name="new_job_title" id="new_job_title" value="<?php echo $new_job_title; ?>"></div>
            <div class="form-group"><label for="new_company">Kompania</label><input type="text" name="new_company" id="new_company" value="<?php echo $new_company; ?>"></div>
            <div class="form-group"><label for="new_work_city_country">Qyteti, Shteti</label><input type="text" name="new_work_city_country" id="new_work_city_country" value="<?php echo $new_work_city; ?>" placeholder="P.sh. Prishtinë, Kosovë"></div>
            <div class="form-group"><label for="new_start_date">Data e Fillimit</label><input type="date" name="new_start_date" id="new_start_date" value="<?php echo $new_start_date; ?>"></div>
            <div class="form-group"><label for="new_end_date">Data e Mbarimit</label><input type="date" name="new_end_date" id="new_end_date" value="<?php echo $new_end_date; ?>" <?php if ($new_is_current_job) echo 'disabled'; ?>></div>
            <div class="form-group full-width checkbox-group">
                <input type="checkbox" name="new_is_current_job" id="new_is_current_job" value="1" <?php if ($new_is_current_job) echo 'checked'; ?> onchange="document.getElementById('new_end_date').disabled = this.checked; if(this.checked) document.getElementById('new_end_date').value='';">
                <label for="new_is_current_job">Punë Aktuale</label>
            </div>
            <div class="form-group full-width"><label for="new_job_description">Përshkrim i Detyrave (një për rresht)</label><textarea name="new_job_description" id="new_job_description" rows="4" placeholder="P.sh., Menaxhova projekte&#10;Zhvillova strategji..."><?php echo $new_job_description; ?></textarea></div>
        </div>
      </div>

      <div class="form-section reveal-on-scroll" data-reveal-delay="200">
        <h3>Edukimi</h3>
         <?php if (!empty($existing_educations)): ?>
            <h4>Edukimet Ekzistuese:</h4>
            <ul class="existing-items-list">
                <?php foreach ($existing_educations as $edu): ?>
                    <li>
                        <strong><?php echo htmlspecialchars($edu['degree'] ?? 'Pa diplomë'); ?> - <?php echo htmlspecialchars($edu['field_of_study'] ?? 'Pa fushë studimi'); ?></strong>
                        <p><?php echo htmlspecialchars($edu['school'] ?? 'Pa shkollë'); ?>, <?php echo htmlspecialchars($edu['city_country'] ?? ''); ?>
                           (Viti Diplomimit: <?php echo htmlspecialchars($edu['graduation_year'] ?? 'N/A'); ?>)
                        </p>
                        <?php if(!empty($edu['edu_description'])): ?><p style="white-space: pre-wrap;"><?php echo htmlspecialchars($edu['edu_description']); ?></p><?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
            <hr style="margin: var(--space-md) 0;">
        <?php endif; ?>
        <h4>Shto Edukim të Ri (opsionale):</h4>
         <div class="form-grid">
            <div class="form-group"><label for="new_school">Institucioni Arsimor</label><input type="text" name="new_school" id="new_school" value="<?php echo $new_school; ?>"></div>
            <div class="form-group"><label for="new_degree">Diploma / Certifikata</label><input type="text" name="new_degree" id="new_degree" value="<?php echo $new_degree; ?>"></div>
            <div class="form-group"><label for="new_field_of_study">Fusha e Studimit</label><input type="text" name="new_field_of_study" id="new_field_of_study" value="<?php echo $new_field_of_study; ?>"></div>
            <div class="form-group"><label for="new_edu_city_country">Qyteti, Shteti</label><input type="text" name="new_edu_city_country" id="new_edu_city_country" value="<?php echo $new_edu_city; ?>"></div>
            <div class="form-group"><label for="new_graduation_year">Viti i Diplomimit/Përfundimit</label><input type="text" name="new_graduation_year" id="new_graduation_year" value="<?php echo $new_graduation_year; ?>" placeholder="YYYY ose Në vazhdim"></div>
            <div class="form-group full-width"><label for="new_edu_description">Përshkrim Shtesë (opsionale)</label><textarea name="new_edu_description" id="new_edu_description" rows="3"><?php echo $new_edu_description; ?></textarea></div>
        </div>
      </div>

      <div class="form-section reveal-on-scroll" data-reveal-delay="250">
        <h3>Aftësitë</h3>
         <?php if (!empty($existing_skills)): ?>
            <h4>Aftësitë Ekzistuese:</h4>
            <ul class="existing-items-list">
                <?php foreach ($existing_skills as $skill): ?>
                    <li>
                        <strong><?php echo htmlspecialchars($skill['skill_name']); ?></strong>
                        <?php if(!empty($skill['category'])): ?> (Kategoria: <?php echo htmlspecialchars($skill['category']); ?>)<?php endif; ?>
                        <?php if(!empty($skill['skill_level'])): ?> - Niveli: <?php echo htmlspecialchars($skill['skill_level']); ?><?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
            <hr style="margin: var(--space-md) 0;">
        <?php endif; ?>
        <h4>Shto Aftësi të Re (opsionale):</h4>
        <div class="form-grid">
            <div class="form-group"><label for="new_skill_name">Emri i Aftësisë</label><input type="text" name="new_skill_name" id="new_skill_name" value="<?php echo $new_skill_name; ?>"></div>
            <div class="form-group"><label for="new_skill_level">Niveli (opsionale)</label><input type="text" name="new_skill_level" id="new_skill_level" value="<?php echo $new_skill_level; ?>" placeholder="P.sh. Fillestar, Avancuar, Ekspert"></div>
            <div class="form-group"><label for="new_skill_category">Kategoria (opsionale)</label><input type="text" name="new_skill_category" id="new_skill_category" value="<?php echo $new_skill_category; ?>" placeholder="P.sh. Gjuhë, Teknik, Softuer"></div>
        </div>
      </div>

      <div class="form-section reveal-on-scroll" data-reveal-delay="250">
        <h3>Interesat (opsionale)</h3>
        <div class="form-group full-width">
            <label for="interests_description">Përshkruani interesat, hobitë tuaja</label>
            <textarea name="interests_description" id="interests_description" rows="4" placeholder="P.sh., Lexim, Vullnetarizëm, Sporte..."><?php echo $interests_textarea_val; ?></textarea>
        </div>
      </div>

      <div class="page-actions form-actions-sticky reveal-on-scroll" data-reveal-delay="300">
        <a href="form.php?edit_id=<?php echo $current_cv_id; ?>" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kthehu te Info Personale</a>
        <button type="submit" class="btn-primary-form btn btn-primary" name="save_experience_details">Ruaj dhe Shiko CV-në <i class="fas fa-eye icon-right"></i></button>
      </div>
    </form>
  </main>
        <?php include 'footer.php'; ?>
  <div class="page-transition-overlay"></div>
  <script src="script.js"></script>
</body>
</html>
