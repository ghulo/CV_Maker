<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AIController extends Controller
{
    public function getSkillSuggestions(Request $request): JsonResponse
    {
        $jobTitle = strtolower($request->input('job_title', 'professional'));
        
        $skillDatabase = [
            'software engineer' => ['JavaScript', 'Python', 'React', 'Node.js', 'Git', 'SQL', 'TypeScript', 'Docker', 'AWS'],
            'product designer' => ['Figma', 'Adobe Creative Suite', 'User Research', 'Prototyping', 'UI/UX Design'],
            'marketing manager' => ['Digital Marketing', 'Google Analytics', 'SEO/SEM', 'Content Strategy', 'Social Media'],
            'data scientist' => ['Python', 'R', 'SQL', 'Machine Learning', 'Statistics', 'TensorFlow'],
            'project manager' => ['Project Planning', 'Agile/Scrum', 'Risk Management', 'Jira', 'Leadership']
        ];

        $skills = $skillDatabase[$jobTitle] ?? ['Communication', 'Problem Solving', 'Team Collaboration', 'Leadership'];

        return response()->json([
            'success' => true,
            'skills' => $skills,
            'job_title' => $jobTitle
        ]);
    }

    public function generateSummary(Request $request): JsonResponse
    {
        $jobTitle = $request->input('job_title', 'professional');
        $experienceYears = $request->input('experience_years', 0);

        $templates = [
            'experienced' => "Experienced {$jobTitle} with {$experienceYears}+ years of expertise in developing innovative solutions and leading high-impact projects.",
            'entry_level' => "Recent graduate and aspiring {$jobTitle} passionate about applying theoretical knowledge to real-world challenges.",
            'senior' => "Senior {$jobTitle} with {$experienceYears}+ years of leadership experience in architecting scalable solutions."
        ];

        $templateKey = $experienceYears >= 8 ? 'senior' : ($experienceYears >= 3 ? 'experienced' : 'entry_level');
        
        return response()->json([
            'success' => true,
            'summary' => $templates[$templateKey]
        ]);
    }

    public function analyzeCv(Request $request): JsonResponse
    {
        $cvData = $request->input('cv_data', []);
        $score = 0;
        $suggestions = [];
        $issues = [];

        // Analyze summary (20 points)
        $summary = $cvData['summary'] ?? '';
        if (strlen($summary) >= 50) {
            $score += 20;
        } elseif (strlen($summary) >= 20) {
            $score += 10;
        }

        // Analyze experience (30 points)
        $experience = $cvData['experience'] ?? [];
        $score += min(count($experience) * 10, 30);

        // Analyze education (15 points)
        $education = $cvData['education'] ?? [];
        if (count($education) >= 1) $score += 15;

        // Analyze skills (20 points)
        $skills = $cvData['skills'] ?? [];
        $score += min(count($skills) * 2.5, 20);

        // Add suggestions
        if (count($skills) < 5) {
            $suggestions[] = [
                'type' => 'suggestion',
                'title' => 'Add More Skills',
                'description' => 'CVs with more skills get better visibility.'
            ];
        }

        return response()->json([
            'success' => true,
            'analysis' => [
                'score' => round($score),
                'suggestions' => $suggestions,
                'issues' => $issues
            ]
        ]);
    }
} 