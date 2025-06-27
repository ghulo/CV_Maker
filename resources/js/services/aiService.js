import axios from 'axios'

// AI Service for CV content suggestions
class AIService {
  constructor() {
    this.baseUrl = '/api/ai'
    this.cache = new Map()
    this.retryCount = 3
    this.timeout = 10000 // 10 seconds
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
   * Generate a contextual professional summary based on actual job data
   */
  async generateSummary(data) {
    try {
      const { personalInfo, experience, education } = data
      
      // Get the most recent/relevant job
      const primaryJob = experience.length > 0 ? experience[0] : null
      const jobTitle = primaryJob?.position || 'Professional'
      const company = primaryJob?.company || ''
      const yearsExperience = this.calculateYearsOfExperience(experience)
      
      // Get highest education
      const highestEducation = education.length > 0 ? education[0] : null
      
      // Generate contextual summary based on actual data
      const summary = this.generateContextualSummary({
        jobTitle,
        company,
        yearsExperience,
        firstName: personalInfo.firstName,
        education: highestEducation,
        experience
      })
      
      return { summary }
    } catch (error) {
      console.error('AI Summary generation failed:', error)
      throw error
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
   * Get relevant skills for a specific job title
   */
  getSkillSuggestions(jobTitle) {
    const normalizedTitle = jobTitle.toLowerCase()
    
    const skillMaps = {
      // Software Development
      'software developer': ['JavaScript', 'Python', 'React', 'Node.js', 'SQL', 'Git', 'REST APIs', 'Agile', 'Docker'],
      'frontend developer': ['HTML/CSS', 'JavaScript', 'React', 'Vue.js', 'TypeScript', 'Webpack', 'SASS', 'Responsive Design'],
      'backend developer': ['Python', 'Java', 'Node.js', 'SQL', 'MongoDB', 'REST APIs', 'Microservices', 'Docker', 'AWS'],
      'full stack developer': ['JavaScript', 'React', 'Node.js', 'Python', 'SQL', 'MongoDB', 'Git', 'Docker', 'AWS'],
      'mobile developer': ['React Native', 'Swift', 'Kotlin', 'Flutter', 'iOS', 'Android', 'REST APIs', 'Firebase'],
      
      // Design
      'ui designer': ['Figma', 'Adobe XD', 'Sketch', 'Photoshop', 'Illustrator', 'Prototyping', 'User Research'],
      'ux designer': ['User Research', 'Wireframing', 'Prototyping', 'Figma', 'Adobe XD', 'Usability Testing', 'Design Systems'],
      'graphic designer': ['Photoshop', 'Illustrator', 'InDesign', 'Figma', 'Branding', 'Typography', 'Print Design'],
      
      // Data & Analytics
      'data analyst': ['Excel', 'SQL', 'Python', 'R', 'Tableau', 'Power BI', 'Statistics', 'Data Visualization'],
      'data scientist': ['Python', 'R', 'Machine Learning', 'SQL', 'TensorFlow', 'Pandas', 'Statistics', 'Jupyter'],
      'business analyst': ['Excel', 'SQL', 'Tableau', 'Power BI', 'Process Mapping', 'Requirements Analysis'],
      
      // Marketing
      'digital marketer': ['Google Analytics', 'SEO', 'SEM', 'Social Media', 'Content Marketing', 'Email Marketing'],
      'content manager': ['Content Strategy', 'SEO', 'WordPress', 'Social Media', 'Analytics', 'Copywriting'],
      'social media manager': ['Facebook Ads', 'Instagram', 'Twitter', 'LinkedIn', 'Content Creation', 'Analytics'],
      
      // Project Management
      'project manager': ['Agile', 'Scrum', 'Jira', 'Confluence', 'Risk Management', 'Stakeholder Management'],
      'product manager': ['Product Strategy', 'Agile', 'User Stories', 'Analytics', 'A/B Testing', 'Roadmapping'],
      
      // Sales & Business
      'sales manager': ['CRM', 'Salesforce', 'Lead Generation', 'Negotiation', 'Pipeline Management', 'Customer Relations'],
      'account manager': ['Client Relations', 'CRM', 'Salesforce', 'Negotiation', 'Project Management', 'Communication'],
      
      // Generic fallback
      'professional': ['Communication', 'Problem Solving', 'Team Collaboration', 'Time Management', 'Microsoft Office']
    }
    
    // Find best match
    for (const [key, skills] of Object.entries(skillMaps)) {
      if (normalizedTitle.includes(key) || key.includes(normalizedTitle)) {
        return skills
      }
    }
    
    // Fallback based on keywords
    if (normalizedTitle.includes('developer') || normalizedTitle.includes('engineer')) {
      return skillMaps['software developer']
    }
    if (normalizedTitle.includes('designer')) {
      return skillMaps['ui designer']
    }
    if (normalizedTitle.includes('manager')) {
      return skillMaps['project manager']
    }
    if (normalizedTitle.includes('analyst')) {
      return skillMaps['data analyst']
    }
    if (normalizedTitle.includes('marketing')) {
      return skillMaps['digital marketer']
    }
    
    return skillMaps['professional']
  }

  /**
   * Enhance job description with contextual improvements
   */
  async enhanceDescription(experience) {
    const { position, company, description } = experience
    
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
   * Get skill suggestions based on job title analysis
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
   * ATS optimization fallback
   */
  optimizeForATSFallback(cvData) {
    const tips = []
    
    // Check for common ATS issues
    if (cvData.summary && cvData.summary.length < 100) {
      tips.push('Expand your professional summary to 100+ characters for better ATS scanning')
    }
    
    const skillCount = (cvData.skills || []).length
    if (skillCount < 8) {
      tips.push(`Add more skills (currently ${skillCount}, recommended 8+) to improve keyword matching`)
    }
    
    const hasJobTitleKeywords = cvData.experience?.some(exp => 
      exp.description && exp.description.length > 50
    )
    if (!hasJobTitleKeywords) {
      tips.push('Add more detailed descriptions to work experience for better keyword density')
    }
    
    return {
      score: Math.min(85, 50 + (skillCount * 3) + (cvData.summary?.length || 0) * 0.2),
      tips,
      keywordDensity: 'moderate',
      atsCompatibility: 'good'
    }
  }

  /**
   * Improvement tips fallback
   */
  getImprovementTipsFallback(cvData) {
    const tips = []
    
    // Content length tips
    if (!cvData.summary || cvData.summary.length < 80) {
      tips.push({
        type: 'content',
        priority: 'high',
        message: 'Add a compelling professional summary (80+ characters) to grab attention'
      })
    }
    
    // Experience tips
    const experienceCount = (cvData.experience || []).length
    if (experienceCount === 0) {
      tips.push({
        type: 'experience',
        priority: 'high',
        message: 'Add your work experience to showcase your professional journey'
      })
    } else if (experienceCount < 2) {
      tips.push({
        type: 'experience',
        priority: 'medium',
        message: 'Consider adding more work experience entries if available'
      })
    }
    
    // Skills tips
    const skillsCount = (cvData.skills || []).length
    if (skillsCount < 5) {
      tips.push({
        type: 'skills',
        priority: 'high',
        message: 'Add more skills to reach the recommended 5-8 skills'
      })
    }
    
    // Education tips
    if (!(cvData.education || []).length) {
      tips.push({
        type: 'education',
        priority: 'medium',
        message: 'Add your educational background to provide complete profile'
      })
    }
    
    return { tips, overall: 'Your CV is progressing well! Focus on the high-priority items first.' }
  }

  // ============ UTILITY METHODS ============

  /**
   * Make HTTP request with retry logic and timeout
   */
  async makeRequest(endpoint, options = {}) {
    const url = `${this.baseUrl}${endpoint}`
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

    let lastError
    for (let attempt = 1; attempt <= this.retryCount; attempt++) {
      try {
        const controller = new AbortController()
        const timeoutId = setTimeout(() => controller.abort(), this.timeout)
        
        config.signal = controller.signal
        const response = await fetch(url, config)
        clearTimeout(timeoutId)
        
        if (!response.ok) {
          if (response.status === 503) {
            throw new Error('service_unavailable')
          }
          throw new Error(`HTTP ${response.status}: ${response.statusText}`)
        }
        
        return await response.json()
        
      } catch (error) {
        lastError = error
        if (error.name === 'AbortError') {
          console.warn(`Request timeout on attempt ${attempt}`)
        } else if (error.message === 'service_unavailable') {
          console.warn('AI service temporarily unavailable')
          throw error
        } else {
          console.warn(`Request failed on attempt ${attempt}:`, error)
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
}

// Export singleton instance
export default new AIService() 