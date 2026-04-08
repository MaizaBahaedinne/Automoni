# CV Parsing Service

**FastAPI microservice for parsing CVs and extracting structured data.**

## Features

✅ Parse PDF, DOCX, and image files  
✅ Extract text using OCR when needed  
✅ API Key authentication  
✅ Structured JSON responses  
✅ Health check endpoint  
✅ Comprehensive logging  

## Installation

### 1. Prerequisites

- **Python 3.8+**
- **Tesseract OCR** (for image parsing)

**Install Tesseract:**

```bash
# macOS
brew install tesseract

# Ubuntu/Debian
sudo apt-get install tesseract-ocr

# Windows
Download from: https://github.com/UB-Mannheim/tesseract/wiki
```

### 2. Install Dependencies

```bash
cd cv-parsing-service
pip install -r requirements.txt
```

### 3. Configure

Copy `.env.example` to `.env`:

```bash
cp .env.example .env
```

Edit `.env` with your settings:

```env
API_KEY=your-secret-key-here
OLLAMA_BASE_URL=http://localhost:11434
OLLAMA_MODEL=mistral
```

## Quick Start

### Start the Service

```bash
python main.py
```

Service runs on `http://localhost:8001`

### Health Check

```bash
curl http://localhost:8001/health
```

**Expected:**
```json
{
  "status": "ok",
  "version": "1.0.0",
  "service": "CV Parsing Service"
}
```

### Parse a CV

```bash
curl -X POST \
  -H "X-API-Key: your-secret-key-here" \
  -F "file=@/path/to/cv.pdf" \
  http://localhost:8001/api/parse-cv
```

**Response:**
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
      "summary": "..."
    },
    "skills": [
      {"name": "PHP", "confidence": 0.8},
      {"name": "Laravel", "confidence": 0.8}
    ],
    "languages": [
      {"name": "English", "proficiency": "fluent", "confidence": 0.85}
    ],
    "experiences": [],
    "education": [],
    "certifications": []
  }
}
```

## API Endpoints

### GET /health

Check service health.

**Response:** `200 OK`
```json
{
  "status": "ok",
  "version": "1.0.0"
}
```

---

### POST /api/parse-cv

Parse a CV file.

**Headers:**
- `X-API-Key: <your-key>`

**Body:**
- `file` (multipart file): PDF, DOCX, JPG, PNG

**Response:** `200 OK`
```json
{
  "success": true,
  "message": "CV parsed successfully",
  "data": {...}
}
```

**Error Response:**
```json
{
  "success": false,
  "message": "Error description",
  "error": "detailed error"
}
```

## File Structure

```
cv-parsing-service/
├── main.py                 # FastAPI app entry point
├── config.py               # Configuration
├── requirements.txt        # Python dependencies
├── .env.example            # Environment variables template
├── README.md               # This file
├── app/
│   ├── __init__.py
│   ├── parsers/            # File parsers (PDF, DOCX, OCR)
│   │   ├── pdf_parser.py
│   │   ├── docx_parser.py
│   │   ├── ocr_parser.py
│   │   └── text_extractor.py
│   ├── models/             # Pydantic schemas
│   │   └── schemas.py
│   ├── routes/             # API routes
│   │   └── cv.py
│   └── utils/              # Utilities
│       ├── logger.py
│       └── validators.py
└── logs/                   # Log files
    └── cv_parsing.log
```

## Development

### Enable Ollama AI (Optional)

For AI-powered extraction instead of regex:

1. Install Ollama: https://ollama.ai
2. Pull a model: `ollama pull mistral`
3. Start Ollama: `ollama serve`
4. Update `.env`:
   ```env
   OLLAMA_BASE_URL=http://localhost:11434
   OLLAMA_MODEL=mistral
   ```

### Run Tests

```bash
pytest tests/
```

### Debug Mode

```python
# In .env or config.py
ENV=development
LOG_LEVEL=DEBUG
```

Then start with reload:
```bash
uvicorn main:app --reload
```

## Troubleshooting

### "No module named 'pytesseract'"

Install Tesseract OCR (see Prerequisites above).

### "API returned 401 Unauthorized"

Check `X-API-Key` header matches `API_KEY` in `.env`.

### Service won't start

Check logs:
```bash
tail -f logs/cv_parsing.log
```

### Slow parsing

- Large files (10MB+) take time to extract
- OCR on images is slower (~5-10s per page)
- Increase timeout in CI4 config

## Performance

| File Type | Size | Time |
|-----------|------|------|
| PDF (text) | 1MB | < 1s |
| DOCX | 2MB | < 1s |
| JPG (scanned) | 5MB | 5-10s |
| PNG (multiple pages) | 10MB | 10-20s |

## Security

- API key required on all endpoints
- File size limits enforced
- File type validation (magic bytes)
- Input sanitization
- HTTPS recommended for production

## Support

📧 Check logs: `logs/cv_parsing.log`  
🐛 Debug mode: Set `LOG_LEVEL=DEBUG`  
📚 API Docs: http://localhost:8001/docs (Swagger)

---

**Version:** 1.0.0  
**License:** MIT  
**Made with ❤️**
