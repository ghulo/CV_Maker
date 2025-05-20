<?php
// cv_maker/process_change_password.php
session_start();
ob_start(); // Start output buffering

// --- Error Reporting (Development) ---
error_reporting(E_ALL);
ini_set('display_errors', 1); // Set to 0 in production
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/php_errors.log'); // Ensure this path is writable

$user_id_session = $_SESSION['user_id'] ?? null;

// --- Authentication ---
if (!$user_id_session) {
    $_SESSION['error_message_profile'] = urlencode("Ju lutemi kyçuni për të ndryshuar fjalëkalimin.");
    header("Location: login.html?redirect_after_login=profile.php"); // Redirect to login then profile
    ob_end_flush(); // Flush buffer and exit
    exit();
}
$logged_in_user_id = (int)$user_id_session;

// --- Database Connection ---
$pdo = null;
try {
    require_once 'connect.php'; // Establishes $pdo
} catch (PDOException $e) {
    error_log("DB Connection FAULT in process_change_password.php: " . $e->getMessage());
    $_SESSION['error_message_profile'] = urlencode("Problem me lidhjen e databazës. Provoni më vonë.");
    header("Location: profile.php"); // Redirect back to profile page
    ob_end_flush(); // Flush buffer and exit
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_new_password = $_POST['confirm_new_password'] ?? '';

    // --- Validation ---
    if (empty($current_password) || empty($new_password) || empty($confirm_new_password)) {
        $_SESSION['error_message_profile'] = urlencode("Të gjitha fushat e fjalëkalimit janë të detyrueshme.");
        header("Location: profile.php");
        ob_end_flush();
        exit();
    }

    if (strlen($new_password) < 6) {
        $_SESSION['error_message_profile'] = urlencode("Fjalëkalimi i ri duhet të ketë të paktën 6 karaktere.");
        header("Location: profile.php");
        ob_end_flush();
        exit();
    }

    if ($new_password !== $confirm_new_password) {
        $_SESSION['error_message_profile'] = urlencode("Fjalëkalimet e reja nuk përputhen.");
        header("Location: profile.php");
        ob_end_flush();
        exit();
    }

    try {
        // Fetch current hashed password from the database
        $stmt_fetch = $pdo->prepare("SELECT password FROM users WHERE id = :user_id");
        $stmt_fetch->bindParam(':user_id', $logged_in_user_id, PDO::PARAM_INT);
        $stmt_fetch->execute();
        $user = $stmt_fetch->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            // Should not happen if session is valid, but as a safeguard
            $_SESSION['error_message_profile'] = urlencode("Përdoruesi nuk u gjet.");
            header("Location: profile.php");
            ob_end_flush();
            exit();
        }

        // Verify the current password provided by the user against the stored hash
        if (!password_verify($current_password, $user['password'])) {
            $_SESSION['error_message_profile'] = urlencode("Fjalëkalimi aktual është i pasaktë.");
            header("Location: profile.php");
            ob_end_flush();
            exit();
        }

        // Hash the new password
        $hashed_new_password = password_hash($new_password, PASSWORD_DEFAULT);
        if ($hashed_new_password === false) {
            error_log("Password hashing failed for user_id: " . $logged_in_user_id);
            $_SESSION['error_message_profile'] = urlencode("Gabim kritik gjatë përgatitjes së fjalëkalimit të ri.");
            header("Location: profile.php");
            ob_end_flush();
            exit();
        }

        // Update the password in the database
        $stmt_update = $pdo->prepare("UPDATE users SET password = :new_password WHERE id = :user_id");
        $stmt_update->bindParam(':new_password', $hashed_new_password, PDO::PARAM_STR);
        $stmt_update->bindParam(':user_id', $logged_in_user_id, PDO::PARAM_INT);

        if ($stmt_update->execute()) {
            $_SESSION['success_message_profile'] = urlencode("Fjalëkalimi u ndryshua me sukses!");
            // Optional: Force re-login for security by destroying session and redirecting to login
            // session_destroy();
            // header("Location: login.html?success=" . urlencode("Fjalëkalimi u ndryshua. Ju lutemi kyçuni përsëri."));
        } else {
            // Log detailed error if update fails
            $errorInfo = $stmt_update->errorInfo();
            error_log("DB Error updating password for user {$logged_in_user_id}: SQLSTATE[{$errorInfo[0]}] {$errorInfo[2]}");
            $_SESSION['error_message_profile'] = urlencode("Ndryshimi i fjalëkalimit dështoi. Provoni përsëri.");
        }

    } catch (PDOException $e) {
        // Catch and log PDO exceptions during DB operations
        error_log("PDOException in process_change_password.php for user {$logged_in_user_id}: " . $e->getMessage());
        $_SESSION['error_message_profile'] = urlencode("Gabim serveri gjatë ndryshimit të fjalëkalimit.");
    } catch (Exception $e) {
        // Catch and log any other general exceptions
        error_log("General Exception in process_change_password.php for user {$logged_in_user_id}: " . $e->getMessage());
        $_SESSION['error_message_profile'] = urlencode("Një gabim i papritur ndodhi.");
    }

    // Redirect back to the profile page to show messages
    header("Location: profile.php");
    ob_end_flush(); // Flush buffer and exit
    exit();

} else {
    // Not a POST request, redirect to profile page
    $_SESSION['error_message_profile'] = urlencode("Kërkesë e pavlefshme.");
    header("Location: profile.php");
    ob_end_flush(); // Flush buffer and exit
    exit();
}
?>
