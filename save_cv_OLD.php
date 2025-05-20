<?php
error_reporting(E_ALL);   // Aktivizon raportimin e të gjitha gabimeve
ini_set('display_errors', 1);  // Shfaq gabimet në ekran

require 'connect.php'; // Include the database connection

// Get form data
$emri = $_POST['emri'] ?? '';
$mbiemri = $_POST['mbiemri'] ?? '';
$email = $_POST['email'] ?? '';
$telefoni = $_POST['telefoni'] ?? '';

// --- Server-side Input Validation (IMPORTANT!) ---
// Add validation here, e.g., check if email is valid format, required fields are not empty.
if (empty($emri) || empty($mbiemri) || empty($email) || empty($telefoni)) {
    die("Gabim: Ju lutemi plotësoni të gjitha fushat e kërkuara.");
}
// Add email format validation: if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { ... }


// Save to the 'cv' table
try {
    $sql = "INSERT INTO cv (emri, mbiemri, email, telefoni) VALUES (?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$emri, $mbiemri, $email, $telefoni]);

    // --- Redirect to the view_cvs page ---
    // Ensure no output happens before this header() call.
     $basePath = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
     header("Location: $basePath/view_cvs.php");
     exit(); // Always call exit() after header()

} catch (PDOException $e) {
     // Log the database error instead of displaying it publicly in production
    error_log("Database Error in save_cv.php: " . $e->getMessage());
    die("Një gabim ndodhi gjatë ruajtjes së CV-së. Ju lutemi provoni përsëri."); // Generic error message
}
?>
