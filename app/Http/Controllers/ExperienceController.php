<?php
// app/Http/Controllers/ExperienceController.php (Replace the content of this file with this)

namespace App\Http\Controllers;

use App\Models\Cv;
use App\Models\WorkExperience;
use App\Models\Education;
use App\Models\Skill;
use App\Models\Interest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB; // For database transactions

class ExperienceController extends Controller
{
    /**
     * Show the form for adding/editing experience, education, and skills.
     */
    public function create(Request $request)
    {
        $userId = Auth::id();
        $cvId = Session::get('cv_id');
        $selectedTemplate = Session::get('selected_template', 'classic');

        // If no CV ID in session, redirect back to personal info form or CV list
        if (!$cvId) {
            return redirect()->route('cv.create')->with('error_message', 'Ju lutemi plotësoni ose zgjidhni informacionin personal së pari.');
        }

        // Verify ownership of the CV
        $cv = Cv::where('id', $cvId)->where('user_id', $userId)->first();
        if (!$cv) {
            Session::forget('cv_id'); // Clear invalid CV ID
            Session::forget('selected_template');
            return redirect()->route('my-cvs.index')->with('error_message', 'Nuk keni leje për të modifikuar këtë CV ose CV-ja nuk ekziston.');
        }

        // Load existing data for the CV
        $existingWorkExperiences = $cv->workExperiences()->orderByDesc('start_date')->orderByDesc('id')->get();
        $existingEducations = $cv->educations()->orderByDesc('graduation_year')->orderByDesc('id')->get();
        $existingSkills = $cv->skills()->orderBy('category')->orderBy('skill_name')->get();
        $existingInterestsDescription = $cv->interest ? $cv->interest->description : '';

        // Prepare data for the view
        return view('cv.experience', [
            'cv_id' => $cvId,
            'selected_template' => $selectedTemplate,
            'header_subtitle' => 'Hapi 2: Eksperienca & Aftësitë (Modeli: ' . ucfirst($selectedTemplate) . ')',
            'existing_work_experiences' => $existingWorkExperiences,
            'existing_educations' => $existingEducations,
            'existing_skills' => $existingSkills,
            'existing_interests_description' => $existingInterestsDescription,
            'current_page' => 'experience.create',
        ]);
    }

    /**
     * Store or update experience, education, and skills data.
     */
    public function store(Request $request)
    {
        $userId = Auth::id();
        $cvId = $request->input('cv_id');

        // Validate CV ID from request and ownership
        $cv = Cv::where('id', $cvId)->where('user_id', $userId)->first();
        if (!$cv) {
            return redirect()->route('my-cvs.index')->with('error_message', 'Nuk keni leje për të modifikuar këtë CV ose CV-ja nuk ekziston.');
        }

        // Validation rules for new entries
        $request->validate([
            // Work Experience
            'new_job_title' => 'nullable|string|max:255',
            'new_company' => 'nullable|string|max:255',
            'new_work_city_country' => 'nullable|string|max:255',
            'new_start_date' => 'nullable|date',
            'new_end_date' => 'nullable|date|after_or_equal:new_start_date|required_if:new_is_current_job,null', // Only required if not current job
            'new_is_current_job' => 'nullable|boolean',
            'new_job_description' => 'nullable|string',

            // Education
            'new_school' => 'nullable|string|max:255',
            'new_degree' => 'nullable|string|max:255',
            'new_field_of_study' => 'nullable|string|max:255',
            'new_edu_city_country' => 'nullable|string|max:255',
            'new_graduation_year' => 'nullable|string|max:20', // Can be year or 'Në vazhdim'
            'new_edu_description' => 'nullable|string',

            // Skill
            'new_skill_name' => 'nullable|string|max:255',
            'new_skill_level' => 'nullable|string|max:100',
            'new_skill_category' => 'nullable|string|max:100',

            // Interests
            'interests_description' => 'nullable|string',
        ]);

        // Use a database transaction for atomicity
        DB::beginTransaction();

        try {
            // Process New Work Experience
            if ($request->filled('new_job_title') || $request->filled('new_company')) {
                WorkExperience::create([
                    'cv_id' => $cvId,
                    'job_title' => $request->input('new_job_title'),
                    'company' => $request->input('new_company'),
                    'city_country' => $request->input('new_work_city_country'),
                    'start_date' => $request->input('new_start_date'),
                    'end_date' => $request->boolean('new_is_current_job') ? null : $request->input('new_end_date'),
                    'is_current_job' => $request->boolean('new_is_current_job'),
                    'job_description' => $request->input('new_job_description'),
                ]);
            }

            // Process New Education
            if ($request->filled('new_school') || $request->filled('new_degree')) {
                Education::create([
                    'cv_id' => $cvId,
                    'school' => $request->input('new_school'),
                    'degree' => $request->input('new_degree'),
                    'field_of_study' => $request->input('new_field_of_study'),
                    'city_country' => $request->input('new_edu_city_country'),
                    'graduation_year' => $request->input('new_graduation_year'),
                    'edu_description' => $request->input('new_edu_description'),
                ]);
            }

            // Process New Skill
            if ($request->filled('new_skill_name')) {
                Skill::create([
                    'cv_id' => $cvId,
                    'skill_name' => $request->input('new_skill_name'),
                    'skill_level' => $request->input('new_skill_level'),
                    'category' => $request->input('new_skill_category'),
                ]);
            }

            // Update/Insert Interests
            if ($request->filled('interests_description')) {
                Interest::updateOrCreate(
                    ['cv_id' => $cvId], // Find by cv_id
                    ['description' => $request->input('interests_description')] // Update or create with this description
                );
            } else {
                // If interests_description is empty, delete any existing interest for this CV
                Interest::where('cv_id', $cvId)->delete();
            }

            // Update the 'updated_at' timestamp in the main 'cvs' table
            $cv->touch(); // Eloquent's touch() method updates the updated_at timestamp

            DB::commit(); // Commit the transaction

            return redirect()->route('cv.preview', $cvId)->with('success_message', 'Të dhënat e eksperiencës, edukimit dhe aftësive u ruajtën/shtuan me sukses!');

        } catch (\Exception $e) {
            DB::rollBack(); // Rollback on error
            // Log the error
            \Log::error("Error saving experience data for CV ID {$cvId}: " . $e->getMessage());
            return redirect()->back()->withInput()->with('error_message', 'Gabim serveri gjatë ruajtjes së detajeve. Ju lutemi provoni përsëri.');
        }
    }
}
