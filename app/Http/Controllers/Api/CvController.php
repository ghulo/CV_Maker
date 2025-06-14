<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cv;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class CvController extends Controller
{
    /**
     * Get all CVs for the authenticated user.
     */
    public function userCvs(Request $request)
    {
        $cvs = Auth::user()->cvs()->latest()->get();
        return response()->json(['success' => true, 'cvs' => $cvs]);
    }

    /**
     * Store a newly created CV in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'personal_details.firstName' => 'required|string|max:255',
            'personal_details.lastName' => 'nullable|string|max:255',
            'personal_details.email' => 'required|email|max:255',
            'personal_details.phone' => 'nullable|string|max:255',
            'personal_details.address' => 'nullable|string|max:255',
            'summary' => 'required|string',
            'experience' => 'present|array',
            'education' => 'present|array',
            'skills' => 'present|array',
            'selected_template' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $validatedData = $validator->validated();
        
        DB::beginTransaction();
        try {
            $cv = Cv::create([
                'user_id' => Auth::id(),
                'emri' => $validatedData['personal_details']['firstName'],
                'mbiemri' => $validatedData['personal_details']['lastName'] ?? '',
                'email' => $validatedData['personal_details']['email'],
                'telefoni' => $validatedData['personal_details']['phone'] ?? null,
                'address' => $validatedData['personal_details']['address'] ?? null,
                'summary' => $validatedData['summary'],
                'cv_title' => $validatedData['title'],
                'selected_template' => $validatedData['selected_template'],
            ]);

            foreach ($validatedData['experience'] as $exp) {
                $cv->workExperiences()->create($exp);
            }
            foreach ($validatedData['education'] as $edu) {
                $cv->education()->create($edu);
            }
            foreach ($validatedData['skills'] as $skill) {
                $cv->skills()->create($skill);
            }

            DB::commit();

            return response()->json(['success' => true, 'message' => 'CV created successfully', 'cv' => $cv], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Failed to create CV: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified CV.
     */
    public function show($id)
    {
        $cv = Cv::with(['workExperiences', 'education', 'skills'])->find($id);

        if (!$cv || $cv->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'CV not found'], 404);
        }
        
        // Re-shape data to match frontend expectations
        $cv_data = [
            'id' => $cv->id,
            'title' => $cv->cv_title,
            'personal_details' => [
                'firstName' => $cv->emri,
                'lastName' => $cv->mbiemri,
                'email' => $cv->email,
                'phone' => $cv->telefoni,
                'address' => $cv->address,
            ],
            'summary' => $cv->summary,
            'experience' => $cv->workExperiences,
            'education' => $cv->education,
            'skills' => $cv->skills,
            'selected_template' => $cv->selected_template,
        ];

        return response()->json(['success' => true, 'cv' => $cv_data]);
    }

    /**
     * Update the specified CV in storage.
     */
    public function update(Request $request, $id)
    {
        $cv = Cv::find($id);

        if (!$cv || $cv->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'CV not found or unauthorized'], 404);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'personal_details.firstName' => 'required|string|max:255',
            'personal_details.lastName' => 'nullable|string|max:255',
            'personal_details.email' => 'required|email|max:255',
            'personal_details.phone' => 'nullable|string|max:255',
            'personal_details.address' => 'nullable|string|max:255',
            'summary' => 'required|string',
            'experience' => 'present|array',
            'education' => 'present|array',
            'skills' => 'present|array',
            'selected_template' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }
        
        $validatedData = $validator->validated();

        DB::beginTransaction();
        try {
            $cv->update([
                'emri' => $validatedData['personal_details']['firstName'],
                'mbiemri' => $validatedData['personal_details']['lastName'] ?? '',
                'email' => $validatedData['personal_details']['email'],
                'telefoni' => $validatedData['personal_details']['phone'] ?? null,
                'address' => $validatedData['personal_details']['address'] ?? null,
                'summary' => $validatedData['summary'],
                'cv_title' => $validatedData['title'],
                'selected_template' => $validatedData['selected_template'],
            ]);

            // Sync relationships
            $cv->workExperiences()->delete();
            $cv->education()->delete();
            $cv->skills()->delete();

            foreach ($validatedData['experience'] as $exp) {
                $cv->workExperiences()->create($exp);
            }
            foreach ($validatedData['education'] as $edu) {
                $cv->education()->create($edu);
            }
            foreach ($validatedData['skills'] as $skill) {
                $cv->skills()->create($skill);
            }

            DB::commit();

            return response()->json(['success' => true, 'message' => 'CV updated successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Failed to update CV: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Duplicate the specified CV in storage.
     */
    public function duplicate($id)
    {
        $originalCv = Cv::with(['workExperiences', 'education', 'skills'])->find($id);

        if (!$originalCv || $originalCv->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'CV not found or unauthorized'], 404);
        }

        try {
            DB::beginTransaction();

            $newCv = $originalCv->replicate();
            $newCv->cv_title = $originalCv->cv_title . ' (Kopje)';
            $newCv->save();

            foreach ($originalCv->workExperiences as $exp) {
                $newCv->workExperiences()->create($exp->toArray());
            }

            foreach ($originalCv->education as $edu) {
                $newCv->education()->create($edu->toArray());
            }

            foreach ($originalCv->skills as $skill) {
                $newCv->skills()->create($skill->toArray());
            }

            DB::commit();

            return response()->json(['success' => true, 'message' => 'CV duplicated successfully', 'cv' => $newCv]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Failed to duplicate CV.', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified CV from storage.
     */
    public function destroy($id)
    {
        $cv = Cv::find($id);

        if (!$cv || $cv->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'CV not found or unauthorized'], 404);
        }

        $cv->delete();

        return response()->json(['success' => true, 'message' => 'CV deleted successfully']);
    }

    /**
     * Download the specified CV as a PDF.
     */
    public function download($id)
    {
        $cv = Cv::with(['workExperiences', 'education', 'skills'])->find($id);

        if (!$cv || $cv->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'CV not found'], 404);
        }

        $pdf = Pdf::loadView('pdf.cv', ['cv' => $cv]);
        $fileName = 'cv-' . str_replace(' ', '-', $cv->emri . '-' . $cv->mbiemri) . '.pdf';

        return $pdf->download($fileName);
    }
}
