<?php

namespace App\Services;

use App\Models\CV;
use App\Models\Education;
use App\Models\WorkExperience;
use App\Models\Skill;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class CvService
{
    /**
     * Create a new CV.
     *
     * @param array $data
     * @return CV
     */
    public function create(array $data)
    {
        return DB::transaction(function () use ($data) {
            $data['user_id'] = Auth::id();
            return CV::create($data);
        });
    }

    /**
     * Update an existing CV.
     *
     * @param CV $cv
     * @param array $data
     * @return CV
     */
    public function update(CV $cv, array $data)
    {
        return DB::transaction(function () use ($cv, $data) {
            $cv->update($data);
            return $cv;
        });
    }

    /**
     * Delete a CV and all related data.
     *
     * @param CV $cv
     * @return bool|null
     */
    public function delete(CV $cv)
    {
        return DB::transaction(function () use ($cv) {
            return $cv->delete();
        });
    }

    /**
     * Add education to a CV.
     *
     * @param CV $cv
     * @param array $data
     * @return \App\Models\Education
     */
    public function addEducation(CV $cv, array $data)
    {
        return $cv->education()->create($data);
    }

    /**
     * Add experience to a CV.
     *
     * @param CV $cv
     * @param array $data
     * @return \App\Models\Experience
     */
    public function addExperience(CV $cv, array $data)
    {
        return $cv->experience()->create($data);
    }

    /**
     * Add skill to a CV.
     *
     * @param CV $cv
     * @param array $data
     * @return \App\Models\Skill
     */
    public function addSkill(CV $cv, array $data)
    {
        return $cv->skills()->create($data);
    }

    /**
     * Generate PDF version of the CV.
     *
     * @param CV $cv
     * @return string Path to generated PDF
     */
    public function generatePdf(CV $cv)
    {
        // Generate PDF using the 'pdf.cv' Blade view
        $pdf = Pdf::loadView('pdf.cv', ['cv' => $cv]);
        $fileName = 'cv-' . str_replace(' ', '-', $cv->emri . '-' . $cv->mbiemri) . '.pdf';
        return $pdf->download($fileName);
    }
} 