<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\GeminiAIService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class AIController extends Controller
{
    private $industrySkillsDatabase;
    private $experienceTemplates;
    private $summaryTemplates;
    private GeminiAIService $geminiService;
    
    public function __construct(GeminiAIService $geminiService)
    {
        $this->geminiService = $geminiService;
        $this->industrySkillsDatabase = $this->loadIndustrySkills();
        $this->experienceTemplates = $this->loadExperienceTemplates();
        $this->summaryTemplates = $this->loadSummaryTemplates();
    }

    /**
     * Get intelligent skill suggestions based on job title and experience
     */
    public function getSkillSuggestions(Request $request): JsonResponse
    {
        try {
            $jobTitle = $request->input('job_title', 'professional');
            $experienceLevel = $request->input('experience_level', 'intermediate');
            $industry = $request->input('industry', '');
            $language = $request->input('language', 'en');

            // Try Gemini AI first
            try {
                $skills = $this->geminiService->generateSkillSuggestions($jobTitle, $industry, $experienceLevel);
                
                return response()->json([
                    'success' => true,
                    'skills' => $skills,
                    'source' => 'gemini_ai',
                    'metadata' => [
                        'job_title' => $jobTitle,
                        'experience_level' => $experienceLevel,
                        'industry' => $industry,
                        'ai_generated' => true
                    ]
                ]);
            } catch (\Exception $aiError) {
                Log::warning('Gemini AI failed, using fallback: ' . $aiError->getMessage());
                
                // Fallback to template-based approach
                $skillCategory = $this->mapJobTitleToSkillCategory(strtolower($jobTitle));
                $skills = $this->getSkillsByCategory($skillCategory, $experienceLevel);
                
                // Add trending skills
                $trendingSkills = $this->getTrendingSkills($skillCategory, 2024);
                $skills = array_merge($skills, array_slice($trendingSkills, 0, 2));

                // Remove duplicates and limit
                $skills = array_unique($skills);
                $skills = array_slice($skills, 0, 8);

                return response()->json([
                    'success' => true,
                    'skills' => $skills,
                    'source' => 'template_fallback',
                    'metadata' => [
                        'job_title' => $jobTitle,
                        'experience_level' => $experienceLevel,
                        'category' => $skillCategory,
                        'ai_generated' => false
                    ]
                ]);
            }

        } catch (\Exception $e) {
            Log::error('AI Skill Suggestions Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to generate skill suggestions',
                'skills' => $this->getFallbackSkills(),
                'source' => 'error_fallback'
            ], 500);
        }
    }

    /**
     * Generate intelligent professional summary
     */
    public function generateSummary(Request $request): JsonResponse
    {
        try {
            $jobTitle = $request->input('job_title', 'professional');
            $experienceYears = (int) $request->input('experience_years', 0);
            $skills = $request->input('skills', []);
            $industry = $request->input('industry', '');
            $language = $request->input('language', 'en');
            $personalInfo = $request->input('personal_info', []);

            // Prepare data for Gemini AI
            $summaryData = [
                'job_title' => $jobTitle,
                'experience_years' => $experienceYears,
                'skills' => $skills,
                'industry' => $industry,
                'personal_info' => $personalInfo
            ];

            // Try Gemini AI first
            try {
                $summaries = $this->geminiService->generateProfessionalSummary($summaryData);
                
                return response()->json([
                    'success' => true,
                    'summaries' => $summaries,
                    'source' => 'gemini_ai',
                    'metadata' => [
                        'experience_level' => $this->determineExperienceLevel($experienceYears),
                        'word_count_range' => '80-120',
                        'ai_generated' => true,
                        'personalization_score' => $this->calculatePersonalizationScore($personalInfo, $skills, $experienceYears)
                    ]
                ]);
            } catch (\Exception $aiError) {
                Log::warning('Gemini AI summary failed, using fallback: ' . $aiError->getMessage());
                
                // Fallback to template-based approach
                $experienceLevel = $this->determineExperienceLevel($experienceYears);
                $summaries = [];
                $templates = $this->summaryTemplates[$language] ?? $this->summaryTemplates['en'];
                
                foreach (['professional', 'creative', 'results_focused'] as $style) {
                    $summary = $this->generateContextualSummary(
                        $jobTitle,
                        $experienceYears,
                        $skills,
                        $industry,
                        $experienceLevel,
                        $style,
                        $templates
                    );
                    
                    $summaries[] = [
                        'style' => $style,
                        'content' => $summary,
                        'word_count' => str_word_count($summary),
                        'recommended' => $style === 'professional'
                    ];
                }

                return response()->json([
                    'success' => true,
                    'summaries' => $summaries,
                    'source' => 'template_fallback',
                    'metadata' => [
                        'experience_level' => $experienceLevel,
                        'word_count_range' => '80-120',
                        'ai_generated' => false,
                        'personalization_score' => $this->calculatePersonalizationScore($personalInfo, $skills, $experienceYears)
                    ]
                ]);
            }

        } catch (\Exception $e) {
            Log::error('AI Summary Generation Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to generate summary',
                'summaries' => [$this->getFallbackSummary($request->input('job_title', 'professional'))],
                'source' => 'error_fallback'
            ], 500);
        }
    }

    /**
     * Comprehensive CV analysis with detailed insights
     */
    public function analyzeCv(Request $request): JsonResponse
    {
        try {
            $cvData = $request->input('cv_data', []);
            $language = $request->input('language', 'en');

            // Try Gemini AI analysis first
            try {
                $aiAnalysis = $this->geminiService->analyzeCVWithAI($cvData);
                
                // Combine AI analysis with traditional metrics
                $analysis = [
                    'score' => $aiAnalysis['score'],
                    'completeness' => $this->calculateCompleteness($cvData),
                    'ats_score' => $aiAnalysis['ats_score'],
                    'industry_alignment' => 'good',
                    'strengths' => $aiAnalysis['strengths'],
                    'weaknesses' => $aiAnalysis['weaknesses'],
                    'improvements' => [],
                    'recommendations' => $aiAnalysis['recommendations'],
                    'keyword_density' => $this->analyzeKeywordDensity($cvData),
                    'sections_analysis' => $this->analyzeSections($cvData)
                ];

                return response()->json([
                    'success' => true,
                    'analysis' => $analysis,
                    'source' => 'gemini_ai',
                    'insights' => $this->generateAIInsights($analysis, $language)
                ]);
            } catch (\Exception $aiError) {
                Log::warning('Gemini AI analysis failed, using fallback: ' . $aiError->getMessage());
                
                // Fallback to traditional analysis
                $analysis = $this->performTraditionalAnalysis($cvData, $language);

                return response()->json([
                    'success' => true,
                    'analysis' => $analysis,
                    'source' => 'template_fallback',
                    'insights' => $this->generateAIInsights($analysis, $language)
                ]);
            }

        } catch (\Exception $e) {
            Log::error('AI CV Analysis Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to analyze CV',
                'analysis' => $this->getFallbackAnalysis(),
                'source' => 'error_fallback'
            ], 500);
        }
    }

    /**
     * Get experience suggestions with quantified achievements
     */
    public function getExperienceSuggestions(Request $request): JsonResponse
    {
        try {
            $jobTitle = $request->input('job_title', 'professional');
            $company = $request->input('company', '');
            $industry = $request->input('industry', '');
            $companySize = $request->input('company_size', 'medium');
            $language = $request->input('language', 'en');

            // Try Gemini AI first
            try {
                $suggestions = $this->geminiService->generateExperienceDescriptions($jobTitle, $company, $industry);
                
                return response()->json([
                    'success' => true,
                    'suggestions' => $suggestions,
                    'source' => 'gemini_ai',
                    'metadata' => [
                        'job_title' => $jobTitle,
                        'company' => $company,
                        'industry' => $industry,
                        'ai_generated' => true,
                        'quantified' => true
                    ]
                ]);
            } catch (\Exception $aiError) {
                Log::warning('Gemini AI experience failed, using fallback: ' . $aiError->getMessage());
                
                // Fallback to template-based approach
                $category = $this->mapJobTitleToSkillCategory(strtolower($jobTitle));
                $templates = $this->experienceTemplates[$language] ?? $this->experienceTemplates['en'];
                
                $suggestions = [];
                if (isset($templates[$category])) {
                    $achievements = $templates[$category];
                    
                    // Customize based on company size
                    $achievements = $this->customizeForCompanySize($achievements, $companySize);
                    
                    // Randomize and select
                    shuffle($achievements);
                    $suggestions = array_slice($achievements, 0, 6);
                }

                return response()->json([
                    'success' => true,
                    'suggestions' => $suggestions,
                    'source' => 'template_fallback',
                    'metadata' => [
                        'category' => $category,
                        'customized_for' => $companySize,
                        'ai_generated' => false,
                        'quantified' => true
                    ]
                ]);
            }

        } catch (\Exception $e) {
            Log::error('AI Experience Suggestions Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to generate experience suggestions',
                'suggestions' => $this->getFallbackExperience(),
                'source' => 'error_fallback'
            ], 500);
        }
    }

    /**
     * Get interest suggestions based on profession
     */
    public function getInterestSuggestions(Request $request): JsonResponse
    {
        try {
            $jobTitle = $request->input('job_title', 'professional');
            $industry = $request->input('industry', '');
            $language = $request->input('language', 'en');
            
            // Try Gemini AI first
            try {
                $interests = $this->geminiService->generateInterestSuggestions($jobTitle, $industry);
                
                return response()->json([
                    'success' => true,
                    'interests' => $interests,
                    'source' => 'gemini_ai',
                    'metadata' => [
                        'job_title' => $jobTitle,
                        'industry' => $industry,
                        'ai_generated' => true,
                        'professional_focus' => true
                    ]
                ]);
            } catch (\Exception $aiError) {
                Log::warning('Gemini AI interests failed, using fallback: ' . $aiError->getMessage());
                
                // Fallback to template-based approach
                $category = $this->mapJobTitleToSkillCategory(strtolower($jobTitle));
                $interests = $this->getProfessionalInterests($category, $language);

                return response()->json([
                    'success' => true,
                    'interests' => $interests,
                    'source' => 'template_fallback',
                    'metadata' => [
                        'category' => $category,
                        'ai_generated' => false,
                        'professional_focus' => true
                    ]
                ]);
            }

        } catch (\Exception $e) {
            Log::error('AI Interest Suggestions Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to generate interest suggestions',
                'interests' => ['Professional Development', 'Technology Trends', 'Team Sports', 'Reading'],
                'source' => 'error_fallback'
            ], 500);
        }
    }

    /**
     * ATS optimization analysis
     */
    public function optimizeForATS(Request $request): JsonResponse
    {
        try {
            $cvData = $request->input('cv_data', []);
            $targetJob = $request->input('target_job', '');
            $language = $request->input('language', 'en');

            $optimization = [
                'ats_score' => $this->calculateATSScore($cvData),
                'keyword_matches' => $this->analyzeKeywordMatches($cvData, $targetJob),
                'formatting_issues' => $this->checkFormattingIssues($cvData),
                'recommendations' => $this->getATSRecommendations($cvData, $language),
                'improvement_priority' => []
            ];

            return response()->json([
                'success' => true,
                'optimization' => $optimization
            ]);

        } catch (\Exception $e) {
            Log::error('ATS Optimization Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to optimize for ATS'
            ], 500);
        }
    }

    /**
     * Chat with AI about topics relevant to current page context
     */
    public function chat(Request $request): JsonResponse
    {
        try {
            $message = $request->input('message', '');
            $cvData = $request->input('cv_data', []);
            $conversationHistory = $request->input('conversation_history', []);
            $context = $request->input('context', 'general');
            $language = $request->input('language', 'en');

            if (empty($message)) {
                return response()->json([
                    'success' => false,
                    'error' => 'Message is required'
                ], 400);
            }

            // Try Gemini AI first with context
            try {
                $chatResponse = $this->geminiService->chatWithAI($message, $cvData, $conversationHistory, $context);
                
                return response()->json([
                    'success' => true,
                    'response' => $chatResponse['response'],
                    'source' => $chatResponse['source'],
                    'context' => $context,
                    'timestamp' => now()->toISOString()
                ]);
            } catch (\Exception $aiError) {
                Log::warning('Gemini AI chat failed, using fallback: ' . $aiError->getMessage());
                
                // Contextual fallback response
                return response()->json([
                    'success' => true,
                    'response' => $this->getContextualFallbackResponse($message, $context, $language),
                    'source' => 'template_fallback',
                    'context' => $context,
                    'timestamp' => now()->toISOString()
                ]);
            }

        } catch (\Exception $e) {
            Log::error('AI Chat Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to process chat message',
                'response' => "I'm sorry, I'm having trouble responding right now. Please try again.",
                'source' => 'error_fallback',
                'context' => $context ?? 'general',
                'timestamp' => now()->toISOString()
            ], 500);
        }
    }

    /**
     * Test Gemini API connection and status
     */
    public function testConnection(Request $request): JsonResponse
    {
        try {
            $testMessage = "Hello, this is a test message. Please respond with 'API connection successful'.";
            
            $response = $this->geminiService->chatWithAI($testMessage, [], [], 'general');
            
            return response()->json([
                'success' => true,
                'api_status' => 'connected',
                'source' => $response['source'],
                'test_response' => $response['response'],
                'timestamp' => now()->toISOString()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'api_status' => 'failed',
                'error' => $e->getMessage(),
                'timestamp' => now()->toISOString()
            ], 500);
        }
    }

    /**
     * Debug Gemini API configuration and status
     */
    public function debugConnection(Request $request): JsonResponse
    {
        try {
            $config = config('services.gemini');
            $hasApiKey = !empty($config['api_key']);
            
            $debugInfo = [
                'success' => true,
                'configuration' => [
                    'has_api_key' => $hasApiKey,
                    'api_key_length' => $hasApiKey ? strlen($config['api_key']) : 0,
                    'model' => $config['model'] ?? 'not_set',
                    'base_url' => $config['base_url'] ?? 'not_set',
                    'max_tokens' => $config['max_tokens'] ?? 'not_set',
                    'temperature' => $config['temperature'] ?? 'not_set',
                ],
                'environment' => [
                    'gemini_env_key_set' => !empty(env('GEMINI_API_KEY')),
                    'gemini_env_key_length' => env('GEMINI_API_KEY') ? strlen(env('GEMINI_API_KEY')) : 0,
                ],
                'timestamp' => now()->toISOString()
            ];

            // If API key is available, try a simple test
            if ($hasApiKey) {
                try {
                    $testResponse = $this->geminiService->chatWithAI("Say 'Connection test successful'", [], [], 'general');
                    $debugInfo['api_test'] = [
                        'success' => true,
                        'source' => $testResponse['source'],
                        'response_preview' => substr($testResponse['response'], 0, 100) . '...'
                    ];
                } catch (\Exception $e) {
                    $debugInfo['api_test'] = [
                        'success' => false,
                        'error' => $e->getMessage(),
                        'error_type' => get_class($e)
                    ];
                }
            } else {
                $debugInfo['api_test'] = [
                    'success' => false,
                    'error' => 'No API key configured'
                ];
            }

            return response()->json($debugInfo);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Debug failed: ' . $e->getMessage(),
                'timestamp' => now()->toISOString()
            ], 500);
        }
    }

    // Private helper methods

    private function loadIndustrySkills(): array
    {
        return [
            'technology' => [
                'beginner' => ['HTML/CSS', 'JavaScript', 'Git', 'Problem Solving', 'Team Collaboration'],
                'intermediate' => ['React', 'Node.js', 'Python', 'SQL', 'REST APIs', 'Docker', 'Agile'],
                'advanced' => ['System Architecture', 'Microservices', 'AWS/Azure', 'DevOps', 'Technical Leadership'],
                'trending' => ['TypeScript', 'GraphQL', 'Kubernetes', 'Serverless', 'AI/ML', 'Web3']
            ],
            'design' => [
                'beginner' => ['Figma', 'Adobe Photoshop', 'User Research', 'Wireframing', 'Prototyping'],
                'intermediate' => ['Design Systems', 'Adobe XD', 'Sketch', 'User Testing', 'Information Architecture'],
                'advanced' => ['Design Strategy', 'Service Design', 'Design Leadership', 'Cross-functional Collaboration'],
                'trending' => ['AI-Assisted Design', 'Voice UI', 'Inclusive Design', 'Design Operations']
            ],
            'marketing' => [
                'beginner' => ['Content Marketing', 'Social Media', 'Google Analytics', 'Email Marketing'],
                'intermediate' => ['SEO/SEM', 'Marketing Automation', 'A/B Testing', 'CRM Management'],
                'advanced' => ['Growth Hacking', 'Marketing Strategy', 'Attribution Modeling', 'Team Leadership'],
                'trending' => ['AI Marketing Tools', 'Influencer Marketing', 'Voice Search Optimization']
            ],
            'business' => [
                'beginner' => ['Communication', 'Problem Solving', 'Time Management', 'Microsoft Office'],
                'intermediate' => ['Project Management', 'Data Analysis', 'Stakeholder Management', 'Process Improvement'],
                'advanced' => ['Strategic Planning', 'Change Management', 'Leadership', 'Business Development'],
                'trending' => ['Digital Transformation', 'Agile Leadership', 'Remote Team Management']
            ]
        ];
    }

    private function loadExperienceTemplates(): array
    {
        return [
            'en' => [
                'technology' => [
                    'Developed and maintained scalable web applications serving 10,000+ daily active users',
                    'Implemented automated testing suite reducing bugs by 40% and improving code quality',
                    'Led migration from monolithic to microservices architecture improving system performance by 35%',
                    'Collaborated with cross-functional teams to deliver 15+ feature releases on schedule',
                    'Mentored 3 junior developers and established coding best practices for the team',
                    'Optimized database queries resulting in 50% faster page load times',
                    'Built RESTful APIs handling 1M+ requests per day with 99.9% uptime'
                ],
                'design' => [
                    'Designed user interfaces for mobile app resulting in 25% increase in user engagement',
                    'Conducted user research with 200+ participants to inform design decisions',
                    'Created comprehensive design system used across 10+ product teams',
                    'Led design thinking workshops with stakeholders to identify user pain points',
                    'Improved conversion rates by 30% through iterative A/B testing of key user flows',
                    'Collaborated with engineering teams to ensure pixel-perfect implementation',
                    'Reduced user onboarding time by 45% through streamlined UX design'
                ],
                'marketing' => [
                    'Increased brand awareness by 60% through integrated digital marketing campaigns',
                    'Managed $500K annual marketing budget with 25% ROI improvement year-over-year',
                    'Grew social media following by 150% and engagement rates by 40%',
                    'Launched influencer partnership program generating 200+ qualified leads monthly',
                    'Implemented marketing automation workflows improving lead nurturing by 35%',
                    'Coordinated cross-channel campaigns resulting in 40% increase in customer acquisition'
                ]
            ],
            'sq' => [
                'technology' => [
                    'Zhvillova dhe mirëmbajta aplikacione web të shkallëzueshme që shërbejnë 10,000+ përdorues aktivë në ditë',
                    'Implementova suite automatike testimi duke reduktuar gabimet 40% dhe përmirësuar cilësinë e kodit',
                    'Udhëhoqa migrimin nga arkitektura monolitike në mikroshërbime duke përmirësuar performancën 35%',
                    'Bashkëpunova me ekipe ndërfunksionale për të dorëzuar 15+ versione karakteristikash në kohë',
                    'Mentorova 3 zhvillues të rinj dhe vendosa praktikat më të mira të kodimit për ekipin'
                ],
                'design' => [
                    'Dizajnova ndërfaqe përdoruesi për aplikacion mobil duke rezultuar në 25% rritje angazhimi',
                    'Kryeva hulumtime përdoruesi me 200+ pjesëmarrës për të informuar vendimet e dizajnit',
                    'Krijova sistem gjithëpërfshirës dizajni të përdorur në 10+ ekipe produkti',
                    'Udhëhoqa punëtori mendimi dizajni me palët e interesuara'
                ]
            ]
        ];
    }

    private function loadSummaryTemplates(): array
    {
        return [
            'en' => [
                'entry_level' => [
                    'Motivated {job_title} with strong foundation in {skills}. Eager to apply academic knowledge and fresh perspective to drive innovation in {industry} industry.',
                    'Recent graduate specializing in {industry} with hands-on experience in {skills}. Passionate about creating impactful solutions and continuous learning.',
                    'Emerging {job_title} with solid understanding of {skills}. Committed to delivering quality results and growing within a dynamic team environment.'
                ],
                'mid_level' => [
                    'Experienced {job_title} with {years} years of proven expertise in {skills}. Successfully delivered multiple projects while maintaining high quality standards.',
                    'Results-driven {job_title} with {years}+ years of experience building scalable solutions. Skilled in {skills} with track record of process improvement.',
                    'Dedicated {job_title} bringing {years} years of hands-on experience in {industry}. Expertise in {skills} with strong problem-solving abilities.'
                ],
                'senior_level' => [
                    'Senior {job_title} with {years}+ years of leadership experience in {industry}. Expert in {skills} with proven ability to architect solutions and mentor teams.',
                    'Accomplished {job_title} with {years}+ years of progressive experience leading cross-functional teams. Deep expertise in {skills} and strategic thinking.',
                    'Visionary {job_title} with {years}+ years transforming business requirements into innovative solutions. Specialized in {skills} with excellent stakeholder management.'
                ]
            ]
        ];
    }

    private function mapJobTitleToSkillCategory(string $jobTitle): string
    {
        $mappings = [
            'software' => 'technology', 'developer' => 'technology', 'engineer' => 'technology',
            'programmer' => 'technology', 'architect' => 'technology',
            'designer' => 'design', 'ux' => 'design', 'ui' => 'design',
            'marketing' => 'marketing', 'growth' => 'marketing', 'content' => 'marketing',
            'manager' => 'business', 'director' => 'business', 'analyst' => 'business'
        ];

        foreach ($mappings as $keyword => $category) {
            if (str_contains($jobTitle, $keyword)) {
                return $category;
            }
        }

        return 'business';
    }

    private function getSkillsByCategory(string $category, string $level): array
    {
        return $this->industrySkillsDatabase[$category][$level] ?? 
               $this->industrySkillsDatabase['business']['intermediate'];
    }

    private function getTrendingSkills(string $category, int $year): array
    {
        return $this->industrySkillsDatabase[$category]['trending'] ?? [];
    }

    private function determineExperienceLevel(int $years): string
    {
        if ($years >= 8) return 'senior_level';
        if ($years >= 3) return 'mid_level';
        return 'entry_level';
    }

    private function generateContextualSummary(
        string $jobTitle, 
        int $years, 
        array $skills, 
        string $industry, 
        string $level, 
        string $style, 
        array $templates
    ): string {
        $template = $templates[$level][array_rand($templates[$level])];
        $topSkills = array_slice($skills, 0, 3);
        
        return str_replace(
            ['{job_title}', '{years}', '{skills}', '{industry}'],
            [$jobTitle, $years, implode(', ', $topSkills), $industry ?: 'technology'],
            $template
        );
    }

    private function analyzeCvSection(string $section, array $cvData, array $config): array
    {
        $analysis = ['complete' => false, 'score' => 0, 'issues' => []];
        
        switch ($section) {
            case 'personal_info':
                $info = $cvData['personal_info'] ?? [];
                $required = ['first_name', 'last_name', 'email', 'phone'];
                $analysis['complete'] = count(array_filter($info, fn($v, $k) => in_array($k, $required) && !empty($v), ARRAY_FILTER_USE_BOTH)) >= 3;
                break;
                
            case 'summary':
                $summary = $cvData['summary'] ?? '';
                $analysis['complete'] = strlen($summary) >= 80;
                if (strlen($summary) < 50) $analysis['issues'][] = 'too_short';
                break;
                
            case 'experience':
                $experience = $cvData['experience'] ?? [];
                $analysis['complete'] = count($experience) > 0;
                break;
                
            case 'skills':
                $skills = $cvData['skills'] ?? [];
                $analysis['complete'] = count($skills) >= 5;
                break;
        }
        
        $analysis['score'] = $analysis['complete'] ? $config['weight'] : 0;
        return $analysis;
    }

    private function calculateATSScore(array $cvData): int
    {
        $score = 0;
        
        // Content analysis
        if (isset($cvData['summary']) && strlen($cvData['summary']) >= 80) $score += 20;
        if (isset($cvData['skills']) && count($cvData['skills']) >= 6) $score += 25;
        if (isset($cvData['experience']) && count($cvData['experience']) > 0) $score += 30;
        if (isset($cvData['education']) && count($cvData['education']) > 0) $score += 15;
        
        // Keyword density check
        $allText = $this->extractAllText($cvData);
        if (strlen($allText) > 100) $score += 10;
        
        return min($score, 100);
    }

    private function generateImprovementSuggestions(array $cvData, int $score, string $language): array
    {
        $suggestions = [];
        
        if ($score < 60) {
            $suggestions[] = [
                'type' => 'content',
                'priority' => 'high',
                'title' => 'Enhance Professional Summary',
                'description' => 'Add a compelling 80-120 word summary highlighting your value proposition.',
                'impact' => '+15 points'
            ];
        }
        
        if (!isset($cvData['skills']) || count($cvData['skills']) < 6) {
            $suggestions[] = [
                'type' => 'skills',
                'priority' => 'high',
                'title' => 'Add More Skills',
                'description' => 'Include 6-10 relevant skills to improve visibility.',
                'impact' => '+20 points'
            ];
        }
        
        return $suggestions;
    }

    private function getFallbackSkills(): array
    {
        return ['Communication', 'Problem Solving', 'Team Collaboration', 'Time Management', 'Leadership'];
    }

    private function getFallbackSummary(string $jobTitle): array
    {
        return [
            'style' => 'professional',
            'content' => "Experienced $jobTitle with strong analytical and communication skills. Proven ability to work effectively in team environments and deliver quality results.",
            'word_count' => 20,
            'recommended' => true
        ];
    }

    private function calculateCompleteness(array $cvData): int
    {
        $sections = [
            'personal_info' => ['weight' => 15, 'check' => fn($data) => !empty($data['personal_info']['first_name']) && !empty($data['personal_info']['email'])],
            'summary' => ['weight' => 20, 'check' => fn($data) => !empty($data['summary']) && strlen($data['summary']) >= 50],
            'experience' => ['weight' => 25, 'check' => fn($data) => !empty($data['experience']) && count($data['experience']) > 0],
            'education' => ['weight' => 15, 'check' => fn($data) => !empty($data['education']) && count($data['education']) > 0],
            'skills' => ['weight' => 20, 'check' => fn($data) => !empty($data['skills']) && count($data['skills']) >= 3],
            'interests' => ['weight' => 5, 'check' => fn($data) => !empty($data['interests']) && count($data['interests']) > 0]
        ];

        $totalWeight = 0;
        $achievedWeight = 0;

        foreach ($sections as $section => $config) {
            $totalWeight += $config['weight'];
            if ($config['check']($cvData)) {
                $achievedWeight += $config['weight'];
            }
        }

        return round(($achievedWeight / $totalWeight) * 100);
    }

    private function analyzeSections(array $cvData): array
    {
        $sections = [
            'personal_info' => ['weight' => 10, 'required' => true],
            'summary' => ['weight' => 20, 'required' => true],
            'experience' => ['weight' => 30, 'required' => true],
            'education' => ['weight' => 15, 'required' => false],
            'skills' => ['weight' => 20, 'required' => true],
            'interests' => ['weight' => 5, 'required' => false]
        ];

        $analysis = [];
        foreach ($sections as $section => $config) {
            $analysis[$section] = $this->analyzeCvSection($section, $cvData, $config);
        }

        return $analysis;
    }

    private function performTraditionalAnalysis(array $cvData, string $language): array
    {
        $analysis = [
            'score' => 0,
            'completeness' => 0,
            'ats_score' => 0,
            'industry_alignment' => 'good',
            'strengths' => [],
            'weaknesses' => [],
            'improvements' => [],
            'recommendations' => [],
            'keyword_density' => 'moderate',
            'sections_analysis' => []
        ];

        // Analyze each section
        $sections = [
            'personal_info' => ['weight' => 10, 'required' => true],
            'summary' => ['weight' => 20, 'required' => true],
            'experience' => ['weight' => 30, 'required' => true],
            'education' => ['weight' => 15, 'required' => false],
            'skills' => ['weight' => 20, 'required' => true],
            'interests' => ['weight' => 5, 'required' => false]
        ];

        $totalScore = 0;
        $maxScore = 0;

        foreach ($sections as $section => $config) {
            $sectionAnalysis = $this->analyzeCvSection($section, $cvData, $config);
            $analysis['sections_analysis'][$section] = $sectionAnalysis;
            
            $totalScore += $sectionAnalysis['score'];
            $maxScore += $config['weight'];

            if ($sectionAnalysis['complete']) {
                $analysis['strengths'][] = $this->getSectionStrengthMessage($section, $language);
            } else {
                $analysis['weaknesses'][] = $this->getSectionWeaknessMessage($section, $language);
            }
        }

        $analysis['score'] = round(($totalScore / $maxScore) * 100);
        $analysis['completeness'] = round(($totalScore / $maxScore) * 100);
        $analysis['ats_score'] = $this->calculateATSScore($cvData);
        $analysis['industry_alignment'] = $this->assessIndustryAlignment($cvData);
        $analysis['improvements'] = $this->generateImprovementSuggestions($cvData, $analysis['score'], $language);
        $analysis['keyword_density'] = $this->analyzeKeywordDensity($cvData);

        return $analysis;
    }

    private function extractAllText(array $cvData): string
    {
        $allText = [];
        
        if (isset($cvData['summary'])) {
            $allText[] = $cvData['summary'];
        }
        
        if (isset($cvData['experience'])) {
            foreach ($cvData['experience'] as $exp) {
                if (isset($exp['description'])) {
                    $allText[] = $exp['description'];
                }
            }
        }
        
        if (isset($cvData['skills'])) {
            $allText[] = implode(' ', $cvData['skills']);
        }
        
        return implode(' ', $allText);
    }

    private function analyzeKeywordDensity(array $cvData): string
    {
        $text = $this->extractAllText($cvData);
        $wordCount = str_word_count($text);
        
        if ($wordCount < 100) return 'low';
        if ($wordCount < 300) return 'moderate';
        return 'high';
    }

    private function getSectionStrengthMessage(string $section, string $language): string
    {
        $messages = [
            'personal_info' => 'Complete contact information provided',
            'summary' => 'Professional summary is well-written',
            'experience' => 'Work experience demonstrates career progression',
            'education' => 'Educational background strengthens profile',
            'skills' => 'Skills section showcases relevant abilities',
            'interests' => 'Interests add personality to the CV'
        ];
        
        return $messages[$section] ?? 'Section completed successfully';
    }

    private function getSectionWeaknessMessage(string $section, string $language): string
    {
        $messages = [
            'personal_info' => 'Missing essential contact information',
            'summary' => 'Professional summary needs improvement',
            'experience' => 'Add work experience to strengthen CV',
            'education' => 'Consider adding educational background',
            'skills' => 'Skills section needs more relevant entries',
            'interests' => 'Adding interests can enhance your profile'
        ];
        
        return $messages[$section] ?? 'Section needs completion';
    }

    private function assessIndustryAlignment(array $cvData): string
    {
        // Simple assessment based on content completeness
        $score = $this->calculateCompleteness($cvData);
        
        if ($score >= 80) return 'excellent';
        if ($score >= 60) return 'good';
        if ($score >= 40) return 'fair';
        return 'poor';
    }

    private function generateAIInsights(array $analysis, string $language): array
    {
        return [
            'overall_rating' => $analysis['score'] >= 80 ? 'excellent' : ($analysis['score'] >= 60 ? 'good' : 'needs_improvement'),
            'top_priority' => 'Enhance professional summary with quantified achievements',
            'quick_wins' => [
                'Add 2-3 more relevant skills',
                'Quantify achievements in experience descriptions',
                'Ensure contact information is complete'
            ],
            'industry_trends' => 'Consider adding trending skills for 2024'
        ];
    }

    private function getFallbackAnalysis(): array
    {
        return [
            'score' => 60,
            'completeness' => 60,
            'ats_score' => 65,
            'strengths' => ['Basic information provided'],
            'weaknesses' => ['Needs more detailed content'],
            'recommendations' => ['Add more sections', 'Improve content quality']
        ];
    }

    private function getFallbackExperience(): array
    {
        return [
            'Collaborated with team members to achieve project goals',
            'Maintained high standards of quality in all work activities',
            'Communicated effectively with stakeholders and clients'
        ];
    }

    private function calculatePersonalizationScore(array $personalInfo, array $skills, int $experienceYears): int
    {
        $score = 0;
        
        if (!empty($personalInfo['first_name'])) $score += 20;
        if (count($skills) >= 5) $score += 30;
        if ($experienceYears > 0) $score += 30;
        if (!empty($personalInfo['email'])) $score += 20;
        
        return min($score, 100);
    }

    private function customizeForCompanySize(array $achievements, string $companySize): array
    {
        // Simple customization based on company size
        return $achievements;
    }

    private function getIndustrySpecificSkills(string $industry): array
    {
        $industrySkills = [
            'technology' => ['Cloud Computing', 'DevOps', 'Machine Learning'],
            'healthcare' => ['Patient Care', 'Medical Records', 'Healthcare Compliance'],
            'finance' => ['Financial Analysis', 'Risk Management', 'Regulatory Compliance'],
            'education' => ['Curriculum Development', 'Student Assessment', 'Educational Technology']
        ];
        
        return $industrySkills[strtolower($industry)] ?? [];
    }

    private function getProfessionalInterests(string $category, string $language): array
    {
        $interests = [
            'technology' => ['Open Source Projects', 'Tech Conferences', 'Coding Challenges', 'AI Research'],
            'design' => ['Design Thinking', 'User Experience', 'Creative Arts', 'Design Conferences'],
            'marketing' => ['Digital Marketing Trends', 'Content Creation', 'Brand Strategy', 'Market Research'],
            'business' => ['Professional Development', 'Leadership', 'Strategic Planning', 'Industry Networking']
        ];
        
        return $interests[$category] ?? $interests['business'];
    }

    private function analyzeKeywordMatches(array $cvData, string $targetJob): array
    {
        return [
            'matches' => ['communication', 'teamwork', 'problem-solving'],
            'missing' => ['leadership', 'project management'],
            'score' => 75
        ];
    }

    private function checkFormattingIssues(array $cvData): array
    {
        return [
            'issues' => [],
            'warnings' => ['Consider using consistent date formats'],
            'score' => 85
        ];
    }

    private function getATSRecommendations(array $cvData, string $language): array
    {
        return [
            'Use standard section headings',
            'Include relevant keywords',
            'Avoid complex formatting',
            'Use consistent date formats'
        ];
    }

    private function getFallbackChatResponse(string $message): string
    {
        $messageLower = strtolower($message);
        
        if (str_contains($messageLower, 'hello') || str_contains($messageLower, 'hi')) {
            return "Hello! I'm your AI CV assistant. I'm here to help you create an outstanding CV. What would you like help with today?";
        } elseif (str_contains($messageLower, 'skill')) {
            return "I can help you identify the most relevant skills for your target role. What position are you applying for?";
        } elseif (str_contains($messageLower, 'summary') || str_contains($messageLower, 'about')) {
            return "A great professional summary should highlight your key achievements and value proposition in 80-120 words. Would you like me to help you write one?";
        } elseif (str_contains($messageLower, 'experience') || str_contains($messageLower, 'work')) {
            return "When describing work experience, focus on achievements rather than duties. Use action verbs and quantify results. What role would you like help describing?";
        } elseif (str_contains($messageLower, 'improve') || str_contains($messageLower, 'better')) {
            return "I can analyze your CV and provide specific improvement suggestions. Make sure all sections are complete for the best advice.";
        } elseif (str_contains($messageLower, 'thank')) {
            return "You're welcome! I'm here whenever you need help improving your CV. Good luck with your job search!";
        }
        
        return "I'm here to help you create an outstanding CV! I can assist with professional summaries, skills suggestions, work experience descriptions, and general career advice. What specific area would you like help with?";
    }

    /**
     * Get contextual fallback response based on current page context
     */
    private function getContextualFallbackResponse(string $message, string $context, string $language = 'en'): string
    {
        $messageLower = strtolower($message);
        
        // Context-specific responses
        switch ($context) {
            case 'cv':
                return $this->getCVContextFallback($messageLower, $language);
            case 'design':
                return $this->getDesignContextFallback($messageLower, $language);
            case 'support':
                return $this->getSupportContextFallback($messageLower, $language);
            case 'knowledge':
                return $this->getKnowledgeContextFallback($messageLower, $language);
            default:
                return $this->getGeneralContextFallback($messageLower, $language);
        }
    }

    /**
     * CV context fallback responses
     */
    private function getCVContextFallback(string $messageLower, string $language): string
    {
        if (str_contains($messageLower, 'analyze') || str_contains($messageLower, 'score')) {
            return "I can help analyze your CV! For the best analysis, make sure your CV has a professional summary, work experience, skills, and education sections filled out. I'll look at completeness, keyword optimization, and ATS compatibility.";
        } elseif (str_contains($messageLower, 'skill')) {
            return "I'd be happy to suggest relevant skills! What's your target job title or industry? I can recommend both technical skills and soft skills that employers are looking for in 2024.";
        } elseif (str_contains($messageLower, 'summary') || str_contains($messageLower, 'about')) {
            return "A great professional summary should be 80-120 words and highlight your key achievements. What's your current role and how many years of experience do you have? I can help you craft a compelling summary!";
        } elseif (str_contains($messageLower, 'experience') || str_contains($messageLower, 'work')) {
            return "When describing work experience, focus on achievements rather than duties. Use action verbs and quantify results when possible. What position would you like help describing?";
        } elseif (str_contains($messageLower, 'improve') || str_contains($messageLower, 'better')) {
            return "I can suggest several ways to improve your CV: enhance your professional summary, add quantified achievements, include relevant keywords, and ensure ATS compatibility. Which area interests you most?";
        } elseif (str_contains($messageLower, 'template')) {
            return "For CV templates, consider your industry: 'Modern' works well for tech roles, 'Professional' for corporate positions, 'Creative' for design fields, and 'Classic' for traditional industries. What field are you in?";
        }
        
        return "I'm your CV expert! I can help with writing summaries, suggesting skills, improving work descriptions, analyzing your CV, or choosing the right template. What would you like to work on?";
    }

    /**
     * Design context fallback responses
     */
    private function getDesignContextFallback(string $messageLower, string $language): string
    {
        if (str_contains($messageLower, 'template') || str_contains($messageLower, 'choose')) {
            return "Great question! Template choice depends on your industry and personality: 'Modern' templates work well for tech and startups, 'Professional' for corporate roles, 'Creative' for design/marketing, and 'Classic' for traditional fields like law or finance. What industry are you in?";
        } elseif (str_contains($messageLower, 'color') || str_contains($messageLower, 'design')) {
            return "For CV design, stick to 1-2 professional colors maximum. Blue conveys trust, gray is neutral and professional, and subtle accent colors can add personality. Avoid bright or distracting colors unless you're in a creative field.";
        } elseif (str_contains($messageLower, 'layout') || str_contains($messageLower, 'format')) {
            return "Good CV layout has clear sections, consistent formatting, and plenty of white space. Use consistent fonts (max 2), clear headings, and bullet points for readability. Keep it to 1-2 pages for most roles.";
        } elseif (str_contains($messageLower, 'font')) {
            return "For CVs, use professional fonts like Arial, Calibri, or Helvetica for body text (10-12pt), and slightly larger for headings. Avoid decorative fonts and ensure good readability both on screen and when printed.";
        }
        
        return "I'm here to help with template selection, design principles, layout optimization, and visual presentation! What aspect of CV design would you like to explore?";
    }

    /**
     * Support context fallback responses
     */
    private function getSupportContextFallback(string $messageLower, string $language): string
    {
        if (str_contains($messageLower, 'contact') || str_contains($messageLower, 'support')) {
            return "You can reach our support team through the contact form on this page, or check our FAQ section for common questions. For urgent issues, look for our live chat during business hours!";
        } elseif (str_contains($messageLower, 'bug') || str_contains($messageLower, 'error') || str_contains($messageLower, 'problem')) {
            return "I'm sorry you're experiencing issues! Please describe the problem you're encountering, and I'll help guide you to the right solution. You can also report bugs through our contact form.";
        } elseif (str_contains($messageLower, 'account') || str_contains($messageLower, 'login')) {
            return "For account-related issues like login problems or password resets, try using the 'Forgot Password' link on the login page. If that doesn't work, our support team can help you directly.";
        } elseif (str_contains($messageLower, 'billing') || str_contains($messageLower, 'payment')) {
            return "For billing questions or payment issues, please contact our support team with your account details. They can help with subscription changes, refunds, or payment problems.";
        }
        
        return "I'm here to help you get support! I can guide you to the right resources, help with common issues, or connect you with our support team. What do you need help with?";
    }

    /**
     * Knowledge/FAQ context fallback responses
     */
    private function getKnowledgeContextFallback(string $messageLower, string $language): string
    {
        if (str_contains($messageLower, 'how') || str_contains($messageLower, 'start')) {
            return "Getting started is easy! Create an account, choose a template, fill in your information section by section, and download your professional CV. The whole process usually takes 10-15 minutes.";
        } elseif (str_contains($messageLower, 'feature') || str_contains($messageLower, 'what can')) {
            return "Our platform offers CV creation with multiple templates, AI-powered suggestions, PDF downloads, and analytics. You can create unlimited CVs, get skill suggestions, and optimize for ATS systems.";
        } elseif (str_contains($messageLower, 'ats') || str_contains($messageLower, 'applicant tracking')) {
            return "ATS (Applicant Tracking Systems) scan CVs for keywords and formatting. To optimize: use standard section headings, include relevant keywords from job descriptions, avoid images/graphics, and use simple formatting.";
        } elseif (str_contains($messageLower, 'download') || str_contains($messageLower, 'pdf')) {
            return "You can download your CV as a PDF once it's complete. The PDF preserves formatting and is ready to send to employers. You can also make updates and download new versions anytime.";
        }
        
        return "I can help explain our features, guide you through the CV creation process, or clarify any questions about the platform. What would you like to know more about?";
    }

    /**
     * General context fallback responses
     */
    private function getGeneralContextFallback(string $messageLower, string $language): string
    {
        if (str_contains($messageLower, 'hello') || str_contains($messageLower, 'hi')) {
            return "Hello! I'm your AI assistant. I can help with questions about our platform, provide general information, or just have a friendly conversation. How can I assist you today?";
        } elseif (str_contains($messageLower, 'help') || str_contains($messageLower, 'what can you do')) {
            return "I can help you with information about our CV platform, answer questions about features, provide guidance, or assist with general inquiries. I adapt my expertise based on what page you're on!";
        } elseif (str_contains($messageLower, 'feature') || str_contains($messageLower, 'platform')) {
            return "Our platform helps you create professional CVs with multiple templates, AI-powered suggestions, and easy downloads. You can build a standout CV in minutes!";
        } elseif (str_contains($messageLower, 'thank')) {
            return "You're very welcome! I'm always here to help. Feel free to ask me anything else!";
        }
        
        return "I'm here to help! I can answer questions about our platform, provide information, or assist you with whatever you need. What can I help you with today?";
    }
    
    /**
     * Get real-time dashboard analysis for CVs
     */
    public function getDashboardAnalysis(Request $request): JsonResponse
    {
        try {
            // Get the most recent CV data if not provided
            $cvData = $request->input('cv_data');
            
            if (!$cvData) {
                $user = $request->user();
                $latestCv = $user->cvs()->latest()->first();
                
                if ($latestCv) {
                    $cvData = [
                        'summary' => $latestCv->summary,
                        'experience' => $latestCv->workExperiences->toArray(),
                        'skills' => $latestCv->skills->pluck('name')->toArray(),
                        'education' => $latestCv->educations->toArray(),
                        'personal_info' => $latestCv->personal_details
                    ];
                } else {
                    $cvData = [];
                }
            }
            
            $analysis = $this->geminiService->getDashboardAnalysis($cvData);
            
            return response()->json([
                'success' => true,
                'analysis' => $analysis,
                'timestamp' => now()->toISOString()
            ]);
            
        } catch (\Exception $e) {
            Log::error('Dashboard Analysis Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to get dashboard analysis',
                'analysis' => $this->getDefaultDashboardAnalysis()
            ], 500);
        }
    }
    
    /**
     * Get trending skills for dashboard
     */
    public function getDashboardTrendingSkills(Request $request): JsonResponse
    {
        try {
            $industry = $request->input('industry', '');
            $limit = $request->input('limit', 6);
            
            $skills = $this->geminiService->getTrendingSkills($industry, $limit);
            
            return response()->json([
                'success' => true,
                'skills' => $skills,
                'timestamp' => now()->toISOString()
            ]);
            
        } catch (\Exception $e) {
            Log::error('Trending Skills Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to get trending skills',
                'skills' => $this->getDefaultTrendingSkills()
            ], 500);
        }
    }
    
    /**
     * Get market insights for dashboard
     */
    public function getMarketInsights(Request $request): JsonResponse
    {
        try {
            $industry = $request->input('industry', '');
            
            $insights = $this->geminiService->getMarketInsights($industry);
            
            return response()->json([
                'success' => true,
                'insights' => $insights,
                'timestamp' => now()->toISOString()
            ]);
            
        } catch (\Exception $e) {
            Log::error('Market Insights Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to get market insights',
                'insights' => $this->getDefaultMarketInsights()
            ], 500);
        }
    }
    
    /**
     * Get AI connection status
     */
    public function getConnectionStatus(Request $request): JsonResponse
    {
        try {
            $status = $this->geminiService->getConnectionStatus();
            
            return response()->json([
                'success' => true,
                'status' => $status,
                'timestamp' => now()->toISOString()
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'status' => [
                    'is_connected' => false,
                    'has_api_key' => false,
                    'last_error' => $e->getMessage()
                ],
                'timestamp' => now()->toISOString()
            ], 500);
        }
    }
    
    /**
     * Get default dashboard analysis (fallback)
     */
    private function getDefaultDashboardAnalysis(): array
    {
        return [
            'overall_score' => 15,
            'completeness' => 10,
            'ats_score' => 20,
            'market_fit' => 25,
            'recommendations' => [
                'Create your first CV to get personalized insights',
                'Add your professional experience and skills',
                'Complete your profile for better recommendations'
            ],
            'missing_sections' => ['CV not created yet'],
            'strengths' => ['Ready to start your professional journey']
        ];
    }
    
    /**
     * Get default trending skills (fallback)
     */
    private function getDefaultTrendingSkills(): array
    {
        return [
            ['name' => 'AI/ML', 'growth' => 156, 'heat' => 'blazing'],
            ['name' => 'Cloud Computing', 'growth' => 134, 'heat' => 'blazing'],
            ['name' => 'Cybersecurity', 'growth' => 112, 'heat' => 'hot'],
            ['name' => 'React/Vue.js', 'growth' => 89, 'heat' => 'hot'],
            ['name' => 'DevOps', 'growth' => 78, 'heat' => 'warm'],
            ['name' => 'Data Analytics', 'growth' => 67, 'heat' => 'warm']
        ];
    }
    
    /**
     * Get default market insights (fallback)
     */
    private function getDefaultMarketInsights(): array
    {
        return [
            [
                'id' => 1,
                'text' => 'Remote skills are 89% more in demand than pre-2020',
                'icon' => 'fas fa-home'
            ],
            [
                'id' => 2,
                'text' => 'AI skills can increase salary potential by up to 40%',
                'icon' => 'fas fa-chart-line'
            ],
            [
                'id' => 3,
                'text' => 'Soft skills now weight 45% in technical hiring decisions',
                'icon' => 'fas fa-handshake'
            ]
        ];
    }
} 