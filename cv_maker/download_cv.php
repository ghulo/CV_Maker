<?php
// cv_maker/download_cv.php
session_start();

// --- Error Reporting (Development) ---
error_reporting(E_ALL);
ini_set('display_errors', 1); // Set to 0 for production
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/php_errors.log');

// Require the mPDF autoload file
$vendor_autoload = __DIR__ . '/vendor/autoload.php';
if (file_exists($vendor_autoload)) {
    require $vendor_autoload;
} else {
    error_log("mPDF critical error: vendor/autoload.php not found. Path checked: " . $vendor_autoload);
    if (!headers_sent()) {
        header("HTTP/1.1 500 Internal Server Error");
    }
    echo "Problem konfigurimi: Libraria PDF (mPDF) nuk është instaluar si duhet ose nuk gjendet. Ju lutemi kontaktoni administratorin.";
    exit();
}

// --- Authentication Check ---
if (!isset($_SESSION['user_id'])) {
    if (!headers_sent()) {
        header("HTTP/1.1 403 Forbidden");
    }
    echo "Qasja e ndaluar. Ju lutemi kyçuni për të shkarkuar CV-në.";
    exit();
}
$logged_in_user_id = (int)$_SESSION['user_id'];
// Get the CV ID (this is the 'id' from the 'cvs' table) from the URL parameter
$cv_id_to_download = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$cv_id_to_download) {
    if (!headers_sent()) {
        header("HTTP/1.1 400 Bad Request");
    }
    echo "ID e CV-së e pavlefshme ose mungon.";
    exit();
}

// --- Database Connection ---
$pdo = null;
try {
    require 'connect.php'; // Provides $pdo
} catch (PDOException $e) {
    error_log("CRITICAL Database Connection Failed in download_cv.php: " . $e->getMessage());
    if (!headers_sent()) {
        header("HTTP/1.1 500 Internal Server Error");
    }
    echo "Gabim kritik në lidhjen me bazën e të dhënave.";
    exit();
}


try {
    // Fetch CV details from 'cvs' table
    $stmt_cv = $pdo->prepare("SELECT * FROM cvs WHERE id = :cv_id AND user_id = :user_id");
    $stmt_cv->execute([':cv_id' => $cv_id_to_download, ':user_id' => $logged_in_user_id]);
    $cv_main_details = $stmt_cv->fetch(PDO::FETCH_ASSOC);

    if (!$cv_main_details) {
        if (!headers_sent()) {
            header("HTTP/1.1 404 Not Found");
        }
        echo "CV nuk u gjet ose nuk keni qasje për ta shkarkuar këtë CV.";
        exit();
    }

    $selected_template = $cv_main_details['selected_template'] ?? 'classic';

    // Fetch other related data using $cv_id_to_download (which is cvs.id)
    $stmt_work = $pdo->prepare("SELECT * FROM work_experiences WHERE cv_id = :cv_id ORDER BY start_date DESC, id DESC");
    $stmt_work->execute([':cv_id' => $cv_id_to_download]);
    $work_experiences = $stmt_work->fetchAll();

    $stmt_education = $pdo->prepare("SELECT * FROM educations WHERE cv_id = :cv_id ORDER BY graduation_year DESC, id DESC");
    $stmt_education->execute([':cv_id' => $cv_id_to_download]);
    $educations = $stmt_education->fetchAll();

    $stmt_interests_fetch = $pdo->prepare("SELECT description FROM interests WHERE cv_id = :cv_id LIMIT 1");
    $stmt_interests_fetch->execute([':cv_id' => $cv_id_to_download]);
    $interests_data = $stmt_interests_fetch->fetch(PDO::FETCH_ASSOC);
    $interests_text = ($interests_data && isset($interests_data['description'])) ? $interests_data['description'] : '';

    // Skills could be fetched here if needed by PDF generation functions
    // $stmt_skills = $pdo->prepare("SELECT * FROM skills WHERE cv_id = :cv_id ORDER BY category, skill_name");
    // $stmt_skills->execute([':cv_id' => $cv_id_to_download]);
    // $skills = $stmt_skills->fetchAll(PDO::FETCH_ASSOC);


} catch (PDOException $e) {
    error_log("DB Error (download_cv.php) for CV ID {$cv_id_to_download}: " . $e->getMessage());
    if (!headers_sent()) {
        header("HTTP/1.1 500 Internal Server Error");
    }
    echo "Gabim serveri gjatë marrjes së të dhënave për PDF.";
    exit();
}

// --- PDF Generation using mPDF ---
try {
    // Define a temporary directory for mPDF
    $tmpDir = __DIR__ . '/tmp';
    if (!is_dir($tmpDir)) {
        // Attempt to create the temporary directory if it doesn't exist
        if (!mkdir($tmpDir, 0775, true)) {
             error_log("mPDF critical error: Failed to create tmp directory at " . $tmpDir);
             echo "Problem konfigurimi: Drejtoria temporare për PDF nuk mund të krijohet.";
             exit();
        }
    }
    // Check if the temporary directory is writable
    if (!is_writable($tmpDir)) {
        error_log("mPDF critical error: tmp directory at " . $tmpDir . " is not writable.");
        echo "Problem konfigurimi: Drejtoria temporare për PDF nuk është e shkruajtshme.";
        exit();
    }


    // Create a new mPDF instance
    $mpdf = new \Mpdf\Mpdf([
        'mode' => 'utf-8',
        'format' => 'A4',
        'margin_left' => 15, 'margin_right' => 15,
        'margin_top' => 18, 'margin_bottom' => 18,
        'default_font' => 'dejavusans', // Use DejaVu Sans for better character support
        'tempDir' => $tmpDir // Specify the temporary directory
    ]);

    // Set PDF metadata
    $cv_title_for_pdf = $cv_main_details['cv_title'] ?? ($cv_main_details['emri'] . ' ' . $cv_main_details['mbiemri']);
    $mpdf->SetTitle('CV - ' . htmlspecialchars($cv_title_for_pdf));
    $mpdf->SetAuthor(htmlspecialchars($cv_main_details['emri'] ?? 'User') . ' ' . htmlspecialchars($cv_main_details['mbiemri'] ?? ''));
    $mpdf->SetCreator('CV Maker');


    // --- Load CSS ---
    // Load common PDF styles
    $common_css_file = __DIR__ . '/pdf_style.css';
    if (file_exists($common_css_file)) {
        $css_common = file_get_contents($common_css_file);
        $mpdf->WriteHTML($css_common, \Mpdf\HTMLParserMode::HEADER_CSS);
    } else {
        error_log("Common PDF CSS file not found: " . $common_css_file);
        // Provide minimal default CSS if the file is missing
        $mpdf->WriteHTML("body { font-family: dejavusans; } h1 {color: #333;} .section-title {color: #0056b3; border-bottom: 1px solid #0056b3; padding-bottom: 2mm; margin-bottom: 4mm;} /* Common CSS Missing */", \Mpdf\HTMLParserMode::HEADER_CSS);
    }

    // Load template-specific PDF styles if they exist
    $template_specific_css_file = null;
    if ($selected_template === 'professional') {
        $template_specific_css_file = __DIR__ . '/pdf_style_professional.css';
    }
    // Add 'else if' for other templates if they have specific PDF CSS
    // elseif ($selected_template === 'modern') {
    //    $template_specific_css_file = __DIR__ . '/pdf_style_modern.css';
    // }

    if ($template_specific_css_file && file_exists($template_specific_css_file)) {
        $css_template_specific = file_get_contents($template_specific_css_file);
        $mpdf->WriteHTML($css_template_specific, \Mpdf\HTMLParserMode::HEADER_CSS);
    } elseif ($template_specific_css_file) {
        error_log("Template-specific PDF CSS file not found for template '{$selected_template}': {$template_specific_css_file}");
    }


    // --- HTML Content Generation ---
    // The HTML generation functions (generate_classic_pdf_html, etc.) will use $cv_main_details
    // as their first argument, which contains all columns from the 'cvs' table.
    // Ensure these functions are updated to expect this structure if they previously expected 'personal_info'.

    // It's good practice to ensure these functions are defined.
    // If not already included by template_renderers.php or similar, define them here or require them.
    // For brevity, I'm assuming they are available.
    // Example: require_once 'pdf_html_generators.php';

    $html = '<div class="cv-container template-' . htmlspecialchars($selected_template) . '">';

    // Call the appropriate HTML generation function based on the selected template
    if ($selected_template === 'professional') {
        // Pass $cv_main_details instead of $personal
        $html .= generate_professional_pdf_html($cv_main_details, $work_experiences, $educations, $interests_text);
    } else { // Default to 'classic' or your original HTML structure
        // Pass $cv_main_details instead of $personal
        $html .= generate_classic_pdf_html($cv_main_details, $work_experiences, $educations, $interests_text);
    }
    $html .= '</div>';

    // Write the generated HTML to the PDF
    $mpdf->WriteHTML($html);

    // Generate a safe filename based on the user's name and current date
    $safe_emri = preg_replace('/[^A-Za-z0-9_\-]/', '', $cv_main_details['emri'] ?? 'CV');
    $safe_mbiemri = preg_replace('/[^A-Za-z0-9_\-]/', '', $cv_main_details['mbiemri'] ?? 'User');
    $filename = "CV_" . $safe_emri . "_" . $safe_mbiemri . "_" . date("Ymd") . ".pdf";

    // Output the PDF for download
    $mpdf->Output($filename, \Mpdf\Output\Destination::DOWNLOAD);
    exit; // Stop script execution after sending the file

} catch (\Mpdf\MpdfException $e) {
    // Catch and log mPDF specific exceptions
    error_log("mPDF Error (download_cv.php) for CV ID {$cv_id_to_download} using template {$selected_template}: " . $e->getMessage() . " (File: " . $e->getFile() . " Line: " . $e->getLine() . ")");
    echo "Problem gjatë gjenerimit të PDF (mPDF): " . htmlspecialchars($e->getMessage());
    exit();
} catch (Exception $e) {
    // Catch and log any other general exceptions during PDF generation
    error_log("General Error (download_cv.php) for CV ID {$cv_id_to_download}: " . $e->getMessage());
    echo "Një gabim i papritur ndodhi gjatë gjenerimit të PDF-së.";
    exit();
}

// --- HTML Generation Functions for PDF (Ensure these are defined or included) ---
// These functions should now expect $cv_data (an associative array from the 'cvs' table)
// as their first parameter, instead of a variable named $personal or $personal_info,
// and access fields like $cv_data['emri'], $cv_data['summary'], etc.

// Example implementation for generate_classic_pdf_html (ensure this is included or defined elsewhere)
if (!function_exists('generate_classic_pdf_html')) {
    function generate_classic_pdf_html($cv_data, $work_experiences, $educations, $interests_text) {
        // Ensure this function uses $cv_data['emri'], $cv_data['summary'] etc.
        // Example:
        $html = '<div class="cv-header">
            <h1>' . htmlspecialchars($cv_data['emri'] ?? '') . ' ' . htmlspecialchars($cv_data['mbiemri'] ?? '') . '</h1>';
        // Use summary as a professional title if it's short, otherwise use a default
        if(!empty($cv_data['summary']) && strlen($cv_data['summary']) < 100) {
            $html .= '<h3>' . htmlspecialchars($cv_data['summary']) . '</h3>';
        } else {
            $html .= '<h3>CURRICULUM VITAE</h3>';
        }
        $html .= '</div>';

        // Personal Information Section
        $html .= '<div class="section personal-info"><h2 class="section-title">INFORMACIONI PERSONAL</h2><table>';
        if(!empty($cv_data['emri'])) $html .= '<tr><td class="info-label">Emri:</td><td class="info-value">' . htmlspecialchars($cv_data['emri']) . '</td></tr>';
        // ... (Update all other fields to use $cv_data['column_name'])
        if(!empty($cv_data['mbiemri'])) $html .= '<tr><td class="info-label">Mbiemri:</td><td class="info-value">' . htmlspecialchars($cv_data['mbiemri']) . '</td></tr>';
        if(!empty($cv_data['email'])) $html .= '<tr><td class="info-label">Email:</td><td class="info-value">' . htmlspecialchars($cv_data['email']) . '</td></tr>';
        if(!empty($cv_data['telefoni'])) $html .= '<tr><td class="info-label">Telefoni:</td><td class="info-value">' . htmlspecialchars($cv_data['telefoni']) . '</td></tr>';
        if (!empty($cv_data['date_of_birth'])) {
            $html .= '<tr><td class="info-label">Datëlindja:</td><td class="info-value">' . htmlspecialchars(date("d F Y", strtotime($cv_data['date_of_birth']))) . '</td></tr>';
        }
        if (!empty($cv_data['address'])) $html .= '<tr><td class="info-label">Adresa:</td><td class="info-value">' . nl2br(htmlspecialchars($cv_data['address'])) . '</td></tr>';
        if (!empty($cv_data['place_of_birth'])) $html .= '<tr><td class="info-label">Vendlindja:</td><td class="info-value">' . htmlspecialchars($cv_data['place_of_birth']) . '</td></tr>';
        if (!empty($cv_data['nationality'])) $html .= '<tr><td class="info-label">Kombësia:</td><td class="info-value">' . htmlspecialchars($cv_data['nationality']) . '</td></tr>';
        if (!empty($cv_data['zip_code'])) $html .= '<tr><td class="info-label">Kodi Postar:</td><td class="info-value">' . htmlspecialchars($cv_data['zip_code']) . '</td></tr>';
        if (!empty($cv_data['marital_status'])) $html .= '<tr><td class="info-label">Statusi Martesor:</td><td class="info-value">' . htmlspecialchars($cv_data['marital_status']) . '</td></tr>';
        if (!empty($cv_data['driving_license'])) $html .= '<tr><td class="info-label">Patentë Shoferi:</td><td class="info-value">' . htmlspecialchars($cv_data['driving_license']) . '</td></tr>';
        $html .= '</table></div>';

        // Professional Summary Section (if summary is long)
        if (!empty($cv_data['summary']) && strlen($cv_data['summary']) >= 100) {
            $html .= '<div class="section summary-section"><h2 class="section-title">PËRMBLEDHJA PROFESIONALE</h2>';
            $html .= '<p class="description">' . nl2br(htmlspecialchars($cv_data['summary'])) . '</p>';
            $html .= '</div>';
        }
        // Work Experience Section
        $html .= '<div class="section work-experience"><h2 class="section-title">EKSPERIENCA PROFESIONALE</h2>';
        if (!empty($work_experiences)) {
            foreach ($work_experiences as $job) {
                if (empty($job['job_title']) && empty($job['company'])) continue;
                $html .= '<div class="entry">';
                $html .= '<div class="entry-title">' . htmlspecialchars($job['job_title'] ?? 'Pozitë e Paspecifikuar') . '</div>';
                $html .= '<div class="entry-subtitle">në ' . htmlspecialchars($job['company'] ?? 'Kompani e Paspecifikuar');
                // The column name in work_experiences table is 'experience_years' based on your experience.php, not job_duration.
                // However, your SQL dump for work_experiences does not show 'experience_years'.
                // Assuming it should be calculated or derived from start_date/end_date or is a field you intend to add.
                // For now, I'll comment out the duration part from here if it's not in the table.
                // if (isset($job['experience_years']) && $job['experience_years'] !== '' && $job['experience_years'] !== null) {
                //      $html .= ' (' . htmlspecialchars($job['experience_years']) . ($job['experience_years'] == 1 ? ' vit' : ' vite') . ' përvojë)';
                // }
                $html .= '</div>';
                 // Display start and end dates if available
                if (!empty($job['start_date']) || !empty($job['end_date']) || $job['is_current_job']) {
                     $date_range = htmlspecialchars($job['start_date'] ? date('M Y', strtotime($job['start_date'])) : '');
                     if ($job['is_current_job']) {
                         $date_range .= ' - Tani';
                     } elseif (!empty($job['end_date'])) {
                         $date_range .= ' - ' . htmlspecialchars(date('M Y', strtotime($job['end_date'])));
                     }
                     if (!empty($date_range)) {
                         $html .= '<div class="entry-dates">' . $date_range . '</div>';
                     }
                }
                if (!empty($job['job_description'])) {
                    $html .= '<div class="description">' . nl2br(htmlspecialchars($job['job_description'])) . '</div>';
                }
                $html .= '</div>';
            }
        } else { $html .= '<p class="no-data">Nuk ka eksperiencë pune të shtuar.</p>'; }
        $html .= '</div>';

        // Education Section
        $html .= '<div class="section education"><h2 class="section-title">EDUKIMI DHE TRAJNIMET</h2>';
        if (!empty($educations)) {
            foreach ($educations as $edu) {
                if (empty($edu['school'])) continue;
                $html .= '<div class="entry">';
                $html .= '<div class="entry-title">' . htmlspecialchars($edu['degree'] ?? 'Diplomë e Paspecifikuar') . '</div>';
                $html .= '<div class="entry-subtitle">' . htmlspecialchars($edu['school']) . '</div>';
                 // Display graduation year if available
                if (!empty($edu['graduation_year'])) {
                     $html .= '<div class="entry-dates">Viti i Diplomimit: ' . htmlspecialchars($edu['graduation_year']) . '</div>';
                }
                 // Display education description if available
                 if (!empty($edu['edu_description'])) {
                     $html .= '<div class="description">' . nl2br(htmlspecialchars($edu['edu_description'])) . '</div>';
                 }
                $html .= '</div>';
            }
        } else { $html .= '<p class="no-data">Nuk ka edukim të shtuar.</p>'; }
        $html .= '</div>';

        // Interests/Skills Section
        if (!empty($interests_text)) {
            $html .= '<div class="section interests"><h2 class="section-title">INTERESAT / AFTËSITË</h2>';
            // Attempt to format as list if it looks like comma-separated values or has newlines
            $interest_items = array_map('trim', explode(',', $interests_text));
            if (count($interest_items) > 1 || (count($interest_items) == 1 && strpos($interests_text, "\n") !== false) ) { // Check for list-like content
                $html .= '<ul class="interests-list">';
                foreach ($interest_items as $item) {
                     if(!empty($item)) $html .= '<li>' . htmlspecialchars($item) . '</li>';
                }
                $html .= '</ul>';
            } else {
                // Otherwise, display as a paragraph
                $html .= '<p class="description">' . nl2br(htmlspecialchars($interests_text)) . '</p>';
            }
            $html .= '</div>';
        }
        return $html;
    }
}

// Example implementation for generate_professional_pdf_html (ensure this is included or defined elsewhere)
if (!function_exists('generate_professional_pdf_html')) {
    function generate_professional_pdf_html($cv_data, $work_experiences, $educations, $interests_text) {
        // Ensure this function uses $cv_data['emri'], $cv_data['summary'] etc.
        // Example:
        $html = '<div class="cv-header">
                    <h1 class="full-name">' . htmlspecialchars(strtoupper($cv_data['emri'] ?? '')) . ' ' . htmlspecialchars(strtoupper($cv_data['mbiemri'] ?? '')) . '</h1>';
        // Use summary as professional title if short, or cv_title as fallback
        $professional_title_pdf = !empty($cv_data['summary']) && strlen($cv_data['summary']) < 100 ? $cv_data['summary'] : ($cv_data['cv_title'] ?? 'PROFESSIONAL PROFILE');
        $html .=   '<h3 class="job-title">' . htmlspecialchars(strtoupper($professional_title_pdf)) . '</h3>
                </div>';
        // Use a table for the two-column layout in PDF
        $html .= '<table class="two-column-layout" width="100%" cellpadding="0" cellspacing="0" border="0">';
        $html .= '<tr>';

        // --- Left Column ---
        $html .= '<td class="left-column" width="30%" style="vertical-align:top;">';
        // Contact Information
        $html .= '<div class="section contact-info"><h2 class="section-title">CONTACT</h2>';
        if(!empty($cv_data['telefoni'])) $html .= '<p class="contact-item"><strong class="icon">P:</strong> ' . htmlspecialchars($cv_data['telefoni']) . '</p>';
        if(!empty($cv_data['email'])) $html .= '<p class="contact-item"><strong class="icon">E:</strong> ' . htmlspecialchars($cv_data['email']) . '</p>';
        if(!empty($cv_data['address'])) {
            $address_pdf = nl2br(htmlspecialchars($cv_data['address']));
            if(!empty($cv_data['zip_code'])) $address_pdf .= ', ' . htmlspecialchars($cv_data['zip_code']);
            $html .= '<p class="contact-item"><strong class="icon">A:</strong> ' . $address_pdf . '</p>';
        } elseif (!empty($cv_data['zip_code'])) { // If address is empty but zip code exists
             $html .= '<p class="contact-item"><strong class="icon">A:</strong> Kodi Postar: ' . htmlspecialchars($cv_data['zip_code']) . '</p>';
        }
        $html .= '</div>';

        // Education Section
        $html .= '<div class="section education-section"><h2 class="section-title">EDUCATION</h2>';
        if (!empty($educations)) {
            foreach ($educations as $edu) {
                 if (empty($edu['school'])) continue;
                $html .= '<div class="education-entry">';
                $html .= '<p class="degree">' . htmlspecialchars($edu['degree'] ?? '') . '</p>';
                $html .= '<p class="university">' . htmlspecialchars($edu['school'] ?? '') . '</p>';
                 // Display graduation year if available
                if (!empty($edu['graduation_year'])) {
                     $html .= '<p class="edu-dates">' . htmlspecialchars($edu['graduation_year']) . '</p>';
                }
                 // Display education description if available
                 if (!empty($edu['edu_description'])) {
                     $html .= '<div class="description">' . nl2br(htmlspecialchars($edu['edu_description'])) . '</div>';
                 }
                $html .= '</div>';
            }
        } else { $html .= '<p class="no-data">No education listed.</p>';}
        $html .= '</div>';

        // Skills Section (using interests_text for simplicity)
        if (!empty($interests_text)) {
            $html .= '<div class="section skills-section"><h2 class="section-title">SKILLS</h2>';
            $skills_array_pdf = array_map('trim', explode(',', $interests_text));
            $html .= '<ul class="skills-list">';
            foreach ($skills_array_pdf as $item) {
                if(!empty($item)) $html .= '<li>' . htmlspecialchars($item) . '</li>';
            }
            $html .= '</ul></div>';
        }

        // Other Personal Details (optional)
        $other_details_html = '';
        if (!empty($cv_data['date_of_birth'])) $other_details_html .= '<p><strong>Date of Birth:</strong> ' . htmlspecialchars(date("d M Y", strtotime($cv_data['date_of_birth']))) . '</p>';
        if (!empty($cv_data['place_of_birth'])) $other_details_html .= '<p><strong>Place of Birth:</strong> ' . htmlspecialchars($cv_data['place_of_birth']) . '</p>';
        if (!empty($cv_data['nationality'])) $other_details_html .= '<p><strong>Nationality:</strong> ' . htmlspecialchars($cv_data['nationality']) . '</p>';
        if (!empty($cv_data['driving_license'])) $other_details_html .= '<p><strong>Driving License:</strong> ' . htmlspecialchars($cv_data['driving_license']) . '</p>';
        if (!empty($cv_data['marital_status'])) $other_details_html .= '<p><strong>Marital Status:</strong> ' . htmlspecialchars($cv_data['marital_status']) . '</p>';

        if(!empty($other_details_html)){
            $html .= '<div class="section other-personal-professional">';
            $html .= '<h3>OTHER</h3>';
            $html .= $other_details_html;
            $html .= '</div>';
        }


        $html .= '</td>';

        // --- Right Column ---
        $html .= '<td class="right-column" width="70%" style="vertical-align:top;">';
        // Display summary here only if it's long (short summary is used as title)
        if(!empty($cv_data['summary']) && strlen($cv_data['summary']) >= 100) {
            $html .= '<div class="section profile-summary-section"><h2 class="section-title">PROFILE SUMMARY</h2>';
            $html .= '<p>' . nl2br(htmlspecialchars($cv_data['summary'])) . '</p>';
            $html .= '</div>';
        }

        // Work Experience Section
        $html .= '<div class="section work-experience-section"><h2 class="section-title">WORK EXPERIENCE</h2>';
        if (!empty($work_experiences)) {
            foreach ($work_experiences as $job) {
                 if (empty($job['job_title']) && empty($job['company'])) continue;
                $html .= '<div class="work-entry">';
                $html .= '<p class="job-role">' . htmlspecialchars($job['job_title'] ?? '') . '</p>';
                $html .= '<p class="company-name">' . htmlspecialchars($job['company'] ?? '') . '</p>';
                // Again, experience_years is not in your work_experiences table dump.
                // if (isset($job['experience_years']) && $job['experience_years'] !== '' && $job['experience_years'] !== null) {
                //     $html .= '<p class="work-dates">' . htmlspecialchars($job['experience_years']) . ($job['experience_years'] == 1 ? ' year' : ' years') . '</p>';
                // }
                 // Display start and end dates if available
                if (!empty($job['start_date']) || !empty($job['end_date']) || $job['is_current_job']) {
                     $date_range = htmlspecialchars($job['start_date'] ? date('M Y', strtotime($job['start_date'])) : '');
                     if ($job['is_current_job']) {
                         $date_range .= ' - Present';
                     } elseif (!empty($job['end_date'])) {
                         $date_range .= ' - ' . htmlspecialchars(date('M Y', strtotime($job['end_date'])));
                     }
                     if (!empty($date_range)) {
                         $html .= '<p class="work-dates">' . $date_range . '</p>';
                     }
                }
                // Display job description as bullet points if multiple lines
                if (!empty($job['job_description'])) {
                    $description_points = array_map('trim', explode("\n", trim($job['job_description'])));
                    $description_points = array_filter($description_points); // Remove empty lines
                    if (count($description_points) > 0) {
                        $html .= '<div class="description"><ul>';
                        foreach($description_points as $point) {
                            $html .= '<li>' . htmlspecialchars($point) . '</li>';
                        }
                        $html .= '</ul></div>';
                    }
                }
                $html .= '</div>';
            }
        } else { $html .= '<p class="no-data">No work experience listed.</p>';}
        $html .= '</div>';
        $html .= '</td>';

        $html .= '</tr></table>';
        return $html;
    }
}


?>
