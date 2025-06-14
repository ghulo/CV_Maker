<?php
// cv_maker/save_personal.php
session_start(); // Start session at the very beginning

// --- Error Reporting (Development) ---
error_reporting(E_ALL);
ini_set('display_errors', 1); // Set to 0 for production
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/php_errors.log'); // Ensure this path is writable by the web server

$user_id_session = $_SESSION['user_id'] ?? null;

// --- Database Connection ---
$pdo = null;
try {
    require_once 'connect.php'; // Establishes $pdo
} catch (PDOException $e) {
    error_log("CRITICAL Database Connection Failed in save_personal.php: " . $e->getMessage());
    $_SESSION['error_message'] = "Gabim kritik në lidhjen me bazën e të dhënave.";
    // Preserve POST data in session before redirecting on DB error
    if(isset($_POST)){ $_SESSION['form_data_temp'] = $_POST; }
    header('Location: form.php'); // Redirect back to form.php
    exit();
}

// --- Handle POST Request ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // --- Get Data from POST ---
    $cv_title = trim($_POST['cv_title'] ?? 'My CV'); // New field for CV title
    $emri = trim($_POST['emri'] ?? '');
    $mbiemri = trim($_POST['mbiemri'] ?? '');
    $email_form = trim($_POST['email'] ?? '');
    $telefoni = trim($_POST['telefoni'] ?? ''); // Changed from 'phone' to 'telefoni' to match DB column name
    $address = trim($_POST['address'] ?? '');
    $summary = trim($_POST['summary'] ?? '');
    $date_of_birth = trim($_POST['date_of_birth'] ?? '');
    $place_of_birth = trim($_POST['place_of_birth'] ?? '');
    $gender = trim($_POST['gender'] ?? '');
    $nationality = trim($_POST['nationality'] ?? '');
    $zip_code = trim($_POST['zip_code'] ?? '');
    $marital_status = trim($_POST['marital_status'] ?? '');
    $driving_license = trim($_POST['driving_license'] ?? '');
    // Get selected template from POST or Session
    $selected_template = trim($_POST['selected_template'] ?? $_SESSION['selected_template'] ?? 'classic');
    // Get CV ID to edit from POST or Session
    $cv_id_to_edit = isset($_POST['cv_id_to_edit']) ? (int)$_POST['cv_id_to_edit'] : ($_SESSION['cv_id'] ?? null);


    // --- Basic Validation ---
    $validation_errors = [];
    if (empty($emri)) $validation_errors[] = "Emri është i detyrueshëm.";
    if (empty($mbiemri)) $validation_errors[] = "Mbiemri është i detyrueshëm.";
    if (!empty($email_form) && !filter_var($email_form, FILTER_VALIDATE_EMAIL)) {
        $validation_errors[] = "Formati i email-it është i pavlefshëm.";
    }
    // Validate date of birth format if provided
    if (!empty($date_of_birth)) {
        $d = DateTime::createFromFormat('Y-m-d', $date_of_birth);
        if (!$d || $d->format('Y-m-d') !== $date_of_birth) {
            $validation_errors[] = "Formati i datëlindjes është i pavlefshëm (YYYY-MM-DD).";
        }
    } else {
        $date_of_birth = null; // Ensure it's null if empty for DB
    }

    // Validate selected template
    $allowed_templates = ['classic', 'modern', 'professional'];
    if (empty($selected_template) || !in_array($selected_template, $allowed_templates)) {
        $validation_errors[] = "Modeli i zgjedhur është i pavlefshëm.";
        $selected_template = 'classic'; // Default if invalid
    }
     // Set a default CV title if none is provided
     if (empty($cv_title)) {
        $cv_title = "CV e " . $emri . " " . $mbiemri;
    }


    // If there are validation errors, redirect back to the form with errors and data
    if (!empty($validation_errors)) {
        $_SESSION['error_message'] = "Ju lutemi korrigjoni gabimet:<ul><li>" . implode("</li><li>", $validation_errors) . "</li></ul>";
        $_SESSION['form_data_temp'] = $_POST; // Preserve form data in session
        // If editing, pass back the cv_id so form.php knows which CV to load
        $redirect_url = 'form.php';
        if ($cv_id_to_edit) {
            $redirect_url .= '?edit_id=' . $cv_id_to_edit;
        }
        header('Location: ' . $redirect_url);
        exit();
    }

    // --- User Authentication Check ---
    // If user is not logged in, save data temporarily and redirect to login
    if (!$user_id_session) {
        $_SESSION['pending_cv_data'] = $_POST; // Save form data
        $_SESSION['pending_cv_data']['selected_template'] = $selected_template; // Save template choice
        $_SESSION['info_message'] = "Ju lutemi kyçuni ose krijoni një llogari për të ruajtur CV-në tuaj.";
        $_SESSION['redirect_after_login'] = 'form.php?action=process_pending'; // Redirect back to form to process pending data after login
        header('Location: login.html');
        exit();
    }

    // --- Database Operation: INSERT or UPDATE ---
    try {
        $operation = "";
        $current_cv_id_for_session = null;

        if ($cv_id_to_edit) { // If cv_id_to_edit is set, it's an UPDATE operation
            // Verify ownership before updating
            $sql_check_owner = "SELECT id FROM cvs WHERE id = :cv_id AND user_id = :user_id";
            $stmt_check_owner = $pdo->prepare($sql_check_owner);
            $stmt_check_owner->execute([':cv_id' => $cv_id_to_edit, ':user_id' => $user_id_session]);
            if (!$stmt_check_owner->fetch()) {
                // If CV not found or does not belong to the user
                $_SESSION['error_message'] = "Gabim: CV-ja që po tentoni të modifikoni nuk u gjet ose nuk ju përket.";
                unset($_SESSION['cv_id']); // Clear potentially invalid CV ID
                header('Location: view_cvs.php'); // Redirect to CV list
                exit();
            }

            // SQL to UPDATE an existing CV record in the 'cvs' table
            $sql = "UPDATE cvs SET
                        cv_title = :cv_title, emri = :emri, mbiemri = :mbiemri, email = :email,
                        telefoni = :telefoni, address = :address, summary = :summary,
                        selected_template = :selected_template, date_of_birth = :date_of_birth,
                        place_of_birth = :place_of_birth, gender = :gender, nationality = :nationality,
                        zip_code = :zip_code, marital_status = :marital_status, driving_license = :driving_license,
                        updated_at = NOW() -- Update the updated_at timestamp
                    WHERE id = :cv_id AND user_id = :user_id_for_update";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':cv_id', $cv_id_to_edit, PDO::PARAM_INT);
            $stmt->bindParam(':user_id_for_update', $user_id_session, PDO::PARAM_INT);
            $operation = "përditësua";
            $current_cv_id_for_session = $cv_id_to_edit; // Keep the same CV ID

        } else { // If cv_id_to_edit is not set, it's an INSERT operation
            // SQL to INSERT a new CV record into the 'cvs' table
            $sql = "INSERT INTO cvs (user_id, cv_title, emri, mbiemri, email, telefoni, address, summary,
                                selected_template, date_of_birth, place_of_birth, gender, nationality,
                                zip_code, marital_status, driving_license)
                    VALUES (:user_id, :cv_title, :emri, :mbiemri, :email, :telefoni, :address, :summary,
                            :selected_template, :date_of_birth, :place_of_birth, :gender, :nationality,
                            :zip_code, :marital_status, :driving_license)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':user_id', $user_id_session, PDO::PARAM_INT);
            $operation = "u ruajt";
        }

        // Bind parameters for both INSERT and UPDATE statements
        $stmt->bindParam(':cv_title', $cv_title, PDO::PARAM_STR);
        $stmt->bindParam(':emri', $emri, PDO::PARAM_STR);
        $stmt->bindParam(':mbiemri', $mbiemri, PDO::PARAM_STR);
        $stmt->bindParam(':email', $email_form, PDO::PARAM_STR);
        $stmt->bindParam(':telefoni', $telefoni, PDO::PARAM_STR); // Using 'telefoni' for DB
        $stmt->bindParam(':address', $address, PDO::PARAM_STR);
        $stmt->bindParam(':summary', $summary, PDO::PARAM_STR);
        $stmt->bindParam(':selected_template', $selected_template, PDO::PARAM_STR);
        // Bind date_of_birth as NULL if empty, otherwise as string
        $stmt->bindParam(':date_of_birth', $date_of_birth, $date_of_birth === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt->bindParam(':place_of_birth', $place_of_birth, PDO::PARAM_STR);
        $stmt->bindParam(':gender', $gender, PDO::PARAM_STR);
        $stmt->bindParam(':nationality', $nationality, PDO::PARAM_STR);
        $stmt->bindParam(':zip_code', $zip_code, PDO::PARAM_STR);
        $stmt->bindParam(':marital_status', $marital_status, PDO::PARAM_STR);
        $stmt->bindParam(':driving_license', $driving_license, PDO::PARAM_STR);

        // Execute the prepared statement
        if ($stmt->execute()) {
            if ($operation === "u ruajt" && !$cv_id_to_edit) { // If it was a new insert
                $current_cv_id_for_session = $pdo->lastInsertId(); // Get the ID of the newly inserted CV
            }
            $_SESSION['success_message'] = "Informacioni personal $operation me sukses!";
            $_SESSION['cv_id'] = $current_cv_id_for_session; // Set/update the cv_id in session
            $_SESSION['selected_template'] = $selected_template; // Ensure template is consistent in session
            unset($_SESSION['form_data_temp']); // Clear temporary form data on success

            // Redirect to the experience page (Step 2)
            header('Location: experience.php');
            exit();
        } else {
            // Log detailed error if execution fails
            $error_info = $stmt->errorInfo();
            $cv_id_log = $cv_id_to_edit ?? 'NEW';
            error_log("save_personal.php: PDO Execute Error user {$user_id_session}, CV ID {$cv_id_log}: SQLSTATE[{$error_info[0]}] {$error_info[2]} | SQL: {$sql}");
            $_SESSION['error_message'] = "Gabim serveri gjatë ruajtjes së informacionit personal. Provoni përsëri.";
            $_SESSION['form_data_temp'] = $_POST; // Preserve form data on error
            // Redirect back to form.php with errors and data
            $redirect_url = 'form.php';
            if ($cv_id_to_edit) { $redirect_url .= '?edit_id=' . $cv_id_to_edit; }
            header('Location: ' . $redirect_url);
            exit();
        }

    } catch (PDOException $e) {
        // Catch and log PDO exceptions during DB operations
        $cv_id_log = $cv_id_to_edit ?? 'NEW';
        error_log("save_personal.php: PDOException user {$user_id_session}, CV ID {$cv_id_log}: " . $e->getMessage());
        $_SESSION['error_message'] = "Gabim i papritur DB gjatë ruajtjes. Provoni përsëri.";
        $_SESSION['form_data_temp'] = $_POST; // Preserve form data on error
        // Redirect back to form.php with errors and data
        $redirect_url = 'form.php';
        if ($cv_id_to_edit) { $redirect_url .= '?edit_id=' . $cv_id_to_edit; }
        header('Location: ' . $redirect_url);
        exit();
    } catch (Exception $e) {
        // Catch and log any other general exceptions
        $cv_id_log = $cv_id_to_edit ?? 'NEW';
        error_log("save_personal.php: General Exception user {$user_id_session}, CV ID {$cv_id_log}: " . $e->getMessage());
        $_SESSION['error_message'] = "Gabim i papritur. Provoni përsëri.";
        $_SESSION['form_data_temp'] = $_POST; // Preserve form data on error
        // Redirect back to form.php with errors and data
        $redirect_url = 'form.php';
        if ($cv_id_to_edit) { $redirect_url .= '?edit_id=' . $cv_id_to_edit; }
        header('Location: ' . $redirect_url);
        exit();
    }

} else {
    // Not a POST request, redirect to choose template or form
    $_SESSION['error_message'] = "Kërkesë e pavlefshme.";
    header('Location: choose_template.php'); // Or form.php depending on desired flow
    exit();
}
?>
