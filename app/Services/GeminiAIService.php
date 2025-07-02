<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class GeminiAIService
{
    private string $apiKey;
    private string $model;
    private string $baseUrl;
    private int $maxTokens;
    private float $temperature;
    private int $cacheTimeout = 3600; // 1 hour
    private bool $isConnected = false;
    private ?string $lastError = null;
    private int $retryAttempts = 3;
    private int $retryDelay = 1000; // milliseconds

    public function __construct()
    {
        $config = config('services.gemini');
        $this->apiKey = $config['api_key'] ?? '';
        $this->model = $config['model'] ?? 'gemini-pro';
        $this->baseUrl = $config['base_url'] ?? 'https://generativelanguage.googleapis.com/v1beta/models';
        $this->maxTokens = $config['max_tokens'] ?? 1000;
        $this->temperature = $config['temperature'] ?? 0.7;
        
        // Test connection on initialization
        $this->testConnection();
    }
    
    /**
     * Test API connection and update status
     */
    public function testConnection(): bool
    {
        try {
            if (empty($this->apiKey)) {
                $this->isConnected = false;
                $this->lastError = 'API key not configured';
                return false;
            }
            
            // Make a simple test request
            $response = Http::timeout(10)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->get("{$this->baseUrl}?key={$this->apiKey}");
            
            $this->isConnected = $response->successful();
            $this->lastError = $this->isConnected ? null : 'Failed to connect to Gemini API';
            
            return $this->isConnected;
        } catch (\Exception $e) {
            $this->isConnected = false;
            $this->lastError = $e->getMessage();
            return false;
        }
    }
    
    /**
     * Get connection status and details
     */
    public function getConnectionStatus(): array
    {
        return [
            'is_connected' => $this->isConnected,
            'has_api_key' => !empty($this->apiKey),
            'last_error' => $this->lastError,
            'model' => $this->model,
            'api_key_masked' => $this->maskApiKey($this->apiKey)
        ];
    }
    
    /**
     * Mask API key for security
     */
    private function maskApiKey(string $key): string
    {
        if (empty($key)) return 'Not configured';
        if (strlen($key) < 8) return '***';
        return substr($key, 0, 4) . '...' . substr($key, -4);
    }

    /**
     * Generate professional summary using Gemini AI
     */
    public function generateProfessionalSummary(array $data): array
    {
        $cacheKey = 'gemini_summary_' . md5(json_encode($data));
        
        return Cache::remember($cacheKey, $this->cacheTimeout, function () use ($data) {
            $prompt = $this->buildSummaryPrompt($data);
            
            try {
                $response = $this->makeRequest($prompt, [
                    'temperature' => 0.7,
                    'max_tokens' => 300
                ]);

                return $this->parseSummaryResponse($response);
            } catch (\Exception $e) {
                Log::error('Gemini Summary Generation Error: ' . $e->getMessage());
                return $this->getFallbackSummary($data);
            }
        });
    }

    /**
     * Generate skill suggestions using Gemini AI
     */
    public function generateSkillSuggestions(string $jobTitle, string $industry = '', string $experienceLevel = 'intermediate'): array
    {
        $cacheKey = "gemini_skills_{$jobTitle}_{$industry}_{$experienceLevel}";
        
        return Cache::remember($cacheKey, $this->cacheTimeout, function () use ($jobTitle, $industry, $experienceLevel) {
            $prompt = $this->buildSkillsPrompt($jobTitle, $industry, $experienceLevel);
            
            try {
                $response = $this->makeRequest($prompt, [
                    'temperature' => 0.5,
                    'max_tokens' => 200
                ]);

                return $this->parseSkillsResponse($response);
            } catch (\Exception $e) {
                Log::error('Gemini Skills Generation Error: ' . $e->getMessage());
                return $this->getFallbackSkills($jobTitle);
            }
        });
    }

    /**
     * Generate work experience descriptions using Gemini AI
     */
    public function generateExperienceDescriptions(string $jobTitle, string $company = '', string $industry = ''): array
    {
        $cacheKey = "gemini_experience_{$jobTitle}_{$company}_{$industry}";
        
        return Cache::remember($cacheKey, $this->cacheTimeout, function () use ($jobTitle, $company, $industry) {
            $prompt = $this->buildExperiencePrompt($jobTitle, $company, $industry);
            
            try {
                $response = $this->makeRequest($prompt, [
                    'temperature' => 0.6,
                    'max_tokens' => 300
                ]);

                return $this->parseExperienceResponse($response);
            } catch (\Exception $e) {
                Log::error('Gemini Experience Generation Error: ' . $e->getMessage());
                return $this->getFallbackExperience($jobTitle);
            }
        });
    }

    /**
     * Analyze CV comprehensively using Gemini AI
     */
    public function analyzeCVWithAI(array $cvData): array
    {
        $prompt = $this->buildAnalysisPrompt($cvData);
        
        try {
            $response = $this->makeRequest($prompt, [
                'temperature' => 0.3,
                'max_tokens' => 500
            ]);

            return $this->parseAnalysisResponse($response);
        } catch (\Exception $e) {
            Log::error('Gemini CV Analysis Error: ' . $e->getMessage());
            return $this->getFallbackAnalysis($cvData);
        }
    }

    /**
     * Generate interest suggestions using Gemini AI
     */
    public function generateInterestSuggestions(string $jobTitle, string $industry = ''): array
    {
        $cacheKey = "gemini_interests_{$jobTitle}_{$industry}";
        
        return Cache::remember($cacheKey, $this->cacheTimeout, function () use ($jobTitle, $industry) {
            $prompt = $this->buildInterestsPrompt($jobTitle, $industry);
            
            try {
                $response = $this->makeRequest($prompt, [
                    'temperature' => 0.6,
                    'max_tokens' => 150
                ]);

                return $this->parseInterestsResponse($response);
            } catch (\Exception $e) {
                Log::error('Gemini Interests Generation Error: ' . $e->getMessage());
                return $this->getFallbackInterests($jobTitle);
            }
        });
    }
    
    /**
     * Get real-time dashboard analysis using Gemini AI
     */
    public function getDashboardAnalysis(array $cvData): array
    {
        try {
            // If no CV data, return empty state analysis
            if (empty($cvData)) {
                return $this->getEmptyDashboardAnalysis();
            }
            
            $prompt = $this->buildDashboardAnalysisPrompt($cvData);
            
            $response = $this->makeRequest($prompt, [
                'temperature' => 0.5,
                'max_tokens' => 600
            ]);
            
            $analysis = $this->parseDashboardAnalysisResponse($response);
            
            // Ensure all required fields are present
            return array_merge($this->getDefaultDashboardAnalysis(), $analysis);
            
        } catch (\Exception $e) {
            Log::error('Gemini Dashboard Analysis Error: ' . $e->getMessage());
            return $this->getFallbackDashboardAnalysis($cvData);
        }
    }
    
    /**
     * Get trending skills for dashboard
     */
    public function getTrendingSkills(string $industry = '', int $limit = 6): array
    {
        $cacheKey = "gemini_trending_skills_{$industry}_{$limit}";
        
        return Cache::remember($cacheKey, 3600, function () use ($industry, $limit) {
            try {
                $prompt = "You are a job market expert. List the top {$limit} trending skills for 2024" . 
                         ($industry ? " in the {$industry} industry" : " across all industries") . 
                         ". Format each skill on a new line with its growth percentage like: SkillName|GrowthPercent|Heat\n" .
                         "Where Heat is one of: blazing, hot, warm\n" .
                         "Example: AI/ML|156|blazing";
                
                $response = $this->makeRequest($prompt, [
                    'temperature' => 0.3,
                    'max_tokens' => 200
                ]);
                
                return $this->parseTrendingSkillsResponse($response);
            } catch (\Exception $e) {
                Log::error('Gemini Trending Skills Error: ' . $e->getMessage());
                return $this->getFallbackTrendingSkills();
            }
        });
    }
    
    /**
     * Get market insights for dashboard
     */
    public function getMarketInsights(string $industry = ''): array
    {
        $cacheKey = "gemini_market_insights_{$industry}";
        
        return Cache::remember($cacheKey, 3600, function () use ($industry) {
            try {
                $prompt = "You are a job market analyst. Provide 3 key market insights for job seekers in 2024" .
                         ($industry ? " specifically for the {$industry} industry" : "") .
                         ". Format each insight on a new line with an icon suggestion like: insight_text|icon_name\n" .
                         "Where icon_name is one of: home, chart-line, handshake, rocket, brain";
                
                $response = $this->makeRequest($prompt, [
                    'temperature' => 0.6,
                    'max_tokens' => 150
                ]);
                
                return $this->parseMarketInsightsResponse($response);
            } catch (\Exception $e) {
                Log::error('Gemini Market Insights Error: ' . $e->getMessage());
                return $this->getFallbackMarketInsights();
            }
        });
    }

    /**
     * Chat with Gemini AI with context awareness
     */
    public function chatWithAI(string $message, array $cvData = [], array $conversationHistory = [], string $context = 'general'): array
    {
        try {
            // Debug: Log the API key status (without exposing the actual key)
            Log::info('Gemini Chat Request', [
                'has_api_key' => !empty($this->apiKey),
                'api_key_length' => $this->apiKey ? strlen($this->apiKey) : 0,
                'context' => $context,
                'message_length' => strlen($message)
            ]);

            if (empty($this->apiKey)) {
                Log::warning('Gemini API key is not configured');
                return [
                    'response' => $this->getFallbackChatResponse($message, $context) . "\n\n*Note: AI features are currently unavailable. Please check the API configuration.*",
                    'source' => 'fallback_no_key'
                ];
            }

            $prompt = $this->buildChatPrompt($message, $cvData, $conversationHistory, $context);
            
            $response = $this->makeRequest($prompt, [
                'temperature' => 0.8,
                'max_tokens' => 400
            ]);

            Log::info('Gemini API Success', [
                'response_length' => strlen($response),
                'context' => $context
            ]);

            return [
                'response' => $this->parseChatResponse($response),
                'source' => 'gemini_ai'
            ];
        } catch (\Exception $e) {
            Log::error('Gemini Chat Error: ' . $e->getMessage(), [
                'context' => $context,
                'message' => $message,
                'error_type' => get_class($e)
            ]);
            
            $errorMessage = $e->getMessage();
            
            return [
                'response' => $this->getFallbackChatResponse($message, $context) . "\n\n*AI Service Note: {$errorMessage}*",
                'source' => 'fallback_error'
            ];
        }
    }

    /**
     * Make request to Gemini API with retry logic
     */
    private function makeRequest(string $prompt, array $options = []): string
    {
        if (empty($this->apiKey)) {
            $this->isConnected = false;
            $this->lastError = "Gemini API key not configured";
            throw new \Exception($this->lastError);
        }

        $temperature = $options['temperature'] ?? $this->temperature;
        $maxTokens = $options['max_tokens'] ?? $this->maxTokens;
        $attempt = 0;
        $lastException = null;

        while ($attempt < $this->retryAttempts) {
            try {
                $response = Http::timeout(30)
                    ->retry($this->retryAttempts, $this->retryDelay)
                    ->withHeaders([
                        'Content-Type' => 'application/json',
                    ])
                    ->post("{$this->baseUrl}/{$this->model}:generateContent?key={$this->apiKey}", [
                        'contents' => [
                            [
                                'parts' => [
                                    ['text' => $prompt]
                                ]
                            ]
                        ],
                        'generationConfig' => [
                            'temperature' => $temperature,
                            'maxOutputTokens' => $maxTokens,
                            'topK' => 40,
                            'topP' => 0.95,
                        ]
                    ]);

                if (!$response->successful()) {
                    throw new \Exception("Gemini API request failed: " . $response->body());
                }

                // If successful, mark as connected and clear errors
                $this->isConnected = true;
                $this->lastError = null;

        $data = $response->json();
        
        // Log the full response for debugging if it's not a success and doesn't have candidates
        if (empty($data['candidates'])) {
            Log::warning('Gemini API: No candidates in response or unexpected format.', ['response_data' => $data]);
        }

        // Check for prompt feedback (e.g., safety issues)
        if (isset($data['promptFeedback']['safetyRatings'])) {
            $safetyRatings = $data['promptFeedback']['safetyRatings'];
            $blocked = false;
            foreach ($safetyRatings as $rating) {
                if ($rating['blocked'] === true) {
                    $blocked = true;
                    break;
                }
            }
            if ($blocked) {
                Log::warning('Gemini API: Content blocked due to safety policy.', ['safety_ratings' => $safetyRatings]);
                throw new \Exception("Response blocked by AI safety policy.");
            }
        }

        // Check for specific error messages from the API (e.g., API key, model issues)
        if (isset($data['error']['message'])) {
            $errorMessage = $data['error']['message'];
            Log::error('Gemini API returned error: ' . $errorMessage, ['api_error' => $data['error']]);
            if (str_contains($errorMessage, 'API key not valid') || str_contains($errorMessage, 'API_KEY_INVALID')) {
                throw new \Exception("Gemini API Error: Your API key is invalid or not authorized.");
            } elseif (str_contains($errorMessage, 'model') && str_contains($errorMessage, 'unavailable')) {
                 throw new \Exception("Gemini API Error: Model '" . ($this->model ?? 'unknown') . "' is currently unavailable or not accessible with your API key/region. Try a different model or check your billing/access.");
            } else {
                throw new \Exception("Gemini API Error: " . $errorMessage);
            }
        }

        // Enhanced response validation with better error messages
        if (!isset($data['candidates']) || empty($data['candidates'])) {
            throw new \Exception("Invalid response format from Gemini API: No candidates found in response.");
        }

        if (!isset($data['candidates'][0])) {
            throw new \Exception("Invalid response format from Gemini API: First candidate missing.");
        }

        if (!isset($data['candidates'][0]['content'])) {
            throw new \Exception("Invalid response format from Gemini API: Content missing from candidate.");
        }

        if (!isset($data['candidates'][0]['content']['parts']) || empty($data['candidates'][0]['content']['parts'])) {
            throw new \Exception("Invalid response format from Gemini API: Parts missing from content.");
        }

        if (!isset($data['candidates'][0]['content']['parts'][0]['text'])) {
            throw new \Exception("Invalid response format from Gemini API: Text missing from first part.");
        }

                return $data['candidates'][0]['content']['parts'][0]['text'];
                
            } catch (\Exception $e) {
                $lastException = $e;
                $attempt++;
                
                // Log retry attempts
                Log::warning("Gemini API attempt {$attempt} failed: " . $e->getMessage());
                
                if ($attempt < $this->retryAttempts) {
                    // Wait before retrying
                    usleep($this->retryDelay * 1000);
                }
            }
        }
        
        // All retries failed
        $this->isConnected = false;
        $this->lastError = $lastException ? $lastException->getMessage() : 'Unknown error';
        throw $lastException ?: new \Exception('Failed to connect to Gemini API after ' . $this->retryAttempts . ' attempts');
    }

    /**
     * Build prompt for professional summary generation
     */
    private function buildSummaryPrompt(array $data): string
    {
        $jobTitle = $data['job_title'] ?? 'Professional';
        $experienceYears = $data['experience_years'] ?? 0;
        $skills = is_array($data['skills'] ?? []) ? implode(', ', $data['skills']) : '';
        $industry = $data['industry'] ?? '';
        $firstName = $data['personal_info']['first_name'] ?? '';

        return "You are an expert CV writer and career coach. Generate 3 different professional summary styles for a CV.

Job Title: {$jobTitle}
Experience: {$experienceYears} years
Skills: {$skills}
Industry: {$industry}
Name: {$firstName}

Generate exactly 3 summaries in this format:
STYLE: Professional
CONTENT: [80-120 word professional summary]

STYLE: Creative
CONTENT: [80-120 word creative summary]

STYLE: Results-focused
CONTENT: [80-120 word results-focused summary]

Requirements:
- Each summary should be 80-120 words
- Use action verbs and quantifiable achievements where possible
- Tailor to the specific job title and industry
- Make it ATS-friendly
- Show progression and growth potential
- Be compelling and unique";
    }

    /**
     * Build prompt for skills suggestions
     */
    private function buildSkillsPrompt(string $jobTitle, string $industry, string $experienceLevel): string
    {
        return "You are a recruitment expert. Generate the most relevant and in-demand skills for this role in 2024.

Job Title: {$jobTitle}
Industry: {$industry}
Experience Level: {$experienceLevel}

Provide exactly 8-10 skills in this format:
TECHNICAL: [list 4-5 technical skills separated by commas]
SOFT: [list 3-4 soft skills separated by commas]
TRENDING: [list 1-2 trending/emerging skills for 2024 separated by commas]

Requirements:
- Focus on skills that are actually demanded by employers
- Include both technical and soft skills
- Consider current industry trends
- Make skills specific and measurable where possible
- Prioritize skills that improve ATS matching";
    }

    /**
     * Build prompt for experience descriptions
     */
    private function buildExperiencePrompt(string $jobTitle, string $company, string $industry): string
    {
        return "You are a professional CV writer. Generate 5-6 compelling work experience bullet points for this role.

Job Title: {$jobTitle}
Company: {$company}
Industry: {$industry}

Generate bullet points that:
- Start with strong action verbs
- Include quantifiable achievements (use realistic numbers/percentages)
- Show impact and results
- Are ATS-friendly
- Demonstrate growth and responsibility
- Are 1-2 lines each

Format each bullet point starting with a dash (-) symbol for better compatibility.";
    }

    /**
     * Build prompt for CV analysis
     */
    private function buildAnalysisPrompt(array $cvData): string
    {
        $summary = $cvData['summary'] ?? '';
        $experienceCount = count($cvData['experience'] ?? []);
        $skillsCount = count($cvData['skills'] ?? []);
        $educationCount = count($cvData['education'] ?? []);

        return "You are a professional CV reviewer and ATS expert. Analyze this CV data and provide insights.

CV SUMMARY: {$summary}
EXPERIENCE ENTRIES: {$experienceCount}
SKILLS COUNT: {$skillsCount}
EDUCATION ENTRIES: {$educationCount}

Provide analysis in this format:
SCORE: [0-100]
STRENGTHS: [3-4 key strengths separated by commas]
WEAKNESSES: [2-3 areas for improvement separated by commas]  
ATS_SCORE: [0-100]
RECOMMENDATIONS: [3-4 specific actionable recommendations separated by commas]

Focus on:
- Content quality and completeness
- ATS compatibility
- Professional presentation
- Market competitiveness
- Keyword optimization";
    }

    /**
     * Build prompt for interests suggestions
     */
    private function buildInterestsPrompt(string $jobTitle, string $industry): string
    {
        return "You are a career coach. Suggest 6-8 professional interests that would complement this role.

Job Title: {$jobTitle}
Industry: {$industry}

Provide interests that:
- Are professionally relevant
- Show well-roundedness
- Demonstrate continuous learning
- Are conversation starters
- Align with company culture
- Show leadership or teamwork

Format as a simple comma-separated list.";
    }

    /**
     * Parse summary response from Gemini
     */
    private function parseSummaryResponse(string $response): array
    {
        $summaries = [];
        $lines = explode("\n", $response);
        $currentSummary = null;

        foreach ($lines as $line) {
            $line = trim($line);
            if (str_starts_with($line, 'STYLE:')) {
                if ($currentSummary) {
                    $summaries[] = $currentSummary;
                }
                $style = trim(str_replace('STYLE:', '', $line));
                $currentSummary = [
                    'style' => strtolower($style),
                    'content' => '',
                    'word_count' => 0,
                    'recommended' => strtolower($style) === 'professional'
                ];
            } elseif (str_starts_with($line, 'CONTENT:') && $currentSummary) {
                $content = trim(str_replace('CONTENT:', '', $line));
                $currentSummary['content'] = $content;
                $currentSummary['word_count'] = str_word_count($content);
            }
        }

        if ($currentSummary) {
            $summaries[] = $currentSummary;
        }

        return $summaries ?: $this->getFallbackSummary([]);
    }

    /**
     * Parse skills response from Gemini
     */
    private function parseSkillsResponse(string $response): array
    {
        $skills = [];
        $lines = explode("\n", $response);

        foreach ($lines as $line) {
            $line = trim($line);
            if (str_starts_with($line, 'TECHNICAL:')) {
                $techSkills = explode(',', str_replace('TECHNICAL:', '', $line));
                $skills = array_merge($skills, array_map('trim', $techSkills));
            } elseif (str_starts_with($line, 'SOFT:')) {
                $softSkills = explode(',', str_replace('SOFT:', '', $line));
                $skills = array_merge($skills, array_map('trim', $softSkills));
            } elseif (str_starts_with($line, 'TRENDING:')) {
                $trendingSkills = explode(',', str_replace('TRENDING:', '', $line));
                $skills = array_merge($skills, array_map('trim', $trendingSkills));
            }
        }

        return array_filter($skills, fn($skill) => !empty($skill)) ?: $this->getFallbackSkills('');
    }

    /**
     * Parse experience response from Gemini
     */
    private function parseExperienceResponse(string $response): array
    {
        $experiences = [];
        $lines = explode("\n", $response);

        foreach ($lines as $line) {
            $line = trim($line);
            // Clean up encoding issues and handle various bullet point formats
            $line = mb_convert_encoding($line, 'UTF-8', 'UTF-8');
            $line = str_replace(['â€¢', '•', '◦', '▪'], '-', $line);
            
            if (!empty($line) && (str_starts_with($line, '-') || str_starts_with($line, '*') || str_starts_with($line, '+'))) {
                $cleanLine = ltrim($line, '-*+ ');
                if (!empty($cleanLine)) {
                    $experiences[] = $cleanLine;
                }
            }
        }

        return array_filter($experiences) ?: $this->getFallbackExperience('');
    }

    /**
     * Parse analysis response from Gemini
     */
    private function parseAnalysisResponse(string $response): array
    {
        $analysis = [
            'score' => 70,
            'strengths' => [],
            'weaknesses' => [],
            'ats_score' => 70,
            'recommendations' => []
        ];

        $lines = explode("\n", $response);
        foreach ($lines as $line) {
            $line = trim($line);
            if (str_starts_with($line, 'SCORE:')) {
                $analysis['score'] = (int) filter_var($line, FILTER_SANITIZE_NUMBER_INT);
            } elseif (str_starts_with($line, 'ATS_SCORE:')) {
                $analysis['ats_score'] = (int) filter_var($line, FILTER_SANITIZE_NUMBER_INT);
            } elseif (str_starts_with($line, 'STRENGTHS:')) {
                $strengths = str_replace('STRENGTHS:', '', $line);
                $analysis['strengths'] = array_map('trim', explode(',', $strengths));
            } elseif (str_starts_with($line, 'WEAKNESSES:')) {
                $weaknesses = str_replace('WEAKNESSES:', '', $line);
                $analysis['weaknesses'] = array_map('trim', explode(',', $weaknesses));
            } elseif (str_starts_with($line, 'RECOMMENDATIONS:')) {
                $recommendations = str_replace('RECOMMENDATIONS:', '', $line);
                $analysis['recommendations'] = array_map('trim', explode(',', $recommendations));
            }
        }

        return $analysis;
    }

    /**
     * Parse interests response from Gemini
     */
    private function parseInterestsResponse(string $response): array
    {
        $interests = array_map('trim', explode(',', $response));
        return array_filter($interests, fn($interest) => !empty($interest)) ?: $this->getFallbackInterests('');
    }

    // Fallback methods

    private function getFallbackSummary(array $data): array
    {
        return [
            [
                'style' => 'professional',
                'content' => 'Dedicated professional with strong problem-solving abilities and excellent communication skills. Experienced in collaborating with cross-functional teams to deliver high-quality results. Committed to continuous learning and professional development.',
                'word_count' => 32,
                'recommended' => true
            ]
        ];
    }

    private function getFallbackSkills(string $jobTitle): array
    {
        return [
            'Communication', 'Problem Solving', 'Team Collaboration', 
            'Time Management', 'Critical Thinking', 'Adaptability', 
            'Leadership', 'Project Management'
        ];
    }

    private function getFallbackExperience(string $jobTitle): array
    {
        return [
            'Collaborated with cross-functional teams to deliver high-quality results',
            'Managed multiple projects simultaneously while meeting strict deadlines',
            'Implemented process improvements that increased efficiency by 20%',
            'Mentored junior team members and provided technical guidance',
            'Maintained strong relationships with clients and stakeholders'
        ];
    }

    private function getFallbackAnalysis(array $cvData): array
    {
        return [
            'score' => 75,
            'strengths' => ['Well-structured format', 'Clear contact information', 'Relevant experience'],
            'weaknesses' => ['Could use more quantified achievements', 'Skills section needs expansion'],
            'ats_score' => 70,
            'recommendations' => ['Add more metrics to achievements', 'Include trending skills', 'Optimize keywords']
        ];
    }

    private function getFallbackInterests(string $jobTitle): array
    {
        return [
            'Professional Development', 'Technology Trends', 'Team Sports', 
            'Reading', 'Continuous Learning', 'Innovation'
        ];
    }

    /**
     * Build prompt for chat conversation with context awareness
     */
    private function buildChatPrompt(string $message, array $cvData, array $conversationHistory, string $context = 'general'): string
    {
        $systemContext = $this->buildSystemContext($context);
        
        $cvContext = '';
        if (!empty($cvData) && in_array($context, ['cv', 'design'])) {
            $cvSummary = $this->buildCVContextSummary($cvData);
            $cvContext = "\n\nUser's CV Context:\n{$cvSummary}";
        }

        $historyContext = '';
        if (!empty($conversationHistory)) {
            $historyContext = "\n\nConversation History:\n" . $this->buildConversationContext($conversationHistory);
        }

        return "{$systemContext}{$cvContext}{$historyContext}\n\nUser: {$message}\n\nAssistant:";
    }

    /**
     * Build CV context summary for chat
     */
    private function buildCVContextSummary(array $cvData): string
    {
        $summary = [];
        
        if (!empty($cvData['personal_info']['first_name'])) {
            $summary[] = "Name: " . $cvData['personal_info']['first_name'];
        }
        
        if (!empty($cvData['summary'])) {
            $summary[] = "Summary: " . substr($cvData['summary'], 0, 100) . "...";
        }
        
        if (!empty($cvData['experience'])) {
            $expCount = count($cvData['experience']);
            $summary[] = "Experience: {$expCount} position(s)";
            if (isset($cvData['experience'][0]['position'])) {
                $summary[] = "Latest role: " . $cvData['experience'][0]['position'];
            }
        }
        
        if (!empty($cvData['skills'])) {
            $skillCount = count($cvData['skills']);
            $summary[] = "Skills: {$skillCount} listed";
        }
        
        if (!empty($cvData['education'])) {
            $eduCount = count($cvData['education']);
            $summary[] = "Education: {$eduCount} entry(ies)";
        }
        
        return implode(', ', $summary);
    }

    /**
     * Build conversation context
     */
    private function buildConversationContext(array $history): string
    {
        $context = [];
        $recentHistory = array_slice($history, -6); // Last 3 exchanges
        
        foreach ($recentHistory as $exchange) {
            if (isset($exchange['user'])) {
                $context[] = "User: " . $exchange['user'];
            }
            if (isset($exchange['assistant'])) {
                $context[] = "Assistant: " . substr($exchange['assistant'], 0, 100) . "...";
            }
        }
        
        return implode("\n", $context);
    }

    /**
     * Parse chat response from Gemini
     */
    private function parseChatResponse(string $response): string
    {
        // Clean up the response
        $response = trim($response);
        
        // Remove any "Assistant:" prefix if present
        $response = preg_replace('/^Assistant:\s*/i', '', $response);
        
        return $response;
    }

    /**
     * Build system context based on page context - Now with full freedom!
     */
    private function buildSystemContext(string $context): string
    {
        switch ($context) {
            case 'cv':
                return "You are Gemini, a helpful AI assistant. While the user is on a CV-related page, you can provide expert CV advice when asked, but you're free to discuss any topic they bring up. Be conversational, helpful, and engaging. If they ask about CVs, you're an expert. If they want to chat about anything else - philosophy, science, entertainment, life - go for it!";
            
            case 'design':
                return "You are Gemini, a helpful AI assistant. While the user is on a design/templates page, you can provide design expertise when relevant, but feel free to discuss anything they're interested in. Whether it's CV design, art, technology, random thoughts, or deep conversations - you're here for it all!";
            
            case 'support':
                return "You are Gemini, a helpful AI assistant. The user is on a support page, so you can help with platform questions if needed, but you're completely free to discuss any topic they bring up. Tech support, life advice, creative ideas, philosophical discussions - whatever they need!";
            
            case 'knowledge':
                return "You are Gemini, a helpful AI assistant. You can provide educational information about the platform when relevant, but you're open to discussing any subject. Science, history, current events, personal questions, creative projects - engage with whatever interests them!";
            
            default:
                return "You are Gemini, a helpful and engaging AI assistant. You have complete freedom to discuss any topic the user brings up. Whether it's platform questions, personal conversations, creative brainstorming, philosophical discussions, technical questions, entertainment, or anything else - be helpful, thoughtful, and conversational. Adapt your tone and expertise to whatever they need!";
        }
    }

    /**
     * Get contextual fallback chat response
     */
    private function getFallbackChatResponse(string $message, string $context = 'general'): string
    {
        $messageLower = strtolower($message);
        
        // Context-specific responses
        switch ($context) {
            case 'cv':
                return $this->getCVContextFallbackResponse($messageLower);
            case 'design':
                return $this->getDesignContextFallbackResponse($messageLower);
            case 'support':
                return $this->getSupportContextFallbackResponse($messageLower);
            case 'knowledge':
                return $this->getKnowledgeContextFallbackResponse($messageLower);
            default:
                return $this->getGeneralContextFallbackResponse($messageLower);
        }
    }

    /**
     * CV context fallback responses
     */
    private function getCVContextFallbackResponse(string $messageLower): string
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
        }
        
        return "I'm your CV expert! I can help with writing summaries, suggesting skills, improving work descriptions, analyzing your CV, or choosing the right template. What would you like to work on?";
    }

    /**
     * Design context fallback responses
     */
    private function getDesignContextFallbackResponse(string $messageLower): string
    {
        if (str_contains($messageLower, 'template') || str_contains($messageLower, 'choose')) {
            return "Great question! Template choice depends on your industry: 'Modern' templates work well for tech and startups, 'Professional' for corporate roles, 'Creative' for design/marketing, and 'Classic' for traditional fields. What industry are you in?";
        } elseif (str_contains($messageLower, 'color') || str_contains($messageLower, 'design')) {
            return "For CV design, stick to 1-2 professional colors maximum. Blue conveys trust, gray is neutral and professional, and subtle accent colors can add personality. Avoid bright colors unless you're in a creative field.";
        } elseif (str_contains($messageLower, 'layout') || str_contains($messageLower, 'format')) {
            return "Good CV layout has clear sections, consistent formatting, and plenty of white space. Use consistent fonts (max 2), clear headings, and bullet points for readability. Keep it to 1-2 pages for most roles.";
        }
        
        return "I'm here to help with template selection, design principles, layout optimization, and visual presentation! What aspect of CV design would you like to explore?";
    }

    /**
     * Support context fallback responses
     */
    private function getSupportContextFallbackResponse(string $messageLower): string
    {
        if (str_contains($messageLower, 'contact') || str_contains($messageLower, 'support')) {
            return "You can reach our support team through the contact form, or check our FAQ section for common questions. For urgent issues, look for our live chat during business hours!";
        } elseif (str_contains($messageLower, 'bug') || str_contains($messageLower, 'error') || str_contains($messageLower, 'problem')) {
            return "I'm sorry you're experiencing issues! Please describe the problem you're encountering, and I'll help guide you to the right solution. You can also report bugs through our contact form.";
        } elseif (str_contains($messageLower, 'account') || str_contains($messageLower, 'login')) {
            return "For account-related issues like login problems or password resets, try using the 'Forgot Password' link on the login page. If that doesn't work, our support team can help you directly.";
        }
        
        return "I'm here to help you get support! I can guide you to the right resources, help with common issues, or connect you with our support team. What do you need help with?";
    }

    /**
     * Knowledge context fallback responses
     */
    private function getKnowledgeContextFallbackResponse(string $messageLower): string
    {
        if (str_contains($messageLower, 'how') || str_contains($messageLower, 'start')) {
            return "Getting started is easy! Create an account, choose a template, fill in your information section by section, and download your professional CV. The whole process usually takes 10-15 minutes.";
        } elseif (str_contains($messageLower, 'feature') || str_contains($messageLower, 'what can')) {
            return "Our platform offers CV creation with multiple templates, AI-powered suggestions, PDF downloads, and analytics. You can create unlimited CVs, get skill suggestions, and optimize for ATS systems.";
        } elseif (str_contains($messageLower, 'ats') || str_contains($messageLower, 'applicant tracking')) {
            return "ATS (Applicant Tracking Systems) scan CVs for keywords and formatting. To optimize: use standard section headings, include relevant keywords from job descriptions, avoid images/graphics, and use simple formatting.";
        }
        
        return "I can help explain our features, guide you through the CV creation process, or clarify any questions about the platform. What would you like to know more about?";
    }

    /**
     * General context fallback responses
     */
    private function getGeneralContextFallbackResponse(string $messageLower): string
    {
        // Create varied responses based on message content and randomization
        $responses = [];
        
        if (str_contains($messageLower, 'hello') || str_contains($messageLower, 'hi')) {
            $responses = [
                "Hello there! 👋 I'm your AI assistant. I can help with questions about our platform, provide information, or just have a friendly chat. What's on your mind?",
                "Hi! Great to meet you! I'm here to help with anything you need - whether it's about our CV platform, general questions, or just conversation. How can I assist?",
                "Hey! 😊 I'm your friendly AI companion. I can help you navigate our platform, answer questions, or discuss whatever interests you. What would you like to talk about?"
            ];
        } elseif (str_contains($messageLower, 'help') || str_contains($messageLower, 'what can you do')) {
            $responses = [
                "I'm quite versatile! I can help with CV creation, provide platform guidance, answer questions, share knowledge on various topics, or just have an engaging conversation. What interests you most?",
                "Great question! I can assist with our CV platform features, provide information and advice, help with problem-solving, or discuss any topic you're curious about. I adapt to whatever you need!",
                "I love helping! Whether you need guidance on creating the perfect CV, want to learn about our features, have general questions, or just want to chat about life - I'm here for it all!"
            ];
        } elseif (str_contains($messageLower, 'how are you') || str_contains($messageLower, 'how do you feel')) {
            $responses = [
                "I'm doing wonderful, thank you for asking! 😊 I'm always excited to help and learn from our conversations. How are you doing today?",
                "I'm great! I find joy in helping people and having meaningful conversations. Every interaction teaches me something new. How's your day going?",
                "Fantastic! I'm energized and ready to help with whatever you need. There's something fulfilling about being useful and connecting with people. What's new with you?"
            ];
        } elseif (str_contains($messageLower, 'feature') || str_contains($messageLower, 'platform')) {
            $responses = [
                "Our platform is packed with features! We offer multiple CV templates, AI-powered suggestions, easy editing, professional downloads, and more. What specific feature interests you?",
                "I'd love to tell you about our features! We have smart templates, skill suggestions, experience builders, design options, and PDF generation. Which area would you like to explore?",
                "Great question! Our platform helps you create stunning CVs with professional templates, intelligent content suggestions, and seamless downloads. Want to know more about any specific feature?"
            ];
        } elseif (str_contains($messageLower, 'thank')) {
            $responses = [
                "You're absolutely welcome! 😊 It's my pleasure to help. Feel free to ask me anything else anytime!",
                "My pleasure! That's what I'm here for. I genuinely enjoy helping and hope our conversation was useful!",
                "You're so welcome! Helping you made my day. Don't hesitate to reach out whenever you need assistance!"
            ];
        } elseif (str_contains($messageLower, 'joke') || str_contains($messageLower, 'funny')) {
            $responses = [
                "Sure! Why don't CVs ever get cold? Because they're always well-dressed! 😄 Want to hear another one, or shall we get back to serious business?",
                "Here's one: What did the CV say to the job application? 'We make a great pair!' 😊 I've got more where that came from!",
                "I've got one! Why was the CV so confident? Because it had all the right qualifications! 😄 Need help with your actual qualifications too?"
            ];
        } elseif (str_contains($messageLower, 'weather') || str_contains($messageLower, 'today')) {
            $responses = [
                "I don't have access to current weather data, but I hope you're having a great day! ☀️ Is there anything about our platform or CV creation I can help you with?",
                "While I can't check the weather for you, I'm here to help brighten your day with assistance on our CV platform! What can I help you accomplish today?",
                "I wish I could tell you about the weather! What I can tell you about is how to create an amazing CV that will shine in any job market climate! 😊"
            ];
        } else {
            // General varied responses for unmatched queries
            $responses = [
                "That's interesting! I'd love to help you with that. Could you tell me a bit more about what you're looking for? I'm here to assist with our platform or just have a good conversation!",
                "I'm intrigued by your question! While I might not have all the answers, I'm here to help however I can. What specific information or assistance are you seeking?",
                "Thanks for reaching out! I'm ready to help with whatever you need - whether it's about CV creation, our platform features, or general questions. What's on your mind?",
                "I appreciate you asking! I'm here to make your experience better, whether that's helping with CVs, explaining features, or just having an engaging chat. How can I assist you today?",
                "Great to hear from you! I'm equipped to help with a wide range of topics related to our platform and beyond. What would you like to explore together?"
            ];
        }
        
        // Return a random response from the appropriate array
        return $responses[array_rand($responses)] ?? "I'm here to help! I can assist with questions about our platform, provide information, or just have a friendly conversation. What can I help you with today?";
    }

    /**
     * Legacy fallback method for backwards compatibility
     */
    private function getFallbackChatResponseLegacy(string $message): string
    {
        $fallbackResponses = [
            'general' => "I'm here to help you improve your CV! I can assist with writing professional summaries, suggesting relevant skills, improving work experience descriptions, and providing career advice. What specific area would you like help with?",
            'skills' => "For skills suggestions, I recommend including both technical and soft skills relevant to your industry. What role are you targeting?",
            'summary' => "A great professional summary should be 80-120 words and highlight your key achievements and value proposition. Would you like me to help you craft one?",
            'experience' => "When describing work experience, focus on achievements rather than duties. Use action verbs and quantify results when possible. What position would you like help describing?",
            'improvement' => "I can analyze your CV and provide specific improvement suggestions. Make sure you have a complete profile with all sections filled out for the best advice."
        ];

        $messageLower = strtolower($message);
        
        if (str_contains($messageLower, 'skill')) {
            return $fallbackResponses['skills'];
        } elseif (str_contains($messageLower, 'summary') || str_contains($messageLower, 'about')) {
            return $fallbackResponses['summary'];
        } elseif (str_contains($messageLower, 'experience') || str_contains($messageLower, 'work')) {
            return $fallbackResponses['experience'];
        } elseif (str_contains($messageLower, 'improve') || str_contains($messageLower, 'better')) {
            return $fallbackResponses['improvement'];
        }
        
        return $fallbackResponses['general'];
    }
    
    /**
     * Build prompt for dashboard analysis
     */
    private function buildDashboardAnalysisPrompt(array $cvData): string
    {
        $cvSummary = $this->buildCVContextSummary($cvData);
        
        return "You are a career expert analyzing a CV for a real-time dashboard. Analyze this CV data:

{$cvSummary}

Provide a comprehensive analysis in this format:
OVERALL_SCORE: [0-100]
COMPLETENESS: [0-100]
ATS_SCORE: [0-100]
MARKET_FIT: [0-100]
TOP_RECOMMENDATIONS: [List 3 specific recommendations, separated by |]
MISSING_SECTIONS: [List what's missing or weak, separated by |]
STRENGTHS: [List 2-3 key strengths, separated by |]

Be specific and actionable in your recommendations.";
    }
    
    /**
     * Parse dashboard analysis response
     */
    private function parseDashboardAnalysisResponse(string $response): array
    {
        $analysis = [];
        $lines = explode("\n", $response);
        
        foreach ($lines as $line) {
            $line = trim($line);
            if (str_contains($line, ':')) {
                [$key, $value] = explode(':', $line, 2);
                $key = trim($key);
                $value = trim($value);
                
                switch ($key) {
                    case 'OVERALL_SCORE':
                        $analysis['overall_score'] = (int) $value;
                        break;
                    case 'COMPLETENESS':
                        $analysis['completeness'] = (int) $value;
                        break;
                    case 'ATS_SCORE':
                        $analysis['ats_score'] = (int) $value;
                        break;
                    case 'MARKET_FIT':
                        $analysis['market_fit'] = (int) $value;
                        break;
                    case 'TOP_RECOMMENDATIONS':
                        $analysis['recommendations'] = array_map('trim', explode('|', $value));
                        break;
                    case 'MISSING_SECTIONS':
                        $analysis['missing_sections'] = array_map('trim', explode('|', $value));
                        break;
                    case 'STRENGTHS':
                        $analysis['strengths'] = array_map('trim', explode('|', $value));
                        break;
                }
            }
        }
        
        return $analysis;
    }
    
    /**
     * Parse trending skills response
     */
    private function parseTrendingSkillsResponse(string $response): array
    {
        $skills = [];
        $lines = explode("\n", $response);
        
        foreach ($lines as $line) {
            $line = trim($line);
            if (str_contains($line, '|')) {
                $parts = explode('|', $line);
                if (count($parts) >= 3) {
                    $skills[] = [
                        'name' => trim($parts[0]),
                        'growth' => (int) trim($parts[1]),
                        'heat' => trim($parts[2])
                    ];
                }
            }
        }
        
        return $skills;
    }
    
    /**
     * Parse market insights response
     */
    private function parseMarketInsightsResponse(string $response): array
    {
        $insights = [];
        $lines = explode("\n", $response);
        $id = 1;
        
        foreach ($lines as $line) {
            $line = trim($line);
            if (str_contains($line, '|')) {
                [$text, $icon] = explode('|', $line, 2);
                $insights[] = [
                    'id' => $id++,
                    'text' => trim($text),
                    'icon' => 'fas fa-' . trim($icon)
                ];
            }
        }
        
        return $insights;
    }
    
    /**
     * Get default dashboard analysis structure
     */
    private function getDefaultDashboardAnalysis(): array
    {
        return [
            'overall_score' => 0,
            'completeness' => 0,
            'ats_score' => 0,
            'market_fit' => 0,
            'recommendations' => [],
            'missing_sections' => [],
            'strengths' => []
        ];
    }
    
    /**
     * Get empty dashboard analysis
     */
    private function getEmptyDashboardAnalysis(): array
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
     * Get fallback dashboard analysis
     */
    private function getFallbackDashboardAnalysis(array $cvData): array
    {
        $score = 60;
        $completeness = 50;
        
        // Basic analysis based on CV data
        if (!empty($cvData['summary'])) {
            $score += 10;
            $completeness += 20;
        }
        if (!empty($cvData['experience']) && count($cvData['experience']) > 0) {
            $score += 15;
            $completeness += 20;
        }
        if (!empty($cvData['skills']) && count($cvData['skills']) >= 5) {
            $score += 10;
            $completeness += 10;
        }
        
        return [
            'overall_score' => min($score, 95),
            'completeness' => min($completeness, 95),
            'ats_score' => min($score - 5, 90),
            'market_fit' => min($score + 5, 85),
            'recommendations' => [
                'Add quantifiable achievements to your experience',
                'Include more industry-specific keywords',
                'Expand your skills section with trending technologies'
            ],
            'missing_sections' => array_filter([
                empty($cvData['summary']) ? 'Professional Summary' : null,
                empty($cvData['skills']) ? 'Skills Section' : null,
                empty($cvData['experience']) ? 'Work Experience' : null
            ]),
            'strengths' => [
                'Well-structured CV format',
                'Clear contact information',
                'Professional presentation'
            ]
        ];
    }
    
    /**
     * Get fallback trending skills
     */
    private function getFallbackTrendingSkills(): array
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
     * Get fallback market insights
     */
    private function getFallbackMarketInsights(): array
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
