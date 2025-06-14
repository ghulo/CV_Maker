<?php
// cv_maker/process_feedback.php
ob_start(); // Start output buffering - MUST BE THE VERY FIRST THING

// --- Error Reporting ---
error_reporting(0); // Turn off error reporting in production
ini_set('display_errors', 0);
ini_set('log_errors', 1); // Log errors
ini_set('error_log', __DIR__ . '/php_errors.log'); // Specify error log file

header('Content-Type: application/json'); // Indicate JSON response
$response = ['success' => false, 'message' => 'Kërkesë e pavlefshme.'];

// Use a try-catch block for the main logic to catch exceptions
try {
    // Allow requests only via POST method
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        $response['message'] = 'Metoda e kërkesës e pavlefshme.';
        // No need to echo and exit here, will be handled by final echo
    } else {
        // Decode the JSON input from the frontend
        $input = json_decode(file_get_contents('php://input'), true);

        // Check if JSON decoding was successful
        if ($input === null && json_last_error() !== JSON_ERROR_NONE) {
            error_log("Feedback Error: Invalid JSON received. Raw: " . file_get_contents('php://php://input') . " JSON Error: " . json_last_error_msg());
            $response['message'] = 'Të dhëna të dërguara në format të gabuar.';
             // No need to echo and exit here
        } else {
            // Extract and sanitize input data
            $contact_name = trim($input['contact_name'] ?? '');
            $contact_email = trim($input['contact_email'] ?? '');
            $feedback_subject = trim($input['feedback_subject'] ?? '');
            $feedback_message = trim($input['feedback_message'] ?? '');

            // Basic Server-side Validation
            $validation_errors = [];
            if (empty($feedback_subject)) {
                $validation_errors[] = "Subjekti është i detyrueshëm.";
            }
            if (empty($feedback_message)) {
                $validation_errors[] = "Mesazhi është i detyrueshëm.";
            }
            if (!empty($contact_email) && !filter_var($contact_email, FILTER_VALIDATE_EMAIL)) {
                $validation_errors[] = "Formati i emailit është i pavlefshëm.";
            }

            // If there are validation errors, return them
            if (!empty($validation_errors)) {
                // Combine validation errors into a single message
                $response['message'] = "Ju lutemi korrigjoni gabimet: " . implode(" ", $validation_errors);
                 // No need to echo and exit here
            } else {
                // Database connection
                $pdo = null;
                try {
                    // Ensure connect.php does NOT output anything, and handles its own errors internally or throws PDOExceptions
                    require_once 'connect.php'; // Include your database connection script
                } catch (PDOException $e) {
                    error_log("Database Connection Failed in process_feedback.php: " . $e->getMessage());
                    $response['message'] = 'Gabim serveri: Nuk mund të lidhet me bazën e të dhënave.';
                    // No need to echo and exit here
                } catch (Exception $e) {
                    error_log("General Error during DB connection include in process_feedback.php: " . $e->getMessage());
                     $response['message'] = 'Gabim serveri i papritur gjatë lidhjes me bazën e të dhënave.';
                }


                // Proceed with DB operation only if connection was successful
                if ($pdo) {
                     try {
                         // Insert data into the feedback table
                        $sql = "INSERT INTO feedback (contact_name, contact_email, feedback_subject, feedback_message)
                                VALUES (:name, :email, :subject, :message)";
                        $stmt = $pdo->prepare($sql);

                        // Bind parameters
                        $stmt->bindParam(':name', $contact_name, PDO::PARAM_STR);
                        $stmt->bindParam(':email', $contact_email, PDO::PARAM_STR);
                        $stmt->bindParam(':subject', $feedback_subject, PDO::PARAM_STR);
                        $stmt->bindParam(':message', $feedback_message, PDO::PARAM_STR);

                        // Execute the statement
                        if ($stmt->execute()) {
                            $response['success'] = true;
                            $response['message'] = 'Mesazhi u dërgua me sukses! Faleminderit për feedback-un.';
                        } else {
                            // Log detailed error if insert fails
                            $errorInfo = $stmt->errorInfo();
                            error_log("Database Insert Failed in process_feedback.php: SQLSTATE[{$errorInfo[0]}] {$errorInfo[2]}");
                            $response['message'] = 'Gabim serveri gjatë ruajtjes së mesazhit. Provoni përsëri më vonë.';
                        }
                     } catch (PDOException $e) {
                         // Catch and log PDO exceptions during DB insert
                         error_log("PDOException during DB insert in process_feedback.php: " . $e->getMessage());
                         $response['message'] = 'Gabim serveri i papritur gjatë ruajtjes së mesazhit.';
                     }
                }
            }
        }
    }

} catch (Exception $e) {
    // Catch any unexpected exceptions that might occur outside specific blocks
    error_log("General Exception in process_feedback.php (main block): " . $e->getMessage());
    $response['message'] = 'Një gabim i papritur ndodhi.';
}

// Clear the output buffer and send the JSON response
ob_clean(); // Clean the buffer - ensures no prior output is sent
echo json_encode($response); // Send the JSON response
ob_end_flush(); // Flush and turn off buffering
exit(); // Ensure script stops
?>
