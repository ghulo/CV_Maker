# ATELIER CV Maker - API Documentation

## 📋 Overview
ATELIER CV Maker provides a comprehensive RESTful API for professional resume creation and management. Built with Laravel 11 and secured with Sanctum authentication.

**Base URL:** `http://localhost:8000/api`
**Authentication:** Bearer Token (Sanctum)

---

## 🔐 Authentication Endpoints

### Register User
```http
POST /api/register
Content-Type: application/json

{
  "name": "John Doe",
  "email": "john@example.com", 
  "password": "password123",
  "password_confirmation": "password123"
}
```

**Response (201):**
```json
{
  "success": true,
  "message": "User registered successfully",
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "created_at": "2024-12-25T10:00:00.000000Z"
  },
  "token": "1|abc123def456..."
}
```

### Login User
```http
POST /api/login
Content-Type: application/json

{
  "email": "john@example.com",
  "password": "password123"
}
```

**Response (200):**
```json
{
  "success": true,
  "message": "Login successful",
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com"
  },
  "token": "2|xyz789abc123..."
}
```

### Logout User
```http
POST /api/logout
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "success": true,
  "message": "Logged out successfully"
}
```

---

## 👤 User Management

### Get Current User
```http
GET /api/user
Authorization: Bearer {token}
```

### Get User Profile
```http
GET /api/user/profile
Authorization: Bearer {token}
```

### Update User Profile
```http
PUT /api/user/profile
Authorization: Bearer {token}
Content-Type: application/json

{
  "headline": "Senior Software Engineer",
  "bio": "Experienced developer with 5+ years...",
  "phone": "+1234567890",
  "address": "123 Main St, City, Country",
  "social_links": {
    "linkedin": "https://linkedin.com/in/johndoe",
    "github": "https://github.com/johndoe"
  }
}
```

---

## 📄 CV Management

### Get User's CVs
```http
GET /api/my-cvs
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "success": true,
  "cvs": [
    {
      "id": 1,
      "title": "Senior Developer CV",
      "personalInfo": {
        "firstName": "John",
        "lastName": "Doe",
        "email": "john@example.com",
        "phone": "+1234567890",
        "address": "123 Main St"
      },
      "selectedTemplate": "modern",
      "isFinalized": true,
      "views": 45,
      "downloads": 12,
      "createdAt": "2024-12-20T10:00:00.000000Z"
    }
  ]
}
```

### Create New CV
```http
POST /api/cvs
Authorization: Bearer {token}
Content-Type: application/json

{
  "title": "Senior Developer CV",
  "personalDetails": {
    "firstName": "John",
    "lastName": "Doe",
    "email": "john@example.com",
    "phone": "+1234567890",
    "address": "123 Main St, City, Country",
    "dateOfBirth": "1990-01-15",
    "nationality": "American",
    "gender": "male"
  },
  "summary": "Experienced software engineer with 8+ years...",
  "experience": [
    {
      "title": "Senior Software Engineer",
      "company": "Tech Corp",
      "startDate": "2020-01",
      "endDate": "2024-01",
      "isCurrent": false,
      "description": "Led development team of 5 engineers..."
    }
  ],
  "education": [
    {
      "institution": "University of Technology",
      "degree": "Bachelor of Computer Science",
      "fieldOfStudy": "Software Engineering",
      "startDate": "2016-09",
      "endDate": "2020-06",
      "location": "New York, USA"
    }
  ],
  "skills": [
    {
      "name": "JavaScript",
      "level": 5,
      "category": "Programming",
      "yearsOfExperience": 6
    },
    {
      "name": "React",
      "level": 4,
      "category": "Frontend",
      "yearsOfExperience": 4
    }
  ],
  "interests": [
    {
      "description": "Web Development, AI/ML, Open Source Contributing"
    }
  ],
  "selectedTemplate": "modern"
}
```

**Response (201):**
```json
{
  "success": true,
  "message": "CV created successfully",
  "cv": {
    "id": 15,
    "title": "Senior Developer CV",
    "userId": 1,
    "selectedTemplate": "modern",
    "isFinalized": false,
    "views": 0,
    "downloads": 0,
    "createdAt": "2024-12-25T14:30:00.000000Z"
  }
}
```

### Get Specific CV
```http
GET /api/cvs/{id}
Authorization: Bearer {token}
```

### Update CV
```http
PUT /api/cvs/{id}
Authorization: Bearer {token}
Content-Type: application/json

{
  "title": "Updated CV Title",
  "personalDetails": { /* same structure as create */ },
  "summary": "Updated professional summary...",
  "experience": [ /* updated experience array */ ],
  "education": [ /* updated education array */ ],
  "skills": [ /* updated skills array */ ],
  "interests": [ /* updated interests array */ ],
  "selectedTemplate": "professional"
}
```

### Delete CV
```http
DELETE /api/cvs/{id}
Authorization: Bearer {token}
```

### Download CV as PDF
```http
GET /api/cvs/{id}/download?style=default&quality=high
Authorization: Bearer {token}
```

**Query Parameters:**
- `style`: `default`, `minimal`, `professional` (optional)
- `quality`: `low`, `medium`, `high` (optional)

**Response:** PDF file download

### Duplicate CV
```http
POST /api/cvs/{id}/duplicate
Authorization: Bearer {token}
```

---

## 🎨 Templates

### Get Available Templates
```http
GET /api/templates
```

**Response (200):**
```json
{
  "success": true,
  "templates": [
    {
      "id": 1,
      "name": "Modern",
      "slug": "modern",
      "description": "Clean, contemporary design perfect for tech professionals",
      "isPremium": false,
      "isActive": true,
      "previewUrl": "/storage/templates/modern-preview.jpg"
    },
    {
      "id": 2,
      "name": "Professional",
      "slug": "professional", 
      "description": "Traditional layout ideal for corporate environments",
      "isPremium": false,
      "isActive": true,
      "previewUrl": "/storage/templates/professional-preview.jpg"
    }
  ]
}
```

### Get Specific Template
```http
GET /api/templates/{slug}
```

---

## 🤖 AI Features

### Generate Professional Summary
```http
POST /api/ai/generate-summary
Authorization: Bearer {token}
Content-Type: application/json

{
  "job_title": "Software Engineer",
  "experience_years": 5
}
```

**Response (200):**
```json
{
  "success": true,
  "summary": "Experienced Software Engineer with 5+ years of expertise in developing innovative solutions and leading high-impact projects."
}
```

### Get Skill Suggestions
```http
POST /api/ai/skills-suggestions
Authorization: Bearer {token}
Content-Type: application/json

{
  "job_title": "software engineer"
}
```

**Response (200):**
```json
{
  "success": true,
  "skills": [
    "JavaScript",
    "Python", 
    "React",
    "Node.js",
    "Git",
    "SQL",
    "Docker",
    "AWS"
  ],
  "job_title": "software engineer"
}
```

### Analyze CV Quality
```http
POST /api/ai/analyze-cv
Authorization: Bearer {token}
Content-Type: application/json

{
  "cv_data": {
    "summary": "Professional summary text...",
    "experience": [/* experience array */],
    "education": [/* education array */],
    "skills": [/* skills array */]
  }
}
```

**Response (200):**
```json
{
  "success": true,
  "analysis": {
    "score": 85,
    "suggestions": [
      {
        "type": "suggestion",
        "title": "Add More Skills",
        "description": "CVs with more skills get better visibility."
      }
    ],
    "issues": []
  }
}
```

---

## 📊 User Statistics

### Get User Statistics
```http
GET /api/user/stats
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "success": true,
  "stats": {
    "totalCvs": 5,
    "totalViews": 245,
    "totalDownloads": 67,
    "mostPopularCv": {
      "id": 3,
      "title": "Senior Developer CV",
      "views": 89,
      "downloads": 23
    },
    "recentActivity": [
      {
        "type": "cv_created",
        "title": "New CV Created",
        "description": "Created 'Marketing Manager CV'",
        "createdAt": "2024-12-25T09:30:00.000000Z"
      }
    ]
  }
}
```

### Get User Activity Log
```http
GET /api/user/activity
Authorization: Bearer {token}
```

---

## 🛡️ Security Features

### Authentication
- **Bearer Token Authentication** using Laravel Sanctum
- **Automatic Token Cleanup** on logout
- **Rate Limiting** on authentication endpoints
- **CSRF Protection** for web routes

### Data Protection
- **Input Validation** on all endpoints
- **SQL Injection Prevention** via Eloquent ORM
- **XSS Protection** through Laravel's built-in sanitization
- **User Data Isolation** - users can only access their own CVs

### Privacy
- **Data Encryption** for sensitive user information
- **Secure Password Storage** using bcrypt hashing
- **Optional Data Export** for GDPR compliance

---

## 📋 Error Responses

### Validation Error (422)
```json
{
  "success": false,
  "errors": {
    "email": [
      "The email field is required."
    ],
    "password": [
      "The password must be at least 8 characters."
    ]
  }
}
```

### Authentication Error (401)
```json
{
  "success": false,
  "message": "Unauthenticated."
}
```

### Authorization Error (403)
```json
{
  "success": false,
  "message": "This action is unauthorized."
}
```

### Not Found Error (404)
```json
{
  "success": false,
  "message": "CV not found or unauthorized"
}
```

### Server Error (500)
```json
{
  "success": false,
  "message": "An error occurred while processing your request",
  "error": "Detailed error message for debugging"
}
```

---

## 🧪 Testing

### Testing Endpoints with Postman

1. **Set Base URL**: `http://localhost:8000/api`
2. **Authentication**: Get token from login, add to Authorization header as `Bearer {token}`
3. **Content-Type**: Set to `application/json` for POST/PUT requests

### Example Test Flow
```bash
# 1. Register a new user
POST /api/register

# 2. Login to get token  
POST /api/login

# 3. Create a CV (use token from step 2)
POST /api/cvs

# 4. Get user's CVs
GET /api/my-cvs

# 5. Download CV as PDF
GET /api/cvs/{id}/download
```

---

## 📈 Performance & Scalability

### Database Optimization
- **Indexed Columns**: Foreign keys and frequently queried fields
- **Query Optimization**: Eager loading of relationships
- **Connection Pooling**: MySQL connection management

### Caching Strategy
- **API Response Caching**: Frequently accessed data
- **Template Caching**: Pre-compiled CV templates
- **Session Management**: Optimized user session storage

### Rate Limiting
- **Authentication Endpoints**: 5 requests per minute
- **API Endpoints**: 60 requests per minute per user
- **File Downloads**: 10 downloads per minute per user

---

**🔧 API Version**: v1.0
**📅 Last Updated**: December 25, 2024
**👨‍💻 Developed by**: ATELIER Development Team 