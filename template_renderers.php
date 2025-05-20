<?php
// cv_maker/template_renderers.php
// This file contains functions to render CV HTML for different templates.
// These functions are used by preview_cv.php and fetch_cv_preview_content.php

// --- Error Reporting (Development) ---
// error_reporting(E_ALL); // Enable in development
// ini_set('display_errors', 1); // Set to 0 for production
// ini_set('log_errors', 1);
// ini_set('error_log', __DIR__ . '/php_errors.log');

// Function to render HTML for the Classic template preview
if (!function_exists('render_classic_preview_html')) {
    function render_classic_preview_html($personal_info, $work_experiences, $educations, $interests_text) {
        // $personal_info is expected to be the associative array fetched from the 'cvs' table
        $html = '<div class="cv-preview-content template-classic">'; // Wrapper div for template-specific styling
        $html .= '<div class="cv-header-classic">';
        $html .= '<h1>' . htmlspecialchars($personal_info['emri'] ?? '') . ' ' . htmlspecialchars($personal_info['mbiemri'] ?? '') . '</h1>';
        // Assuming 'summary' can act as a professional title for classic template if desired and is short
        if(!empty($personal_info['summary']) && strlen($personal_info['summary']) < 100) {
             $html .= '<h2 class="professional-title-classic">' . htmlspecialchars($personal_info['summary']) . '</h2>';
        }
        $html .= '</div>';

        // Personal Information Section
        $html .= '<div class="cv-section personal-details-classic">';
        $html .= '<h3>Informacioni Personal</h3>';
        $html .= '<div class="info-grid">'; // Use grid for layout
        if (!empty($personal_info['email'])) $html .= '<div class="info-item"><span class="info-label">Email:</span><span class="info-value">' . htmlspecialchars($personal_info['email']) . '</span></div>';
        if (!empty($personal_info['telefoni'])) $html .= '<div class="info-item"><span class="info-label">Telefoni:</span><span class="info-value">' . htmlspecialchars($personal_info['telefoni']) . '</span></div>'; // Use 'telefoni'
        if (!empty($personal_info['address'])) $html .= '<div class="info-item info-address"><span class="info-label">Adresa:</span><span class="info-value">' . nl2br(htmlspecialchars($personal_info['address'])) . '</span></div>';
        if (!empty($personal_info['date_of_birth'])) $html .= '<div class="info-item"><span class="info-label">Datëlindja:</span><span class="info-value">' . htmlspecialchars(date("d F Y", strtotime($personal_info['date_of_birth']))) . '</span></div>';
        if (!empty($personal_info['place_of_birth'])) $html .= '<div class="info-item"><span class="info-label">Vendlindja:</span><span class="info-value">' . htmlspecialchars($personal_info['place_of_birth']) . '</span></div>';
        if (!empty($personal_info['gender'])) $html .= '<div class="info-item"><span class="info-label">Gjinia:</span><span class="info-value">' . htmlspecialchars($personal_info['gender']) . '</span></div>';
        if (!empty($personal_info['nationality'])) $html .= '<div class="info-item"><span class="info-label">Kombësia:</span><span class="info-value">' . htmlspecialchars($personal_info['nationality']) . '</span></div>';
        if (!empty($personal_info['zip_code'])) $html .= '<div class="info-item"><span class="info-label">Kodi Postar:</span><span class="info-value">' . htmlspecialchars($personal_info['zip_code']) . '</span></div>';
        if (!empty($personal_info['marital_status'])) $html .= '<div class="info-item"><span class="info-label">Statusi Martesor:</span><span class="info-value">' . htmlspecialchars($personal_info['marital_status']) . '</span></div>';
        if (!empty($personal_info['driving_license'])) $html .= '<div class="info-item"><span class="info-label">Patentë Shoferi:</span><span class="info-value">' . htmlspecialchars($personal_info['driving_license']) . '</span></div>';
        $html .= '</div>';
        $html .= '</div>';

        // Professional Summary Section (if summary is longer)
        if (!empty($personal_info['summary']) && strlen($personal_info['summary']) >= 100) {
            $html .= '<div class="cv-section summary-section-classic">';
            $html .= '<h3>Përmbledhja Profesionale</h3>';
            $html .= '<p>' . nl2br(htmlspecialchars($personal_info['summary'])) . '</p>';
            $html .= '</div>';
        }

        // Work Experience Section
        $html .= '<div class="cv-section work-experience-classic">';
        $html .= '<h3>Eksperienca e Punës</h3>';
        if (!empty($work_experiences)) {
            foreach ($work_experiences as $work) {
                if (empty($work['job_title']) && empty($work['company'])) continue; // Skip empty entries
                $html .= '<div class="entry">';
                $html .= '<h4>' . htmlspecialchars($work['job_title'] ?? 'Pozitë') . '</h4>';
                $html .= '<p class="company-duration">' . htmlspecialchars($work['company'] ?? 'Kompani');
                 // Display start and end dates if available
                if (!empty($work['start_date']) || !empty($work['end_date']) || $work['is_current_job']) {
                     $date_range = htmlspecialchars($work['start_date'] ? date('M Y', strtotime($work['start_date'])) : '');
                     if ($work['is_current_job']) {
                         $date_range .= ' - Tani';
                     } elseif (!empty($work['end_date'])) {
                         $date_range .= ' - ' . htmlspecialchars(date('M Y', strtotime($work['end_date'])));
                     }
                     if (!empty($date_range)) {
                         $html .= ' (' . $date_range . ')';
                     }
                }
                $html .= '</p>';
                if (!empty($work['job_description'])) $html .= '<div class="description">' . nl2br(htmlspecialchars($work['job_description'])) . '</div>';
                $html .= '</div>';
            }
        } else { $html .= '<p class="no-data">Nuk ka eksperiencë pune të shtuar.</p>'; }
        $html .= '</div>';

        // Education Section
        $html .= '<div class="cv-section education-classic">';
        $html .= '<h3>Edukimi</h3>';
        if (!empty($educations)) {
            foreach ($educations as $edu) {
                if (empty($edu['school'])) continue; // Skip empty entries
                $html .= '<div class="entry">';
                $html .= '<h4>' . htmlspecialchars($edu['degree'] ?? 'Diplomë') . '</h4>';
                $html .= '<p class="school-graduation">' . htmlspecialchars($edu['school'] ?? 'Institucion');
                 // Display graduation year if available
                 if (!empty($edu['graduation_year'])) {
                     $html .= ' - Diplomuar: ' . htmlspecialchars($edu['graduation_year']);
                 }
                $html .= '</p>';
                 // Display education description if available
                 if (!empty($edu['edu_description'])) {
                     $html .= '<div class="description">' . nl2br(htmlspecialchars($edu['edu_description'])) . '</div>';
                 }
                $html .= '</div>';
            }
        } else { $html .= '<p class="no-data">Nuk ka edukim të shtuar.</p>'; }
        $html .= '</div>';

        // Interests Section
        if (!empty($interests_text)) {
            $html .= '<div class="cv-section interests-classic">';
            $html .= '<h3>Interesat / Aftësitë</h3>';
            $html .= '<p>' . nl2br(htmlspecialchars($interests_text)) . '</p>'; // Display as plain text with line breaks
            $html .= '</div>';
        }
         $html .= '</div>'; // End cv-preview-content
        return $html;
    }
}

// Function to render HTML for the Modern template preview
if (!function_exists('render_modern_preview_html')) {
    function render_modern_preview_html($personal_info, $work_experiences, $educations, $interests_text) {
         // $personal_info is expected to be the associative array fetched from the 'cvs' table
        $html = '<div class="cv-preview-content template-modern">'; // Wrapper div for template-specific styling
        $html .= '<div class="cv-modern-layout">';
        // Sidebar (Left Column)
        $html .= '<div class="cv-modern-sidebar">';
        // Contact Information
        $html .= '<h3>Kontakt</h3>';
        if (!empty($personal_info['email'])) $html .= '<p><i class="fas fa-envelope icon"></i> ' . htmlspecialchars($personal_info['email']) . '</p>';
        if (!empty($personal_info['telefoni'])) $html .= '<p><i class="fas fa-phone icon"></i> ' . htmlspecialchars($personal_info['telefoni']) . '</p>'; // Use 'telefoni'
        if (!empty($personal_info['address'])) $html .= '<p><i class="fas fa-map-marker-alt icon"></i> ' . nl2br(htmlspecialchars($personal_info['address'])) . '</p>';
        if (!empty($personal_info['zip_code'])) $html .=  '<p style="padding-left:1.4em;">Kodi Postar: '.htmlspecialchars($personal_info['zip_code']).'</p>';


        // Additional Personal Information Section
        $html .= '<h3 class="sidebar-section-title">Informacione Shtesë</h3>';
        if (!empty($personal_info['date_of_birth'])) $html .= '<p><i class="fas fa-birthday-cake icon"></i> ' . htmlspecialchars(date("d M Y", strtotime($personal_info['date_of_birth']))) . '</p>';
        if (!empty($personal_info['place_of_birth'])) $html .= '<p><i class="fas fa-map-pin icon"></i> Vendlindja: ' . htmlspecialchars($personal_info['place_of_birth']) . '</p>';
        if (!empty($personal_info['nationality'])) $html .= '<p><i class="fas fa-flag icon"></i> Kombësia: ' . htmlspecialchars($personal_info['nationality']) . '</p>';
        if (!empty($personal_info['driving_license'])) $html .= '<p><i class="fas fa-car icon"></i> Patentë: ' . htmlspecialchars($personal_info['driving_license']) . '</p>';


        // Skills Section (using interests_text and displaying as a list)
        if (!empty($interests_text)) {
            $html .= '<h3 class="sidebar-section-title skills-header">Aftësitë</h3>';
            // Assuming interests_text can be comma-separated for skills in this template
            $skills_array = array_map('trim', explode(',', $interests_text));
            $html .= '<ul class="skills-list">';
            foreach($skills_array as $skill_item) {
                if(!empty($skill_item)) $html .= '<li>' . htmlspecialchars($skill_item) . '</li>';
            }
            $html .= '</ul>';
        }
        $html .= '</div>'; // End Sidebar

        // Main Content (Right Column)
        $html .= '<div class="cv-modern-main">';
        $html .= '<div class="cv-header-modern">';
        $html .= '<h1>' . htmlspecialchars($personal_info['emri'] ?? '') . ' ' . htmlspecialchars($personal_info['mbiemri'] ?? '') . '</h1>';
        // Use summary as professional title if it's short, otherwise it's a separate section
        if(!empty($personal_info['summary']) && strlen($personal_info['summary']) < 100) {
             $html .= '<h2 class="professional-title-modern">' . htmlspecialchars($personal_info['summary']) . '</h2>';
        }
        $html .= '</div>';

        // Professional Summary Section (if summary is longer)
        if (!empty($personal_info['summary']) && strlen($personal_info['summary']) >= 100) {
            $html .= '<div class="cv-section summary-section-modern">';
            $html .= '<h3>Përmbledhja Profesionale</h3>';
            $html .= '<p>' . nl2br(htmlspecialchars($personal_info['summary'])) . '</p>';
            $html .= '</div>';
        }

        // Work Experience Section
        $html .= '<div class="cv-section work-experience-modern">';
        $html .= '<h3>Eksperienca e Punës</h3>';
        if (!empty($work_experiences)) {
            foreach ($work_experiences as $work) {
                 if (empty($work['job_title']) && empty($work['company'])) continue; // Skip empty entries
                $html .= '<div class="entry">';
                $html .= '<h4>' . htmlspecialchars($work['job_title'] ?? 'Pozitë') . '</h4>';
                $html .= '<p class="company-duration">' . htmlspecialchars($work['company'] ?? 'Kompani');
                 // Display start and end dates if available
                if (!empty($work['start_date']) || !empty($work['end_date']) || $work['is_current_job']) {
                     $date_range = htmlspecialchars($work['start_date'] ? date('M Y', strtotime($work['start_date'])) : '');
                     if ($work['is_current_job']) {
                         $date_range .= ' - Tani';
                     } elseif (!empty($work['end_date'])) {
                         $date_range .= ' - ' . htmlspecialchars(date('M Y', strtotime($work['end_date'])));
                     }
                     if (!empty($date_range)) {
                         $html .= ' (' . $date_range . ')';
                     }
                }
                $html .= '</p>';
                if (!empty($work['job_description'])) $html .= '<div class="description">' . nl2br(htmlspecialchars($work['job_description'])) . '</div>';
                $html .= '</div>';
            }
        } else { $html .= '<p class="no-data">Nuk ka eksperiencë pune të shtuar.</p>'; }
        $html .= '</div>';

        // Education Section
        $html .= '<div class="cv-section education-modern">';
        $html .= '<h3>Edukimi</h3>';
        if (!empty($educations)) {
            foreach ($educations as $edu) {
                 if (empty($edu['school'])) continue; // Skip empty entries
                $html .= '<div class="entry">';
                $html .= '<h4>' . htmlspecialchars($edu['degree'] ?? 'Diplomë') . '</h4>';
                $html .= '<p class="school-graduation">' . htmlspecialchars($edu['school'] ?? 'Institucion');
                 // Display graduation year if available
                 if (!empty($edu['graduation_year'])) {
                     $html .= ' - Diplomuar: ' . htmlspecialchars($edu['graduation_year']);
                 }
                $html .= '</p>';
                 // Display education description if available
                 if (!empty($edu['edu_description'])) {
                     $html .= '<div class="description">' . nl2br(htmlspecialchars($edu['edu_description'])) . '</div>';
                 }
                $html .= '</div>';
            }
        } else { $html .= '<p class="no-data">Nuk ka edukim të shtuar.</p>'; }
        $html .= '</div>';

        $html .= '</div>'; // End Main Content
        $html .= '</div>'; // End Modern Layout
         $html .= '</div>'; // End cv-preview-content
        return $html;
    }
}

// Function to render HTML for the Professional template preview
if (!function_exists('render_professional_preview_html')) {
    function render_professional_preview_html($personal_info, $work_experiences, $educations, $interests_text) {
         // $personal_info is expected to be the associative array fetched from the 'cvs' table
        $html = '<div class="cv-preview-content template-professional">'; // Wrapper div for template-specific styling
        $html .= '<div class="cv-header-professional">';
        $html .= '<h1>' . strtoupper(htmlspecialchars($personal_info['emri'] ?? '')) . ' ' . strtoupper(htmlspecialchars($personal_info['mbiemri'] ?? '')) . '</h1>';
        // Use summary as professional title if short, or cv_title as fallback
        $professional_title = !empty($personal_info['summary']) && strlen($personal_info['summary']) < 100 ? $personal_info['summary'] : ($personal_info['cv_title'] ?? 'PROFESSIONAL TITLE');
        $html .= '<h2 class="professional-title-main">' . strtoupper(htmlspecialchars($professional_title)) . '</h2>';
        $html .= '</div>';

        $html .= '<div class="cv-professional-layout">';
        // Left Column
        $html .= '<div class="cv-professional-left">';
        // Contact Information
        $html .= '<div class="cv-section contact-professional">';
        $html .= '<h3>KONTAKT</h3>';
        if (!empty($personal_info['email'])) $html .= '<p><i class="fas fa-envelope icon"></i> ' . htmlspecialchars($personal_info['email']) . '</p>';
        if (!empty($personal_info['telefoni'])) $html .= '<p><i class="fas fa-phone icon"></i> ' . htmlspecialchars($personal_info['telefoni']) . '</p>'; // Use 'telefoni'
        if (!empty($personal_info['address'])) {
            $address_html = nl2br(htmlspecialchars($personal_info['address']));
            if (!empty($personal_info['zip_code'])) {
                $address_html .= ', ' . htmlspecialchars($personal_info['zip_code']);
            }
            $html .= '<p><i class="fas fa-map-marker-alt icon"></i> ' . $address_html . '</p>';
        } elseif (!empty($personal_info['zip_code'])) { // If address is empty but zip code exists
            $html .= '<p><i class="fas fa-map-marker-alt icon"></i> Kodi Postar: ' . htmlspecialchars($personal_info['zip_code']) . '</p>';
        }
        $html .= '</div>';

        // Education Section
        $html .= '<div class="cv-section education-professional">';
        $html .= '<h3>EDUKIMI</h3>';
        if (!empty($educations)) {
            foreach ($educations as $edu) {
                if (empty($edu['school'])) continue; // Skip empty entries
                $html .= '<div class="entry">';
                $html .= '<h4>' . htmlspecialchars($edu['degree'] ?? 'Diplomë') . '</h4>';
                $html .= '<p class="school">' . htmlspecialchars($edu['school'] ?? 'Institucion') . '</p>';
                 // Display graduation year if available
                if (!empty($edu['graduation_year'])) {
                     $html .= '<p class="graduation-year">' . htmlspecialchars($edu['graduation_year']) . '</p>';
                }
                 // Display education description if available
                 if (!empty($edu['edu_description'])) {
                     $html .= '<div class="description">' . nl2br(htmlspecialchars($edu['edu_description'])) . '</div>';
                 }
                $html .= '</div>';
            }
        } else { $html .= '<p class="no-data">Nuk ka edukim të shtuar.</p>'; }
        $html .= '</div>';

        // Skills Section (using interests_text and displaying as a list)
        if (!empty($interests_text)) {
            $html .= '<div class="cv-section skills-professional">';
            $html .= '<h3>AFTËSITË</h3>';
            // Assuming interests_text can be comma-separated for skills in this template
            $skills_array = array_map('trim', explode(',', $interests_text));
            $html .= '<ul class="skills-list-prof">';
            foreach($skills_array as $skill_item) {
                if(!empty($skill_item)) $html .= '<li>' . htmlspecialchars($skill_item) . '</li>';
            }
            $html .= '</ul>';
            $html .= '</div>';
        }

        // Other personal details for professional left column (optional)
        $other_details_html = '';
        if (!empty($personal_info['date_of_birth'])) $other_details_html .= '<p><strong>Datëlindja:</strong> ' . htmlspecialchars(date("d M Y", strtotime($personal_info['date_of_birth']))) . '</p>';
        if (!empty($personal_info['place_of_birth'])) $other_details_html .= '<p><strong>Vendlindja:</strong> ' . htmlspecialchars($personal_info['place_of_birth']) . '</p>';
        if (!empty($personal_info['nationality'])) $other_details_html .= '<p><strong>Kombësia:</strong> ' . htmlspecialchars($personal_info['nationality']) . '</p>';
        if (!empty($personal_info['driving_license'])) $other_details_html .= '<p><strong>Patentë Shoferi:</strong> ' . htmlspecialchars($personal_info['driving_license']) . '</p>';
        if (!empty($personal_info['marital_status'])) $other_details_html .= '<p><strong>Statusi Martesor:</strong> ' . htmlspecialchars($personal_info['marital_status']) . '</p>';


        if(!empty($other_details_html)){
            $html .= '<div class="cv-section other-personal-professional">';
            $html .= '<h3>TË TJERA</h3>';
            $html .= $other_details_html;
            $html .= '</div>';
        }


        $html .= '</div>'; // End Left Column

        // Right Column (Main Content)
        $html .= '<div class="cv-professional-right">';
        // Display summary here only if it's long (short summary is used as title)
        if (!empty($personal_info['summary']) && strlen($personal_info['summary']) >= 100) {
            $html .= '<div class="cv-section summary-professional">';
            $html .= '<h3>PROFILI PROFESIONAL</h3>';
            $html .= '<p>' . nl2br(htmlspecialchars($personal_info['summary'])) . '</p>';
            $html .= '</div>';
        }

        // Work Experience Section
        $html .= '<div class="cv-section work-experience-professional">';
        $html .= '<h3>EKSPERIENCA E PUNËS</h3>';
        if (!empty($work_experiences)) {
            foreach ($work_experiences as $work) {
                if (empty($work['job_title']) && empty($work['company'])) continue; // Skip empty entries
                $html .= '<div class="entry">';
                $html .= '<h4>' . htmlspecialchars($work['job_title'] ?? 'Pozitë') . '</h4>';
                $html .= '<p class="company-duration">' . htmlspecialchars($work['company'] ?? 'Kompani');
                 // Display start and end dates if available
                if (!empty($work['start_date']) || !empty($work['end_date']) || $work['is_current_job']) {
                     $date_range = htmlspecialchars($work['start_date'] ? date('M Y', strtotime($work['start_date'])) : '');
                     if ($work['is_current_job']) {
                         $date_range .= ' - Tani';
                     } elseif (!empty($work['end_date'])) {
                         $date_range .= ' - ' . htmlspecialchars(date('M Y', strtotime($work['end_date'])));
                     }
                     if (!empty($date_range)) {
                         $html .= ' (' . $date_range . ')';
                     }
                }
                $html .= '</p>';
                // Display job description as bullet points if multiple lines
                if (!empty($work['job_description'])) {
                    $desc_points = array_map('trim', explode("\n", trim($work['job_description'])));
                    $desc_points = array_filter($desc_points); // Remove empty lines
                    if(count($desc_points) > 1){
                        $html .= '<ul class="description-points">';
                        foreach($desc_points as $point){
                           $html .= '<li>' . htmlspecialchars($point) . '</li>';
                        }
                        $html .= '</ul>';
                    } else if (count($desc_points) === 1 && !empty($desc_points[0])) {
                         // If only one line, display as a paragraph
                         $html .= '<p class="description">' . nl2br(htmlspecialchars($desc_points[0])) . '</p>';
                    }
                }
                $html .= '</div>';
            }
        } else { $html .= '<p class="no-data">Nuk ka eksperiencë pune të shtuar.</p>'; }
        $html .= '</div>';
        $html .= '</div>'; // End Right Column
        $html .= '</div>'; // End Professional Layout
         $html .= '</div>'; // End cv-preview-content
        return $html;
    }
}
?>
