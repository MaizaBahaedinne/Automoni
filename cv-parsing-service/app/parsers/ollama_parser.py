"""
Ollama LLM Parser — Stage 3 of the CV parsing pipeline.

Sends extracted CV text + spaCy hints to a local Ollama model and asks it to
return a structured JSON object.  Falls back gracefully to None when Ollama is
not running, allowing the caller to use the spaCy result instead.
"""
import json
import re
import logging
import requests
from typing import Dict, Optional

import config

logger = logging.getLogger(__name__)

# ── Prompt template ────────────────────────────────────────────────────────────
# Kept as a module-level constant so it is easy to iterate on without touching
# the parser logic.  Use low temperature (0.1) for deterministic JSON output.
_PROMPT_TEMPLATE = """\
You are an expert CV/resume parser.

Given the CV text and the pre-extracted data below, extract ALL information and
return a SINGLE valid JSON object.  No markdown. No explanation. Just the JSON.

=== CV TEXT ===
{cv_text}

=== PRE-EXTRACTED DATA (hints from NLP, may be incomplete) ===
{partial_data}

Return this EXACT JSON structure (use null for missing fields):
{{
  "name":     "Full Name",
  "headline": "Current job title / professional headline",
  "email":    "email@example.com",
  "phone":    "+XX XXX XXX XXX",
  "city":     "City",
  "country":  "Country",
  "summary":  "Professional summary – max 500 characters",
  "skills": [
    {{"name": "Python",  "confidence": 0.95}},
    {{"name": "Laravel", "confidence": 0.90}}
  ],
  "languages": [
    {{"name": "English", "proficiency": "Native",        "confidence": 0.95}},
    {{"name": "French",  "proficiency": "Intermediate",  "confidence": 0.85}}
  ],
  "experiences": [
    {{
      "job_title":    "Senior Developer",
      "company_name": "Acme Corp",
      "start_date":   "2020-01",
      "end_date":     "Present",
      "location":     "Paris, France",
      "description":  "Led the backend team …",
      "confidence":   0.85
    }}
  ],
  "education": [
    {{
      "institution":    "University of Paris",
      "degree":         "Master",
      "field_of_study": "Computer Science",
      "graduation_year": "2018",
      "confidence":     0.90
    }}
  ],
  "certifications": [
    {{
      "name":            "AWS Certified Developer",
      "issuer":          "Amazon Web Services",
      "issue_date":      "2022-06",
      "expiration_date": null,
      "confidence":      0.90
    }}
  ]
}}

Rules:
- Return ONLY the JSON object, nothing else before or after
- Dates: YYYY or YYYY-MM format
- Confidence scores: 0.0 – 1.0  (higher = more certain)
- Deduplicate skills and languages
- Keep summary under 500 characters
- If a section has no data found, return an empty array []
"""


class OllamaParser:
    """Structure CV text into a JSON object using a local Ollama model."""

    @classmethod
    def parse(cls, cv_text: str, partial_data: Dict) -> Optional[Dict]:
        """
        Send a prompt to Ollama and return the parsed JSON dict.

        Args:
            cv_text:      Raw text extracted from the CV file.
            partial_data: Pre-extracted data from SpacyExtractor.extract()
                          (used as hints inside the prompt).

        Returns:
            Parsed dict on success, None on failure / Ollama unavailable.
        """
        try:
            prompt = _PROMPT_TEMPLATE.format(
                # Trim to first 8 000 chars to stay within typical context windows
                cv_text=cv_text[:8_000],
                partial_data=json.dumps(partial_data, ensure_ascii=False, indent=2),
            )

            response = requests.post(
                f"{config.OLLAMA_BASE_URL}/api/generate",
                json={
                    "model":  config.OLLAMA_MODEL,
                    "prompt": prompt,
                    "stream": False,
                    "options": {
                        "temperature": 0.1,   # Near-deterministic JSON
                        "top_p":       0.9,
                        "num_predict": 2048,
                    },
                },
                timeout=config.OLLAMA_TIMEOUT,
            )

            if response.status_code != 200:
                logger.warning("Ollama returned HTTP %d", response.status_code)
                return None

            raw = response.json().get("response", "")
            return cls._extract_json(raw)

        except requests.exceptions.ConnectionError:
            logger.warning("Ollama not reachable (connection refused) — using spaCy fallback")
            return None
        except requests.exceptions.Timeout:
            logger.warning("Ollama timed out (%ds) — using spaCy fallback", config.OLLAMA_TIMEOUT)
            return None
        except Exception as e:
            logger.error("Ollama unexpected error: %s", e)
            return None

    @staticmethod
    def _extract_json(raw: str) -> Optional[Dict]:
        """
        Parse JSON from the raw Ollama output.

        Handles three common output formats:
          1. Clean JSON string
          2. JSON wrapped in ```json … ``` fences
          3. JSON embedded somewhere inside prose text
        """
        # Strip markdown code fences
        raw = re.sub(r'```(?:json)?\s*', '', raw).strip()

        # Attempt 1 — direct parse
        try:
            return json.loads(raw)
        except json.JSONDecodeError:
            pass

        # Attempt 2 — extract first { … } block
        m = re.search(r'\{.*\}', raw, re.DOTALL)
        if m:
            try:
                return json.loads(m.group())
            except json.JSONDecodeError:
                pass

        logger.warning("Could not parse Ollama response as JSON (response length: %d)", len(raw))
        return None
