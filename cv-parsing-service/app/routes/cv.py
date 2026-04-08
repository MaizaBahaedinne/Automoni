"""
CV parsing routes
"""
from fastapi import APIRouter, UploadFile, File, HTTPException, Header
from fastapi.responses import JSONResponse
import logging
import os
import tempfile
import config
from app.models.schemas import CVParseResponse, ParsedCV, Profile
from app.parsers.text_extractor import TextExtractor
from app.utils.validators import validate_file

logger = logging.getLogger(__name__)
router = APIRouter()


def verify_api_key(x_api_key: str = Header(None)) -> bool:
    """Verify API key from header"""
    return x_api_key == config.API_KEY


@router.get("/health")
async def health_check():
    """Health check endpoint"""
    return {
        "status": "ok",
        "version": "1.0.0",
        "service": "CV Parsing Service"
    }


@router.post("/api/parse-cv", response_model=CVParseResponse)
async def parse_cv(
    file: UploadFile = File(...),
    x_api_key: str = Header(None)
):
    """
    Parse CV file
    
    Args:
        file: CV file to parse
        x_api_key: API key for authentication
        
    Returns:
        Parsed CV data with confidence scores
    """
    
    # Verify API key
    if x_api_key != config.API_KEY:
        logger.warning("Unauthorized access attempt")
        raise HTTPException(status_code=401, detail="Unauthorized")
    
    # Save temp file
    temp_file = None
    try:
        # Save uploaded file temporarily
        with tempfile.NamedTemporaryFile(delete=False, suffix=f".{file.filename.split('.')[-1]}") as tmp:
            content = await file.read()
            tmp.write(content)
            temp_file = tmp.name
        
        # Validate file
        validate_file(temp_file)
        
        # Extract text
        logger.info(f"Starting CV parsing for: {file.filename}")
        text = TextExtractor.extract(temp_file)
        
        if not text.strip():
            return CVParseResponse(
                success=False,
                message="Could not extract any text from file",
                error="No text found"
            )
        
        # Parse with simple regex (no AI needed for basic extraction)
        parsed_data = _parse_cv_text(text)
        
        logger.info(f"CV parsing completed for: {file.filename}")
        
        return CVParseResponse(
            success=True,
            message="CV parsed successfully",
            data=parsed_data
        )
        
    except ValueError as e:
        logger.error(f"Validation error: {e}")
        return CVParseResponse(
            success=False,
            message=str(e),
            error=str(e)
        )
    except Exception as e:
        logger.error(f"Error parsing CV: {e}", exc_info=True)
        return CVParseResponse(
            success=False,
            message="Error parsing CV",
            error=str(e)
        )
    finally:
        # Clean up temp file
        if temp_file and os.path.exists(temp_file):
            try:
                os.unlink(temp_file)
            except:
                pass


def _parse_cv_text(text: str) -> ParsedCV:
    """
    Parse CV text and extract structured data
    
    This is a basic parser using regex patterns.
    For AI-powered extraction with Ollama, see the ollama_parser module.
    """
    import re
    
    lines = text.split('\n')
    profile = Profile()
    skills = []
    languages = []
    experiences = []
    education = []
    certifications = []
    
    # Extract name (usually first non-empty line)
    for line in lines[:5]:
        if line.strip() and len(line.strip()) < 100:
            profile.name = line.strip()
            break
    
    # Extract email
    email_pattern = r'[\w\.-]+@[\w\.-]+\.\w+'
    email_match = re.search(email_pattern, text)
    if email_match:
        profile.email = email_match.group()
    
    # Extract phone
    phone_pattern = r'[\+]?[(]?[0-9]{1,4}[)]?[-\s\.]?[(]?[0-9]{1,4}[)]?[-\s\.]?[0-9]{1,9}'
    phone_match = re.search(phone_pattern, text)
    if phone_match:
        profile.phone = phone_match.group()
    
    # Extract summary (first paragraph with "summary", "profile", "objective")
    for i, line in enumerate(lines):
        if any(keyword in line.lower() for keyword in ['summary', 'profile', 'objective']):
            # Get next non-empty lines as summary
            summary_lines = []
            for j in range(i+1, min(i+5, len(lines))):
                if lines[j].strip():
                    summary_lines.append(lines[j].strip())
                else:
                    break
            if summary_lines:
                profile.summary = ' '.join(summary_lines)[:500]
            break
    
    # Simple skill extraction
    common_skills = [
        'Python', 'JavaScript', 'PHP', 'Java', 'C++', 'C#', 'Go', 'Rust',
        'HTML', 'CSS', 'React', 'Vue', 'Angular', 'Laravel', 'Django',
        'Docker', 'Kubernetes', 'AWS', 'Azure', 'GCP',
        'PostgreSQL', 'MySQL', 'MongoDB', 'Redis',
        'Git', 'Linux', 'Windows', 'macOS'
    ]
    
    found_skills = set()
    for skill in common_skills:
        if skill.lower() in text.lower():
            found_skills.add(skill)
    
    for skill in found_skills:
        skills.append({
            "name": skill,
            "confidence": 0.8
        })
    
    # Extract languages (basic pattern)
    language_pattern = r'(English|French|Spanish|German|Italian|Portuguese|Dutch|Russian|Chinese|Japanese|Korean)\s*[:\-]?\s*(fluent|native|intermediate|basic|professional)*'
    for match in re.finditer(language_pattern, text, re.IGNORECASE):
        lang = match.group(1)
        prof = match.group(2) if match.group(2) else "intermediate"
        languages.append({
            "name": lang,
            "proficiency": prof.lower(),
            "confidence": 0.85
        })
    
    return ParsedCV(
        profile=profile,
        skills=skills,
        languages=languages,
        experiences=experiences,
        education=education,
        certifications=certifications
    )
