# CV Parsing Service - Installation & Setup

## 📋 Prerequisites

Before starting, ensure you have:
- **Python 3.8+**
- **pip** (Python package manager)
- **Tesseract OCR** (optional, for image parsing)

### Install Tesseract

**macOS:**
```bash
brew install tesseract
```

**Ubuntu/Debian:**
```bash
sudo apt-get install tesseract-ocr
```

**Windows:**
Download from: https://github.com/UB-Mannheim/tesseract/wiki

---

## 🚀 Quick Install (2 Steps)

### Step 1: Run Setup Script

```bash
cd cv-parsing-service
./setup.sh
```

The script will:
- ✅ Check Python version
- ✅ Create `.env` file from template
- ✅ Install Python dependencies
- ✅ Verify Tesseract (if available)

### Step 2: Start Service

```bash
python3 main.py
```

Service will start on **http://localhost:8001**

---

## 📝 Manual Installation

If the setup script doesn't work:

### 1. Create Virtual Environment

```bash
python3 -m venv venv
source venv/bin/activate  # On Windows: venv\Scripts\activate
```

### 2. Install Dependencies

```bash
pip install -r requirements.txt
```

### 3. Configure

```bash
cp .env.example .env
# Edit .env with your settings
```

### 4. Start

```bash
python3 main.py
```

---

## ⚙️ Configuration

Edit `.env` file:

```env
# API Settings
API_KEY=your-secret-key-here
API_PORT=8001

# Ollama (optional AI integration)
OLLAMA_BASE_URL=http://localhost:11434
OLLAMA_MODEL=mistral

# File Upload
MAX_FILE_SIZE_MB=10

# Logging
LOG_LEVEL=INFO
ENV=development
```

**In CI4, make sure `.env` has matching `API_KEY`:**
```env
CV_PARSING_API_KEY=your-secret-key-here
CV_PARSING_BASE_URL=http://localhost:8001
```

---

## ✅ Verify Installation

### Health Check

```bash
curl http://localhost:8001/health
```

**Expected Response:**
```json
{
  "status": "ok",
  "version": "1.0.0",
  "service": "CV Parsing Service"
}
```

### Test Parse Endpoint

```bash
curl -X POST \
  -H "X-API-Key: your-secret-key-here" \
  -F "file=@/path/to/cv.pdf" \
  http://localhost:8001/api/parse-cv
```

---

## 🧪 Test Files

Create a test CV for testing:

**test_cv.txt:**
```
JOHN DOE
Senior PHP Developer
john@example.com
+1-555-123-4567

PROFESSIONAL SUMMARY
10+ years of web development

SKILLS
PHP, Laravel, Docker, Kubernetes

LANGUAGES
English (Fluent), French (Native)

EXPERIENCE
Senior Developer at TechCorp
2020-Present | Paris, France
Led backend team development

EDUCATION
Master's in Computer Science
University of Paris, 2012
```

Convert to PDF and test:
```bash
# On macOS with installed tools
cupsfilter test_cv.txt > test_cv.pdf

# Then test
curl -X POST \
  -H "X-API-Key: your-secret-key-here" \
  -F "file=@test_cv.pdf" \
  http://localhost:8001/api/parse-cv
```

---

## 📊 API Documentation

Swagger UI available at:
```
http://localhost:8001/docs
```

---

## 🐛 Troubleshooting

### Error: "No module named 'fastapi'"

Install dependencies:
```bash
pip install -r requirements.txt
```

### Error: "Connection refused" on Ollama

If using Ollama for AI, make sure it's running:
```bash
ollama serve  # In another terminal
```

Or disable it in `.env`:
```env
OLLAMA_ENABLED=false
```

### Error: ImportError pytesseract

Tesseract OCR not installed. Install it or disable image parsing.

### Slow Startup

First run downloads Python packages - subsequent runs are faster.

### Port Already in Use

Change port in `.env`:
```env
API_PORT=8002
```

---

## 📦 Virtual Environment

To use a virtual environment:

```bash
# Create
python3 -m venv venv

# Activate
source venv/bin/activate  # macOS/Linux
# or
venv\Scripts\activate  # Windows

# Install dependencies
pip install -r requirements.txt

# Deactivate
deactivate
```

---

## 🔄 Keep Service Running

### Option 1: Background Process

```bash
python3 main.py &
```

### Option 2: systemd Service (Linux)

Create `/etc/systemd/system/cv-parsing.service`:

```ini
[Unit]
Description=CV Parsing Service
After=network.target

[Service]
Type=simple
User=www-data
WorkingDirectory=/path/to/cv-parsing-service
ExecStart=/usr/bin/python3 main.py
Restart=always

[Install]
WantedBy=multi-user.target
```

Then:
```bash
sudo systemctl start cv-parsing
sudo systemctl enable cv-parsing  # Auto-start on reboot
```

### Option 3: Docker

```dockerfile
FROM python:3.11-slim

WORKDIR /app
COPY . .

RUN apt-get update && apt-get install -y tesseract-ocr
RUN pip install -r requirements.txt

EXPOSE 8001
CMD ["python3", "main.py"]
```

---

## 📚 Next Steps

1. **Start the service:** `python3 main.py`
2. **Test health:** `curl http://localhost:8001/health`
3. **Upload a CV** via `http://localhost:8000/profile/cv-integrate` (CI4)
4. **Check logs:** `tail -f logs/cv_parsing.log`

---

## 📞 Support

- Check logs: `logs/cv_parsing.log`
- Enable debug: Set `LOG_LEVEL=DEBUG` in `.env`
- View API docs: http://localhost:8001/docs

**Happy parsing!** 🎉
