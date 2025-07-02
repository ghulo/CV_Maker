# Gemini AI Integration Setup Guide

## Overview
This CV Maker application now includes Google Gemini AI integration for intelligent CV content generation.

## Environment Configuration

Add the following variables to your `.env` file:

```env
# Google Gemini AI Configuration
GEMINI_API_KEY=your_gemini_api_key_here
GEMINI_MODEL=gemini-1.5-flash

# Optional: For production, you might want to use gemini-1.5-pro
# GEMINI_MODEL=gemini-1.5-pro
```

## Getting a Gemini API Key

1. Visit [Google AI Studio](https://aistudio.google.com/app/apikey)
2. Sign in with your Google account
3. Click "Create API Key"
4. Choose "Create API key in new project" or select an existing project
5. Copy the generated API key
6. Add it to your `.env` file as `GEMINI_API_KEY=your_actual_api_key_here`

## AI Features Available

### 1. Professional Summary Generation
- Generates 3 different summary styles (Professional, Creative, Results-focused)
- Tailored to job title, experience level, and industry
- 80-120 words optimized for ATS compatibility

### 2. Skills Suggestions
- Industry-specific skill recommendations
- Trending skills for 2024
- Technical and soft skills balanced approach

### 3. Work Experience Descriptions
- Quantified achievement-focused bullet points
- Action verb optimization
- Industry and company size customization

### 4. CV Analysis
- Comprehensive scoring (0-100)
- ATS compatibility analysis
- Actionable improvement recommendations

### 5. Interest Suggestions
- Professionally relevant interests
- Industry-aligned recommendations
- Personality showcasing while maintaining professionalism

## API Endpoints

All AI features are available through these endpoints:

```
POST /api/ai/skills-suggestions
POST /api/ai/generate-summary
POST /api/ai/experience-suggestions
POST /api/ai/analyze-cv
POST /api/ai/interest-suggestions
```

## Fallback System

The application includes a robust fallback system:

1. **Primary**: Gemini AI generation
2. **Fallback**: Template-based generation
3. **Emergency**: Static fallback responses

This ensures the application remains functional even if:
- Gemini API is unavailable
- API key is missing or invalid
- Network connectivity issues occur

## Response Format

All AI endpoints return responses with a `source` field indicating:
- `gemini_ai`: Generated using Gemini AI
- `template_fallback`: Generated using templates
- `error_fallback`: Static fallback due to errors

## Performance Features

- **Caching**: AI responses are cached for 1 hour to improve performance
- **Timeouts**: 30-second timeout to prevent hanging requests
- **Retry Logic**: Built-in retry mechanism with exponential backoff
- **Error Logging**: Comprehensive logging for debugging

## Usage Example

Frontend usage:
```javascript
// Generate skills suggestions
const response = await aiService.getSkillSuggestions('Software Engineer', 'Technology', 'intermediate');

// Generate professional summary
const summaries = await aiService.generateSummary({
  job_title: 'Software Engineer',
  experience_years: 3,
  skills: ['JavaScript', 'React', 'Node.js'],
  industry: 'Technology'
});
```

## Monitoring

Check logs for AI service usage:
```bash
tail -f storage/logs/laravel.log | grep "Gemini"
```

## Cost Considerations

- Gemini AI has usage-based pricing
- Responses are cached to minimize API calls
- Fallback system reduces dependency on paid API
- Monitor usage through Google Cloud Console

## Security

- API key is stored securely in environment variables
- No sensitive data is logged
- Requests are made over HTTPS
- User data is not stored by Google Gemini

## Troubleshooting

### Common Issues

1. **"Gemini API key not configured"**
   - Ensure `GEMINI_API_KEY` is set in `.env`
   - Restart the application after adding the key

2. **"Gemini API request failed"**
   - Check API key validity
   - Verify internet connectivity
   - Check Google AI Studio for service status

3. **Responses seem generic**
   - This indicates fallback mode is active
   - Check logs for Gemini AI errors
   - Verify API quota hasn't been exceeded

### Debug Mode

Enable debug logging by setting `LOG_LEVEL=debug` in your `.env` file.

## Updates and Maintenance

- API keys should be rotated periodically
- Monitor Google AI updates for new models
- Update model names as newer versions become available
- Regular testing of AI endpoints recommended 