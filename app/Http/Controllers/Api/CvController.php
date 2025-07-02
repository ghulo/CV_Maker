<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cv;
use App\Models\UserActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class CvController extends Controller
{
    /**
     * Get all CVs for the authenticated user (index method for dashboard).
     */
    public function index(Request $request)
    {
        return $this->userCvs($request);
    }

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
        // Log incoming request data for debugging
        \Log::info('CV Store Request Data:', $request->all());
        
        // More lenient validation for drafts
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'personalDetails.firstName' => 'required|string|max:255',
            'personalDetails.lastName' => 'nullable|string|max:255',
            'personalDetails.email' => 'required|email|max:255',
            'personalDetails.phone' => 'nullable|string|max:255',
            'personalDetails.address' => 'nullable|string|max:500',
            'personalDetails.dateOfBirth' => 'nullable|date',
            'personalDetails.placeOfBirth' => 'nullable|string|max:255',
            'personalDetails.gender' => 'nullable|string|in:male,female,other,prefer_not_to_say',
            'personalDetails.nationality' => 'nullable|string|max:255',
            'personalDetails.zipCode' => 'nullable|string|max:20',
            'personalDetails.maritalStatus' => 'nullable|string|in:single,married,divorced,widowed',
            'personalDetails.drivingLicense' => 'nullable|string|max:255',
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

            // Log activity
            UserActivity::log(
                Auth::id(),
                UserActivity::TYPE_CV_CREATED,
                'CV Created',
                'Created new CV: "' . $cv->cv_title . '"',
                [
                    'cv_id' => $cv->id,
                    'cv_title' => $cv->cv_title,
                    'template' => $cv->selected_template,
                    'sections' => [
                        'experience_count' => count($cvData['experience']),
                        'education_count' => count($cvData['education']),
                        'skills_count' => count($cvData['skills']),
                        'interests_count' => count($cvData['interests'])
                    ]
                ]
            );

            $cv->load(['workExperiences', 'education', 'skills', 'interests']);
            return response()->json([
                'success' => true, 
                'message' => 'CV created successfully', 
                'cv' => $this->transformCvToApi($cv),
                'id' => $cv->id // Add ID at root level for easier access
            ], 201);
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

        // Log activity (only log every 5th view to avoid spam)
        if ($cv->views % 5 === 0) {
            UserActivity::log(
                Auth::id(),
                UserActivity::TYPE_CV_PREVIEWED,
                'CV Previewed',
                'Viewed CV: "' . $cv->cv_title . '"',
                [
                    'cv_id' => $cv->id,
                    'cv_title' => $cv->cv_title,
                    'total_views' => $cv->views
                ]
            );
        }

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
            'personalDetails.dateOfBirth' => 'nullable|date',
            'personalDetails.placeOfBirth' => 'nullable|string|max:255',
            'personalDetails.gender' => 'nullable|string|in:male,female,other,prefer_not_to_say',
            'personalDetails.nationality' => 'nullable|string|max:255',
            'personalDetails.zipCode' => 'nullable|string|max:20',
            'personalDetails.maritalStatus' => 'nullable|string|in:single,married,divorced,widowed',
            'personalDetails.drivingLicense' => 'nullable|string|max:255',
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

            // Log activity
            UserActivity::log(
                Auth::id(),
                UserActivity::TYPE_CV_UPDATED,
                'CV Updated',
                'Updated CV: "' . $cv->cv_title . '"',
                [
                    'cv_id' => $cv->id,
                    'cv_title' => $cv->cv_title,
                    'template' => $cv->selected_template,
                    'sections' => [
                        'experience_count' => count($cvData['experience']),
                        'education_count' => count($cvData['education']),
                        'skills_count' => count($cvData['skills']),
                        'interests_count' => count($cvData['interests'])
                    ]
                ]
            );
            
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

            // Log activity
            UserActivity::log(
                Auth::id(),
                UserActivity::TYPE_CV_CREATED,
                'CV Duplicated',
                'Duplicated CV: "' . $originalCv->cv_title . '" as "' . $newCv->cv_title . '"',
                [
                    'original_cv_id' => $originalCv->id,
                    'new_cv_id' => $newCv->id,
                    'original_title' => $originalCv->cv_title,
                    'new_title' => $newCv->cv_title,
                    'template' => $newCv->selected_template
                ]
            );

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

        $cvTitle = $cv->cv_title;
        $cvId = $cv->id;

        $cv->delete();

        // Log activity
        UserActivity::log(
            Auth::id(),
            UserActivity::TYPE_CV_DELETED,
            'CV Deleted',
            'Deleted CV: "' . $cvTitle . '"',
            [
                'cv_id' => $cvId,
                'cv_title' => $cvTitle
            ]
        );

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

        // Log activity
        UserActivity::log(
            Auth::id(),
            UserActivity::TYPE_CV_DOWNLOADED,
            'CV Downloaded',
            'Downloaded CV: "' . $cv->cv_title . '" as PDF',
            [
                'cv_id' => $cv->id,
                'cv_title' => $cv->cv_title,
                'style' => $style,
                'quality' => $quality,
                'total_downloads' => $cv->downloads
            ]
        );
        
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
                'dateOfBirth' => $cv->date_of_birth,
                'placeOfBirth' => $cv->place_of_birth,
                'gender' => $cv->gender,
                'nationality' => $cv->nationality,
                'zipCode' => $cv->zip_code,
                'maritalStatus' => $cv->marital_status,
                'drivingLicense' => $cv->driving_license,
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
        // Enhanced data extraction with fallbacks
        $personalDetails = $requestData['personalDetails'] ?? [];
        
        // Log transformation data for debugging
        \Log::info('Transforming CV data:', [
            'personalDetails' => $personalDetails,
            'title' => $requestData['title'] ?? 'NO_TITLE',
            'summary' => $requestData['summary'] ?? 'NO_SUMMARY'
        ]);
        
        $cvData = [
            'cv' => [
                'user_id' => Auth::id(),
                'emri' => trim($personalDetails['firstName'] ?? ''),
                'mbiemri' => trim($personalDetails['lastName'] ?? ''),
                'email' => trim($personalDetails['email'] ?? ''),
                'telefoni' => trim($personalDetails['phone'] ?? ''),
                'address' => trim($personalDetails['address'] ?? ''),
                'date_of_birth' => $this->formatDate($personalDetails['dateOfBirth'] ?? null),
                'place_of_birth' => trim($personalDetails['placeOfBirth'] ?? ''),
                'gender' => trim($personalDetails['gender'] ?? ''),
                'nationality' => trim($personalDetails['nationality'] ?? ''),
                'zip_code' => trim($personalDetails['zipCode'] ?? ''),
                'marital_status' => trim($personalDetails['maritalStatus'] ?? ''),
                'driving_license' => trim($personalDetails['drivingLicense'] ?? ''),
                'summary' => trim($requestData['summary'] ?? ''),
                'cv_title' => trim($requestData['title'] ?? ''),
                'selected_template' => $requestData['selectedTemplate'] ?? 'modern',
                'is_finalized' => (bool)($requestData['isFinalized'] ?? false),
            ],
            'experience' => [],
            'education' => [],
            'skills' => [],
            'interests' => [],
        ];
        
        // Process enhanced experience data safely
        if (isset($requestData['experience']) && is_array($requestData['experience'])) {
            foreach ($requestData['experience'] as $exp) {
                if (is_array($exp) && !empty($exp['title']) && !empty($exp['company'])) {
                    $cvData['experience'][] = [
                        'job_title' => trim($exp['title']),
                        'company' => trim($exp['company']),
                        'city_country' => trim($exp['cityCountry'] ?? ''),
                        'start_date' => $this->formatDate($exp['startDate'] ?? null),
                        'end_date' => $this->formatDate($exp['endDate'] ?? null),
                        'is_current_job' => (bool)($exp['current'] ?? $exp['isCurrentJob'] ?? false),
                        'job_description' => trim($exp['description'] ?? ''),
                    ];
                }
            }
        }
        
        // Process enhanced education data safely
        if (isset($requestData['education']) && is_array($requestData['education'])) {
            foreach ($requestData['education'] as $edu) {
                if (is_array($edu) && !empty($edu['degree']) && !empty($edu['university'])) {
                    $cvData['education'][] = [
                        'institution' => trim($edu['university']),
                        'degree' => trim($edu['degree']),
                        'field_of_study' => trim($edu['fieldOfStudy'] ?? ''),
                        'location' => trim($edu['location'] ?? ''),
                        'start_date' => $this->formatDate($edu['startDate'] ?? null),
                        'end_date' => $this->formatDate($edu['endDate'] ?? null),
                        'is_current' => (bool)($edu['isCurrent'] ?? false),
                        'description' => trim($edu['description'] ?? ''),
                    ];
                }
            }
        }
        
        // Process enhanced skills data safely
        if (isset($requestData['skills']) && is_array($requestData['skills'])) {
            foreach ($requestData['skills'] as $skill) {
                if (is_array($skill) && !empty($skill['name'])) {
                    $cvData['skills'][] = [
                        'name' => trim($skill['name']),
                        'level' => isset($skill['level']) ? (int)$skill['level'] : 3,
                        'category' => trim($skill['category'] ?? ''),
                        'years_of_experience' => isset($skill['yearsOfExperience']) ? (float)$skill['yearsOfExperience'] : null,
                    ];
                }
            }
        }
        
        // Process interests data safely
        if (isset($requestData['interests']) && is_array($requestData['interests'])) {
            foreach ($requestData['interests'] as $interest) {
                if (is_array($interest) && !empty($interest['name'])) {
                    $cvData['interests'][] = [
                        'description' => trim($interest['name']),
                    ];
                }
            }
        }
        
        \Log::info('Transformed CV data:', $cvData);
        return $cvData;
    }
    
    /**
     * Format date for database storage
     */
    private function formatDate($date)
    {
        if (empty($date)) return null;
        
        try {
            // Handle different date formats
            if (preg_match('/^\d{4}-\d{2}$/', $date)) {
                return $date . '-01'; // Add day for YYYY-MM format
            }
            if (preg_match('/^\d{4}$/', $date)) {
                return $date . '-01-01'; // Add month and day for YYYY format
            }
            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
                return $date; // Already in correct format
            }
            return null;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Debug endpoint to test data transformation
     */
    public function debugStore(Request $request)
    {
        \Log::info('=== CV DEBUG STORE ===');
        \Log::info('Raw Request:', $request->all());
        
        try {
            $transformedData = $this->transformCvFromRequest($request->all());
            \Log::info('Transformed Data:', $transformedData);
            
            return response()->json([
                'success' => true,
                'raw_data' => $request->all(),
                'transformed_data' => $transformedData,
                'message' => 'Debug data transformation successful'
            ]);
        } catch (\Exception $e) {
            \Log::error('Debug transformation failed:', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }
}
