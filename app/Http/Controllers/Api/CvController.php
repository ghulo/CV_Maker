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
        try {
            $cvs = Auth::user()->cvs()->with(['workExperiences', 'education', 'skills', 'interests'])->latest()->get();

            $formattedCvs = $cvs->map(fn($cv) => $this->transformCvToApi($cv));

            return response()->json(['success' => true, 'cvs' => $formattedCvs]);
        } catch (\Exception $e) {
            \Log::error('Error in userCvs: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching CVs',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created CV in storage.
     */
    public function store(Request $request)
    {
        // More lenient validation for drafts
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'personalDetails.firstName' => 'required|string|max:255',
            'personalDetails.lastName' => 'nullable|string|max:255',
            'personalDetails.email' => 'required|email|max:255',
            'personalDetails.phone' => 'nullable|string|max:255',
            'personalDetails.address' => 'nullable|string|max:500',
            'summary' => 'nullable|string|max:2000',
            'experience' => 'present|array',
            'education' => 'present|array',
            'skills' => 'present|array',
            'interests' => 'present|array',
            'selectedTemplate' => 'required|string|in:modern,classic,professional,creative',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $validatedData = $validator->validated();

        DB::beginTransaction();
        try {
            $cvData = $this->transformCvFromRequest($validatedData);
            $cv = Cv::create($cvData['cv']);

            foreach ($cvData['experience'] as $exp) {
                $cv->workExperiences()->create($exp);
            }
            foreach ($cvData['education'] as $edu) {
                $cv->education()->create($edu);
            }
            foreach ($cvData['skills'] as $skill) {
                $cv->skills()->create($skill);
            }
            foreach ($cvData['interests'] as $interest) {
                $cv->interests()->create($interest);
            }

            DB::commit();

            $cv->load(['workExperiences', 'education', 'skills', 'interests']);
            return response()->json(['success' => true, 'message' => 'CV created successfully', 'cv' => $this->transformCvToApi($cv)], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Failed to create CV: ' . $e->getMessage() . '\n' . $e->getTraceAsString());
            return response()->json(['success' => false, 'message' => 'Failed to create CV: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified CV.
     */
    public function show($id)
    {
        $cv = Cv::with(['workExperiences', 'education', 'skills', 'interests'])->find($id);

        if (!$cv || $cv->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'CV not found'], 404);
        }

        // Increment view count
        $cv->increment('views');

        return response()->json(['success' => true, 'cv' => $this->transformCvToApi($cv)]);
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
            'personalDetails.firstName' => 'required|string|max:255',
            'personalDetails.lastName' => 'nullable|string|max:255',
            'personalDetails.email' => 'required|email|max:255',
            'personalDetails.phone' => 'nullable|string|max:255',
            'personalDetails.address' => 'nullable|string|max:500',
            'summary' => 'nullable|string|max:2000',
            'experience' => 'present|array',
            'education' => 'present|array',
            'skills' => 'present|array',
            'interests' => 'present|array',
            'selectedTemplate' => 'required|string|in:modern,classic,professional,creative',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $validatedData = $validator->validated();

        DB::beginTransaction();
        try {
            $cvData = $this->transformCvFromRequest($validatedData);
            $cv->update($cvData['cv']);

            // Sync relationships
            $cv->workExperiences()->delete();
            $cv->education()->delete();
            $cv->skills()->delete();
            $cv->interests()->delete();

            foreach ($cvData['experience'] as $exp) {
                $cv->workExperiences()->create($exp);
            }
            foreach ($cvData['education'] as $edu) {
                $cv->education()->create($edu);
            }
            foreach ($cvData['skills'] as $skill) {
                $cv->skills()->create($skill);
            }
            foreach ($cvData['interests'] as $interest) {
                $cv->interests()->create($interest);
            }

            DB::commit();
            
            $cv->load(['workExperiences', 'education', 'skills', 'interests']);
            return response()->json(['success' => true, 'message' => 'CV updated successfully', 'cv' => $this->transformCvToApi($cv)]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Failed to update CV: ' . $e->getMessage() . '\n' . $e->getTraceAsString());
            return response()->json(['success' => false, 'message' => 'Failed to update CV: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Duplicate the specified CV in storage.
     */
    public function duplicate($id)
    {
        $originalCv = Cv::with(['workExperiences', 'education', 'skills', 'interests'])->find($id);

        if (!$originalCv || $originalCv->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'CV not found or unauthorized'], 404);
        }

        DB::beginTransaction();
        try {
            $newCv = $originalCv->replicate(['views', 'downloads']);
            $newCv->cv_title = $originalCv->cv_title . ' (Kopje)';
            $newCv->created_at = now();
            $newCv->updated_at = now();
            $newCv->views = 0;
            $newCv->downloads = 0;
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
            foreach ($originalCv->interests as $interest) {
                $newCv->interests()->create($interest->toArray());
            }

            DB::commit();

            $newCv->load(['workExperiences', 'education', 'skills', 'interests']);

            return response()->json([
                'success' => true,
                'message' => 'CV duplicated successfully',
                'cv' => $this->transformCvToApi($newCv)
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Failed to duplicate CV: ' . $e->getMessage() . '\n' . $e->getTraceAsString());
            return response()->json(['success' => false, 'message' => 'Failed to duplicate CV'], 500);
        }
    }

    /**
     * Remove the specified CV from storage.
     */
    public function destroy($id)
    {
        $cv = Cv::find($id);

        if (!$cv) {
            return response()->json(['success' => false, 'message' => 'CV not found'], 404);
        }

        if ($cv->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $cv->delete();

        return response()->json(['success' => true, 'message' => 'CV deleted successfully']);
    }

    /**
     * Download the specified CV as PDF.
     */
    public function download(Request $request, $id)
    {
        $cv = Cv::with(['workExperiences', 'education', 'skills', 'interests'])->find($id);

        if (!$cv || $cv->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'CV not found'], 404);
        }
        
        // Get style and quality parameters
        $style = $request->get('style', 'default');
        $quality = $request->get('quality', 'high');
        
        // Increment download count
        $cv->increment('downloads');
        
        $pdf = Pdf::loadView('pdf.cv', ['cv' => $cv, 'style' => $style, 'quality' => $quality]);
        
        // Set PDF options based on quality
        if ($quality === 'high') {
            $pdf->setPaper('A4')->setOptions(['dpi' => 150, 'defaultFont' => 'sans-serif']);
        } elseif ($quality === 'medium') {
            $pdf->setPaper('A4')->setOptions(['dpi' => 100, 'defaultFont' => 'sans-serif']);
        } else {
            $pdf->setPaper('A4')->setOptions(['dpi' => 72, 'defaultFont' => 'sans-serif']);
        }
        
        $fileName = 'cv-' . str_replace(' ', '-', strtolower($cv->emri . '-' . $cv->mbiemri)) . '-' . $style . '.pdf';

        return $pdf->download($fileName);
    }
    
    /**
     * Transforms a CV model into a consistent API response structure.
     */
    private function transformCvToApi(Cv $cv): array
    {
        return [
            'id' => $cv->id,
            'title' => $cv->cv_title,
            'personalDetails' => [
                'firstName' => $cv->emri,
                'lastName' => $cv->mbiemri,
                'fullName' => trim($cv->emri . ' ' . $cv->mbiemri),
                'email' => $cv->email,
                'phone' => $cv->telefoni,
                'address' => $cv->address,
            ],
            'summary' => $cv->summary,
            'experience' => $cv->workExperiences->map(function($exp) {
                return [
                    'id' => $exp->id,
                    'title' => $exp->job_title,
                    'company' => $exp->company,
                    'startDate' => $exp->start_date,
                    'endDate' => $exp->end_date,
                    'description' => $exp->job_description,
                ];
            }),
            'education' => $cv->education->map(function($edu) {
                return [
                    'id' => $edu->id,
                    'degree' => $edu->degree,
                    'university' => $edu->institution,
                    'startDate' => $edu->start_date,
                    'endDate' => $edu->end_date,
                ];
            }),
            'skills' => $cv->skills->map(function($skill) {
                return [
                    'id' => $skill->id,
                    'name' => $skill->name,
                    'level' => $skill->level,
                ];
            }),
            'interests' => $cv->interests->map(function($interest) {
                return [
                    'id' => $interest->id,
                    'name' => $interest->description,
                ];
            }),
            'selectedTemplate' => $cv->selected_template,
            'createdAt' => $cv->created_at,
            'updatedAt' => $cv->updated_at,
            'isFinalized' => $cv->is_finalized,
            'views' => $cv->views ?? 0,
            'downloads' => $cv->downloads ?? 0,
        ];
    }
    
    /**
     * Transforms request data into a structure for CV model creation/update.
     */
    private function transformCvFromRequest(array $requestData): array
    {
        return [
            'cv' => [
                'user_id' => Auth::id(),
                'emri' => $requestData['personalDetails']['firstName'],
                'mbiemri' => $requestData['personalDetails']['lastName'] ?? '',
                'email' => $requestData['personalDetails']['email'],
                'telefoni' => $requestData['personalDetails']['phone'] ?? null,
                'address' => $requestData['personalDetails']['address'] ?? null,
                'summary' => $requestData['summary'] ?? '',
                'cv_title' => $requestData['title'],
                'selected_template' => $requestData['selectedTemplate'],
                'is_finalized' => $requestData['isFinalized'] ?? false,
            ],
            'experience' => array_map(function($exp) {
                return [
                    'job_title' => $exp['title'],
                    'company' => $exp['company'],
                    'start_date' => $exp['startDate'] ?? null,
                    'end_date' => $exp['endDate'] ?? null,
                    'job_description' => $exp['description'] ?? null,
                ];
            }, $requestData['experience']),
            'education' => array_map(function($edu) {
                return [
                    'institution' => $edu['university'],
                    'degree' => $edu['degree'],
                    'start_date' => $edu['startDate'] ?? null,
                    'end_date' => $edu['endDate'] ?? null,
                ];
            }, $requestData['education']),
            'skills' => array_map(function($skill) {
                return [
                    'name' => $skill['name'],
                    'level' => $skill['level'] ?? null,
                ];
            }, $requestData['skills']),
            'interests' => array_map(function($interest) {
                return [
                    'description' => $interest['name'],
                ];
            }, $requestData['interests']),
        ];
    }
}
