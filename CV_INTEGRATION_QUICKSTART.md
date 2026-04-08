# CV Integration - Quick Start Guide

## 🚀 Get Started in 5 Minutes

### Option A: Without Python Service (Basic Mode)

Use native CI4 CV parsing (no AI):

```bash
# 1. Nothing to install - works out of the box!
# 2. Users can already upload CVs via /profile/cv-analyze
# 3. Basic extraction (email, phone, skills)
```

**URL**: `/profile/cv-analyze`

---

### Option B: With Python Service (AI Mode - Recommended)

Full intelligent parsing with Ollama:

#### **Step 1: Install Python Service**

```bash
# Clone repository
cd /tmp
git clone <your-repo> cv-parsing-service

# Install dependencies
cd cv-parsing-service
pip install -r requirements.txt

# Install Ollama (https://ollama.ai)
# macOS: brew install ollama
# Linux: Follow https://ollama.ai/download/linux
# Windows: Download from https://ollama.ai

# Pull a model
ollama pull mistral
# Takes 2-3 min first time (4GB download)
```

**For production deployment**, see [DEPLOYMENT.md](cv-parsing-service/DEPLOYMENT.md)

#### **Step 2: Start Services**

```bash
# Terminal 1: Start Ollama
ollama serve
# Runs on localhost:11434

# Terminal 2: Start Python service
cd cv-parsing-service
python main.py
# Runs on localhost:8001
# Check: curl http://localhost:8001/health
```

#### **Step 3: Configure CI4**

```bash
# In your Automoni/.env file:
CV_PARSING_ENABLED=true
CV_PARSING_BASE_URL=http://localhost:8001
CV_PARSING_API_KEY=your-secret-key-here
```

#### **Step 4: Test**

```bash
# Open browser
http://localhost:8000/profile/cv-integrate

# Upload a CV
# Click "Parse CV"
# Should see preview with parsed data
```

---

## 📋 What Gets Parsed

```
✅ Profile:
  - Name, headline, email, phone
  - City, country, summary

✅ Skills:
  - Extracted with confidence scores
  - Can edit/remove

✅ Work Experience:
  - Title, company, dates, location
  - Job descriptions

✅ Education:
  - Degree, field, institution
  - Graduation year

✅ Languages:
  - Language, proficiency level

✅ Certifications:
  - Name, issuer, dates
```

---

## 🧪 Testing

### With Real CV

```bash
# 1. Go to http://localhost:8000/profile/cv-integrate
# 2. Upload your own CV
# 3. Review parsed data
# 4. Click "Save & Update Profile"
# 5. Check /profile to see updated data
```

### With Demo CV

Create `test_cv.txt`:
```
JOHN DOE
Senior PHP Developer
john.doe@example.com
+33 6 12 34 56 78

PROFESSIONAL SUMMARY
10+ years of PHP development experience

SKILLS
PHP, Laravel, Docker, Kubernetes, PostgreSQL, Redis

LANGUAGES
English (Fluent), French (Native)

WORK EXPERIENCE
Senior Developer at TechCorp
2020 - Present | Paris, France
Led backend development team

EDUCATION
Master's in Computer Science
University of Paris, 2012
```

Upload as PDF (convert txt to PDF first)

---

## 🔍 URLs

| Action | URL |
|--------|-----|
| Upload & Parse | `/profile/cv-integrate` |
| Profile (show) | `/profile` |
| Profile (edit) | `/profile/edit` |
| Old CV page | `/profile/cv-analyze` |

---

## 🆘 Troubleshooting

### "Service unavailable"
```bash
# Check Python service
curl http://localhost:8001/health
# Should return: {"status":"ok"}

# If not running, start it:
cd cv-parsing-service && python main.py
```

### "File upload too large"
```bash
# In .env, increase limit:
CV_PARSING_MAX_SIZE_MB=20
```

### "Low confidence scores"
```
• CV is poorly formatted → User can edit manually
• Scanned image → Enable OCR in Python service
• Try a different model: neural-chat, dolphin-mixtral
```

### "Session errors"
```bash
# Clear browser cookies and try again
# or use incognito mode
```

---

## 📊 System Check

Run this in your project:

```php
// Add to a test route
$client = new \App\Services\CvParsingClient();
echo $client->isHealthy() ? '✅ Healthy' : '❌ Down';
```

---

## 🎓 Common Use Cases

### Use Case 1: Import from LinkedIn
```
1. Export CV from LinkedIn (PDF)
2. Upload to /profile/cv-integrate
3. Review and save
Done! ✨
```

### Use Case 2: Manual CV Update
```
1. Upload old CV
2. Edit fields in preview
3. Remove outdated items
4. Save
Done! ✨
```

### Use Case 3: Batch Import
```
(Future feature)
- Upload multiple CVs
- Auto-fill all at once
- Review each one
```

---

## 📚 More Info

See full documentation: `CV_INTEGRATION_DOCUMENTATION.md`

---

**Need help?** Check logs:

```bash
# CI4 logs
tail -f ./writable/logs/log-*.log

# Python logs
tail -f cv-parsing-service/logs/cv_parsing.log
```

**Made with ❤️ by the Automoni Team**
