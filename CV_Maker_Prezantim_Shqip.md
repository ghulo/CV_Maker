# 🎯 CV_Maker - Prezantim Teknik në Shqip

## 📋 Përmbajtja e Prezantimit

1. **Arkitektura e Përgjithshme**
2. **Backend (Laravel)**
3. **Frontend (Vue.js)**
4. **Veçoritë Kryesore**
5. **Integrim me AI**
6. **Bazë e të Dhënave**
7. **Fluksi i Punës**

---

## 🏗️ 1. Arkitektura e Përgjithshme

### Teknologjitë e Përdorura:
- **Backend**: Laravel 11 + PHP 8.1
- **Frontend**: Vue.js 3 + Vue Router
- **Bazë të Dhënash**: MySQL
- **Stilizim**: Tailwind CSS + CSS Custom
- **Build Tools**: Vite
- **PDF Generation**: Laravel DomPDF
- **AI**: Google Gemini API

### Struktura e Projektit:
```
CV_Maker/
├── app/                    # Laravel Backend
│   ├── Http/Controllers/   # Kontrolluesit
│   ├── Models/            # Modelet e të dhënave
│   └── Services/          # Shërbimet e biznesit
├── resources/js/          # Vue.js Frontend
│   ├── components/        # Komponentët Vue
│   ├── pages/            # Faqet kryesore
│   └── services/         # Shërbimet frontend
├── database/             # Migrimet dhe seeders
└── public/               # Burimet statike
```

---

## ⚙️ 2. Backend (Laravel)

### Kontrolluesit Kryesorë:

#### **API Controllers:**
```php
// app/Http/Controllers/Api/CvController.php
class CvController extends Controller 
{
    public function store(Request $request) {
        // Krijon CV të ri
        $cv = $this->cvService->create($request->all());
        return response()->json($cv);
    }
    
    public function show($id) {
        // Merr CV-në me ID
        $cv = Cv::with(['workExperiences', 'education', 'skills'])->find($id);
        return response()->json($cv);
    }
    
    public function downloadPdf($id) {
        // Gjeneron PDF nga CV
        $cv = Cv::find($id);
        $pdf = Pdf::loadView('pdf.cv', ['cv' => $cv]);
        return $pdf->download('cv.pdf');
    }
}
```

#### **AI Controller:**
```php
// app/Http/Controllers/Api/AIController.php
class AIController extends Controller 
{
    public function generateSummary(Request $request) {
        // Gjeneron përmbledhje profesionale me AI
        $summary = $this->geminiService->generateSummary($request->all());
        return response()->json(['summary' => $summary]);
    }
    
    public function skillsSuggestions(Request $request) {
        // Sugjeron aftësi bazuar në profesion
        $skills = $this->geminiService->suggestSkills($request->jobTitle);
        return response()->json(['skills' => $skills]);
    }
}
```

### Modelet e Të Dhënave:

#### **CV Model:**
```php
// app/Models/Cv.php
class Cv extends Model 
{
    protected $fillable = [
        'user_id', 'title', 'template', 'status', 'views', 'downloads'
    ];
    
    public function user() {
        return $this->belongsTo(User::class);
    }
    
    public function workExperiences() {
        return $this->hasMany(WorkExperience::class);
    }
    
    public function education() {
        return $this->hasMany(Education::class);
    }
    
    public function skills() {
        return $this->hasMany(Skill::class);
    }
}
```

### Shërbimet e Biznesit:

#### **GeminiAIService:**
```php
// app/Services/GeminiAIService.php
class GeminiAIService 
{
    public function generateSummary($data) {
        // Krijon prompt për AI
        $prompt = $this->buildSummaryPrompt($data);
        
        // Dërgon kërkesë në Gemini API
        $response = $this->callGeminiAPI($prompt);
        
        // Analizon përgjigjen
        return $this->parseSummaryResponse($response);
    }
    
    public function suggestSkills($jobTitle) {
        // Sugjeron aftësi bazuar në titull pune
        $skills = $this->getSkillsForJob($jobTitle);
        return $skills;
    }
}
```

---

## 🎨 3. Frontend (Vue.js)

### Komponenti Kryesor - CreateCV.vue:

#### **Struktura e Të Dhënave:**
```javascript
// resources/js/components/pages/CreateCV.vue
const cv = ref({
    title: '',
    personalInfo: {
        firstName: '',
        lastName: '',
        email: '',
        phone: '',
        address: '',
        dateOfBirth: '',
        nationality: ''
    },
    summary: '',
    experience: [],
    education: [],
    skills: [],
    interests: [],
    selectedTemplate: 'modern'
});
```

#### **Hapat e Krijimit:**
```javascript
const wizardSteps = ref([
    { id: 0, title: 'Informacioni Personal', timeEstimate: 3 },
    { id: 1, title: 'Përmbledhja Profesionale', timeEstimate: 5 },
    { id: 2, title: 'Përvoja e Punës', timeEstimate: 8 },
    { id: 3, title: 'Arsimimi & Aftësitë', timeEstimate: 4 },
    { id: 4, title: 'Shqyrtimi & Finalizimi', timeEstimate: 2 }
]);
```

#### **Funksionet Kryesore:**
```javascript
// Auto-save funksionaliteti
const triggerAutoSave = () => {
    clearTimeout(autoSaveTimer.value);
    autoSaveTimer.value = setTimeout(() => {
        saveCvDraft();
    }, 2000);
};

// Shtimi i përvojës së punës
const addExperience = () => {
    cv.value.experience.push({
        position: '',
        company: '',
        start_date: '',
        end_date: '',
        description: '',
        current: false
    });
};

// Gjenerimi i përmbledhjes me AI
const generateAISummary = async () => {
    try {
        const response = await aiService.generateSummary({
            jobTitle: cv.value.experience[0]?.position,
            experience: cv.value.experience,
            skills: cv.value.skills
        });
        cv.value.summary = response.summary;
    } catch (error) {
        console.error('AI Summary Error:', error);
    }
};
```

### Shërbimi i AI-së:

#### **AI Service Frontend:**
```javascript
// resources/js/services/aiService.js
class AIService {
    async generateSummary(data) {
        const response = await fetch('/api/ai/generate-summary', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });
        return response.json();
    }
    
    async suggestSkills(jobTitle) {
        const response = await fetch('/api/ai/skills-suggestions', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ jobTitle })
        });
        return response.json();
    }
    
    analyzeCV(cvData) {
        let score = 0;
        // Analiza e plotësisë së CV-së
        if (cvData.summary && cvData.summary.length >= 50) score += 20;
        if (cvData.experience.length >= 2) score += 30;
        if (cvData.education.length >= 1) score += 15;
        if (cvData.skills.length >= 5) score += 20;
        // ... më shumë logjikë analize
        return { score, suggestions: this.generateSuggestions(cvData) };
    }
}
```

---

## 🔧 4. Veçoritë Kryesore

### **Sistemi i Shablloneve:**
```javascript
// Shabllone të ndryshme CV-je
const templates = [
    { 
        id: 'classic', 
        name: 'Klasik', 
        description: 'Dizajn tradicional dhe profesional' 
    },
    { 
        id: 'modern', 
        name: 'Modern', 
        description: 'Dizajn i pastër dhe bashkëkohor' 
    },
    { 
        id: 'professional', 
        name: 'Profesional', 
        description: 'Dizajn korporativ për role biznesi' 
    },
    { 
        id: 'creative', 
        name: 'Kreativ', 
        description: 'Dizajn unik për profesionistë kreativë' 
    }
];
```

### **Sistemi i Vlerësimit:**
```javascript
// Vlerësimi i cilësisë së CV-së
const calculateCVScore = (cvData) => {
    const sections = [
        { name: 'personalInfo', weight: 10 },
        { name: 'summary', weight: 20 },
        { name: 'experience', weight: 30 },
        { name: 'education', weight: 15 },
        { name: 'skills', weight: 20 },
        { name: 'interests', weight: 5 }
    ];
    
    let totalScore = 0;
    sections.forEach(section => {
        if (isCompletedSection(cvData[section.name])) {
            totalScore += section.weight;
        }
    });
    
    return totalScore;
};
```

### **Funksionaliteti i Auto-Save:**
```javascript
// Ruajtja automatike e ndryshimeve
const autoSaveSystem = {
    timer: null,
    interval: 2000, // 2 sekonda
    
    trigger() {
        clearTimeout(this.timer);
        this.timer = setTimeout(() => {
            this.save();
        }, this.interval);
    },
    
    async save() {
        try {
            await fetch('/api/cvs/draft', {
                method: 'POST',
                body: JSON.stringify(cv.value)
            });
            showNotification('CV u ruajt automatikisht', 'success');
        } catch (error) {
            showNotification('Gabim në ruajtjen automatike', 'error');
        }
    }
};
```

---

## 🤖 5. Integrim me AI (Gemini)

### **Gjenerimi i Përmbledhjes:**
```javascript
// Gjenerimi i përmbledhjes profesionale
const generateProfessionalSummary = async (userData) => {
    const prompt = `
        Krijo një përmbledhje profesionale për:
        - Pozicioni: ${userData.jobTitle}
        - Vite përvoje: ${userData.yearsExperience}
        - Aftësi kryesore: ${userData.skills.join(', ')}
        
        Përmbledhja duhet të jetë 80-120 fjalë dhe profesionale.
    `;
    
    const response = await geminiAPI.generateContent(prompt);
    return response.text();
};
```

### **Sugjerimet e Aftësive:**
```javascript
// Sugjerimet e aftësive bazuar në profesion
const getSkillSuggestions = (jobTitle) => {
    const skillsDatabase = {
        'Software Engineer': [
            'JavaScript', 'Python', 'React', 'Vue.js', 'Node.js',
            'Docker', 'AWS', 'Git', 'SQL', 'TypeScript'
        ],
        'Designer': [
            'Figma', 'Adobe Photoshop', 'Adobe Illustrator',
            'UI/UX Design', 'Prototyping', 'User Research'
        ],
        'Marketing Manager': [
            'Digital Marketing', 'SEO/SEM', 'Google Analytics',
            'Social Media', 'Content Marketing', 'Email Marketing'
        ]
    };
    
    return skillsDatabase[jobTitle] || [];
};
```

---

## 🗄️ 6. Bazë e të Dhënave

### **Struktura e Tabelave:**
```sql
-- Tabela e përdoruesve
CREATE TABLE users (
    id BIGINT PRIMARY KEY,
    name VARCHAR(255),
    email VARCHAR(255) UNIQUE,
    password VARCHAR(255),
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- Tabela e CV-ve
CREATE TABLE cvs (
    id BIGINT PRIMARY KEY,
    user_id BIGINT,
    title VARCHAR(255),
    template VARCHAR(50),
    status ENUM('draft', 'published'),
    views INT DEFAULT 0,
    downloads INT DEFAULT 0,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Tabela e përvojës së punës
CREATE TABLE work_experiences (
    id BIGINT PRIMARY KEY,
    cv_id BIGINT,
    position VARCHAR(255),
    company VARCHAR(255),
    start_date DATE,
    end_date DATE,
    description TEXT,
    current BOOLEAN DEFAULT false,
    FOREIGN KEY (cv_id) REFERENCES cvs(id)
);

-- Tabela e arsimimit
CREATE TABLE education (
    id BIGINT PRIMARY KEY,
    cv_id BIGINT,
    degree VARCHAR(255),
    institution VARCHAR(255),
    start_date DATE,
    end_date DATE,
    gpa DECIMAL(3,2),
    FOREIGN KEY (cv_id) REFERENCES cvs(id)
);

-- Tabela e aftësive
CREATE TABLE skills (
    id BIGINT PRIMARY KEY,
    cv_id BIGINT,
    name VARCHAR(255),
    level ENUM('beginner', 'intermediate', 'advanced', 'expert'),
    category VARCHAR(100),
    years_experience INT,
    FOREIGN KEY (cv_id) REFERENCES cvs(id)
);
```

---

## 🔄 7. Fluksi i Punës

### **Procesi i Krijimit të CV-së:**

1. **Regjistrimi/Kyçja**
   ```javascript
   // Përdoruesi regjistrohet ose kyçet
   await authService.login(credentials);
   router.push('/dashboard');
   ```

2. **Zgjedhja e Shabllonit**
   ```javascript
   // Përdoruesi zgjedh shabllonin
   selectTemplate(templateId);
   router.push(`/create-cv?template=${templateId}`);
   ```

3. **Plotësimi i Informacionit (5 Hapa)**
   ```javascript
   // Hapi 1: Informacioni Personal
   updatePersonalInfo(personalData);
   
   // Hapi 2: Përmbledhja (me AI)
   if (useAI) {
       cv.summary = await generateAISummary();
   }
   
   // Hapi 3: Përvoja e Punës
   addWorkExperience(experienceData);
   
   // Hapi 4: Arsimimi & Aftësitë
   addEducation(educationData);
   addSkills(skillsData);
   
   // Hapi 5: Shqyrtimi & Finalizimi
   reviewAndFinalize();
   ```

4. **Ruajtja dhe Eksportimi**
   ```javascript
   // Ruajtja e CV-së
   await saveCv(cvData);
   
   // Eksportimi si PDF
   const pdfUrl = await generatePDF(cvId);
   downloadFile(pdfUrl);
   ```

### **Komunikimi Frontend-Backend:**

```javascript
// Fluksi i të dhënave
Frontend (Vue.js) 
    ↓ HTTP Request
Backend (Laravel API)
    ↓ Database Query
MySQL Database
    ↓ Response
Backend (Laravel API)
    ↓ JSON Response
Frontend (Vue.js)
    ↓ Update UI
User Interface
```

---

## 📊 Statistikat e Performancës

### **Optimizimi i Performancës:**
- **Caching**: Ruajtja e përkohshme e sugjerimeve të AI
- **Lazy Loading**: Ngarkimi i komponentëve kur duhen
- **Debouncing**: Vonesa në auto-save për të shmangur kërkesa të tepërta
- **Database Indexing**: Indekse për kërkime të shpejta

### **Metritat Kryesore:**
- **Koha e ngarkimit**: < 2 sekonda
- **Koha e ruajtjes**: < 1 sekond
- **Gjenerimi i PDF**: < 3 sekonda
- **Përgjigja e AI**: < 5 sekonda

---

## 🎯 Përfundim

**CV_Maker** është një aplikacion i plotë që kombinon teknologjitë moderne për të krijuar një përvojë të shkëlqyer për përdoruesit. Përdorimi i Laravel-it për backend dhe Vue.js për frontend, së bashku me integrimin e AI-së, e bën atë një zgjidhje të fuqishme dhe moderne për krijimin e CV-ve profesionale.

### **Pikat e Forta:**
- ✅ Arkitekturë e qartë dhe e mirë-organizuar
- ✅ Integrim i avancuar me AI për sugjerime inteligjente
- ✅ Sistem shabllonesh i shumëllojshëm
- ✅ Ruajtje automatike dhe eksportim PDF
- ✅ Ndërfaqe përdoruesi intuitive dhe responsive

### **Mundësi për Përmirësim:**
- 🔄 Testim më i gjerë i funksionaliteteve
- 🔄 Optimizim i mëtejshëm i performancës
- 🔄 Shtim i më shumë gjuhëve mbështetëse
- 🔄 Analitika të avancuara për përdoruesit

---

*Prezantimi i krijuar për të shpjeguar arkitekturën dhe funksionalitetin e aplikacionit CV_Maker në gjuhën shqipe.* 