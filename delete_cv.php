<?php
// cv_maker/delete_cv.php
session_start(); // Start session
require 'connect.php'; // Establishes $pdo

// --- Error Reporting ---
error_reporting(E_ALL);
ini_set('display_errors', 1); // Set to 0 for production
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/php_errors.log'); // Ensure this path is writable

// --- Authentication Check ---
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html?error=" . urlencode("Ju lutemi kyçuni për të fshirë CV-në."));
    exit();
}
$logged_in_user_id = (int)$_SESSION['user_id'];

// Get the CV ID (this is the 'id' from the 'cvs' table) from the URL parameter
$cv_id_to_delete = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($cv_id_to_delete <= 0) {
    header("Location: view_cvs.php?error=" . urlencode("ID e pavlefshme e CV-së."));
    exit();
}

try {
    // --- Authorization: Check if the CV (from 'cvs' table) belongs to the logged-in user BEFORE deleting ---
    $stmt_check_owner = $pdo->prepare("SELECT id FROM cvs WHERE id = :cv_id AND user_id = :user_id");
    $stmt_check_owner->execute([':cv_id' => $cv_id_to_delete, ':user_id' => $logged_in_user_id]);

    if (!$stmt_check_owner->fetch()) {
        // If CV not found or does not belong to the user
        error_log("Auth error (delete_cv.php): User {$logged_in_user_id} cannot delete CV ID {$cv_id_to_delete} from 'cvs' table (not found or not owned).");
        header("Location: view_cvs.php?error=" . urlencode("CV nuk u gjet ose nuk keni të drejtë ta fshini."));
        exit();
    }

    // --- Delete the CV entry from 'cvs' table ---
    // Assuming foreign keys with ON DELETE CASCADE are set up in the database
    // for work_experiences, educations, interests, and skills tables referencing cvs(id).
    // This will automatically delete related records in those tables when the main CV is deleted.
    $sql_delete = "DELETE FROM cvs WHERE id = :cv_id AND user_id = :user_id";
    $stmt_delete = $pdo->prepare($sql_delete);
    $stmt_delete->execute([':cv_id' => $cv_id_to_delete, ':user_id' => $logged_in_user_id]);

    // Check if any rows were affected (i.e., if the deletion was successful)
    if ($stmt_delete->rowCount() > 0) {
        // If the deleted CV was the one currently active in session for preview/editing, clear it.
        if (isset($_SESSION['cv_id']) && $_SESSION['cv_id'] == $cv_id_to_delete) {
            unset($_SESSION['cv_id']);
            unset($_SESSION['selected_template']); // Also clear its template
        }
        // Redirect to the CV list page with a success message
        header("Location: view_cvs.php?success=" . urlencode("CV u fshi me sukses."));
        exit();
    } else {
        // This case might happen if the CV was just deleted by another process,
        // or if there was an unexpected issue where the rowCount is 0 despite authorization passing.
        error_log("Delete Warning (delete_cv.php): CV ID {$cv_id_to_delete} for user {$logged_in_user_id} was authorized but rowCount was 0 on delete.");
        header("Location: view_cvs.php?error=" . urlencode("CV nuk u gjet ose nuk mund të fshihej (gabim i papritur)."));
        exit();
    }

} catch (PDOException $e) {
    // Catch and log PDO exceptions during deletion
    error_log("DB Error (delete_cv.php) for CV ID {$cv_id_to_delete}: " . $e->getMessage());
    header("Location: view_cvs.php?error=" . urlencode("Gabim serveri gjatë fshirjes së CV-së. Ju lutemi provoni përsëri."));
    exit();
}
?>
