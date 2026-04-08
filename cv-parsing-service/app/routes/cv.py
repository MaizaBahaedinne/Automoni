"""
CV parsing API routes

Pipeline:
  Stage 1 — Text extraction   (PDF → pdfplumber, DOCX → python-docx, image → OCR)
  Stage 2 — spaCy NLP         (fast offline entity extraction)
  Stage 3 — Ollama LLM        (intelligent JSON structuration, with spaCy fallback)
"""
import logging
import os
import tempfile

import requests
from fastapi import APIRouter, File, Header, HTTPException, UploadFile

import config
from app.models.schemas import (
    CVParseResponse,
    Certification,
    Education,
    Experience,
    Language,
    ParsedCV,
    Profile,
    Skill,
)
from app.parsers.ollama_parser import OllamaParser
from app.parsers.spacy_extractor import SpacyExtractor
from app.parsers.text_extractor import TextExtractor
from app.utils.validators import validate_file

logger = logging.getLogger(__name__)
router = APIRouter()


# ── Health check ──────────────────────────────────────────────────────────────

@router.get("/health")
async def health_check():
    """Return service status and availability of each component."""
    ollama_status = "unavailable"
    spacy_status  = "unavailable"

    try:
        r = requests.get(f"{config.OLLAMA_BASE_URL}/api/tags", timeout=2)
        if r.status_code == 200:
            ollama_status = "available"
    except Exception:
        pass

    try:
        SpacyExtractor.load_model()
        spacy_status = "available"
    except Exception:
        pass

    return {
        "status":  "ok",
        "version": "1.1.0",
        "service": "CV Parsing Service",
        "components": {
            "spacy":  spacy_status,
            "ollama": ollama_status,
            "model":  config.OLLAMA_MODEL,
        },
    }


# ── Main parse endpoint ───────────────────────────────────────────────────────

@router.post("/api/parse-cv", response_model=CVParseResponse)
async def parse_cv(
    file: UploadFile = File(...),
    x_api_key: str = Header(None),
):
    """
    Parse a CV file through the 3-stage pipeline.

    The service is bound to 127.0.0.1 (localhost only), so network-level
    isolation is the primary security layer. API key auth is enforced only
    when a non-default key is configured AND a key is provided.
    """
    api_key_configured = config.API_KEY and config.API_KEY != 'your-secret-key-here'
    if api_key_configured and x_api_key and x_api_key != config.API_KEY:
        logger.warning("Unauthorized access attempt from upload endpoint")
        raise HTTPException(status_code=401, detail="Unauthorized")

    temp_file: str | None = None
    try:
        # ── Save upload to a temp file ────────────────────────────────────────
        ext = file.filename.rsplit(".", 1)[-1].lower() if "." in file.filename else "tmp"
        with tempfile.NamedTemporaryFile(delete=False, suffix=f".{ext}") as tmp:
            tmp.write(await file.read())
            temp_file = tmp.name

        validate_file(temp_file)

        # ── Stage 1: Text extraction ──────────────────────────────────────────
        logger.info("[1/3] Extracting text from: %s", file.filename)
        text = TextExtractor.extract(temp_file)

        if not text.strip():
            return CVParseResponse(
                success=False,
                message="Could not extract text from the file.",
                error="Empty text extraction",
            )

        # ── Stage 2: spaCy NLP ────────────────────────────────────────────────
        logger.info("[2/3] Running spaCy NLP extraction")
        spacy_data = SpacyExtractor.extract(text)

        # ── Stage 3: Ollama LLM ───────────────────────────────────────────────
        logger.info("[3/3] Running Ollama structuration (model: %s)", config.OLLAMA_MODEL)
        ollama_result = OllamaParser.parse(text, spacy_data)

        if ollama_result:
            logger.info("✅ Ollama structuration succeeded")
            parsed_data = _map_ollama_to_parsed_cv(ollama_result, spacy_data)
            message = "CV parsed successfully"
        else:
            logger.warning("⚠️  Ollama unavailable — using spaCy fallback")
            parsed_data = _map_spacy_to_parsed_cv(spacy_data)
            message = "CV parsed (basic mode — Ollama unavailable)"

        logger.info("✅ Parsing complete: %s", file.filename)
        return CVParseResponse(success=True, message=message, data=parsed_data)

    except ValueError as e:
        logger.error("Validation error: %s", e)
        return CVParseResponse(success=False, message=str(e), error=str(e))
    except Exception as e:
        logger.error("Unexpected error parsing CV: %s", e, exc_info=True)
        return CVParseResponse(
            success=False, message="Error parsing CV", error=str(e)
        )
    finally:
        if temp_file and os.path.exists(temp_file):
            try:
                os.unlink(temp_file)
            except Exception:
                pass


# ── Mappers ───────────────────────────────────────────────────────────────────

def _map_ollama_to_parsed_cv(ollama: dict, spacy: dict) -> ParsedCV:
    """Convert Ollama JSON response → ParsedCV, using spaCy to fill gaps."""

    profile = Profile(
        name     = ollama.get("name")    or spacy.get("name"),
        headline = ollama.get("headline"),
        email    = ollama.get("email")   or spacy.get("email"),
        phone    = ollama.get("phone")   or spacy.get("phone"),
        city     = ollama.get("city"),
        country  = ollama.get("country"),
        summary  = ollama.get("summary"),
    )

    # Skills — merge Ollama + spaCy, deduplicated by lowercase name
    seen: set = set()
    skills: list = []
    for raw in (ollama.get("skills") or []) + (spacy.get("skills") or []):
        name = (raw.get("name") or "").strip()
        if name and name.lower() not in seen:
            seen.add(name.lower())
            skills.append(Skill(name=name, confidence=float(raw.get("confidence", 0.85))))

    languages = [
        Language(
            name        = l.get("name", ""),
            proficiency = l.get("proficiency"),
            confidence  = float(l.get("confidence", 0.85)),
        )
        for l in (ollama.get("languages") or []) if l.get("name")
    ]

    experiences = [
        Experience(
            job_title    = e.get("job_title"),
            company_name = e.get("company_name"),
            start_date   = e.get("start_date"),
            end_date     = e.get("end_date"),
            location     = e.get("location"),
            description  = e.get("description"),
            confidence   = float(e.get("confidence", 0.80)),
        )
        for e in (ollama.get("experiences") or [])
    ]

    education = [
        Education(
            institution    = e.get("institution"),
            degree         = e.get("degree"),
            field_of_study = e.get("field_of_study"),
            graduation_year= e.get("graduation_year"),
            confidence     = float(e.get("confidence", 0.85)),
        )
        for e in (ollama.get("education") or [])
    ]

    certifications = [
        Certification(
            name            = c.get("name"),
            issuer          = c.get("issuer"),
            issue_date      = c.get("issue_date"),
            expiration_date = c.get("expiration_date"),
            confidence      = float(c.get("confidence", 0.80)),
        )
        for c in (ollama.get("certifications") or [])
    ]

    return ParsedCV(
        profile        = profile,
        skills         = skills,
        languages      = languages,
        experiences    = experiences,
        education      = education,
        certifications = certifications,
    )


def _map_spacy_to_parsed_cv(spacy: dict) -> ParsedCV:
    """Convert spaCy extracted data → ParsedCV (fallback when Ollama is down)."""
    return ParsedCV(
        profile = Profile(
            name  = spacy.get("name"),
            email = spacy.get("email"),
            phone = spacy.get("phone"),
        ),
        skills = [
            Skill(name=s["name"], confidence=s.get("confidence", 0.80))
            for s in (spacy.get("skills") or [])
        ],
        languages = [
            Language(
                name        = l["name"],
                proficiency = l.get("proficiency"),
                confidence  = l.get("confidence", 0.80),
            )
            for l in (spacy.get("languages") or [])
        ],
        experiences    = [],
        education      = [],
        certifications = [],
    )

