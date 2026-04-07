# CV Integration - API Testing Guide

## 📡 API Endpoints

### 1. Show Upload Page
```
GET /profile/cv-integrate
```

**Response:** HTML page with upload form

**Security:** Requires authentication (`auth` filter)

**Example:**
```bash
curl -b "cookies.txt" http://localhost:8000/profile/cv-integrate
```

---

### 2. Parse CV (AJAX)
```
POST /profile/cv-parse
```

**Headers:**
```
Content-Type: multipart/form-data
X-Requested-With: XMLHttpRequest
```

**Body:**
```
cv_file: <binary file data>
<csrf_name>: <csrf_value>
```

**Success Response (200):**
```json
{
  "success": true,
  "message": "CV parsed successfully",
  "data": {
    "profile": {
      "name": "John Doe",
      "headline": "Senior PHP Developer",
      "email": "john@example.com",
      "phone": "+1-555-123-4567",
      "city": "San Francisco",
      "country": "United States",
      "summary": "10+ years in web development..."
    },
    "skills": [
      {
        "name": "PHP",
        "confidence": 0.95
      },
      {
        "name": "Laravel",
        "confidence": 0.92
      }
    ],
    "languages": [
      {
        "name": "English",
        "proficiency": "fluent",
        "confidence": 0.99
      }
    ],
    "experiences": [
      {
        "job_title": "Senior Software Engineer",
        "company_name": "TechCorp",
        "start_date": "2020-01-01",
        "end_date": null,
        "location": "San Francisco, CA",
        "description": "Led backend team..."
      }
    ],
    "education": [
      {
        "institution": "Stanford University",
        "field_of_study": "Computer Science",
        "degree": "Bachelor's",
        "graduation_year": "2012-01-01"
      }
    ],
    "certifications": [
      {
        "name": "AWS Solutions Architect",
        "issuer": "Amazon",
        "issue_date": "2021-06-15",
        "expiration_date": null
      }
    ]
  }
}
```

**Error Response (400/500):**
```json
{
  "success": false,
  "message": "File type not supported. Allowed: pdf, docx, doc, jpg, jpeg, png"
}
```

**Test with cURL:**
```bash
# Create test file
echo "John Doe\nSenior PHP Developer\njohn@example.com" > test_cv.txt

# Get CSRF token first
alias curl_get_csrf='grep -oP "name=\"[^\"]*\" value=\"\K[^\"]*(?=\".*csrfFieldName)" || grep -oP "tokenName.*?name=\"\K[^\"]*"'

# Get CSRF values (manually or from browser DevTools)
CSRF_NAME="csrf_token"
CSRF_VALUE="your_csrf_token_here"

# POST to parse endpoint
curl -X POST \
  -H "X-Requested-With: XMLHttpRequest" \
  -F "cv_file=@test_cv.txt" \
  -F "${CSRF_NAME}=${CSRF_VALUE}" \
  -b "cookies.txt" \
  http://localhost:8000/profile/cv-parse
```

---

### 3. Save Parsed Data to Profile
```
POST /profile/cv-save
```

**Headers:**
```
Content-Type: application/json
X-Requested-With: XMLHttpRequest
```

**Body:**
```json
{
  "profile": {
    "headline": "Senior PHP Developer",
    "summary": "10+ years in web development...",
    "phone": "+1-555-123-4567",
    "city": "San Francisco",
    "country": "United States"
  },
  "skills": [
    {"name": "PHP", "confidence": 0.95},
    {"name": "Laravel", "confidence": 0.92}
  ],
  "languages": [
    {"name": "English", "proficiency": "fluent"}
  ],
  "experiences": [
    {
      "job_title": "Senior Software Engineer",
      "company_name": "TechCorp",
      "start_date": "2020-01-01",
      "location": "San Francisco, CA",
      "description": "Led backend team..."
    }
  ],
  "education": [
    {
      "institution": "Stanford University",
      "degree": "Bachelor's",
      "field_of_study": "Computer Science",
      "graduation_year": "2012"
    }
  ],
  "certifications": [
    {
      "name": "AWS Solutions Architect",
      "issuer": "Amazon"
    }
  ]
}
```

**Success Response (200):**
```json
{
  "success": true,
  "message": "Profile updated successfully",
  "profile_url": "/profile"
}
```

**Error Response (400):**
```json
{
  "success": false,
  "message": "Invalid data format"
}
```

**Test with cURL:**
```bash
curl -X POST \
  -H "Content-Type: application/json" \
  -H "X-Requested-With: XMLHttpRequest" \
  -d @payload.json \
  -b "cookies.txt" \
  http://localhost:8000/profile/cv-save
```

---

## 🧪 Integration Tests

### Test 1: Complete Upload Flow
```javascript
// In browser console
async function testCVIntegration() {
  try {
    // 1. Get form
    const response = await fetch('/profile/cv-integrate');
    const html = await response.text();
    console.log('✅ Form loaded');

    // 2. Get CSRF tokens from form
    const parser = new DOMParser();
    const doc = parser.parseFromString(html, 'text/html');
    const csrfInput = doc.querySelector('[name*="csrf"]');
    const csrfName = csrfInput.name;
    const csrfValue = csrfInput.value;

    console.log('✅ CSRF tokens obtained');

    // 3. Create FormData
    const formData = new FormData();
    formData.append('cv_file', new File(['test content'], 'test.pdf', { type: 'application/pdf' }));
    formData.append(csrfName, csrfValue);

    // 4. Upload and parse
    const parseResponse = await fetch('/profile/cv-parse', {
      method: 'POST',
      headers: { 'X-Requested-With': 'XMLHttpRequest' },
      body: formData
    });

    const parseData = await parseResponse.json();
    console.log('✅ Parse response:', parseData);

    if (!parseData.success) throw new Error(parseData.message);

    // 5. Save to profile
    const saveResponse = await fetch('/profile/cv-save', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      },
      body: JSON.stringify(parseData.data)
    });

    const saveData = await saveResponse.json();
    console.log('✅ Save response:', saveData);

    if (saveData.success) {
      console.log('✅✅✅ Complete flow successful!');
      window.location.href = saveData.profile_url;
    }
  } catch (error) {
    console.error('❌ Error:', error.message);
  }
}

// Run test
testCVIntegration();
```

### Test 2: Error Handling
```javascript
// Test with no file
async function testNoFile() {
  const formData = new FormData();
  const parseResponse = await fetch('/profile/cv-parse', {
    method: 'POST',
    headers: { 'X-Requested-With': 'XMLHttpRequest' },
    body: formData
  });
  const data = await parseResponse.json();
  console.log('No file result:', data.success ? '❌ FAILED' : '✅ Correctly rejected');
}

// Test with invalid file type
async function testInvalidType() {
  const formData = new FormData();
  formData.append('cv_file', new File(['code'], 'file.exe', { type: 'application/exe' }));
  const parseResponse = await fetch('/profile/cv-parse', {
    method: 'POST',
    headers: { 'X-Requested-With': 'XMLHttpRequest' },
    body: formData
  });
  const data = await parseResponse.json();
  console.log('Invalid type result:', data.success ? '❌ FAILED' : '✅ Correctly rejected');
}

// Test with oversized file
async function testOversized() {
  const largeData = new Array(500 * 1024 * 1024).join('x'); // 500MB
  const formData = new FormData();
  formData.append('cv_file', new File([largeData], 'huge.pdf'));
  const parseResponse = await fetch('/profile/cv-parse', {
    method: 'POST',
    headers: { 'X-Requested-With': 'XMLHttpRequest' },
    body: formData
  });
  const data = await parseResponse.json();
  console.log('Oversized result:', data.success ? '❌ FAILED' : '✅ Correctly rejected');
}
```

### Test 3: Python Service Health
```bash
# Check if Python service is running
curl -s http://localhost:8001/health | jq .

# Expected output:
# {
#   "status": "ok",
#   "version": "1.0.0"
# }

# If not running:
# curl: (7) Failed to connect

# Then start it:
cd /path/to/cv-parsing-service
python main.py
```

---

## 📊 Performance Benchmarks

### Expected Response Times

| Operation | Time | Notes |
|-----------|------|-------|
| Load form | < 200ms | No processing |
| Parse small PDF (< 1MB) | 5-10s | Depends on Ollama speed |
| Parse large PDF (5-10MB) | 20-60s | OCR adds time |
| Save to DB | < 500ms | 6 database operations |
| **Total E2E** | **25-70s** | Mostly Ollama processing |

### Optimization Tips
```bash
# Use lightweight CV format (DOCX faster than PDF)
# Disable OCR for born-digital PDFs
# Pre-process images to improve text extraction
# Use smaller Ollama model for faster parsing (but lower accuracy)
```

---

## 🔒 Security Testing

### Test 1: CSRF Protection
```bash
# This should FAIL (no CSRF token)
curl -X POST \
  -F "cv_file=@test.pdf" \
  http://localhost:8000/profile/cv-parse
# Expected: 403 Forbidden
```

### Test 2: Authentication
```bash
# This should FAIL (not logged in)
curl http://localhost:8000/profile/cv-integrate
# Expected: 302 Redirect to login
```

### Test 3: File Type Validation
```bash
# Create malicious file with .pdf extension
cp /bin/ls malicious.pdf

# This should FAIL (magic bytes don't match)
curl -b "cookies.txt" \
  -F "cv_file=@malicious.pdf" \
  http://localhost:8000/profile/cv-parse
# Expected: 400 Bad Request
```

### Test 4: XSS in Parsed Data
```json
{
  "profile": {
    "name": "<script>alert('xss')</script>"
  }
}
```
Should be escaped in the output (no alert should show)

---

## 📋 Checklist for Production

- [ ] Python service deployed and stable
- [ ] `.env` configured with production API key
- [ ] SSL/TLS enabled for file upload
- [ ] File upload directory restricted from web access
- [ ] Virus scanning enabled on uploaded files
- [ ] Rate limiting on `/profile/cv-parse`
- [ ] Monitoring on Python service health
- [ ] Logs configured and rotating
- [ ] User documentation updated
- [ ] Training completed for support team

---

**Last Updated:** 2025-01-XX
**Version:** 1.0.0
**Tested On:** CodeIgniter 4.4.8, PHP 8.1
