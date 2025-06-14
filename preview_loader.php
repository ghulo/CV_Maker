<?php
// cv_maker/preview_loader.php
// This script acts as an intermediary to ensure the user is logged in
// and owns the CV before redirecting to the preview page.
session_start();
// --- Error Reporting ---
error_reporting(0); // Should be E_ALL in development
ini_set('display_errors', 0); // Should be 1 in development
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/php_errors.log'); // Ensure this path is writable


// --- Authentication Check ---
if (!isset($_SESSION['user_id'])) {
    // If user is not logged in, save the requested CV ID and redirect to login
    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
        $_SESSION['load_cv_id_after_login'] = (int)$_GET['id']; // This will be the cv_id to load after login
    }
    // Construct redirect URL carefully, ensuring 'id' is passed if present
    $redirect_param = isset($_GET['id']) ? '?id='.(int)$_GET['id'] : '';
    $_SESSION['redirect_after_login'] = 'preview_loader.php' . $redirect_param; // Redirect back to this loader after login

    header("Location: login.html?error=" . urlencode("Ju lutemi kyçuni për të parë CV-në."));
    exit();
}
$logged_in_user_id = (int)$_SESSION['user_id'];
// Get the CV ID from the GET request
$cv_to_load_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// --- Validate CV ID ---
if ($cv_to_load_id <= 0) {
    header("Location: view_cvs.php?error=" . urlencode("ID e pavlefshme e CV-së."));
    exit();
}

// --- Database Connection ---
try {
    require 'connect.php'; // Establishes $pdo
    // --- Authorization: Verify ownership of the CV ---
    // Fetch from 'cvs' table, checking both ID and user_id
    $stmt_check_cv_owner = $pdo->prepare("SELECT id, selected_template FROM cvs WHERE id = :cv_id AND user_id = :user_id");
    $stmt_check_cv_owner->execute([':cv_id' => $cv_to_load_id, ':user_id' => $logged_in_user_id]);
    $cv_data = $stmt_check_cv_owner->fetch(PDO::FETCH_ASSOC);

    if ($cv_data) {
        // If CV is found and belongs to the user, set session variables and redirect to preview page
        $_SESSION['cv_id'] = $cv_data['id']; // Use 'cv_id' consistently for the session
        $_SESSION['selected_template'] = $cv_data['selected_template']; // Store selected template in session
        header("Location: preview_cv.php"); // Redirect to the actual preview page
        exit();
    } else {
        // If CV not found or does not belong to the user
        error_log("Auth error (preview_loader.php): User {$logged_in_user_id} / CV ID {$cv_to_load_id} - Not found or not owned in 'cvs' table.");
        header("Location: view_cvs.php?error=" . urlencode("CV nuk u gjet ose nuk keni qasje."));
        exit();
    }
} catch (PDOException $e) {
    // Catch and log database exceptions
    error_log("DB Error (preview_loader.php) for CV ID {$cv_to_load_id}: " . $e->getMessage());
    header("Location: view_cvs.php?error=" . urlencode("Gabim serveri gjatë ngarkimit të CV-së."));
    exit();
} catch (Exception $e) {
    // Catch and log any other general exceptions
    error_log("General Error (preview_loader.php) for CV ID {$cv_to_load_id}: " . $e->getMessage());
    header("Location: view_cvs.php?error=" . urlencode("Gabim i papritur serveri."));
    exit();
}
?>
