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
     * Display the specified CV's preview data as JSON for Vue.js SPA.
     */
    public function show(Cv $cv) // Route Model Binding fetches the Cv
    {
        // Ensure the authenticated user owns this CV
        if (Auth::id() !== $cv->user_id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to CV preview.'
            ], 403);
        }

        // Load related data using Eloquent relationships
        $workExperiences = $cv->workExperiences()->orderByDesc('start_date')->orderByDesc('id')->get();
        $educations = $cv->education()->orderByDesc('end_date')->orderByDesc('id')->get();
        $skills = $cv->skills()->get();
        $interests = $cv->interests()->get();

        // Transform CV data to match frontend expectations
        $cvData = [
            'id' => $cv->id,
            'title' => $cv->cv_title,
            'selectedTemplate' => $cv->selected_template,
            'personalInfo' => [
                'firstName' => $cv->emri,
                'lastName' => $cv->mbiemri,
                'email' => $cv->email,
                'phone' => $cv->telefoni,
                'address' => $cv->address,
            ],
            'summary' => $cv->summary,
            'workExperiences' => $workExperiences->map(function ($exp) {
                return [
                    'job_title' => $exp->job_title,
                    'company' => $exp->company,
                    'start_date' => $exp->start_date,
                    'end_date' => $exp->end_date,
                    'description' => $exp->job_description,
                ];
            }),
            'educations' => $educations->map(function ($edu) {
                return [
                    'degree' => $edu->degree,
                    'institution' => $edu->institution,
                    'start_date' => $edu->start_date,
                    'end_date' => $edu->end_date,
                ];
            }),
            'skills' => $skills->map(function ($skill) {
                return [
                    'name' => $skill->name,
                    'level' => $skill->level ?? 'Intermediate',
                ];
            }),
            'interests' => $interests->map(function ($interest) {
                return [
                    'name' => $interest->description,
                ];
            }),
        ];

        return response()->json([
            'success' => true,
            'cv' => $cvData
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
