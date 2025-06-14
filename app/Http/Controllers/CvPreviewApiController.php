<?php
// app/Http/Controllers/CvPreviewApiController.php (Replace the content of this file with this)

namespace App\Http\Controllers;

use App\Models\Cv;
use App\Models\WorkExperience;
use App\Models\Education;
use App\Models\Interest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response; // For returning HTML response
use Barryvdh\DomPDF\Facade\Pdf;

class CvPreviewApiController extends Controller
{
    /**
     * Display the specified CV's preview HTML.
     */
    public function show(Cv $cv) // Route Model Binding fetches the Cv
    {
        // Ensure the authenticated user owns this CV
        if (Auth::id() !== $cv->user_id) {
            // For a web route, returning a view with an error is more user-friendly
            // than a JSON response. Or redirect with an error message.
            abort(403, 'Unauthorized access to CV preview.');
        }

        // Load related data using Eloquent relationships
        $workExperiences = $cv->workExperiences()->orderByDesc('start_date')->orderByDesc('id')->get();
        $educations = $cv->educations()->orderByDesc('graduation_year')->orderByDesc('id')->get();
        $interest = $cv->interest; // HasOne relationship, so it's a single model or null
        $interestsText = $interest ? $interest->description : '';

        // Pass data to the full preview Blade view
        return view('cv.full-preview', [
            'cv_details' => $cv,
            'work_experiences' => $workExperiences,
            'educations' => $educations,
            'interests_text' => $interestsText,
            'selected_template' => $cv->selected_template, // This is crucial for template selection
        ]);
    }

    /**
     * Download the CV as PDF.
     */
    public function downloadPdf(Cv $cv)
    {
        $pdf = PDF::loadView('cv.pdf', compact('cv'));
        
        // Set paper size and orientation
        $pdf->setPaper('a4', 'portrait');
        
        // Generate a filename
        $filename = str_slug($cv->full_name . '-cv-' . now()->format('Y-m-d')) . '.pdf';
        
        // Return the PDF for download
        return $pdf->download($filename);
    }
}
