<?php
// cv_maker/save_experience.php
ob_start(); // Start output buffering
session_start(); // Start session at the very beginning

// --- Error Reporting (Development) ---
error_reporting(E_ALL);
ini_set('display_errors', 1); // Set to 0 for production
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/php_errors.log');

$user_id_session = $_SESSION['user_id'] ?? null;

// --- Authentication Check ---
if (!$user_id_session) {
    $_SESSION['error_message_exp'] = "Ju lutemi kyçuni për të ruajtur të dhënat.";
    // Preserve the current CV ID if available for redirect after login
    if (headers_sent($file, $line)) { error_log("save_experience.php: Headers sent {$file}:{$line} before login redirect."); }
    else { header("Location: login.html?redirect_after_login=experience.php" . (isset($_POST['cv_id']) ? '&cv_id_resume='.(int)$_POST['cv_id'] : '') ); }
    exit();
}

// --- CV ID Check ---
// Ensure a valid CV ID is provided in the POST request
if (!isset($_POST['cv_id']) || !is_numeric($_POST['cv_id']) || $_POST['cv_id'] <= 0) {
    error_log("save_experience.php [INVALID_CV_ID]: CV ID missing or invalid in POST. User: {$user_id_session}");
    $_SESSION['experience_form_errors_msg'] = "ID e CV-së mungon ose është e pavlefshme.";
    header("Location: experience.php"); // Redirect back, experience.php will handle session cv_id
    exit();
}
$current_cv_id = (int)$_POST['cv_id'];

// Ensure the CV ID from POST matches the one in session if session one is set,
// or set session cv_id if it's not already set (e.g., resuming after login)
if (isset($_SESSION['cv_id']) && $_SESSION['cv_id'] != $current_cv_id) {
    // This case should ideally not happen if flow is correct, but as a safeguard:
    error_log("save_experience.php [CV_ID_MISMATCH]: POST CV ID {$current_cv_id} differs from SESSION CV ID {$_SESSION['cv_id']}. User: {$user_id_session}. Using POST CV ID.");
}
$_SESSION['cv_id'] = $current_cv_id; // Ensure session is updated/set with the CV being worked on


// --- Database Connection ---
$pdo = null;
try {
    require_once 'connect.php'; // Establishes $pdo
     // Verify ownership of the CV before proceeding
    $stmt_check_owner = $pdo->prepare("SELECT id FROM cvs WHERE id = :cv_id AND user_id = :user_id");
    $stmt_check_owner->execute([':cv_id' => $current_cv_id, ':user_id' => $user_id_session]);
    if (!$stmt_check_owner->fetch()) {
        error_log("save_experience.php [AUTH_FAIL]: User {$user_id_session} does not own CV ID {$current_cv_id}.");
        $_SESSION['experience_form_errors_msg'] = "Nuk keni leje për të modifikuar këtë CV ose CV-ja nuk ekziston.";
        header("Location: view_cvs.php"); // Redirect to CV list
        exit();
    }
} catch (PDOException $e) {
    error_log("save_experience.php DB Connect/Auth Error: " . $e->getMessage());
    $_SESSION['experience_form_errors_msg'] = "Gabim kritik në lidhjen me DB ose verifikimin e CV-së.";
    // Redirect back to experience page with error, preserving CV ID in GET if possible
    header("Location: experience.php?cv_id_resume=" . $current_cv_id);
    exit();
}


// --- Handle POST Request ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $validation_errors = [];
    $_SESSION['experience_form_data'] = $_POST; // Preserve form data for repopulation on error

    // --- Process New Work Experience ---
    $new_job_title = trim($_POST['new_job_title'] ?? '');
    $new_company = trim($_POST['new_company'] ?? '');
    $new_work_city_country = trim($_POST['new_work_city_country'] ?? '');
    $new_start_date = trim($_POST['new_start_date'] ?? '');
    $new_end_date = trim($_POST['new_end_date'] ?? '');
    $new_is_current_job = isset($_POST['new_is_current_job']) ? 1 : 0;
    $new_job_description = trim($_POST['new_job_description'] ?? '');

    // Validate new work experience data if title or company is provided
    if (!empty($new_job_title) || !empty($new_company)) { // Only process if at least title or company is given
        if (empty($new_job_title)) $validation_errors[] = "Titulli i pozitës së re është i detyrueshëm nëse shtoni eksperiencë.";
        if (empty($new_company)) $validation_errors[] = "Kompania për pozitën e re është e detyrueshme.";
        // Validate start date format if provided
        if (!empty($new_start_date) && !DateTime::createFromFormat('Y-m-d', $new_start_date)) {
            $validation_errors[] = "Formati i datës së fillimit për punën e re është i pavlefshëm.";
        } else {
            $new_start_date = !empty($new_start_date) ? $new_start_date : null; // Set to null if empty
        }
        // Validate end date format if provided and it's not a current job
        if (!$new_is_current_job && !empty($new_end_date) && !DateTime::createFromFormat('Y-m-d', $new_end_date)) {
            $validation_errors[] = "Formati i datës së mbarimit për punën e re është i pavlefshëm.";
        } elseif ($new_is_current_job) {
            $new_end_date = null; // End date is null for current jobs
        } else {
             $new_end_date = !empty($new_end_date) ? $new_end_date : null; // Set to null if empty
        }
         // Basic date logic validation (start date before end date)
        if ($new_start_date && $new_end_date && !$new_is_current_job && strtotime($new_start_date) > strtotime($new_end_date)) {
             $validation_errors[] = "Data e fillimit nuk mund të jetë pas datës së mbarimit për punën e re.";
        }
    }

    // --- Process New Education ---
    $new_school = trim($_POST['new_school'] ?? '');
    $new_degree = trim($_POST['new_degree'] ?? '');
    $new_field_of_study = trim($_POST['new_field_of_study'] ?? '');
    $new_edu_city_country = trim($_POST['new_edu_city_country'] ?? '');
    $new_graduation_year = trim($_POST['new_graduation_year'] ?? '');
    $new_edu_description = trim($_POST['new_edu_description'] ?? '');

    // Validate new education data if school or degree is provided
    if (!empty($new_school) || !empty($new_degree)) { // Only process if school or degree is given
        if (empty($new_school)) $validation_errors[] = "Institucioni arsimor i ri është i detyrueshëm.";
        if (empty($new_degree)) $validation_errors[] = "Diploma/Programi i ri është i detyrueshëm.";
        // Basic validation for graduation year if provided
        if (!empty($new_graduation_year) && strlen($new_graduation_year) > 20) { // Arbitrary length check
            $validation_errors[] = "Viti i diplomimit për edukimin e ri është shumë i gjatë.";
        }
         // Optional: Add more robust year validation (e.g., is_numeric, within a reasonable range)
         if (!empty($new_graduation_year) && !is_numeric($new_graduation_year) && strtolower($new_graduation_year) !== 'në vazhdim') {
             $validation_errors[] = "Viti i diplomimit duhet të jetë një numër ose 'Në vazhdim'.";
         }
    }

    // --- Process New Skill ---
    $new_skill_name = trim($_POST['new_skill_name'] ?? '');
    $new_skill_level = trim($_POST['new_skill_level'] ?? '');
    $new_skill_category = trim($_POST['new_skill_category'] ?? '');

    // Validate new skill data if skill name is provided
    if (!empty($new_skill_name)) {
        // Basic validation for skill name if provided
        if (strlen($new_skill_name) > 255) $validation_errors[] = "Emri i aftësisë së re është shumë i gjatë.";
         // Optional: Validate skill level/category length if needed
         if (!empty($new_skill_level) && strlen($new_skill_level) > 100) $validation_errors[] = "Niveli i aftësisë së re është shumë i gjatë.";
         if (!empty($new_skill_category) && strlen($new_skill_category) > 100) $validation_errors[] = "Kategoria e aftësisë së re është shumë e gjatë.";
    }

    // --- Process Interests ---
    $interests_description = trim($_POST['interests_description'] ?? '');


    // --- If Validation Errors, Redirect Back ---
    if (!empty($validation_errors)) {
        $_SESSION['experience_form_errors'] = $validation_errors; // Store specific errors
        // $_SESSION['experience_form_data'] is already set above
        header("Location: experience.php"); // Redirect back, cv_id is already in session
        exit();
    }

    // --- Database Operations (using Transactions) ---
    try {
        $pdo->beginTransaction(); // Start a transaction

        // Insert New Work Experience (if provided)
        if (!empty($new_job_title) && !empty($new_company)) {
            $sql_work = "INSERT INTO work_experiences (cv_id, job_title, company, city_country, start_date, end_date, is_current_job, job_description)
                         VALUES (:cv_id, :job_title, :company, :city_country, :start_date, :end_date, :is_current_job, :job_description)";
            $stmt_work = $pdo->prepare($sql_work);
            $stmt_work->execute([
                ':cv_id' => $current_cv_id,
                ':job_title' => $new_job_title,
                ':company' => $new_company,
                ':city_country' => $new_work_city_country,
                ':start_date' => $new_start_date,
                ':end_date' => $new_end_date,
                ':is_current_job' => $new_is_current_job,
                ':job_description' => $new_job_description
            ]);
        }

        // Insert New Education (if provided)
        if (!empty($new_school) && !empty($new_degree)) {
            $sql_edu = "INSERT INTO educations (cv_id, school, degree, field_of_study, city_country, graduation_year, edu_description)
                        VALUES (:cv_id, :school, :degree, :field_of_study, :city_country, :graduation_year, :edu_description)";
            $stmt_edu = $pdo->prepare($sql_edu);
            $stmt_edu->execute([
                ':cv_id' => $current_cv_id,
                ':school' => $new_school,
                ':degree' => $new_degree,
                ':field_of_study' => $new_field_of_study,
                ':city_country' => $new_edu_city_country,
                ':graduation_year' => $new_graduation_year,
                ':edu_description' => $new_edu_description
            ]);
        }

        // Insert New Skill (if provided)
        if (!empty($new_skill_name)) {
            $sql_skill = "INSERT INTO skills (cv_id, skill_name, skill_level, category)
                          VALUES (:cv_id, :skill_name, :skill_level, :category)";
            $stmt_skill = $pdo->prepare($sql_skill);
            $stmt_skill->execute([
                ':cv_id' => $current_cv_id,
                ':skill_name' => $new_skill_name,
                ':skill_level' => !empty($new_skill_level) ? $new_skill_level : null,
                ':category' => !empty($new_skill_category) ? $new_skill_category : null
            ]);
        }

        // Update/Insert Interests
        // First, try to delete existing interests for this cv_id to ensure only one row.
        $stmt_delete_interests = $pdo->prepare("DELETE FROM interests WHERE cv_id = :cv_id");
        $stmt_delete_interests->execute([':cv_id' => $current_cv_id]);
        // Then, insert the new one if provided
        if (!empty($interests_description)) {
            $sql_interests = "INSERT INTO interests (cv_id, description) VALUES (:cv_id, :description)";
            $stmt_interests = $pdo->prepare($sql_interests);
            $stmt_interests->execute([':cv_id' => $current_cv_id, ':description' => $interests_description]);
        }

        // Update the 'updated_at' timestamp in the main 'cvs' table
        $stmt_update_timestamp = $pdo->prepare("UPDATE cvs SET updated_at = NOW() WHERE id = :cv_id");
        $stmt_update_timestamp->execute([':cv_id' => $current_cv_id]);


        $pdo->commit(); // Commit the transaction if all operations were successful
        $_SESSION['success_message_exp'] = "Të dhënat e eksperiencës, edukimit dhe aftësive u ruajtën/shtuan me sukses!";
        unset($_SESSION['experience_form_data']); // Clear form data on success

        // Redirect to the preview page
        if (headers_sent($file, $line)) {
            error_log("save_experience.php: Headers sent {$file}:{$line} before success redirect.");
            echo "Të dhënat u ruajtën, por ridrejtimi dështoi.";
        } else {
            ob_end_clean(); // Clean the output buffer before redirecting
            header("Location: preview_cv.php"); // Redirect to preview the CV
        }
        exit();

    } catch (PDOException $e) {
        // Rollback the transaction if any DB operation failed
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        error_log("save_experience.php DB Save Error CV ID {$current_cv_id}: " . $e->getMessage());
        $_SESSION['experience_form_errors_msg'] = "Gabim serveri gjatë ruajtjes së detajeve. Ju lutemi provoni përsëri.";
        // $_SESSION['experience_form_data'] is already set
        // Redirect back to experience page with error
        if (headers_sent($file, $line)) {
            error_log("save_experience.php: Headers sent {$file}:{$line} before DB save err redirect.");
        } else {
            ob_end_clean();
            header("Location: experience.php");
        }
        exit();
    } catch (Exception $e) {
        // Catch any other general exceptions
         if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        error_log("save_experience.php General Exception CV ID {$current_cv_id}: " . $e->getMessage());
        $_SESSION['experience_form_errors_msg'] = "Gabim i papritur. Provoni përsëri.";
        // $_SESSION['experience_form_data'] is already set
        // Redirect back to experience page with error
         if (headers_sent($file, $line)) {
            error_log("save_experience.php: Headers sent {$file}:{$line} before general err redirect.");
        } else {
            ob_end_clean();
            header("Location: experience.php");
        }
        exit();
    }
} else {
    // Not a POST request, redirect to experience page
    $_SESSION['experience_form_errors_msg'] = "Kërkesë e pavlefshme.";
    if (headers_sent($file, $line)) { error_log("save_experience.php: Headers sent {$file}:{$line} before non-POST redirect.");}
    else { ob_end_clean(); header("Location: experience.php");}
    exit();
}

if (ob_get_length() > 0) { ob_end_flush(); } // Final buffer flush if anything was accidentally outputted
?>
