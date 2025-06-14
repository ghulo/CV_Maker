<?php
// cv_maker/fetch_cv_preview_content.php
session_start();

// --- Error Reporting (Development) ---
error_reporting(E_ALL);
ini_set('display_errors', 1); // Set to 0 for production
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/php_errors.log');

// Ensure template_renderers.php is in the same directory or adjust path
require_once 'template_renderers.php';

header('Content-Type: text/html; charset=UTF-8'); // Set content type to HTML

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401); // Unauthorized
    echo '<p class="message error">Ju lutemi kyçuni për të parë parapamjen e CV-së.</p>';
    exit();
}
$logged_in_user_id = (int)$_SESSION['user_id'];

// Get the CV ID from the GET request
$cv_id_to_fetch = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($cv_id_to_fetch <= 0) {
    http_response_code(400); // Bad Request
    echo '<p class="message error">ID e pavlefshme e CV-së.</p>';
    exit();
}

// Database connection
$pdo = null;
try {
    require 'connect.php'; // This will provide $pdo or throw an exception
} catch (PDOException $e) {
    error_log("DB Connection Failed in fetch_cv_preview_content.php: " . $e->getMessage());
    http_response_code(500); // Internal Server Error
    echo '<p class="message error">Gabim në lidhjen me bazën e të dhënave.</p>';
    exit();
}

try {
    // Fetch CV details from 'cvs' table, ensuring ownership
    $stmt_cv = $pdo->prepare("SELECT * FROM cvs WHERE id = :cv_id AND user_id = :user_id");
    $stmt_cv->execute([':cv_id' => $cv_id_to_fetch, ':user_id' => $logged_in_user_id]);
    $cv_details = $stmt_cv->fetch(PDO::FETCH_ASSOC);

    if (!$cv_details) {
        http_response_code(404); // Not Found
        echo '<p class="message error">CV nuk u gjet ose nuk keni qasje.</p>';
        exit();
    }

    $selected_template = $cv_details['selected_template'] ?? 'classic';

    // Fetch work experience for this CV
    $stmt_work = $pdo->prepare("SELECT * FROM work_experiences WHERE cv_id = :cv_id ORDER BY start_date DESC, id DESC");
    $stmt_work->execute([':cv_id' => $cv_id_to_fetch]);
    $work_experiences = $stmt_work->fetchAll(PDO::FETCH_ASSOC);

    // Fetch education for this CV
    $stmt_edu = $pdo->prepare("SELECT * FROM educations WHERE cv_id = :cv_id ORDER BY graduation_year DESC, id DESC");
    $stmt_edu->execute([':cv_id' => $cv_id_to_fetch]);
    $educations = $stmt_edu->fetchAll(PDO::FETCH_ASSOC);

    // Fetch interests for this CV
    $stmt_interests_fetch = $pdo->prepare("SELECT description FROM interests WHERE cv_id = :cv_id LIMIT 1");
    $stmt_interests_fetch->execute([':cv_id' => $cv_id_to_fetch]);
    $interests_data = $stmt_interests_fetch->fetch(PDO::FETCH_ASSOC);
    $interests_text = ($interests_data && isset($interests_data['description'])) ? $interests_data['description'] : '';

    // Fetch skills (if your templates use them directly in preview)
    // $stmt_skills = $pdo->prepare("SELECT * FROM skills WHERE cv_id = :cv_id ORDER BY category, skill_name");
    // $stmt_skills->execute([':cv_id' => $cv_id_to_fetch]);
    // $skills = $stmt_skills->fetchAll(PDO::FETCH_ASSOC);


    // --- Generate HTML for the preview using the centralized renderers ---
    // Wrap the generated HTML in a div with template-specific class for styling
    $html_content = '<div class="cv-preview-content template-' . htmlspecialchars($selected_template) . '">';

    // Call the appropriate rendering function based on the selected template
    switch ($selected_template) {
        case 'modern':
            $html_content .= render_modern_preview_html($cv_details, $work_experiences, $educations, $interests_text);
            break;
        case 'professional':
            $html_content .= render_professional_preview_html($cv_details, $work_experiences, $educations, $interests_text);
            break;
        case 'classic':
        default:
            $html_content .= render_classic_preview_html($cv_details, $work_experiences, $educations, $interests_text);
            break;
    }

    $html_content .= '</div>'; // End cv-preview-content wrapper

    // Output the generated HTML
    echo $html_content;

} catch (PDOException $e) {
    // Catch and log PDO exceptions during data fetching
    error_log("DB Error (fetch_cv_preview_content.php) for CV ID {$cv_id_to_fetch}: " . $e->getMessage());
    http_response_code(500); // Internal Server Error
    echo '<p class="message error">Gabim serveri gjatë ngarkimit të të dhënave të CV-së.</p>';
} catch (Exception $e) {
    // Catch and log any other general exceptions
    error_log("General Error (fetch_cv_preview_content.php) for CV ID {$cv_id_to_fetch}: " . $e->getMessage());
    http_response_code(500); // Internal Server Error
    echo '<p class="message error">Një gabim i papritur ndodhi.</p>';
}
?>
