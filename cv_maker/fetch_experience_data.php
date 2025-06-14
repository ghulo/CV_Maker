<?php
// cv_maker/fetch_experience_data.php
// This file seems to be intended for fetching experience data via AJAX,
// but it currently only outputs session data.
// Based on script.js and experience.php, data loading happens directly in experience.php.
// This file might be vestigial or intended for future AJAX editing features.
// Keeping it as is based on provided code.

session_start();
header('Content-Type: application/json'); // Set content type to JSON

// Check if session data for experience exists
if (isset($_SESSION['experience_info'])) {
    // Output the session data as a JSON object
    echo json_encode($_SESSION['experience_info']);
} else {
    // If no session data, output an empty JSON object
    echo json_encode([]); // Corrected to output an empty array/object
}

// Optionally clear session data after fetching if you only want it for one return trip
// unset($_SESSION['experience_info']);
?>
