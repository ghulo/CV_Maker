<?php
// cv_maker/process_signup.php
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
        exit();
    }

    // Decode the JSON input from the frontend
    $input = json_decode(file_get_contents('php://input'), true);

    // Check if JSON decoding was successful
    if ($input === null && json_last_error() !== JSON_ERROR_NONE) {
        error_log("Signup Error: Invalid JSON. Raw: " . file_get_contents('php://php://input') . " JSON Error: " . json_last_error_msg());
        $response['message'] = 'Të dhëna të dërguara në format të gabuar.';
        echo json_encode($response);
        exit();
    }

    // Extract and sanitize input data
    $email = trim($input['email'] ?? '');
    $password = $input['password'] ?? '';
    $confirmPassword = $input['confirm_password'] ?? '';

    // Basic Server-side Validation
    if (empty($email) || empty($password) || empty($confirmPassword)) {
        $response['message'] = 'Ju lutemi plotësoni të gjitha fushat e kërkuara.';
        echo json_encode($response);
        exit();
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response['message'] = 'Formati i emailit është i pavlefshëm.';
        echo json_encode($response);
        exit();
    }
    if (strlen($password) < 6) {
        $response['message'] = 'Fjalëkalimi duhet të ketë të paktën 6 karaktere.';
        echo json_encode($response);
        exit();
    }
    if ($password !== $confirmPassword) {
        $response['message'] = 'Fjalëkalimet nuk përputhen.';
        echo json_encode($response);
        exit();
    }

    // Check if email already exists in the database
    $stmt_check = $pdo->prepare("SELECT id FROM users WHERE email = :email");
    $stmt_check->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt_check->execute();
    if ($stmt_check->fetch()) {
        $response['message'] = 'Ky email është i regjistruar tashmë. Provoni të kyçeni ose përdorni një email tjetër.';
        echo json_encode($response);
        exit();
    }

    // Hash the password before storing it
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    if ($hashedPassword === false) {
        error_log("Password hashing failed for signup (email: {$email}).");
        $response['message'] = 'Gabim kritik serveri #S2HASH.';
        echo json_encode($response);
        exit();
    }

    // Insert the new user into the database
    $sql_insert_user = "INSERT INTO users (email, password) VALUES (:email, :password)";
    $stmt_insert = $pdo->prepare($sql_insert_user);
    $stmt_insert->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt_insert->bindParam(':password', $hashedPassword, PDO::PARAM_STR);

    if ($stmt_insert->execute()) {
        // Signup successful
        $response = ['success' => true, 'message' => 'Regjistrimi ishte i suksesshëm! Tani mund të kyçeni.'];
    } else {
        // Log detailed error if insert fails
        $errorInfo = $stmt_insert->errorInfo();
        error_log("DB Error (signup insert for {$email}): SQLSTATE[{$errorInfo[0]}] {$errorInfo[2]}");
        $response['message'] = 'Regjistrimi dështoi #S3DB. Ju lutemi provoni përsëri më vonë.';
    }
} catch (PDOException $db_ex) {
    // Catch and log database exceptions
    error_log("Database Exception (process_signup.php for {$email}): " . $db_ex->getMessage());
    $response['message'] = 'Gabim serveri #S1DB. Ju lutemi provoni përsëri.';
} catch (Exception $ex) {
    // Catch and log any other general exceptions
    error_log("General Exception (process_signup.php for {$email}): " . $ex->getMessage());
    $response['message'] = 'Gabim serveri #S4GEN. Ju lutemi provoni përsëri më vonë.';
}

// Send the JSON response
echo json_encode($response);
exit();
?>
