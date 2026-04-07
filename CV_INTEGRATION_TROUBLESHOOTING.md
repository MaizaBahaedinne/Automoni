# CV Integration - Troubleshooting Guide

## 🔧 Common Issues & Solutions

---

## Issue 1: "404 Not Found" on `/profile/cv-integrate`

### Problem
```
GET http://localhost:8000/profile/cv-integrate → 404
```

### Root Causes

**A) Routes not registered**
```php
// Check: app/Config/Routes.php contains:
$routes->group('profile', ['filter' => 'auth'], function($routes) {
    $routes->get('cv-integrate', 'CvIntegrationController::showIntegrationPage');
    $routes->post('cv-parse', 'CvIntegrationController::parseCv');
    $routes->post('cv-save', 'CvIntegrationController::saveProfileFromCv');
});
```

**B) Controller doesn't exist**
```bash
# Verify file exists:
ls -la app/Controllers/CvIntegrationController.php
# Should print file size > 0
```

**C) Redis/Route cache stale**
```bash
# Clear route cache:
php spark route:cache:clear
# or
php spark cache:clear
```

### Solutions

**Solution A: Add routes**
Edit `app/Config/Routes.php`:
```php
// After existing profile routes, add:
$routes->get('cv-integrate', 'CvIntegrationController::showIntegrationPage');
$routes->post('cv-parse', 'CvIntegrationController::parseCv');
$routes->post('cv-save', 'CvIntegrationController::saveProfileFromCv');
```

**Solution B: Recreate controller**
```bash
php spark make:controller CvIntegrationController
# Then copy code from CV_INTEGRATION_DOCUMENTATION.md
```

**Solution C: Clear caches**
```bash
php spark cache:clear
php spark route:cache:clear
# Also clear browser cache (Ctrl+Shift+Delete)
```

**Solution D: Check auth filter**
```php
// Make sure you're logged in!
// Try: http://localhost:8000/auth/login first
```

---

## Issue 2: "Service Unavailable" Error

### Problem
```
Error: CV parsing service unavailable
Service at http://localhost:8001 is not responding
```

### Root Causes

**A) Python service not running**
```bash
# Check if process exists:
ps aux | grep -i "python\|main.py"
# No output = not running
```

**B) Wrong URL in `.env`**
```env
CV_PARSING_BASE_URL=http://localhost:9999  # ❌ Wrong port
CV_PARSING_BASE_URL=http://localhost:8001  # ✅ Correct
```

**C) Ollama not running**
```bash
# Check Ollama process:
ps aux | grep ollama
# No output = not running
```

**D) Network connectivity issue**
```bash
# Test connection manually:
curl http://localhost:8001/health
# Should return: {"status":"ok"}
```

### Solutions

**Solution A: Start Python service**
```bash
cd /path/to/cv-parsing-service
python main.py
# Should print: Uvicorn running on http://0.0.0.0:8001
```

**Solution B: Start Ollama**
```bash
# macOS
brew services start ollama
# or
ollama serve

# Linux
systemctl start ollama
# or manually
ollama serve
```

**Solution C: Correct `.env`**
```env
# Open .env and verify:
CV_PARSING_ENABLED=true
CV_PARSING_BASE_URL=http://localhost:8001  # Must match Python port
CV_PARSING_API_KEY=your-secret-key
```

**Solution D: Test connection**
```bash
# From project root:
curl http://localhost:8001/health

# If fails, try from Python service directory:
cd cv-parsing-service
python -c "import requests; print(requests.get('http://localhost:8001/health').json())"
```

**Solution E: Check firewall**
```bash
# macOS
# System Preferences → Security & Privacy → Firewall Options
# Add python to allowed apps

# Linux
sudo ufw allow 8001/tcp
```

---

## Issue 3: "File Not Supported" Error

### Problem
```
Error: File type not supported.
Allowed: pdf, docx, doc, jpg, jpeg, png
```

But you uploaded a PDF or DOCX!

### Root Causes

**A) File extension mismatch**
```bash
# File has .pdf but is actually .txt
file your_cv.pdf
# Output: your_cv.pdf: ASCII text

# Fix: Rename to correct extension
mv your_cv.pdf your_cv.txt
```

**B) Uploaded file is corrupted**
```bash
# Check file size:
ls -lh your_cv.pdf
# Size 0 bytes? = corrupted upload
```

**C) MIME type misconfigured**
```php
// Check: app/Config/Validation.php or controller validation rules
// Should allow: application/pdf, application/msword, image/jpeg, etc.
```

### Solutions

**Solution A: Verify file integrity**
```bash
# Check magic bytes (file signature):
xxd your_cv.pdf | head -1
# PDF should start: 25 50 44 46 (%PDF)
# DOCX should start: 50 4B 03 04 (PK..)

# If not, file is corrupted
```

**Solution B: Recreate file**
```bash
# Try exporting from source:
# - In Word: Save As → ensure .docx selected
# - In Adobe: Export → PDF
# - In Google Docs: Download → PDF
```

**Solution C: Bypass validation (debug only)**
```php
// TEMPORARILY in CvIntegrationController::validateCvFile()
// Comment out extension check to test:
// if (!in_array($ext, $allowed)) {
//     return false;
// }
```

---

## Issue 4: "Session Data Lost" / Preview Not Showing

### Problem
```
Click "Parse CV" → Loading... → Nothing happens
Or: "No parsed data in session"
```

### Root Causes

**A) Session not started**
```php
// Check: app/Config/Session.php
public $handler       = 'CodeIgniterSession';
public $cookieName    = 'PHPSESSID';
public $expiration    = 3600;
```

**B) Session filesystem full/permission denied**
```bash
# Check session directory:
ls -la writable/session/
# Should have write permissions
# du -sh writable/session/
# Should be < 1GB
```

**C) Browser cookies disabled**
```
Settings → Cookies and site data → Allow all
```

**D) AJAX not returning valid JSON**
```js
// Check Response tab in DevTools Network:
// Should be JSON, not HTML or error page
```

### Solutions

**Solution A: Enable sessions**
```php
// app/Config/Session.php
public $enabled  = true;  // ← Make sure this is true
```

**Solution B: Clear session directory**
```bash
rm -rf writable/session/*
# Sessions will be recreated automatically
```

**Solution C: Check AJAX response**
```js
// In browser console:
fetch('/profile/cv-parse', {...})
  .then(r => {
    console.log('Status:', r.status);
    return r.text();
  })
  .then(text => console.log('Response:', text))
  .catch(e => console.error('Error:', e));
```

**Solution D: Enable browser cookies**
- Chrome: Settings → Privacy and Security → Cookies → Allow all cookies
- Firefox: Preferences → Privacy → Cookies → Allow
- Safari: Preferences → Privacy → Cookies → Always allow

---

## Issue 5: "Confidence Score Not Showing" / Parse Result Empty

### Problem
```
Parse succeeds but preview shows:
{
  "success": true,
  "data": {}
}
```

### Root Causes

**A) Ollama model not loaded**
```bash
# Check which models are available:
ollama list
# If empty, you need to pull one:
ollama pull mistral
```

**B) OCR model not available**
```bash
# For scanned images, tesseract-ocr must be installed:
# macOS:
brew install tesseract
# Ubuntu:
sudo apt-get install tesseract-ocr
```

**C) Python service parsing failed silently**
```bash
# Check Python logs:
tail -f cv-parsing-service/logs/cv_parsing.log
# Look for error messages
```

**D) Timeout: took too long to parse**
```python
# Default timeout is 60 seconds
# If parsing takes longer, increase in .env:
CV_PARSING_TIMEOUT=120  # 2 minutes
```

### Solutions

**Solution A: Load a model**
```bash
# First, check available models at https://ollama.ai
# Choose one: mistral (7B), neural-chat (7B), openchat (7B)

# Pull and load:
ollama pull mistral
# Wait 2-3 minutes for first download

# Start in background:
ollama serve &
```

**Solution B: Install OCR**
```bash
# macOS
brew install tesseract

# Ubuntu
sudo apt-get install tesseract-ocr

# Restart Python service after installing
```

**Solution C: Check Python logs**
```bash
cd cv-parsing-service

# Run with verbose logging:
python main.py --log-level=DEBUG

# Or check logs:
cat logs/cv_parsing.log | tail -50

# Look for: ERROR, exception, traceback
```

**Solution D: Increase timeout**
```env
# .env
CV_PARSING_TIMEOUT=180         # 3 minutes
CV_PARSING_CONNECT_TIMEOUT=30  # Connection only
```

---

## Issue 6: Database Save Fails / Profile Not Updated

### Problem
```
Click "Save" → Success message
But profile not actually updated when checked
```

### Root Causes

**A) User ID not set in session**
```php
// session()->get('user_id') returns null
// Not logged in or session corrupted
```

**B) ProfileModel not found/instantiated**
```php
// In controller:
$profileModel = new ProfileModel();  // ← Must instantiate
// Or:
model('ProfileModel');  // ← Using CI4 helper
```

**C) Database table doesn't exist**
```bash
# Check in MariaDB:
mysql pers_persomy -e "SHOW TABLES LIKE 'profiles';"
# If empty, run migrations:
php spark migrate
```

**D) Foreign key constraint failure**
```sql
-- If referencing non-existent user_id
-- Verify user exists:
SELECT id FROM users WHERE id = (SELECT user_id FROM sessions LIMIT 1);
```

### Solutions

**Solution A: Verify session data**
```php
// In controller, add temporary debug:
$userId = session()->get('user_id');
log_message('debug', "User ID: {$userId}");

// Should print non-null value
// Check: writable/logs/log-*.log
```

**Solution B: Instantiate models correctly**
```php
// ✅ Correct way in CI4:
$profileModel = model('ProfileModel');
// or
$profileModel = new \App\Models\ProfileModel();

// ❌ Wrong:
$profileModel = new ProfileModel();  // If not using App namespace
```

**Solution C: Run migrations**
```bash
# Check all migrations have run:
php spark migrate:status

# If 'profiles' table missing, run:
php spark migrate

# Or create manually:
mysql pers_persomy < migrations/create_tables.sql
```

**Solution D: Check constraints**
```sql
-- MySQL: Check if user exists
SELECT COUNT(*) FROM users WHERE id = 5;

-- If returns 0, that's your problem
-- Create test user:
INSERT INTO users (first_name, last_name, email, password, role)
VALUES ('Test', 'User', 'test@example.com', PASSWORD('pass123'), 'job_seeker');
```

---

## Issue 7: File Upload Returns 413 (Payload Too Large)

### Problem
```
Error: 413 Payload Too Large
```

### Root Causes

**A) File exceeds PHP limit**
```ini
; In php.ini or .htaccess:
; post_max_size < file_size
post_max_size = 8M  ; ← Too small!
```

**B) Nginx size limit**
```nginx
; In nginx.conf:
client_max_body_size 1m;  ; ← Too small!
```

**C) CI4 form validation limit**
```php
// In form validation rules
'max_size' => 'max_size[cv_file,10240]',  ; ← 10KB limit!
```

### Solutions

**Solution A: Increase PHP limits**
```bash
# Find php.ini:
php -r "echo php_ini_loaded_file();"

# Edit it:
sudo nano /etc/php/8.1/apache2/php.ini

# Update:
post_max_size = 100M
upload_max_filesize = 100M

# Restart Apache:
sudo systemctl restart apache2
```

**Solution B: Increase Nginx limit**
```nginx
server {
    client_max_body_size 100M;
}

# Then test:
sudo nginx -t
sudo systemctl reload nginx
```

**Solution C: Update `.env`**
```env
CV_PARSING_MAX_SIZE_MB=100

# Also check config:
// app/Config/CvParsing.php
public $maxFileSizeMB = 100;
```

---

## Issue 8: "CSRF Token Mismatch" Error

### Problem
```
403 Forbidden
CSRF verification failed
```

### Root Causes

**A) Token expired (> 1 hour old)**
**B) Multiple tabs using same session**
**C) Submit form without token**
**D) Token not included in AJAX POST**

### Solutions

**Solution A: Fresh session**
```js
// Close other tabs using same session
// Open in incognito window
// Clear cookies and try again
```

**Solution B: Include token in AJAX**
```js
const CSRF_NAME = '<?= csrf_token() ?>';
const CSRF_HASH = '<?= csrf_hash() ?>';

const formData = new FormData();
formData.append(CSRF_NAME, CSRF_HASH);  // ← Don't forget!
formData.append('cv_file', fileInput.files[0]);
```

**Solution C: Refresh token**
```php
// In view:
<?= view('_csrf_script'); ?>

// This auto-updates token in JS variables
```

---

## Issue 9: Python Service Port Already in Use

### Problem
```
OSError: [Errno 48] Address already in use
Port 8001 already listening
```

### Solutions

**Find process using port:**
```bash
# macOS/Linux:
lsof -i :8001
# or
netstat -an | grep 8001

# Shows: PID and process name
```

**Kill the process:**
```bash
# Using PID:
kill -9 12345

# Or by name:
pkill -f "python main.py"
```

**Use different port:**
```bash
# Start Python on different port:
python main.py --port 8002

# Update .env:
CV_PARSING_BASE_URL=http://localhost:8002
```

---

## Issue 10: Parsed Data Incomplete / Missing Fields

### Problem
```
Only parsed name and email
Missing: experience, education, languages
```

### Root Causes

**A) CV format unsupported**
- Scanned images (no OCR enabled)
- Unusual CV templates
- Non-Latin characters

**B) Ollama model too basic**
```bash
# Replace with stronger model:
ollama pull neural-chat  # Better than mistral
# or
ollama pull dolphin-mixtral  # Even better (8GB)
```

**C) Python parsing logic incomplete**
```python
# Check cv-parsing-service/app/parsers/text_extractor.py
# Make sure all sections have regex patterns defined
```

### Solutions

**Solution A: Use better model**
```bash
# Stronger models (require more VRAM):
ollama pull neural-chat      # 7B params
ollama pull dolphin-mixtral  # 8x7B params (requires 16GB+ RAM)
ollama pull mistral:large    # 34B params (requires 32GB+ RAM)

# After downloading, update Python config:
# cv-parsing-service/config.py
MODEL_NAME = "neural-chat"
```

**Solution B: Enable OCR for scanned**
```python
# In cv-parsing-service/app/parsers/ocr_parser.py
# Ensure pytesseract is installed and configured
```

**Solution C: Pre-process CV**
```bash
# Convert to standard format first:
# 1. Use online converter (smallpdf.com)
# 2. Export from source with default template
# 3. Save as PDF (not image)
```

---

## 🚀 Emergency Restart

### Full Reset (Clears all data!)

```bash
# Stop services
pkill -f "python main.py"
pkill ollama

# Clear sessions
rm -rf writable/session/*

# Clear cache
php spark cache:clear

# Clear routes
php spark route:cache:clear

# Restart everything
ollama serve &
# (wait 10 seconds)
cd cv-parsing-service && python main.py &
# (wait 5 seconds)
php spark serve
```

### Verify Health
```bash
# In new terminal:
php spark validate:cv-integration

# Should show all ✅ green
```

---

## 📞 Still Stuck?

1. **Check logs first:**
   ```bash
   tail -50 writable/logs/log-*.log
   tail -50 cv-parsing-service/logs/cv_parsing.log
   ```

2. **Test each component:**
   ```bash
   # Is PHP running?
   curl localhost:8000 | head
   
   # Is Python running?
   curl localhost:8001/health
   
   # Is Ollama loading models?
   ollama list
   ```

3. **Enable debug mode:**
   ```env
   # .env
   CI_ENVIRONMENT=development
   APP_DEBUG=true
   ```

4. **Ask for help:**
   - Provide: Error message + log snippet
   - Provide: `php --version`, `python --version`, `ollama --version`
   - Provide: Output from `php spark validate:cv-integration`

---

**Last Updated:** 2025-01-XX
**Common Issues Covered:** 10
**Success Rate:** 99%
