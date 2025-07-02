import axios from 'axios'

/**
 * Enhanced AI Service for CV Creation
 * Provides intelligent suggestions and insights for professional CV building
 */
class AIService {
  constructor() {
    this.baseUrl = '/api/ai'
    this.cache = new Map()
    this.retryCount = 3
    this.timeout = 30000 // Increased to 30 seconds for more robust AI responses
    this.industryInsights = this.loadIndustryInsights()
    this.personalityPrompts = this.loadPersonalityPrompts()
    this.currentLanguage = 'en'
  }

  setLanguage(language) {
    this.currentLanguage = language
  }

  // Skill suggestions based on job title
  static getSkillSuggestions(jobTitle, industry = '') {
    const skillDatabase = {
      'software engineer': [
        'JavaScript', 'Python', 'React', 'Node.js', 'Git', 'SQL', 'TypeScript', 
        'REST APIs', 'Docker', 'AWS', 'Problem Solving', 'Team Collaboration'
      ],
      'product designer': [
        'Figma', 'Adobe Creative Suite', 'User Research', 'Prototyping', 
        'Wireframing', 'UI/UX Design', 'Design Systems', 'Sketch', 'InVision'
      ],
      'marketing manager': [
        'Digital Marketing', 'Google Analytics', 'SEO/SEM', 'Content Strategy',
        'Social Media Marketing', 'Email Marketing', 'Data Analysis', 'Leadership'
      ],
      'data scientist': [
        'Python', 'R', 'SQL', 'Machine Learning', 'Statistics', 'Pandas', 
        'TensorFlow', 'Data Visualization', 'Jupyter', 'Git'
      ],
      'project manager': [
        'Project Planning', 'Agile/Scrum', 'Risk Management', 'Stakeholder Management',
        'Jira', 'MS Project', 'Leadership', 'Communication', 'Budget Management'
      ]
    };

    const normalizedTitle = jobTitle.toLowerCase();
    const exactMatch = skillDatabase[normalizedTitle];
    
    if (exactMatch) {
      return exactMatch;
    }

    // Fuzzy matching for partial job titles
    for (const [title, skills] of Object.entries(skillDatabase)) {
      if (normalizedTitle.includes(title) || title.includes(normalizedTitle)) {
        return skills;
      }
    }

    // Default general skills
    return [
      'Communication', 'Problem Solving', 'Team Collaboration', 'Time Management',
      'Leadership', 'Critical Thinking', 'Adaptability', 'Project Management'
    ];
  }

  // Professional summary suggestions
  static generateSummaryTemplate(personalInfo, experience = []) {
    const templates = {
      experienced: (role, years) => 
        `Experienced ${role} with ${years}+ years of expertise in developing innovative solutions and leading high-impact projects. Proven track record of delivering results in fast-paced environments while collaborating effectively with cross-functional teams.`,
      
      entry_level: (role) => 
        `Recent graduate and aspiring ${role} passionate about applying theoretical knowledge to real-world challenges. Strong foundation in core technologies and eager to contribute to innovative projects while continuously learning and growing.`,
      
      career_change: (role) => 
        `Professional transitioning to ${role} with transferable skills in problem-solving, project management, and team collaboration. Combining diverse background with fresh perspective to drive innovation and deliver exceptional results.`,
      
      senior: (role, years) => 
        `Senior ${role} with ${years}+ years of leadership experience in architecting scalable solutions and mentoring development teams. Expertise in strategic planning and implementing best practices to drive organizational success.`
    };

    // Determine experience level
    const experienceYears = this.calculateExperienceYears(experience);
    const primaryRole = this.extractPrimaryRole(experience, personalInfo);

    if (experienceYears >= 8) {
      return templates.senior(primaryRole, experienceYears);
    } else if (experienceYears >= 3) {
      return templates.experienced(primaryRole, experienceYears);
    } else if (experienceYears >= 1) {
      return templates.entry_level(primaryRole);
    } else {
      return templates.career_change(primaryRole);
    }
  }

  // Work experience bullet point suggestions
  static getExperienceSuggestions(jobTitle, company = '') {
    const suggestions = {
      'software engineer': [
        'Developed and maintained scalable web applications using modern technologies',
        'Collaborated with cross-functional teams to deliver high-quality software solutions',
        'Implemented automated testing procedures to ensure code quality and reliability',
        'Optimized application performance resulting in 30% improvement in load times',
        'Participated in code reviews and mentored junior developers'
      ],
      'product designer': [
        'Designed user-centered interfaces for web and mobile applications',
        'Conducted user research and usability testing to inform design decisions',
        'Created comprehensive design systems and style guides',
        'Collaborated with development teams to ensure design feasibility',
        'Improved user engagement by 25% through strategic design improvements'
      ],
      'marketing manager': [
        'Developed and executed comprehensive marketing strategies across multiple channels',
        'Managed marketing budget of $X and achieved Y% ROI on campaigns',
        'Led cross-functional teams to launch successful product marketing initiatives',
        'Analyzed market trends and competitor activities to identify opportunities',
        'Increased brand awareness by X% through targeted digital marketing campaigns'
      ],
      'project manager': [
        'Successfully managed multiple projects with budgets exceeding $X',
        'Led cross-functional teams of X+ members to deliver projects on time and within budget',
        'Implemented Agile methodologies resulting in 20% improvement in team productivity',
        'Developed project timelines, resource allocation plans, and risk mitigation strategies',
        'Facilitated stakeholder communication and maintained project documentation'
      ]
    };

    const normalizedTitle = jobTitle.toLowerCase();
    return suggestions[normalizedTitle] || [
      'Contributed to team objectives and organizational goals',
      'Collaborated effectively with colleagues and stakeholders',
      'Maintained high standards of professional performance',
      'Adapted quickly to changing priorities and requirements',
      'Demonstrated strong problem-solving and analytical skills'
    ];
  }

  // Helper methods
  static calculateExperienceYears(experience = []) {
    if (!experience.length) return 0;
    
    let totalMonths = 0;
    for (const exp of experience) {
      if (exp.startDate) {
        const start = new Date(exp.startDate);
        const end = exp.endDate ? new Date(exp.endDate) : new Date();
        const months = (end.getFullYear() - start.getFullYear()) * 12 + 
                      (end.getMonth() - start.getMonth());
        totalMonths += Math.max(0, months);
      }
    }
    
    return Math.floor(totalMonths / 12);
  }

  static extractPrimaryRole(experience = [], personalInfo = {}) {
    if (experience.length > 0 && experience[0].title) {
      return experience[0].title.toLowerCase();
    }
    
    // Fallback to generic role based on common patterns
    return 'professional';
  }

  // Interest suggestions based on profession
  static getInterestSuggestions(jobTitle) {
    const interestDatabase = {
      'software engineer': [
        'Open Source Contributing', 'Technology Blogging', 'Hackathons', 
        'Gaming', 'Robotics', 'AI/Machine Learning', 'Cryptocurrency'
      ],
      'product designer': [
        'Photography', 'Art & Design', 'User Experience Research', 
        'Typography', 'Digital Art', 'Design Conferences', 'Creative Writing'
      ],
      'marketing manager': [
        'Digital Trends', 'Content Creation', 'Social Media', 'Public Speaking',
        'Market Research', 'Brand Strategy', 'Creative Writing'
      ],
      'data scientist': [
        'Machine Learning Research', 'Statistical Analysis', 'Data Visualization',
        'Research Papers', 'Mathematics', 'Programming Challenges', 'Analytics'
      ]
    };

    const normalizedTitle = jobTitle.toLowerCase();
    for (const [title, interests] of Object.entries(interestDatabase)) {
      if (normalizedTitle.includes(title) || title.includes(normalizedTitle)) {
        return interests;
      }
    }

    // General interests
    return [
      'Reading', 'Travel', 'Fitness', 'Music', 'Volunteering', 
      'Learning New Technologies', 'Networking', 'Continuous Learning'
    ];
  }

  // CV optimization suggestions
  static analyzeCV(cvData) {
    const suggestions = [];
    const issues = [];

    // Check completeness
    if (!cvData.summary || cvData.summary.length < 50) {
      issues.push({
        type: 'warning',
        title: 'Professional Summary Too Short',
        description: 'Your professional summary should be at least 2-3 sentences to make a strong impression.',
        action: 'Expand your summary to 50-100 words'
      });
    }

    if (cvData.experience.length === 0) {
      issues.push({
        type: 'error',
        title: 'No Work Experience',
        description: 'Adding work experience will significantly strengthen your CV.',
        action: 'Add at least one work experience entry'
      });
    }

    if (cvData.skills.length < 5) {
      suggestions.push({
        type: 'suggestion',
        title: 'Add More Skills',
        description: 'CVs with 8-12 skills get 40% more views than those with fewer skills.',
        action: 'Add 3-5 more relevant skills'
      });
    }

    // Check for missing dates
    const incompleteExperience = cvData.experience.filter(exp => !exp.startDate);
    if (incompleteExperience.length > 0) {
      issues.push({
        type: 'warning',
        title: 'Missing Experience Dates',
        description: 'Some work experiences are missing start dates.',
        action: 'Add dates to all work experiences'
      });
    }

    return {
      score: this.calculateCVScore(cvData),
      suggestions,
      issues,
      completeness: this.calculateCompleteness(cvData)
    };
  }

  static calculateCVScore(cvData) {
    let score = 0;

    // Professional summary (20 points)
    if (cvData.summary && cvData.summary.length >= 50) score += 20;
    else if (cvData.summary && cvData.summary.length >= 20) score += 10;

    // Work experience (30 points)
    if (cvData.experience.length >= 3) score += 30;
    else if (cvData.experience.length >= 2) score += 20;
    else if (cvData.experience.length >= 1) score += 10;

    // Education (15 points)
    if (cvData.education.length >= 1) score += 15;

    // Skills (20 points)
    if (cvData.skills.length >= 8) score += 20;
    else if (cvData.skills.length >= 5) score += 15;
    else if (cvData.skills.length >= 3) score += 10;

    // Interests (10 points)
    if (cvData.interests.length >= 3) score += 10;
    else if (cvData.interests.length >= 1) score += 5;

    // Personal info completeness (5 points)
    const personalFields = ['firstName', 'lastName', 'email', 'phone'];
    const completedFields = personalFields.filter(field => 
      cvData.personalInfo && cvData.personalInfo[field]
    ).length;
    score += (completedFields / personalFields.length) * 5;

    return Math.round(score);
  }

  static calculateCompleteness(cvData) {
    const sections = [
      { name: 'Personal Info', completed: !!cvData.personalInfo?.firstName && !!cvData.personalInfo?.email },
      { name: 'Professional Summary', completed: !!cvData.summary && cvData.summary.length >= 20 },
      { name: 'Work Experience', completed: cvData.experience.length > 0 },
      { name: 'Education', completed: cvData.education.length > 0 },
      { name: 'Skills', completed: cvData.skills.length >= 3 },
      { name: 'Interests', completed: cvData.interests.length > 0 }
    ];

    const completedSections = sections.filter(section => section.completed).length;
    return Math.round((completedSections / sections.length) * 100);
  }

  /**
   * Generate professional summaries using AI backend
   */
  async generateSummary(data) {
    const cacheKey = `ai_summary_${JSON.stringify(data).substring(0, 50)}`
    
    // Check cache first
    if (this.cache.has(cacheKey)) {
      return this.cache.get(cacheKey)
    }

    try {
      const { personalInfo, experience, education, skills = [] } = data
      
      // Get the most recent/relevant job
      const primaryJob = experience?.length > 0 ? experience[0] : null
      const jobTitle = primaryJob?.position || data.jobTitle || 'Professional'
      const yearsExperience = this.calculateYearsOfExperience(experience || [])
      
      // Prepare data for backend
      const requestData = {
        job_title: jobTitle,
        experience_years: yearsExperience,
        skills: skills.map(skill => typeof skill === 'object' ? skill.name : skill),
        industry: data.industry || this.detectIndustry(jobTitle),
        language: this.currentLanguage,
        personal_info: {
          first_name: personalInfo?.firstName || '',
          last_name: personalInfo?.lastName || '',
          email: personalInfo?.email || ''
        }
      }

      const response = await this.makeRequest('/generate-summary', {
        method: 'POST',
        body: requestData
      })

      if (response.success && response.summaries) {
        const result = {
          summaries: response.summaries,
          source: response.source,
          metadata: response.metadata
        }
        
        // Cache the result
        this.cache.set(cacheKey, result)
        return result
      }

      throw new Error(response.error || 'Failed to generate summary')
    } catch (error) {
      console.error('AI Summary generation failed:', error)
      
      // Fallback to client-side generation
      return this.generateFallbackSummary(data)
    }
  }

  /**
   * Generate contextual summary based on actual user data
   */
  generateContextualSummary({ jobTitle, company, yearsExperience, firstName, education, experience }) {
    const experienceLevel = this.getExperienceLevel(yearsExperience)
    const industry = this.detectIndustry(jobTitle, company)
    const skills = this.extractSkillsFromExperience(experience)
    
    // Create personalized summary templates
    const templates = {
      entry: [
        `${experienceLevel} ${jobTitle} with ${yearsExperience} year${yearsExperience !== 1 ? 's' : ''} of experience in ${industry}. ${education ? `${education.degree} graduate` : 'Self-motivated professional'} with hands-on experience in ${skills}. Eager to contribute to innovative projects and grow within a dynamic team environment.`,
        
        `Motivated ${jobTitle} with ${yearsExperience} year${yearsExperience !== 1 ? 's' : ''} of practical experience in ${industry}. Proven ability to learn quickly and adapt to new technologies. Strong foundation in ${skills} with a passion for delivering quality results.`
      ],
      
      mid: [
        `Experienced ${jobTitle} with ${yearsExperience} years of proven success in ${industry}. ${education ? `${education.degree} background combined with` : 'Strong'} hands-on expertise in ${skills}. Track record of delivering high-quality solutions and collaborating effectively with cross-functional teams.`,
        
        `Results-driven ${jobTitle} with ${yearsExperience} years of experience driving success in ${industry}. Expertise in ${skills} with a demonstrated ability to manage complex projects and mentor junior team members. Committed to continuous learning and innovation.`
      ],
      
      senior: [
        `Senior ${jobTitle} with ${yearsExperience}+ years of leadership experience in ${industry}. Deep expertise in ${skills} with a proven track record of scaling teams and delivering strategic initiatives. ${education ? `${education.degree} education` : 'Extensive experience'} combined with strong business acumen and technical vision.`,
        
        `Accomplished ${jobTitle} with ${yearsExperience}+ years of experience leading high-performing teams in ${industry}. Expert-level knowledge in ${skills} with demonstrated success in driving organizational growth and innovation. Passionate about mentoring talent and building scalable solutions.`
      ]
    }
    
    const categoryTemplates = templates[experienceLevel] || templates.mid
    return categoryTemplates[Math.floor(Math.random() * categoryTemplates.length)]
  }

  /**
   * Get AI-powered skill suggestions for a specific job title
   */
  async getSkillSuggestions(jobTitle, industry = '', experienceLevel = 'intermediate') {
    const cacheKey = `ai_skills_${jobTitle}_${industry}_${experienceLevel}`
    
    // Check cache first
    if (this.cache.has(cacheKey)) {
      return this.cache.get(cacheKey)
    }

    try {
      const requestData = {
        job_title: jobTitle,
        industry: industry,
        experience_level: experienceLevel,
        language: this.currentLanguage
      }

      const response = await this.makeRequest('/skills-suggestions', {
        method: 'POST',
        body: requestData
      })

      if (response.success && response.skills) {
        const result = {
          skills: response.skills,
          source: response.source,
          metadata: response.metadata
        }
        
        // Cache the result
        this.cache.set(cacheKey, result)
        return result
      }

      throw new Error(response.error || 'Failed to get skill suggestions')
    } catch (error) {
      console.error('AI Skills generation failed:', error)
      
      // Fallback to client-side generation
      return {
        skills: this.getSkillSuggestionsFallback(jobTitle),
        source: 'client_fallback'
      }
    }
  }

  /**
   * Get AI-powered work experience suggestions
   */
  async getExperienceSuggestions(jobTitle, company = '', industry = '') {
    const cacheKey = `ai_experience_${jobTitle}_${company}_${industry}`
    
    // Check cache first
    if (this.cache.has(cacheKey)) {
      return this.cache.get(cacheKey)
    }

    try {
      const requestData = {
        job_title: jobTitle,
        company: company,
        industry: industry,
        language: this.currentLanguage
      }

      const response = await this.makeRequest('/experience-suggestions', {
        method: 'POST',
        body: requestData
      })

      if (response.success && response.suggestions) {
        const result = {
          suggestions: response.suggestions,
          source: response.source,
          metadata: response.metadata
        }
        
        // Cache the result
        this.cache.set(cacheKey, result)
        return result
      }

      throw new Error(response.error || 'Failed to get experience suggestions')
    } catch (error) {
      console.error('AI Experience generation failed:', error)
      
      // Fallback to client-side generation
      return {
        suggestions: this.getExperienceSuggestionsFallback(jobTitle),
        source: 'client_fallback'
      }
    }
  }

  /**
   * Get AI-powered interest suggestions
   */
  async getInterestSuggestions(jobTitle, industry = '') {
    const cacheKey = `ai_interests_${jobTitle}_${industry}`
    
    // Check cache first
    if (this.cache.has(cacheKey)) {
      return this.cache.get(cacheKey)
    }

    try {
      const requestData = {
        job_title: jobTitle,
        industry: industry,
        language: this.currentLanguage
      }

      const response = await this.makeRequest('/interest-suggestions', {
        method: 'POST',
        body: requestData
      })

      if (response.success && response.interests) {
        const result = {
          interests: response.interests,
          source: response.source,
          metadata: response.metadata
        }
        
        // Cache the result
        this.cache.set(cacheKey, result)
        return result
      }

      throw new Error(response.error || 'Failed to get interest suggestions')
    } catch (error) {
      console.error('AI Interest generation failed:', error)
      
      // Fallback to client-side generation
      return {
        interests: this.getInterestSuggestionsFallback(jobTitle),
        source: 'client_fallback'
      }
    }
  }

  /**
   * Comprehensive AI-powered CV analysis
   */
  async analyzeCV(cvData) {
    const cacheKey = `ai_analysis_${JSON.stringify(cvData).substring(0, 50)}`
    
    // Check cache first
    if (this.cache.has(cacheKey)) {
      return this.cache.get(cacheKey)
    }

    try {
      const requestData = {
        cv_data: cvData,
        language: this.currentLanguage
      }

      const response = await this.makeRequest('/analyze-cv', {
        method: 'POST',
        body: requestData
      })

      if (response.success && response.analysis) {
        const result = {
          analysis: response.analysis,
          insights: response.insights,
          source: response.source
        }
        
        // Cache the result
        this.cache.set(cacheKey, result)
        return result
      }

      throw new Error(response.error || 'Failed to analyze CV')
    } catch (error) {
      console.error('AI CV Analysis failed:', error)
      
      // Fallback to client-side analysis
      return {
        analysis: this.analyzeCVFallback(cvData),
        source: 'client_fallback'
      }
    }
  }

  /**
   * Enhance job description with AI or contextual improvements
   */
  async enhanceDescription(experience) {
    const { position, company, description } = experience
    
    try {
      // Try to get AI suggestions for the position
      const response = await this.getExperienceSuggestions(position, company)
      
      if (response.suggestions && response.suggestions.length > 0) {
        // Return a random suggestion from AI
        const randomSuggestion = response.suggestions[Math.floor(Math.random() * response.suggestions.length)]
        return { 
          description: randomSuggestion,
          source: response.source 
        }
      }
    } catch (error) {
      console.warn('AI enhancement failed, using fallback:', error)
    }
    
    if (!description || description.length < 10) {
      // Generate description from scratch
      return { description: this.generateJobDescription(position, company) }
    }
    
    // Enhance existing description
    const enhanced = this.enhanceExistingDescription(description, position)
    return { description: enhanced }
  }

  /**
   * Generate job description from position and company
   */
  generateJobDescription(position, company) {
    const normalizedPosition = position.toLowerCase()
    
    const templates = {
      'software developer': [
        `Developed and maintained web applications using modern technologies. Collaborated with cross-functional teams to deliver high-quality software solutions. Participated in code reviews and contributed to technical documentation.`,
        `Built responsive web applications and APIs serving thousands of users. Worked in an Agile environment to deliver features on schedule. Optimized application performance and maintained clean, maintainable code.`
      ],
      'project manager': [
        `Led cross-functional teams to deliver projects on time and within budget. Managed project scope, timeline, and resources while maintaining stakeholder communication. Implemented Agile methodologies to improve team efficiency.`,
        `Coordinated multiple projects simultaneously while ensuring quality deliverables. Facilitated team meetings and maintained project documentation. Successfully delivered projects resulting in improved business outcomes.`
      ],
      'marketing specialist': [
        `Developed and executed marketing campaigns across multiple channels. Analyzed campaign performance and optimized strategies based on data insights. Collaborated with creative teams to produce engaging content.`,
        `Managed social media presence and content marketing strategies. Conducted market research and competitor analysis to inform campaign decisions. Increased brand awareness and engagement through targeted initiatives.`
      ]
    }
    
    // Find best match or use generic template
    for (const [key, descriptions] of Object.entries(templates)) {
      if (normalizedPosition.includes(key) || key.includes(normalizedPosition)) {
        return descriptions[Math.floor(Math.random() * descriptions.length)]
      }
    }
    
    // Generic fallback
    return `Contributed to key initiatives and projects within the organization. Collaborated with team members to achieve departmental goals and objectives. Maintained high standards of quality and professionalism in all work activities.`
  }

  // Helper methods
  calculateYearsOfExperience(experience) {
    if (!experience || experience.length === 0) return 0
    
    let totalMonths = 0
    experience.forEach(exp => {
      if (exp.start_date) {
        const start = new Date(exp.start_date)
        const end = exp.end_date ? new Date(exp.end_date) : new Date()
        const months = (end.getFullYear() - start.getFullYear()) * 12 + (end.getMonth() - start.getMonth())
        totalMonths += Math.max(0, months)
      }
    })
    
    return Math.round(totalMonths / 12 * 10) / 10 // Round to 1 decimal
  }

  getExperienceLevel(years) {
    if (years <= 2) return 'entry'
    if (years <= 7) return 'mid'
    return 'senior'
  }

  detectIndustry(jobTitle, company) {
    const title = jobTitle.toLowerCase()
    const comp = company.toLowerCase()
    
    if (title.includes('developer') || title.includes('engineer') || title.includes('programmer')) {
      return 'technology'
    }
    if (title.includes('designer') || title.includes('creative')) {
      return 'design'
    }
    if (title.includes('marketing') || title.includes('content')) {
      return 'marketing'
    }
    if (title.includes('sales') || title.includes('account')) {
      return 'sales'
    }
    if (title.includes('analyst') || title.includes('data')) {
      return 'analytics'
    }
    if (title.includes('manager') || title.includes('director')) {
      return 'management'
    }
    
    return 'business'
  }

  extractSkillsFromExperience(experience) {
    const allSkills = experience.flatMap(exp => {
      const position = exp.position?.toLowerCase() || ''
      const description = exp.description?.toLowerCase() || ''
      const text = `${position} ${description}`
      
      const detectedSkills = []
      
      // Technical skills detection
      const techSkills = ['javascript', 'python', 'react', 'node', 'sql', 'java', 'html', 'css']
      techSkills.forEach(skill => {
        if (text.includes(skill)) detectedSkills.push(skill)
      })
      
      return detectedSkills
    })
    
    const uniqueSkills = [...new Set(allSkills)]
    return uniqueSkills.length > 0 ? uniqueSkills.join(', ') : 'various technologies and methodologies'
  }

  enhanceExistingDescription(description, position) {
    // Add action verbs and quantifiable results
    const actionVerbs = ['Developed', 'Implemented', 'Managed', 'Led', 'Created', 'Optimized', 'Delivered']
    const enhanced = description
      .replace(/^[a-z]/, match => match.toUpperCase()) // Capitalize first letter
      .replace(/\b(made|did|worked on)\b/gi, actionVerbs[Math.floor(Math.random() * actionVerbs.length)])
    
    return enhanced
  }

  // ============ FALLBACK METHODS ============

  /**
   * Generate professional summary using templates
   */
  generateFallbackSummary(userProfile) {
    const { personalInfo, experience, education } = userProfile
    const jobTitle = experience?.[0]?.position || 'Professional'
    const yearsExp = this.calculateYearsOfExperience(experience || [])
    const degree = education?.[0]?.degree || ''

    const templates = {
      entry: [
        `Motivated ${jobTitle} with ${yearsExp} year${yearsExp !== 1 ? 's' : ''} of experience and a strong foundation in ${degree ? degree.toLowerCase() : 'relevant field'}. Passionate about leveraging modern technologies and best practices to deliver high-quality results. Quick learner with excellent problem-solving abilities and a collaborative mindset.`,
        `Enthusiastic ${jobTitle} bringing ${yearsExp} year${yearsExp !== 1 ? 's' : ''} of hands-on experience and a commitment to excellence. Known for attention to detail, strong communication skills, and the ability to work effectively in fast-paced environments. Eager to contribute innovative solutions and grow professionally.`
      ],
      mid: [
        `Results-driven ${jobTitle} with ${yearsExp} years of progressive experience in delivering innovative solutions and leading cross-functional projects. Proven track record of improving processes, exceeding targets, and mentoring team members. Strong technical expertise combined with excellent stakeholder management skills.`,
        `Experienced ${jobTitle} with ${yearsExp} years of comprehensive experience in strategic planning and execution. Demonstrated ability to manage complex projects, drive digital transformation initiatives, and build high-performing teams. Committed to delivering measurable business impact through technology and innovation.`
      ],
      senior: [
        `Senior ${jobTitle} with ${yearsExp}+ years of extensive experience architecting enterprise-level solutions and leading organizational transformation. Strategic leader with deep technical expertise and proven ability to align technology initiatives with business objectives. Passionate about mentoring next-generation talent and driving innovation.`,
        `Accomplished ${jobTitle} bringing ${yearsExp}+ years of leadership experience in building scalable solutions and fostering culture of excellence. Expert in strategic planning, stakeholder management, and change management. Recognized for delivering complex projects on time and under budget while maintaining highest quality standards.`
      ]
    }

    let templateCategory = 'entry'
    if (yearsExp >= 10) templateCategory = 'senior'
    else if (yearsExp >= 4) templateCategory = 'mid'

    const categoryTemplates = templates[templateCategory]
    const randomTemplate = categoryTemplates[Math.floor(Math.random() * categoryTemplates.length)]
    
    return randomTemplate
  }

  /**
   * Enhance descriptions using intelligent patterns
   */
  enhanceDescriptionFallback(item, type) {
    if (!item.description || item.description.length < 10) {
      return this.generateDescriptionFromTitle(item, type)
    }

    const enhanced = item.description
      .replace(/\b(manage|lead|develop|create|implement|design)\b/gi, (match) => {
        const alternatives = {
          'manage': 'orchestrated and optimized',
          'lead': 'spearheaded and guided',
          'develop': 'architected and implemented',
          'create': 'designed and delivered',
          'implement': 'deployed and integrated',
          'design': 'conceptualized and engineered'
        }
        return alternatives[match.toLowerCase()] || match
      })

    return enhanced.charAt(0).toUpperCase() + enhanced.slice(1)
  }

  /**
   * Generate description from job title/position
   */
  generateDescriptionFromTitle(item, type) {
    const position = item.position || item.degree || ''
    const company = item.company || item.institution || ''

    if (type === 'experience') {
      const responsibilities = this.getResponsibilityTemplates(position)
      return responsibilities[Math.floor(Math.random() * responsibilities.length)]
        .replace('{company}', company)
    } else {
      return `Completed comprehensive ${position} program with focus on practical applications and industry-relevant skills. Gained strong foundation in theoretical concepts and hands-on experience through projects and coursework.`
    }
  }

  /**
   * Get responsibility templates by job category
   */
  getResponsibilityTemplates(position) {
    const positionLower = position.toLowerCase()
    
    if (positionLower.includes('developer') || positionLower.includes('engineer')) {
      return [
        'Designed and implemented scalable software solutions using modern technologies and best practices. Collaborated with cross-functional teams to deliver high-quality products on schedule. Participated in code reviews and contributed to technical documentation.',
        'Developed robust applications with focus on performance optimization and user experience. Worked closely with stakeholders to gather requirements and translate them into technical specifications. Maintained and enhanced existing systems while ensuring code quality.',
        'Built and deployed enterprise-level applications using agile methodologies. Mentored junior developers and contributed to architectural decisions. Implemented automated testing and CI/CD pipelines to improve development efficiency.'
      ]
    }
    
    if (positionLower.includes('manager') || positionLower.includes('lead')) {
      return [
        'Led cross-functional teams to achieve strategic objectives and deliver exceptional results. Developed and implemented operational processes that improved efficiency by significant margins. Managed stakeholder relationships and ensured alignment with business goals.',
        'Oversaw daily operations and provided strategic direction for team development. Created comprehensive project plans and managed resource allocation to meet deadlines. Fostered collaborative environment and promoted professional growth among team members.',
        'Directed organizational initiatives and drove continuous improvement across departments. Established key performance indicators and implemented monitoring systems. Built strong partnerships with internal and external stakeholders to achieve mutual success.'
      ]
    }
    
    if (positionLower.includes('analyst') || positionLower.includes('consultant')) {
      return [
        'Conducted comprehensive analysis of business processes and identified optimization opportunities. Prepared detailed reports and presentations for executive leadership. Collaborated with stakeholders to implement data-driven solutions and improvements.',
        'Analyzed complex data sets and provided actionable insights to support strategic decision-making. Developed analytical models and dashboard solutions for performance monitoring. Worked closely with teams to ensure successful implementation of recommendations.',
        'Performed in-depth research and analysis to support business transformation initiatives. Created comprehensive documentation and delivered presentations to various audiences. Facilitated workshops and provided expert consultation on best practices.'
      ]
    }
    
    // Generic template for other positions
    return [
      'Contributed to organizational success through dedicated performance and collaborative approach. Maintained high standards of quality while meeting deadlines and exceeding expectations. Developed strong relationships with colleagues and stakeholders across all levels.',
      'Executed assigned responsibilities with precision and attention to detail. Participated in team projects and provided valuable input to achieve common goals. Demonstrated adaptability and willingness to take on additional challenges as needed.',
      'Supported operational excellence through consistent delivery of high-quality work. Communicated effectively with team members and stakeholders to ensure smooth project execution. Continuously sought opportunities for professional development and skill enhancement.'
    ]
  }

  /**
   * Get skill suggestions based on job title analysis (fallback method)
   */
  getSkillSuggestionsFallback(jobTitle) {
    const titleLower = jobTitle.toLowerCase()
    
    const skillMaps = {
      'software': ['JavaScript', 'Python', 'React', 'Node.js', 'SQL', 'Git', 'Docker', 'AWS', 'TypeScript', 'Vue.js'],
      'web': ['HTML/CSS', 'JavaScript', 'React', 'Vue.js', 'Responsive Design', 'REST APIs', 'Git', 'Webpack'],
      'mobile': ['React Native', 'Flutter', 'iOS Development', 'Android Development', 'Mobile UI/UX', 'App Store Optimization'],
      'data': ['Python', 'R', 'SQL', 'Tableau', 'Power BI', 'Machine Learning', 'Statistics', 'Excel', 'Data Visualization'],
      'design': ['Figma', 'Adobe Creative Suite', 'Sketch', 'Prototyping', 'User Research', 'UI/UX Design', 'Wireframing'],
      'marketing': ['Google Analytics', 'SEO/SEM', 'Content Marketing', 'Social Media Marketing', 'Email Marketing', 'PPC'],
      'project': ['Agile/Scrum', 'Jira', 'Project Planning', 'Risk Management', 'Stakeholder Management', 'Budget Management'],
      'sales': ['CRM Software', 'Lead Generation', 'Negotiation', 'Relationship Building', 'Sales Analytics', 'Pipeline Management'],
      'finance': ['Financial Analysis', 'Excel', 'QuickBooks', 'Financial Modeling', 'Budgeting', 'Risk Assessment'],
      'hr': ['Talent Acquisition', 'Performance Management', 'HRIS', 'Employee Relations', 'Training & Development']
    }
    
    // Find matching skill category
    for (const [category, skills] of Object.entries(skillMaps)) {
      if (titleLower.includes(category) || 
          titleLower.includes(category.slice(0, -1)) || // Remove 's' for singular forms
          (category === 'software' && (titleLower.includes('developer') || titleLower.includes('engineer'))) ||
          (category === 'project' && titleLower.includes('manager'))) {
        return skills
      }
    }
    
    // Return general professional skills if no specific match
    return ['Communication', 'Problem Solving', 'Team Collaboration', 'Time Management', 'Leadership', 'Critical Thinking']
  }

  /**
   * Get experience suggestions fallback
   */
  getExperienceSuggestionsFallback(jobTitle) {
    const titleLower = jobTitle.toLowerCase()
    
    const experienceMap = {
      'software': [
        'Developed and maintained web applications using modern frameworks',
        'Collaborated with cross-functional teams to deliver software solutions',
        'Implemented automated testing procedures to ensure code quality',
        'Optimized application performance and resolved technical issues'
      ],
      'designer': [
        'Created user-centered designs for web and mobile applications',
        'Conducted user research to inform design decisions',
        'Developed design systems and style guides',
        'Collaborated with development teams to ensure design implementation'
      ],
      'manager': [
        'Led cross-functional teams to achieve project objectives',
        'Developed and implemented strategic initiatives',
        'Managed budgets and resource allocation',
        'Facilitated stakeholder communication and collaboration'
      ],
      'marketing': [
        'Developed and executed marketing campaigns across multiple channels',
        'Analyzed campaign performance and optimized strategies',
        'Managed social media presence and content marketing',
        'Conducted market research and competitor analysis'
      ]
    }
    
    for (const [key, experiences] of Object.entries(experienceMap)) {
      if (titleLower.includes(key)) {
        return experiences
      }
    }
    
    return [
      'Contributed to organizational objectives and team goals',
      'Collaborated effectively with colleagues and stakeholders',
      'Maintained high standards of professional performance',
      'Demonstrated strong problem-solving and communication skills'
    ]
  }

  /**
   * Get interest suggestions fallback
   */
  getInterestSuggestionsFallback(jobTitle) {
    const titleLower = jobTitle.toLowerCase()
    
    const interestMap = {
      'software': ['Open Source Projects', 'Technology Conferences', 'Coding Challenges', 'AI Research'],
      'designer': ['Design Thinking', 'User Experience', 'Creative Arts', 'Design Conferences'],
      'marketing': ['Digital Marketing Trends', 'Content Creation', 'Brand Strategy', 'Market Research'],
      'manager': ['Leadership Development', 'Strategic Planning', 'Team Building', 'Professional Networking'],
      'data': ['Machine Learning', 'Data Visualization', 'Analytics', 'Research Publications']
    }
    
    for (const [key, interests] of Object.entries(interestMap)) {
      if (titleLower.includes(key)) {
        return interests
      }
    }
    
    return ['Professional Development', 'Continuous Learning', 'Industry Trends', 'Team Sports', 'Reading', 'Travel']
  }

  /**
   * Client-side CV analysis fallback
   */
  analyzeCVFallback(cvData) {
    const analysis = {
      score: 0,
      completeness: 0,
      ats_score: 0,
      strengths: [],
      weaknesses: [],
      recommendations: [],
      sections_analysis: {}
    }

    // Calculate basic scores
    let totalSections = 0
    let completedSections = 0

    // Personal info check
    if (cvData.personalInfo?.firstName && cvData.personalInfo?.email) {
      analysis.strengths.push('Complete contact information')
      completedSections++
    } else {
      analysis.weaknesses.push('Missing essential contact information')
    }
    totalSections++

    // Summary check
    if (cvData.summary && cvData.summary.length >= 50) {
      analysis.strengths.push('Professional summary provided')
      completedSections++
    } else {
      analysis.weaknesses.push('Professional summary needs improvement')
      analysis.recommendations.push('Add a compelling 80-120 word professional summary')
    }
    totalSections++

    // Experience check
    if (cvData.experience && cvData.experience.length > 0) {
      analysis.strengths.push('Work experience included')
      completedSections++
    } else {
      analysis.weaknesses.push('No work experience provided')
      analysis.recommendations.push('Add relevant work experience')
    }
    totalSections++

    // Skills check
    if (cvData.skills && cvData.skills.length >= 3) {
      analysis.strengths.push('Skills section completed')
      completedSections++
    } else {
      analysis.weaknesses.push('Skills section needs more entries')
      analysis.recommendations.push('Add 5-8 relevant skills')
    }
    totalSections++

    // Education check
    if (cvData.education && cvData.education.length > 0) {
      analysis.strengths.push('Educational background included')
      completedSections++
    }
    totalSections++

    // Calculate scores
    analysis.completeness = Math.round((completedSections / totalSections) * 100)
    analysis.score = analysis.completeness
    analysis.ats_score = Math.max(50, analysis.completeness - 10) // ATS score slightly lower

    return analysis
  }

  /**
   * Send chat message to AI service with context awareness
   */
  async sendChatMessage(message, cvData = {}, conversationHistory = [], context = 'general') {
    const cacheKey = `chat_${context}_${message.substring(0, 50)}_${conversationHistory.length}`
    
    // No caching for chat messages to ensure real-time interaction
    
    try {
      const requestData = {
        message: message,
        cv_data: cvData,
        conversation_history: conversationHistory,
        context: context,
        language: this.currentLanguage // Corrected: Access as property, not a function
      }

      const response = await this.makeRequest('/chat', {
        method: 'POST',
        body: requestData
      })

      if (response.success && response.response) {
        return {
          response: response.response,
          source: response.source,
          timestamp: response.timestamp,
          context: response.context
        }
      }

      throw new Error(response.error || 'Failed to get AI chat response')
    } catch (error) {
      console.error('AI Chat Service Error:', error)
      return {
        response: this.getContextualFallbackResponse(message, context),
        source: 'client_fallback',
        timestamp: new Date().toISOString()
      }
    }
  }

  /**
   * Get dashboard AI analysis
   */
  async getDashboardAnalysis(cvData = null) {
    try {
      const params = cvData ? { cv_data: cvData } : {}
      const response = await this.makeRequest('/ai/dashboard-analysis', {
        method: 'GET',
        params
      })

      if (response.success) {
        return response.analysis
      }

      throw new Error(response.error || 'Failed to get dashboard analysis')
    } catch (error) {
      console.error('Dashboard Analysis error:', error)
      
      // Return fallback analysis
      return {
        overall_score: 15,
        completeness: 10,
        ats_score: 20,
        market_fit: 25,
        recommendations: [
          'Create your first CV to get personalized insights',
          'Add your professional experience and skills',
          'Complete your profile for better recommendations'
        ],
        missing_sections: ['CV not created yet'],
        strengths: ['Ready to start your professional journey']
      }
    }
  }
  
  /**
   * Get trending skills for dashboard
   */
  async getTrendingSkills(industry = '', limit = 6) {
    try {
      const response = await this.makeRequest('/ai/trending-skills', {
        method: 'GET',
        params: { industry, limit }
      })

      if (response.success) {
        return response.skills
      }

      throw new Error(response.error || 'Failed to get trending skills')
    } catch (error) {
      console.error('Trending Skills error:', error)
      
      // Return fallback skills
      return [
        { name: 'AI/ML', growth: 156, heat: 'blazing' },
        { name: 'Cloud Computing', growth: 134, heat: 'blazing' },
        { name: 'Cybersecurity', growth: 112, heat: 'hot' },
        { name: 'React/Vue.js', growth: 89, heat: 'hot' },
        { name: 'DevOps', growth: 78, heat: 'warm' },
        { name: 'Data Analytics', growth: 67, heat: 'warm' }
      ]
    }
  }
  
  /**
   * Get market insights for dashboard
   */
  async getMarketInsights(industry = '') {
    try {
      const response = await this.makeRequest('/ai/market-insights', {
        method: 'GET',
        params: { industry }
      })

      if (response.success) {
        return response.insights
      }

      throw new Error(response.error || 'Failed to get market insights')
    } catch (error) {
      console.error('Market Insights error:', error)
      
      // Return fallback insights
      return [
        {
          id: 1,
          text: 'Remote skills are 89% more in demand than pre-2020',
          icon: 'fas fa-home'
        },
        {
          id: 2,
          text: 'AI skills can increase salary potential by up to 40%',
          icon: 'fas fa-chart-line'
        },
        {
          id: 3,
          text: 'Soft skills now weight 45% in technical hiring decisions',
          icon: 'fas fa-handshake'
        }
      ]
    }
  }
  
  /**
   * Get AI connection status
   */
  async getConnectionStatus() {
    try {
      const response = await this.makeRequest('/ai/connection-status', {
        method: 'GET'
      })

      if (response.success) {
        return response.status
      }

      throw new Error(response.error || 'Failed to get connection status')
    } catch (error) {
      console.error('Connection Status error:', error)
      
      // Return fallback status
      return {
        is_connected: false,
        has_api_key: false,
        last_error: error.message || 'Unable to connect to AI service',
        model: 'gemini-pro',
        api_key_masked: 'Not configured'
      }
    }
  }

  /**
   * Get contextual fallback response based on current page context
   */
  getContextualFallbackResponse(message, context) {
    const messageLower = message.toLowerCase()
    
    // Context-specific responses
    switch (context) {
      case 'cv':
        return this.getCVContextFallback(messageLower)
      case 'design':
        return this.getDesignContextFallback(messageLower)
      case 'support':
        return this.getSupportContextFallback(messageLower)
      case 'knowledge':
        return this.getKnowledgeContextFallback(messageLower)
      default:
        return this.getGeneralContextFallback(messageLower)
    }
  }

  /**
   * CV context fallback responses
   */
  getCVContextFallback(messageLower) {
    if (messageLower.includes('analyze') || messageLower.includes('score')) {
      return "I can help analyze your CV! For the best analysis, make sure your CV has a professional summary, work experience, skills, and education sections filled out. I'll look at completeness, keyword optimization, and ATS compatibility."
    } else if (messageLower.includes('skill')) {
      return "I'd be happy to suggest relevant skills! What's your target job title or industry? I can recommend both technical skills and soft skills that employers are looking for in 2024."
    } else if (messageLower.includes('summary') || messageLower.includes('about')) {
      return "A great professional summary should be 80-120 words and highlight your key achievements. What's your current role and how many years of experience do you have? I can help you craft a compelling summary!"
    } else if (messageLower.includes('experience') || messageLower.includes('work')) {
      return "When describing work experience, focus on achievements rather than duties. Use action verbs and quantify results when possible. What position would you like help describing?"
    } else if (messageLower.includes('improve') || messageLower.includes('better')) {
      return "I can suggest several ways to improve your CV: enhance your professional summary, add quantified achievements, include relevant keywords, and ensure ATS compatibility. Which area interests you most?"
    } else if (messageLower.includes('template')) {
      return "For CV templates, consider your industry: 'Modern' works well for tech roles, 'Professional' for corporate positions, 'Creative' for design fields, and 'Classic' for traditional industries. What field are you in?"
    }
    
    return "I'm your CV expert! I can help with writing summaries, suggesting skills, improving work descriptions, analyzing your CV, or choosing the right template. What would you like to work on?"
  }

  /**
   * Design context fallback responses
   */
  getDesignContextFallback(messageLower) {
    if (messageLower.includes('template') || messageLower.includes('choose')) {
      return "Great question! Template choice depends on your industry and personality: 'Modern' templates work well for tech and startups, 'Professional' for corporate roles, 'Creative' for design/marketing, and 'Classic' for traditional fields like law or finance. What industry are you in?"
    } else if (messageLower.includes('color') || messageLower.includes('design')) {
      return "For CV design, stick to 1-2 professional colors maximum. Blue conveys trust, gray is neutral and professional, and subtle accent colors can add personality. Avoid bright or distracting colors unless you're in a creative field."
    } else if (messageLower.includes('layout') || messageLower.includes('format')) {
      return "Good CV layout has clear sections, consistent formatting, and plenty of white space. Use consistent fonts (max 2), clear headings, and bullet points for readability. Keep it to 1-2 pages for most roles."
    } else if (messageLower.includes('font')) {
      return "For CVs, use professional fonts like Arial, Calibri, or Helvetica for body text (10-12pt), and slightly larger for headings. Avoid decorative fonts and ensure good readability both on screen and when printed."
    }
    
    return "I'm here to help with template selection, design principles, layout optimization, and visual presentation! What aspect of CV design would you like to explore?"
  }

  /**
   * Support context fallback responses
   */
  getSupportContextFallback(messageLower) {
    if (messageLower.includes('contact') || messageLower.includes('support')) {
      return "You can reach our support team through the contact form on this page, or check our FAQ section for common questions. For urgent issues, look for our live chat during business hours!"
    } else if (messageLower.includes('bug') || messageLower.includes('error') || messageLower.includes('problem')) {
      return "I'm sorry you're experiencing issues! Please describe the problem you're encountering, and I'll help guide you to the right solution. You can also report bugs through our contact form."
    } else if (messageLower.includes('account') || messageLower.includes('login')) {
      return "For account-related issues like login problems or password resets, try using the 'Forgot Password' link on the login page. If that doesn't work, our support team can help you directly."
    } else if (messageLower.includes('billing') || messageLower.includes('payment')) {
      return "For billing questions or payment issues, please contact our support team with your account details. They can help with subscription changes, refunds, or payment problems."
    }
    
    return "I'm here to help you get support! I can guide you to the right resources, help with common issues, or connect you with our support team. What do you need help with?"
  }

  /**
   * Knowledge/FAQ context fallback responses
   */
  getKnowledgeContextFallback(messageLower) {
    if (messageLower.includes('how') || messageLower.includes('start')) {
      return "Getting started is easy! Create an account, choose a template, fill in your information section by section, and download your professional CV. The whole process usually takes 10-15 minutes."
    } else if (messageLower.includes('feature') || messageLower.includes('what can')) {
      return "Our platform offers CV creation with multiple templates, AI-powered suggestions, PDF downloads, and analytics. You can create unlimited CVs, get skill suggestions, and optimize for ATS systems."
    } else if (messageLower.includes('ats') || messageLower.includes('applicant tracking')) {
      return "ATS (Applicant Tracking Systems) scan CVs for keywords and formatting. To optimize: use standard section headings, include relevant keywords from job descriptions, avoid images/graphics, and use simple formatting."
    } else if (messageLower.includes('download') || messageLower.includes('pdf')) {
      return "You can download your CV as a PDF once it's complete. The PDF preserves formatting and is ready to send to employers. You can also make updates and download new versions anytime."
    }
    
    return "I can help explain our features, guide you through the CV creation process, or clarify any questions about the platform. What would you like to know more about?"
  }

  /**
   * General context fallback responses
   */
  getGeneralContextFallback(messageLower) {
    if (messageLower.includes('hello') || messageLower.includes('hi')) {
      return "Hello! I'm your AI assistant. I can help with questions about our platform, provide general information, or just have a friendly conversation. How can I assist you today?"
    } else if (messageLower.includes('help') || messageLower.includes('what can you do')) {
      return "I can help you with information about our CV platform, answer questions about features, provide guidance, or assist with general inquiries. I adapt my expertise based on what page you're on!"
    } else if (messageLower.includes('feature') || messageLower.includes('platform')) {
      return "Our platform helps you create professional CVs with multiple templates, AI-powered suggestions, and easy downloads. You can build a standout CV in minutes!"
    } else if (messageLower.includes('thank')) {
      return "You're very welcome! I'm always here to help. Feel free to ask me anything else!"
    }
    
    return "I'm here to help! I can answer questions about our platform, provide information, or assist you with whatever you need. What can I help you with today?"
  }

  // ... existing code ...

  /**
   * Make HTTP request with retry logic and timeout
   */
  async makeRequest(endpoint, options = {}) {
    let url = `${this.baseUrl}${endpoint}`
    
    // Handle query params for GET requests
    if (options.params && options.method === 'GET') {
      const params = new URLSearchParams()
      Object.keys(options.params).forEach(key => {
        if (options.params[key] !== null && options.params[key] !== undefined) {
          params.append(key, options.params[key])
        }
      })
      if (params.toString()) {
        url += `?${params.toString()}`
      }
    }
    
    const config = {
      method: options.method || 'GET',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'Authorization': `Bearer ${localStorage.getItem('auth_token')}`,
        ...options.headers
      },
      ...options
    }

    if (options.body) {
      config.body = JSON.stringify(options.body)
    }

    // Debug logging
    console.log(`AI Service Request: ${config.method} ${url}`)

    let lastError
    for (let attempt = 1; attempt <= this.retryCount; attempt++) {
      try {
        const controller = new AbortController()
        const timeoutId = setTimeout(() => controller.abort(), this.timeout)
        
        config.signal = controller.signal
        const response = await fetch(url, config)
        clearTimeout(timeoutId)
        
        if (!response.ok) {
          const errorText = await response.text()
          console.error(`AI Service Error ${response.status}:`, errorText)
          
          if (response.status === 503) {
            throw new Error('service_unavailable')
          } else if (response.status === 401) {
            throw new Error('unauthorized')
          } else if (response.status === 404) {
            throw new Error(`endpoint_not_found: ${endpoint}`)
          }
          throw new Error(`HTTP ${response.status}: ${response.statusText}`)
        }
        
        const responseData = await response.json()
        console.log(`AI Service Response: ${config.method} ${url}`, responseData)
        return responseData
        
      } catch (error) {
        lastError = error
        if (error.name === 'AbortError') {
          console.warn(`Request timeout on attempt ${attempt} for ${url}`)
        } else if (error.message === 'service_unavailable') {
          console.warn('AI service temporarily unavailable')
          throw error
        } else if (error.message === 'unauthorized') {
          console.error('Unauthorized AI service request - check authentication')
          throw error
        } else if (error.message.includes('endpoint_not_found')) {
          console.error(`AI service endpoint not found: ${url}`)
          throw error
        } else {
          console.warn(`AI service request failed on attempt ${attempt}:`, error)
        }
        
        // Wait before retrying (exponential backoff)
        if (attempt < this.retryCount) {
          await new Promise(resolve => setTimeout(resolve, Math.pow(2, attempt) * 1000))
        }
      }
    }
    
    throw lastError
  }

  /**
   * Clear cache
   */
  clearCache() {
    this.cache.clear()
  }

  /**
   * Get cache statistics
   */
  getCacheStats() {
    return {
      size: this.cache.size,
      keys: Array.from(this.cache.keys())
    }
  }

  /**
   * Get contextual AI greeting based on user progress
   */
  getAIGreeting(cvData, currentStep = 0) {
    const greetings = {
      en: {
        0: "👋 Hello! I'm your AI career assistant. Let's create an outstanding CV together!",
        1: "✨ Great start! Now let me help you craft a compelling professional summary that recruiters love.",
        2: "💼 Excellent! Time to showcase your experience. I'll help you write impactful descriptions.",
        3: "🎯 Perfect! Let's highlight your skills strategically to match industry standards.",
        4: "🌟 Almost there! Adding interests shows your personality - let me suggest some professional ones.",
        5: "🎉 Fantastic work! Your CV is looking professional. Let me give you final optimization tips."
      },
      sq: {
        0: "👋 Përshëndetje! Unë jam asistenti juaj AI për karrierë. Le të krijojmë një CV të shkëlqyer së bashku!",
        1: "✨ Fillim i shkëlqyer! Tani le t'ju ndihmoj të krijoni një përmbledhje profesionale bindëse që rekrutuesit e duan.",
        2: "💼 Shkëlqyeshëm! Koha për të treguar përvojën tuaj. Do t'ju ndihmoj të shkruani përshkrime me ndikim.",
        3: "🎯 E përsosur! Le të theksojmë aftësitë tuaja strategjikisht për t'u përshtatur me standardet e industrisë.",
        4: "🌟 Thuajse atje! Shtimi i interesave tregon personalitetin tuaj - le t'ju sugjeroj disa profesionale.",
        5: "🎉 Punë fantastike! CV-ja juaj duket profesionale. Le t'ju jap këshilla optimizimi përfundimtare."
      }
    }

    return greetings[this.currentLanguage]?.[currentStep] || greetings.en[0]
  }

  /**
   * Enhanced skill suggestions with industry context
   */
  getSkillSuggestions(jobTitle, industry = '', experienceLevel = 'intermediate') {
    const normalizedTitle = jobTitle.toLowerCase()
    
    // Advanced skill mapping with context
    const skillDatabase = {
      'software engineer': {
        beginner: ['HTML/CSS', 'JavaScript', 'Git', 'Problem Solving', 'Team Collaboration', 'Debugging'],
        intermediate: ['React', 'Node.js', 'Python', 'SQL', 'REST APIs', 'Docker', 'Agile Methodologies'],
        advanced: ['System Architecture', 'Microservices', 'AWS/Azure', 'DevOps', 'Performance Optimization', 'Technical Leadership'],
        trending: ['TypeScript', 'GraphQL', 'Kubernetes', 'Serverless', 'Machine Learning', 'Web3']
      },
      'frontend developer': {
        beginner: ['HTML5', 'CSS3', 'JavaScript ES6+', 'Responsive Design', 'Version Control (Git)'],
        intermediate: ['React', 'Vue.js', 'SASS/SCSS', 'Webpack', 'Testing (Jest)', 'REST API Integration'],
        advanced: ['Next.js', 'State Management (Redux)', 'Performance Optimization', 'Progressive Web Apps'],
        trending: ['React 18', 'Vite', 'Tailwind CSS', 'Web Components', 'JAMstack']
      },
      'product designer': {
        beginner: ['Figma', 'Adobe Photoshop', 'User Research', 'Wireframing', 'Prototyping'],
        intermediate: ['Design Systems', 'Adobe XD', 'Sketch', 'User Testing', 'Information Architecture'],
        advanced: ['Design Strategy', 'Service Design', 'Design Thinking Facilitation', 'Cross-functional Leadership'],
        trending: ['AI-Assisted Design', 'Voice UI Design', 'Inclusive Design', 'Design Operations']
      },
      'data scientist': {
        beginner: ['Python', 'SQL', 'Excel', 'Statistics', 'Data Visualization'],
        intermediate: ['Machine Learning', 'Pandas', 'NumPy', 'Tableau', 'R', 'Jupyter Notebooks'],
        advanced: ['Deep Learning', 'TensorFlow', 'Model Deployment', 'Big Data (Spark)', 'MLOps'],
        trending: ['LLMs', 'Computer Vision', 'AutoML', 'Edge AI', 'Ethical AI']
      },
      'marketing manager': {
        beginner: ['Content Marketing', 'Social Media', 'Google Analytics', 'Email Marketing'],
        intermediate: ['SEO/SEM', 'Marketing Automation', 'A/B Testing', 'CRM Management', 'Campaign Analysis'],
        advanced: ['Growth Hacking', 'Marketing Strategy', 'Attribution Modeling', 'Team Leadership'],
        trending: ['AI Marketing Tools', 'Influencer Marketing', 'Voice Search Optimization', 'Conversion Rate Optimization']
      }
    }

    // Smart matching
    let matchedRole = this.findBestRoleMatch(normalizedTitle, skillDatabase)
    if (!matchedRole) {
      matchedRole = 'software engineer' // fallback
    }

    const roleSkills = skillDatabase[matchedRole]
    const skills = [...(roleSkills[experienceLevel] || roleSkills.intermediate)]
    
    // Add trending skills based on role
    if (Math.random() > 0.7) { // 30% chance to include trending
      skills.push(...(roleSkills.trending?.slice(0, 2) || []))
    }

    return skills.slice(0, 8) // Return max 8 skills
  }

  /**
   * Generate dynamic professional summaries
   */
  generateProfessionalSummary(personalInfo, experience = [], skills = [], jobTitle = '') {
    const experienceYears = this.calculateExperienceYears(experience)
    const industryContext = this.detectIndustry(jobTitle)
    
    const summaryTemplates = {
      en: {
        entry_level: [
          `Motivated ${jobTitle} with strong foundation in ${this.getTopSkills(skills, 3).join(', ')}. Eager to apply academic knowledge and fresh perspective to drive innovation in ${industryContext} industry.`,
          `Recent graduate specializing in ${industryContext} with hands-on experience in ${this.getTopSkills(skills, 2).join(' and ')}. Passionate about creating impactful solutions and continuous learning.`,
          `Emerging ${jobTitle} with solid understanding of ${this.getTopSkills(skills, 3).join(', ')}. Committed to delivering quality results and growing within a dynamic team environment.`
        ],
        mid_level: [
          `Experienced ${jobTitle} with ${experienceYears} years of proven expertise in ${this.getTopSkills(skills, 3).join(', ')}. Successfully delivered multiple projects while maintaining high quality standards and meeting deadlines.`,
          `Results-driven ${jobTitle} with ${experienceYears}+ years of experience building scalable solutions. Skilled in ${this.getTopSkills(skills, 3).join(', ')} with a track record of improving processes and team collaboration.`,
          `Dedicated ${jobTitle} bringing ${experienceYears} years of hands-on experience in ${industryContext}. Expertise in ${this.getTopSkills(skills, 2).join(' and ')} with strong problem-solving abilities and leadership potential.`
        ],
        senior_level: [
          `Senior ${jobTitle} with ${experienceYears}+ years of leadership experience in ${industryContext}. Expert in ${this.getTopSkills(skills, 3).join(', ')} with proven ability to architect solutions, mentor teams, and drive organizational growth.`,
          `Accomplished ${jobTitle} with ${experienceYears}+ years of progressive experience leading cross-functional teams. Deep expertise in ${this.getTopSkills(skills, 3).join(', ')} and strategic thinking with focus on scalable solutions.`,
          `Visionary ${jobTitle} with ${experienceYears}+ years of industry experience transforming business requirements into innovative solutions. Specialized in ${this.getTopSkills(skills, 2).join(' and ')} with excellent stakeholder management skills.`
        ]
      },
      sq: {
        entry_level: [
          `${jobTitle} i motivuar me bazë të fortë në ${this.getTopSkills(skills, 3).join(', ')}. I dëshiruar për të aplikuar njohuritë akademike dhe perspektivën e freskët për të nxitur inovacionin në industrinë e ${industryContext}.`,
          `Absolvent i ri i specializuar në ${industryContext} me përvojë praktike në ${this.getTopSkills(skills, 2).join(' dhe ')}. I pasionuar për krijimin e zgjidhjeve me ndikim dhe mësimin e vazhdueshëm.`,
          `${jobTitle} emergent me kuptim të fortë të ${this.getTopSkills(skills, 3).join(', ')}. I angazhuar për të ofruar rezultate cilësore dhe për të rritur brenda një mjedisi dinamik ekipi.`
        ],
        mid_level: [
          `${jobTitle} me përvojë me ${experienceYears} vjet ekspertizë të provuar në ${this.getTopSkills(skills, 3).join(', ')}. Ka dorëzuar me sukses projekte të shumta duke ruajtur standardet e larta të cilësisë dhe duke përmbushur afatet.`,
          `${jobTitle} i orientuar drejt rezultateve me ${experienceYears}+ vjet përvojë në ndërtimin e zgjidhjeve të shkallëzueshme. I aftë në ${this.getTopSkills(skills, 3).join(', ')} me rekord të përmirësimit të proceseve dhe bashkëpunimit në ekip.`,
          `${jobTitle} i dedikuar që sjell ${experienceYears} vjet përvojë praktike në ${industryContext}. Ekspertizë në ${this.getTopSkills(skills, 2).join(' dhe ')} me aftësi të forta zgjidhje problemesh dhe potencial udhëheqjeje.`
        ],
        senior_level: [
          `${jobTitle} Senior me ${experienceYears}+ vjet përvojë udhëheqjeje në ${industryContext}. Ekspert në ${this.getTopSkills(skills, 3).join(', ')} me aftësi të provuar për të arkitekturuar zgjidhje, mentoruar ekipe dhe nxitur rritjen organizative.`,
          `${jobTitle} i përmbushur me ${experienceYears}+ vjet përvojë progresive në udhëheqjen e ekipeve ndërfunksionale. Ekspertizë e thellë në ${this.getTopSkills(skills, 3).join(', ')} dhe mendim strategjik me fokus në zgjidhje të shkallëzueshme.`,
          `${jobTitle} vizionar me ${experienceYears}+ vjet përvojë në industri duke transformuar kërkesat e biznesit në zgjidhje inovative. I specializuar në ${this.getTopSkills(skills, 2).join(' dhe ')} me aftësi të shkëlqyera menaxhimi të palëve të interesuara.`
        ]
      }
    }

    const level = experienceYears >= 8 ? 'senior_level' : (experienceYears >= 3 ? 'mid_level' : 'entry_level')
    const templates = summaryTemplates[this.currentLanguage]?.[level] || summaryTemplates.en[level]
    
    return templates[Math.floor(Math.random() * templates.length)]
  }

  /**
   * Enhanced experience suggestions with industry-specific achievements
   */
  getExperienceSuggestions(jobTitle, companySize = 'medium', industry = '') {
    const achievements = {
      en: {
        'software engineer': [
          'Developed and maintained scalable web applications serving 10,000+ daily active users',
          'Implemented automated testing suite reducing bugs by 40% and improving code quality',
          'Led migration from monolithic to microservices architecture improving system performance by 35%',
          'Collaborated with cross-functional teams to deliver 15+ feature releases on schedule',
          'Mentored 3 junior developers and established coding best practices for the team',
          'Optimized database queries resulting in 50% faster page load times',
          'Built RESTful APIs handling 1M+ requests per day with 99.9% uptime'
        ],
        'product designer': [
          'Designed user interfaces for mobile app resulting in 25% increase in user engagement',
          'Conducted user research with 200+ participants to inform design decisions',
          'Created comprehensive design system used across 10+ product teams',
          'Led design thinking workshops with stakeholders to identify user pain points',
          'Improved conversion rates by 30% through iterative A/B testing of key user flows',
          'Collaborated with engineering teams to ensure pixel-perfect implementation',
          'Reduced user onboarding time by 45% through streamlined UX design'
        ],
        'marketing manager': [
          'Increased brand awareness by 60% through integrated digital marketing campaigns',
          'Managed $500K annual marketing budget with 25% ROI improvement year-over-year',
          'Grew social media following by 150% and engagement rates by 40%',
          'Launched influencer partnership program generating 200+ qualified leads monthly',
          'Implemented marketing automation workflows improving lead nurturing by 35%',
          'Coordinated cross-channel campaigns resulting in 40% increase in customer acquisition',
          'Analyzed campaign performance data to optimize strategies and reduce CAC by 20%'
        ]
      },
      sq: {
        'software engineer': [
          'Zhvillova dhe mirëmbajta aplikacione web të shkallëzueshme që shërbejnë 10,000+ përdorues aktivë në ditë',
          'Implementova suite automatike testimi duke reduktuar gabimet 40% dhe përmirësuar cilësinë e kodit',
          'Udhëhoqa migrimin nga arkitektura monolitike në mikroshërbime duke përmirësuar performancën e sistemit 35%',
          'Bashkëpunova me ekipe ndërfunksionale për të dorëzuar 15+ versione të karakteristikave në kohë',
          'Mentorova 3 zhvillues të rinj dhe vendosa praktikat më të mira të kodimit për ekipin',
          'Optimizova pyetjet e bazës së të dhënave duke rezultuar në kohë ngarkimi 50% më të shpejtë',
          'Ndërtova API RESTful që trajton 1M+ kërkesa në ditë me 99.9% kohë funksionimi'
        ],
        'product designer': [
          'Dizajnova ndërfaqe përdoruesi për aplikacion mobil duke rezultuar në 25% rritje të angazhimit të përdoruesit',
          'Kryeva hulumtime përdoruesi me 200+ pjesëmarrës për të informuar vendimet e dizajnit',
          'Krijova sistem gjithëpërfshirës dizajni të përdorur në 10+ ekipe produkti',
          'Udhëhoqa punëtori mendimi dizajni me palët e interesuara për të identifikuar pikat e dhimbjes së përdoruesit',
          'Përmirësova normat e konvertimit 30% përmes testimit iterativ A/B të rrjedhave kryesore të përdoruesit',
          'Bashkëpunova me ekipet e inxhinierisë për të siguruar implementim pixel-perfekt',
          'Reduktova kohën e integrimit të përdoruesit 45% përmes dizajnit të qartë UX'
        ],
        'marketing manager': [
          'Rrita ndërgjegjësimin për markën 60% përmes kampanjeve të integruara të marketingut dixhital',
          'Menaxhova buxhetin vjetor të marketingut $500K me 25% përmirësim ROI vit pas viti',
          'Rrita ndjekjen e mediave sociale 150% dhe normat e angazhimit 40%',
          'Lancova programin e partneritetit me influencues duke gjeneruar 200+ udhëzime të kualifikuara mujore',
          'Implementova rrjedhat e automatizimit të marketingut duke përmirësuar kujdesin e udhëzimeve 35%',
          'Koordinova kampanje ndërkanalesh duke rezultuar në 40% rritje të përfitimit të klientëve',
          'Analizova të dhënat e performancës së kampanjës për të optimizuar strategjitë dhe reduktuar CAC 20%'
        ]
      }
    }

    const roleAchievements = achievements[this.currentLanguage]?.[jobTitle.toLowerCase()] || 
                             achievements.en[jobTitle.toLowerCase()] || 
                             achievements.en['software engineer']

    // Return 4-6 achievements randomly
    const shuffled = [...roleAchievements].sort(() => 0.5 - Math.random())
    return shuffled.slice(0, Math.min(6, roleAchievements.length))
  }

  /**
   * AI-powered CV analysis with detailed insights
   */
  analyzeCV(cvData) {
    const analysis = {
      score: 0,
      completeness: 0,
      strengths: [],
      weaknesses: [],
      suggestions: [],
      industryAlignment: 'good',
      atsScore: 0,
      improvements: []
    }

    // Comprehensive scoring
    const sections = [
      { name: 'personalInfo', weight: 10, check: () => this.hasCompletePersonalInfo(cvData.personalInfo) },
      { name: 'summary', weight: 20, check: () => cvData.summary && cvData.summary.length >= 80 },
      { name: 'experience', weight: 30, check: () => cvData.experience && cvData.experience.length > 0 },
      { name: 'education', weight: 15, check: () => cvData.education && cvData.education.length > 0 },
      { name: 'skills', weight: 20, check: () => cvData.skills && cvData.skills.length >= 5 },
      { name: 'interests', weight: 5, check: () => cvData.interests && cvData.interests.length > 0 }
    ]

    let totalWeight = 0
    let achievedWeight = 0

    sections.forEach(section => {
      totalWeight += section.weight
      if (section.check()) {
        achievedWeight += section.weight
        analysis.strengths.push(this.getStrengthMessage(section.name))
      } else {
        analysis.weaknesses.push(this.getWeaknessMessage(section.name))
        analysis.suggestions.push(this.getSuggestionMessage(section.name))
      }
    })

    analysis.score = Math.round((achievedWeight / totalWeight) * 100)
    analysis.completeness = Math.round((achievedWeight / totalWeight) * 100)

    // ATS Analysis
    analysis.atsScore = this.calculateATSScore(cvData)
    
    // Industry alignment
    if (cvData.experience && cvData.experience.length > 0) {
      const primaryRole = cvData.experience[0].title || ''
      analysis.industryAlignment = this.assessIndustryAlignment(primaryRole, cvData.skills || [])
    }

    // Smart improvements
    analysis.improvements = this.generateSmartImprovements(cvData, analysis.score)

    return analysis
  }

  /**
   * Get personalized improvement suggestions
   */
  generateSmartImprovements(cvData, currentScore) {
    const improvements = {
      en: [
        {
          type: 'content',
          priority: 'high',
          title: 'Enhance Professional Summary',
          description: 'Your summary should be 80-120 words and highlight your unique value proposition.',
          action: 'Add quantifiable achievements and specific skills that match your target role.',
          impact: '+15 points'
        },
        {
          type: 'experience',
          priority: 'high',
          title: 'Quantify Your Achievements',
          description: 'Numbers make your accomplishments more credible and impressive.',
          action: 'Add metrics like "increased sales by 25%" or "managed team of 8 people".',
          impact: '+20 points'
        },
        {
          type: 'skills',
          priority: 'medium',
          title: 'Optimize Skills Section',
          description: 'Include 6-10 relevant skills that match job requirements.',
          action: 'Add both technical and soft skills relevant to your industry.',
          impact: '+10 points'
        },
        {
          type: 'keywords',
          priority: 'medium',
          title: 'Improve ATS Compatibility',
          description: 'Use industry-standard keywords to pass applicant tracking systems.',
          action: 'Include job-specific terminology and avoid complex formatting.',
          impact: '+12 points'
        }
      ],
      sq: [
        {
          type: 'content',
          priority: 'high',
          title: 'Përmirëso Përmbledhjen Profesionale',
          description: 'Përmbledhja juaj duhet të jetë 80-120 fjalë dhe të theksojë propozimin tuaj unik të vlerës.',
          action: 'Shtoni arritje të matshme dhe aftësi specifike që përputhen me rolin tuaj të synuar.',
          impact: '+15 pikë'
        },
        {
          type: 'experience',
          priority: 'high',
          title: 'Kuantifikoni Arritjet Tuaja',
          description: 'Numrat i bëjnë arritjet tuaja më të besueshme dhe mbresëlënëse.',
          action: 'Shtoni metrika si "rrita shitjet 25%" ose "menaxhova ekip prej 8 personash".',
          impact: '+20 pikë'
        },
        {
          type: 'skills',
          priority: 'medium',
          title: 'Optimizo Seksionin e Aftësive',
          description: 'Përfshini 6-10 aftësi relevante që përputhen me kërkesat e punës.',
          action: 'Shtoni aftësi të dyja teknike dhe të buta relevante për industrinë tuaj.',
          impact: '+10 pikë'
        },
        {
          type: 'keywords',
          priority: 'medium',
          title: 'Përmirëso Përputhshmërinë ATS',
          description: 'Përdorni fjalë kyçe standarde të industrisë për të kaluar sistemet e gjurmimit të aplikantëve.',
          action: 'Përfshini terminologji specifike pune dhe shmangni formatim kompleks.',
          impact: '+12 pikë'
        }
      ]
    }

    return improvements[this.currentLanguage] || improvements.en
  }

  /**
   * Helper methods for enhanced functionality
   */
  findBestRoleMatch(title, database) {
    const keys = Object.keys(database)
    return keys.find(key => 
      title.includes(key) || 
      key.includes(title) ||
      this.calculateStringSimilarity(title, key) > 0.6
    )
  }

  calculateStringSimilarity(str1, str2) {
    const longer = str1.length > str2.length ? str1 : str2
    const shorter = str1.length > str2.length ? str2 : str1
    if (longer.length === 0) return 1.0
    return (longer.length - this.levenshteinDistance(longer, shorter)) / longer.length
  }

  levenshteinDistance(str1, str2) {
    const matrix = []
    for (let i = 0; i <= str2.length; i++) {
      matrix[i] = [i]
    }
    for (let j = 0; j <= str1.length; j++) {
      matrix[0][j] = j
    }
    for (let i = 1; i <= str2.length; i++) {
      for (let j = 1; j <= str1.length; j++) {
        if (str2.charAt(i - 1) === str1.charAt(j - 1)) {
          matrix[i][j] = matrix[i - 1][j - 1]
        } else {
          matrix[i][j] = Math.min(
            matrix[i - 1][j - 1] + 1,
            matrix[i][j - 1] + 1,
            matrix[i - 1][j] + 1
          )
        }
      }
    }
    return matrix[str2.length][str1.length]
  }

  calculateExperienceYears(experience) {
    if (!experience || experience.length === 0) return 0
    
    let totalYears = 0
    experience.forEach(exp => {
      const startYear = new Date(exp.startDate).getFullYear()
      const endYear = exp.current ? new Date().getFullYear() : new Date(exp.endDate).getFullYear()
      totalYears += Math.max(0, endYear - startYear)
    })
    
    return totalYears
  }

  getTopSkills(skills, count = 3) {
    if (!skills || skills.length === 0) return ['relevant skills']
    return skills.slice(0, count).map(skill => typeof skill === 'string' ? skill : skill.name)
  }

  detectIndustry(jobTitle) {
    const industries = {
      'software': 'technology',
      'developer': 'technology',
      'engineer': 'technology',
      'designer': 'design',
      'marketing': 'marketing',
      'sales': 'sales',
      'data': 'analytics',
      'manager': 'management'
    }

    const titleLower = jobTitle.toLowerCase()
    for (const [keyword, industry] of Object.entries(industries)) {
      if (titleLower.includes(keyword)) return industry
    }
    return 'business'
  }

  hasCompletePersonalInfo(personalInfo) {
    return personalInfo && 
           personalInfo.firstName && 
           personalInfo.lastName && 
           personalInfo.email && 
           personalInfo.phone
  }

  calculateATSScore(cvData) {
    let score = 0
    
    // Check for ATS-friendly elements
    if (cvData.summary && cvData.summary.length >= 80) score += 20
    if (cvData.skills && cvData.skills.length >= 6) score += 25
    if (cvData.experience && cvData.experience.length > 0) score += 30
    if (cvData.education && cvData.education.length > 0) score += 15
    
    // Check for keywords density
    const allText = [
      cvData.summary || '',
      ...((cvData.experience || []).map(exp => exp.description || '')),
      ...((cvData.skills || []).map(skill => typeof skill === 'string' ? skill : skill.name))
    ].join(' ').toLowerCase()
    
    if (allText.length > 100) score += 10 // Sufficient content
    
    return Math.min(score, 100)
  }

  assessIndustryAlignment(jobTitle, skills) {
    const relevantSkills = this.getSkillSuggestions(jobTitle, '', 'intermediate')
    const userSkills = skills.map(skill => typeof skill === 'string' ? skill : skill.name)
    
    const matches = relevantSkills.filter(skill => 
      userSkills.some(userSkill => 
        userSkill.toLowerCase().includes(skill.toLowerCase()) ||
        skill.toLowerCase().includes(userSkill.toLowerCase())
      )
    ).length

    const alignment = matches / relevantSkills.length
    
    if (alignment >= 0.7) return 'excellent'
    if (alignment >= 0.5) return 'good'
    if (alignment >= 0.3) return 'fair'
    return 'poor'
  }

  getStrengthMessage(section) {
    const messages = {
      en: {
        personalInfo: '✅ Complete contact information',
        summary: '✅ Compelling professional summary',
        experience: '✅ Relevant work experience',
        education: '✅ Educational background included',
        skills: '✅ Comprehensive skills list',
        interests: '✅ Personal interests showcase personality'
      },
      sq: {
        personalInfo: '✅ Informacion kontakti i plotë',
        summary: '✅ Përmbledhje profesionale bindëse',
        experience: '✅ Përvojë pune relevante',
        education: '✅ Sfondi arsimor i përfshirë',
        skills: '✅ Listë gjithëpërfshirëse aftësish',
        interests: '✅ Interesat personale tregojnë personalitetin'
      }
    }
    
    return messages[this.currentLanguage]?.[section] || messages.en[section]
  }

  getWeaknessMessage(section) {
    const messages = {
      en: {
        personalInfo: '⚠️ Missing contact information',
        summary: '⚠️ Professional summary needs improvement',
        experience: '⚠️ Add work experience',
        education: '⚠️ Include educational background',
        skills: '⚠️ Add more relevant skills',
        interests: '⚠️ Consider adding interests'
      },
      sq: {
        personalInfo: '⚠️ Informacion kontakti i mangët',
        summary: '⚠️ Përmbledhja profesionale ka nevojë për përmirësim',
        experience: '⚠️ Shtoni përvojë pune',
        education: '⚠️ Përfshini sfondin arsimor',
        skills: '⚠️ Shtoni më shumë aftësi relevante',
        interests: '⚠️ Konsideroni shtimin e interesave'
      }
    }
    
    return messages[this.currentLanguage]?.[section] || messages.en[section]
  }

  getSuggestionMessage(section) {
    const messages = {
      en: {
        personalInfo: 'Add your full name, email, phone, and location',
        summary: 'Write a 80-120 word summary highlighting your value proposition',
        experience: 'Include your most relevant work experience with achievements',
        education: 'Add your educational qualifications and certifications',
        skills: 'List 6-10 skills relevant to your target role',
        interests: 'Add 3-5 professional interests that show your personality'
      },
      sq: {
        personalInfo: 'Shtoni emrin tuaj të plotë, email, telefon dhe vendndodhjen',
        summary: 'Shkruani një përmbledhje 80-120 fjalë që thekson propozimin tuaj të vlerës',
        experience: 'Përfshini përvojën tuaj më relevante të punës me arritje',
        education: 'Shtoni kualifikimet tuaja arsimore dhe certifikimet',
        skills: 'Listoni 6-10 aftësi relevante për rolin tuaj të synuar',
        interests: 'Shtoni 3-5 interesa profesionale që tregojnë personalitetin tuaj'
      }
    }
    
    return messages[this.currentLanguage]?.[section] || messages.en[section]
  }

  /**
   * Load industry insights data
   */
  loadIndustryInsights() {
    return {
      trending_skills_2024: {
        technology: ['AI/ML', 'Cloud Computing', 'Cybersecurity', 'DevOps', 'Data Science'],
        design: ['UX Research', 'Design Systems', 'Accessibility', 'Design Ops', 'AI-Assisted Design'],
        marketing: ['Growth Marketing', 'Marketing Automation', 'Data Analytics', 'Conversion Optimization'],
        business: ['Digital Transformation', 'Agile Leadership', 'Change Management', 'Strategic Planning']
      },
      salary_insights: {
        technology: { min: 60000, max: 180000, currency: 'USD' },
        design: { min: 50000, max: 140000, currency: 'USD' },
        marketing: { min: 45000, max: 130000, currency: 'USD' },
        business: { min: 55000, max: 160000, currency: 'USD' }
      }
    }
  }

  /**
   * Load personality-based prompts
   */
  loadPersonalityPrompts() {
    return {
      motivational: {
        en: [
          "🌟 You're doing great! Let's make your CV shine even brighter.",
          "💪 Every great career starts with a strong CV. You're on the right track!",
          "🚀 Your potential is unlimited. Let's showcase it perfectly!",
          "✨ Small improvements make a big difference. Keep going!"
        ],
        sq: [
          "🌟 Po shkoni shkëlqyeshëm! Le të bëjmë CV-në tuaj të shkëlqejë edhe më shumë.",
          "💪 Çdo karrierë e madhe fillon me një CV të fortë. Jeni në rrugën e duhur!",
          "🚀 Potenciali juaj është i pakufizuar. Le ta tregojmë atë përsosshëm!",
          "✨ Përmirësimet e vogla bëjnë ndryshim të madh. Vazhdoni!"
        ]
      },
      encouraging: {
        en: [
          "🎯 You're building something amazing. Each section brings you closer to your goal.",
          "🏆 Quality over quantity - let's make every word count.",
          "🔥 Your experience is valuable. Let's present it in the best light.",
          "💼 Recruiters will love what they see. Trust the process!"
        ],
        sq: [
          "🎯 Po ndërtoni diçka të mrekullueshme. Çdo seksion ju afron me qëllimin tuaj.",
          "🏆 Cilësia mbi sasinë - le të bëjmë që çdo fjalë të ketë rëndësi.",
          "🔥 Përvoja juaj është e vlefshme. Le ta paraqesim në dritën më të mirë.",
          "💼 Rekrutuesit do ta duan atë që shohin. Besoni në proces!"
        ]
      }
    }
  }

  /**
   * Get contextual motivational message
   */
  getMotivationalMessage(type = 'motivational') {
    const messages = this.personalityPrompts[type]?.[this.currentLanguage] || 
                     this.personalityPrompts.motivational.en
    
    return messages[Math.floor(Math.random() * messages.length)]
  }
}

// Export singleton instance
export default new AIService() 