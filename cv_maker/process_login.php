<?php // cv_maker/process_login.php
ob_start(); // Start output buffering
session_start(); // Start session
// --- Error Reporting ---
error_reporting(0); // Turn off error reporting in production
ini_set('display_errors', 0);
ini_set('log_errors', 1); // Log errors

header('Content-Type: application/json'); // Indicate JSON response
$response = ['success' => false, 'message' => 'Kërkesë e pavlefshme.'];

try {
    require_once 'connect.php'; // Include database connection

    // Allow requests only via POST method
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        $response['message'] = 'Metoda e kërkesës e pavlefshme.';
        echo json_encode($response);
        ob_end_flush(); // Flush output buffer
        exit(); // Ensure script stops
    }

    // Decode the JSON input from the frontend
    $input = json_decode(file_get_contents('php://input'), true);

    // Check if JSON decoding was successful
    if ($input === null && json_last_error() !== JSON_ERROR_NONE) {
        error_log("Login Error: Invalid JSON. Raw: " . file_get_contents('php://php://input') . " JSON Error: " . json_last_error_msg());
        $response['message'] = 'Të dhëna të dërguara në format të gabuar.';
        echo json_encode($response);
        ob_end_flush(); // Flush output buffer
        exit(); // Ensure script stops
    }

    // Extract and sanitize input data
    $email = trim($input['email'] ?? '');
    $password_from_form = $input['password'] ?? '';

    // Basic Server-side Validation
    if (empty($email) || empty($password_from_form)) {
        $response['message'] = 'Ju lutemi plotësoni emailin dhe fjalëkalimin.';
        echo json_encode($response);
        ob_end_flush(); // Flush output buffer
        exit(); // Ensure script stops
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response['message'] = 'Formati i emailit është i pavlefshëm.';
        echo json_encode($response);
        ob_end_flush(); // Flush output buffer
        exit(); // Ensure script stops
    }

    // Fetch user from database based on email
    $stmt = $pdo->prepare("SELECT id, password AS hashedPasswordFromDB FROM users WHERE email = :email");
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verify user exists and password matches
    if ($user && isset($user['hashedPasswordFromDB'])) {
        if (password_verify($password_from_form, $user['hashedPasswordFromDB'])) {
            // Login successful: Regenerate session ID for security and set session variables
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $email;

            // Determine redirect URL
            $redirect_url = 'view_cvs.php'; // Default redirect page

            // Check if there was an intended page before login (e.g., trying to access profile while logged out)
            if (isset($_SESSION['load_cv_id_after_login']) && !empty($_SESSION['load_cv_id_after_login'])) {
                 // If user was trying to view a specific CV, redirect there
                $_SESSION['cv_id'] = (int)$_SESSION['load_cv_id_after_login']; // Set cv_id in session
                unset($_SESSION['load_cv_id_after_login']); // Clear the load flag
                $redirect_url = 'preview_loader.php?id=' . $_SESSION['cv_id']; // Redirect via loader to ensure ownership check
            } elseif (isset($_SESSION['redirect_after_login']) && !empty($_SESSION['redirect_after_login'])) {
                // If a general redirect was set, use it after basic validation
                $intended_redirect = $_SESSION['redirect_after_login'];
                unset($_SESSION['redirect_after_login']); // Clear the redirect flag

                // Basic validation to prevent open redirects
                $base_name_intended = basename(parse_url($intended_redirect, PHP_URL_PATH));
                $allowed_redirects = ['form.php', 'experience.php', 'view_cvs.php', 'preview_cv.php', 'choose_template.php', 'preview_loader.php', 'profile.php']; // Add allowed pages

                // Check if the intended redirect is one of the allowed internal pages and doesn't contain directory traversal attempts
                if (in_array($base_name_intended, $allowed_redirects) && strpos($intended_redirect, '..') === false && !preg_match('#^/|http#', $intended_redirect)) {
                    $redirect_url = $intended_redirect;
                } else {
                    // Log a warning for potentially unsafe redirect attempt
                    error_log("Login Warning: Potentially unsafe redirect_after_login '{$intended_redirect}' for user {$email}. Using default.");
                }
            }
            // Add success message to session for the redirected page to display
            $_SESSION['success_message'] = 'Kyçje e suksesshme!';

            // Prepare success response with redirect URL
            $response = ['success' => true, 'message' => 'Kyçje e suksesshme!', 'redirect' => $redirect_url];
        } else {
            // Password mismatch
            $response['message'] = 'Email ose fjalëkalimi gabim.';
            error_log("Login Failure: Password mismatch for email [{$email}].");
        }
    } else {
        // User not found
        $response['message'] = 'Email ose fjalëkalimi gabim.';
        error_log("Login Failure: No user found for email [{$email}].");
    }
} catch (PDOException $db_ex) {
    // Catch and log database exceptions
    error_log("Database Exception (process_login.php for {$email}): " . $db_ex->getMessage());
    $response['message'] = 'Gabim serveri #L1DB. Ju lutemi provoni përsëri.';
} catch (Exception $ex) {
    // Catch and log any other general exceptions
    error_log("General Exception (process_login.php for {$email}): " . $ex->getMessage());
    $response['message'] = 'Një gabim i papritur ndodhi #L2GEN. Ju lutemi provoni përsëri.';
}

// Send the JSON response
echo json_encode($response);
ob_end_flush(); // Flush output buffer
exit(); // Ensure script stops
?>
