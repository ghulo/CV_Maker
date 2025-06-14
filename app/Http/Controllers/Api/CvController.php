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
            'personal_details.full_name' => 'required|string|max:255',
            'personal_details.email' => 'required|email',
            'summary' => 'required|string',
            'experiences' => 'present|array',
            'educations' => 'present|array',
            'skills' => 'present|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $validatedData = $validator->validated();
        $fullName = explode(' ', $validatedData['personal_details']['full_name'], 2);

        DB::beginTransaction();
        try {
            $cv = Cv::create([
                'user_id' => Auth::id(),
                'emri' => $fullName[0],
                'mbiemri' => $fullName[1] ?? '',
                'email' => $validatedData['personal_details']['email'],
                'telefoni' => $validatedData['personal_details']['phone_number'] ?? null,
                'address' => $validatedData['personal_details']['address'] ?? null,
                'summary' => $validatedData['summary'],
                'cv_title' => 'CV of ' . $validatedData['personal_details']['full_name'],
            ]);

            foreach ($validatedData['experiences'] as $exp) {
                $cv->experience()->create($exp);
            }
            foreach ($validatedData['educations'] as $edu) {
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
        $cv = Cv::with(['experience', 'education', 'skills'])->find($id);

        if (!$cv || $cv->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'CV not found'], 404);
        }
        
        // Re-shape data to match frontend expectations
        $cv_data = [
            'id' => $cv->id,
            'personal_details' => [
                'full_name' => $cv->emri . ' ' . $cv->mbiemri,
                'email' => $cv->email,
                'phone_number' => $cv->telefoni,
                'address' => $cv->address,
            ],
            'summary' => $cv->summary,
            'experiences' => $cv->experience,
            'educations' => $cv->education,
            'skills' => $cv->skills,
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
            'personal_details.full_name' => 'required|string|max:255',
            'summary' => 'required|string',
            'experiences' => 'present|array',
            'educations' => 'present|array',
            'skills' => 'present|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }
        
        $validatedData = $validator->validated();
        $fullName = explode(' ', $validatedData['personal_details']['full_name'], 2);

        DB::beginTransaction();
        try {
            $cv->update([
                'emri' => $fullName[0],
                'mbiemri' => $fullName[1] ?? '',
                'email' => $validatedData['personal_details']['email'],
                'telefoni' => $validatedData['personal_details']['phone_number'] ?? null,
                'address' => $validatedData['personal_details']['address'] ?? null,
                'summary' => $validatedData['summary'],
            ]);

            // Sync relationships
            $cv->experience()->delete();
            $cv->education()->delete();
            $cv->skills()->delete();

            foreach ($validatedData['experiences'] as $exp) {
                $cv->experience()->create($exp);
            }
            foreach ($validatedData['educations'] as $edu) {
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
        $originalCv = Cv::with(['experience', 'education', 'skills'])->find($id);

        if (!$originalCv || $originalCv->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'CV not found or unauthorized'], 404);
        }

        try {
            DB::beginTransaction();

            $newCv = $originalCv->replicate(['template_id']); // template_id might not be set
            $newCv->emri = $originalCv->emri . ' (Kopje)';
            $newCv->save();

            foreach ($originalCv->experience as $exp) {
                $newCv->experience()->create($exp->toArray());
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
        $cv = Cv::with(['experience', 'education', 'skills'])->find($id);

        if (!$cv || $cv->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'CV not found'], 404);
        }

        $pdf = Pdf::loadView('pdf.cv', ['cv' => $cv]);
        $fileName = 'cv-' . str_replace(' ', '-', $cv->emri . '-' . $cv->mbiemri) . '.pdf';

        return $pdf->download($fileName);
    }
}
