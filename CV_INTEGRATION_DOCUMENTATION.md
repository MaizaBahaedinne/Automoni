# CV Integration System - Documentation

## 🎯 Overview

This document describes the **CV Integration System** - a complete workflow for uploading CVs, parsing them with AI, and auto-filling user profiles with validated data.

The system integrates:
- **CI4 Backend** - File upload, profile management, data validation
- **Python Microservice** - Intelligent CV parsing with Ollama (local AI)
- **Session Storage** - Temporary data holding before validation

---

## 🏗️ Architecture

```
┌─────────────────────────────────────────────────────────────┐
│                    USER BROWSER                             │
│  ┌─────────────┐  ┌──────────────┐  ┌──────────────────┐   │
│  │Upload CV    │→ │Preview Data  │→ │Save to Profile   │   │
│  │(Drag&Drop)  │  │(Editable)    │  │(Confirmed)       │   │
│  └─────────────┘  └──────────────┘  └──────────────────┘   │
└────────┬──────────────────┬────────────────────┬────────────┘
         │                  │                    │
    [AJAX POST]         [AJAX POST]          [AJAX POST]
      /parse-cv          (process)           /cv-save
         │                  │                    │
         ▼                  ▼                    ▼
┌─────────────────────────────────────────────────────────────┐
│         CI4 BACKEND (CodeIgniter 4)                         │
│                                                              │
│  ┌─────────────────────┐  ┌──────────────────────────────┐  │
│  │ CvIntegration       │  │ CvParsingClient (Service)    │  │
│  │ Controller          │  │ - Calls Python API           │  │
│  │ - parseCv()         │→ │ - Sends file                 │  │
│  │ - saveProfileFromCv │  │ - Receives JSON              │  │
│  └────────┬────────────┘  └──────────────────────────────┘  │
│           │                                                   │
│           │ Session Storage                                   │
│           │ (Temporary data holding)                          │
│           │                                                   │
│      ┌────▼────────────────────┐                            │
│      │ session('cv_parse_result')                           │
│      │ - Stores parsed data    │                            │
│      │ - No DB commit yet      │                            │
│      └────────────────────────┘                            │
│                                                              │
└────────┬─────────────────────────────────────────────────────┘
         │
         │ (Guzzle HTTP Client)
         │
         ▼
┌─────────────────────────────────────────────────────────────┐
│    PYTHON MICROSERVICE (FastAPI)                            │
│    [http://localhost:8001]                                  │
│                                                              │
│  ┌──────────────────────────────────────────────────────┐  │
│  │ POST /api/parse-cv                                  │  │
│  │                                                      │  │
│  │  1. Receive file (PDF/DOCX/JPG/PNG)                │  │
│  │  2. Extract text                                   │  │
│  │  3. Parse with Ollama (Local LLM)                  │  │
│  │  4. Calculate confidence scores                    │  │
│  │  5. Return JSON with structured data               │  │
│  │                                                      │  │
│  │  Response:                                          │  │
│  │  {                                                  │  │
│  │    success: true,                                  │  │
│  │    data: {                                          │  │
│  │      profile: {...},                               │  │
│  │      skills: [...],                                │  │
│  │      experiences: [...],                           │  │
│  │      education: [...],                             │  │
│  │      languages: [...]                              │  │
│  │    }                                                │  │
│  │  }                                                  │  │
│  └──────────────────────────────────────────────────────┘  │
│                                                              │
└────────┬──────────────────────────────────────────────────────┘
         │
         ▼
┌─────────────────────────────────────────────────────────────┐
│    OLLAMA (Local LLM)                                        │
│    - Mistral / Neural-Chat / Dolphin-Mixtral               │
│    - Runs locally on machine                                │
│    - No cloud dependency                                    │
└─────────────────────────────────────────────────────────────┘
```

---

## 📂 File Structure

```
app/
├── Controllers/
│   └── CvIntegrationController.php      (NEW) Main controller
│
├── Services/
│   └── CvParsingClient.php              (NEW) Python API client
│
├── Config/
│   └── CvParsing.php                    (NEW) Configuration
│
└── Views/
    └── profile/
        └── cv_integrate.php             (NEW) Integration page
```

---

## 🔄 User Flow

### **Step 1: Upload CV**
```
User: Opens /profile/cv-integrate
→ Uploads CV file (PDF/DOCX/JPG/PNG)
→ Clicks "Parse CV" button
→ AJAX POST to /profile/cv-parse
```

### **Step 2: Parsing**
```
Controller::parseCv()
│
├─ Validate file (type, size)
├─ Save temporarily
├─ Call CvParsingClient::parseCv()
│   ├─ Make Guzzle request to Python service
│   ├─ Send file to http://localhost:8001/api/parse-cv
│   ├─ Wait for JSON response
│   └─ Return structured data
│
└─ Store in session: session('cv_parse_result', $result)
   (No database commit yet)
```

### **Step 3: Preview & Edit**
```
Frontend receives JSON
→ Renders form with extracted data
→ All fields are editable
→ Shows confidence scores
→ User can:
   - Edit any field
   - Add/remove items
   - Review before saving
```

### **Step 4: Save**
```
User confirms data
→ AJAX POST to /profile/cv-save
→ Controller::saveProfileFromCv()
   ├─ Profile table (headline, phone, summary, etc.)
   ├─ Skills table (bulk insert)
   ├─ Languages table (bulk insert)
   ├─ Experiences table (bulk insert)
   ├─ Education table (bulk insert)
   ├─ Certifications table (bulk insert)
   └─ Recalculate profile completeness
→ Clear session data
→ Redirect to /profile
```

---

## 🔐 Security & Validation

### **File Validation**
- ✅ Extension check (pdf, doc, docx, jpg, jpeg, png)
- ✅ Size limit (10MB default, configurable)
- ✅ MIME type validation
- ✅ Auth filter (logged-in users only)

### **Data Validation**
- ✅ XSS prevention (esc() on all user-editable fields)
- ✅ CSRF protection (token verification)
- ✅ Type casting (int for years, trim for strings)
- ✅ URL validation for profile fields

### **Session Security**
- ✅ Data stored in session (server-side, secure)
- ✅ Automatic cleanup after save
- ✅ No data persisted to disk
- ✅ Timeout if user doesn't complete flow

---

## ⚙️ Setup & Configuration

### **1. Install Python Service (Optional but Recommended)**

If you want intelligent CV parsing with Ollama:

```bash
# Clone or create Python service
cd cv-parsing-service/
pip install -r requirements.txt

# Install Ollama
# See: https://ollama.ai

# Pull a model
ollama pull mistral
# or: ollama pull neural-chat, ollama pull dolphin-mixtral

# Start service
python main.py
# Runs on http://localhost:8001
```

### **2. Configure CI4**

Create/edit `.env` file in project root:

```bash
# Enable CV Parsing
CV_PARSING_ENABLED=true

# Python service URL
CV_PARSING_BASE_URL=http://localhost:8001

# API Key (must match Python service)
CV_PARSING_API_KEY=your-secret-key-here

# Timeouts (seconds)
CV_PARSING_TIMEOUT=60
CV_PARSING_CONNECT_TIMEOUT=10

# Max file size (MB)
CV_PARSING_MAX_SIZE_MB=10
```

### **3. Verify Routes**

```bash
./spark routes | grep cv-integrate
# Should see:
# GET  profile/cv-integrate  CvIntegrationController::showIntegrationPage
# POST profile/cv-parse      CvIntegrationController::parseCv
# POST profile/cv-save       CvIntegrationController::saveProfileFromCv
```

---

## 📋 API Response Example

**Python Service Returns:**

```json
{
  "success": true,
  "message": "CV parsed successfully",
  "data": {
    "profile": {
      "first_name": "Jean",
      "last_name": "Dupont",
      "headline": "Senior PHP Developer",
      "email": "jean.dupont@example.com",
      "phone": "+33 6 12 34 56 78",
      "city": "Paris",
      "country": "France",
      "summary": "10+ years of experience in PHP development...",
      "confidences": {
        "first_name": {"score": 0.95, "reason": "Found in header"},
        "email": {"score": 0.99, "reason": "Email pattern matched"},
        "headline": {"score": 0.87, "reason": "Job title extracted"},
        ...
      }
    },
    "skills": [
      {"name": "PHP", "confidence": 0.98, "category": "Programming Language"},
      {"name": "Laravel", "confidence": 0.92, "category": "Framework"},
      {"name": "Docker", "confidence": 0.85, "category": "DevOps"}
    ],
    "experiences": [
      {
        "title": "Senior Developer",
        "organization": "TechCorp",
        "start_year": 2020,
        "end_year": 2024,
        "location": "Paris",
        "is_current": false,
        "confidence": 0.90
      }
    ],
    "education": [
      {
        "degree": "Master's",
        "field": "Computer Science",
        "institution": "University of Paris",
        "year_graduated": 2012,
        "confidence": 0.88
      }
    ],
    "languages": [
      {"name": "English", "level": "C2", "confidence": 0.95},
      {"name": "French", "level": "native", "confidence": 1.0}
    ],
    "certifications": [
      {
        "name": "AWS Solutions Architect",
        "organization": "Amazon",
        "issue_date": "2022-06-15",
        "credential_url": "..."
      }
    ]
  }
}
```

---

## 🧠 Temporary Data Storage Decision

**Why Session instead of Cache?**

| Aspect | Session | Cache | Database |
|--------|---------|-------|----------|
| **Persistence** | During browser session | Configurable TTL | Permanent |
| **User isolation** | ✅ Per user | ❌ Global / shared | ✅ Per user |
| **Data size** | ✅ Optimal | ✅ OK | ✅ OK |
| **Privacy** | ✅ Secure | ❌ potential issues | ✅ Private |
| **Cleanup** | ✅ Auto on logout | ❌ Manual | ❌ Manual |
| **Use case** | **✅ BEST FOR FORMS** | Cached content | Persistence |

**Decision: Use Session** because:
- 🔒 User-isolated (no cross-user pollution)
- 🧹 Automatic cleanup on logout
- 📝 Perfect for multi-step forms
- ⏰ Natural lifecycle (browser session)

---

## 🔗 Controller Methods Reference

### `CvIntegrationController::showIntegrationPage()`
```php
GET /profile/cv-integrate
- Shows upload form
- No parameters
- Returns: HTML view
```

### `CvIntegrationController::parseCv()`
```php
POST /profile/cv-parse
- Parameters: multipart form data (cv_file)
- Returns: JSON
  {
    success: bool,
    message: string,
    data: {...parsed_data...}
  }
```

### `CvIntegrationController::saveProfileFromCv()`
```php
POST /profile/cv-save
- Parameters: JSON body with form data
  {
    profile: {...},
    skills: [...],
    experiences: [...],
    ...
  }
- Returns: JSON
  {
    success: bool,
    message: string,
    redirect: string
  }
- Side effects:
  - Updates profile table
  - Updates related tables (skills, exp, etc.)
  - Clears session data
```

---

## 🚀 Features

✅ **Smart Upload** - Drag & drop or click to select  
✅ **File Validation** - Type, size, and content checks  
✅ **AI Parsing** - Intelligent extraction with Ollama  
✅ **Confidence Scores** - Shows how confident each field is  
✅ **Editable Preview** - Users can review and modify before saving  
✅ **Batch Operations** - Add multiple skills, languages, experiences  
✅ **Session Safety** - No data saved until user confirms  
✅ **Error Handling** - Graceful fallbacks and clear messages  
✅ **Responsive Design** - Works on desktop and mobile  
✅ **Logging** - Track all operations

---

## 📝 Notes & Warnings

### ⚠️ Important

1. **Python Service Must Be Running** - If disabled/down, system gracefully degrades
2. **API Key Security** - Change `CV_PARSING_API_KEY` in production
3. **File Size** - Large files (>10MB) will be rejected
4. **OCR Accuracy** - Scanned PDFs may have lower confidence scores
5. **Session Timeout** - Data is cleared if browser session ends

### 🔧 Troubleshooting

**"CV Parsing service unavailable"**
- Check Python service is running: `http://localhost:8001/health`
- Check `CV_PARSING_BASE_URL` in `.env`

**"File too large"**
- Increase `CV_PARSING_MAX_SIZE_MB` in `.env`

**"Low confidence scores"**
- CV may be poorly formatted or scanned image
- User can manually edit any field

---

## 🎓 Extension Points

### Add New Profile Fields

In `CvIntegrationController::saveProfileFromCv()`:

```php
// ── New Section ────────────────────────────────────────
if (!empty($data['my_field'])) {
    $profileData['my_field'] = esc($data['my_field']);
}
```

In `app/Views/profile/cv_integrate.php`:

```html
<!-- Add to preview-card -->
<div class="preview-field">
    <label>My Field</label>
    <input type="text" class="form-control" name="profile[my_field]" 
           id="my_field" placeholder="...">
</div>
```

In `renderPreview()` JavaScript:

```javascript
document.getElementById('my_field').value = profile.my_field || '';
```

---

## 📈 Future Enhancements

- [ ] Batch import (multiple CVs)
- [ ] Template matching (CV templates vs standards)
- [ ] Profile field auto-mapping (skills → profile sections)
- [ ] History/versioning (track changes)
- [ ] Export to different formats
- [ ] Integration with LinkedIn API (two-way sync)

---

Generated: 2026-04-07  
**Maintainer**: Development Team
