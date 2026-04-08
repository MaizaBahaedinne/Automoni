"""
spaCy NLP Extractor — Stage 2 of the CV parsing pipeline.

Extracts structured information from raw CV text using named entity
recognition and keyword pattern matching.  Fast, fully offline, no GPU
required.  Acts as both the primary extraction engine (skills, contact info)
and the fallback when Ollama is unavailable.
"""
import re
import logging
import unicodedata
import spacy
from typing import Dict, List, Optional

logger = logging.getLogger(__name__)


def _norm(s: str) -> str:
    """Lowercase + strip accents for section header matching."""
    return ''.join(
        c for c in unicodedata.normalize('NFD', s.lower())
        if unicodedata.category(c) != 'Mn'
    )

# ── Technical skills dictionary ───────────────────────────────────────────────
# Each entry is checked against the CV text (case-insensitive).
# Confidence is boosted slightly for each additional mention.
TECH_SKILLS: List[str] = [
    # Programming languages
    "Python", "JavaScript", "TypeScript", "PHP", "Java", "Kotlin", "Swift",
    "C", "C++", "C#", "Go", "Rust", "Ruby", "Scala", "R", "MATLAB",
    "Dart", "Perl", "Bash", "Shell", "PowerShell", "Haskell", "Elixir", "VBA",
    # Frontend
    "HTML", "CSS", "SASS", "LESS", "React", "Vue", "Angular", "Svelte",
    "Next.js", "Nuxt.js", "jQuery", "Bootstrap", "Tailwind", "Material UI",
    # Backend
    "Laravel", "Symfony", "Django", "Flask", "FastAPI", "Node.js", "Express",
    "Spring", "Spring Boot", "Rails", "CodeIgniter", "CakePHP", "Lumen",
    # Databases
    "PostgreSQL", "MySQL", "MariaDB", "SQLite", "MongoDB", "Redis",
    "Elasticsearch", "Cassandra", "Neo4j", "DynamoDB", "Firebase",
    # DevOps / Cloud
    "Docker", "Kubernetes", "Terraform", "Ansible", "Jenkins", "GitHub Actions",
    "AWS", "Azure", "Microsoft Azure", "GCP", "Heroku", "Vercel", "Netlify", "DigitalOcean",
    # Tools & protocols
    "Git", "Linux", "Nginx", "Apache", "GraphQL", "REST", "gRPC", "WebSocket",
    "RabbitMQ", "Kafka", "Celery", "Webpack", "Vite", "CI/CD",
    # AI / ML
    "TensorFlow", "PyTorch", "scikit-learn", "Keras", "spaCy", "NLTK",
    "Pandas", "NumPy", "OpenCV", "HuggingFace", "LangChain", "Ollama",
    # Testing
    "PHPUnit", "Jest", "pytest", "Selenium", "Cypress",
    # Methodologies
    "Agile", "Scrum", "Kanban", "JIRA", "Figma", "Photoshop",
    # ── SAP ecosystem ────────────────────────────────────────────────────────
    "SAP", "SAP BASIS", "SAP HANA", "SAP S/4HANA", "SAP ECC", "SAP BW",
    "SAP Fiori", "SAP PI", "SAP PO", "SAP PI/PO", "Solution Manager",
    "ChaRM", "IDocs", "RFC", "ABAP", "SAP GRC", "SAP HR", "SAP MM",
    "SAP SD", "SAP FI", "SAP CO", "SAP PM", "SAP PS", "SAP WM", "SAP TM",
    "SAP CRM", "SAP SRM", "SAP HCM", "SAP SuccessFactors", "SAP Ariba",
    "SAP BTP", "SAP MDG",
    # ── Security / compliance ────────────────────────────────────────────────
    "ISO 27001", "ISO 9001", "RGPD", "GDPR", "PCI DSS", "SOC 2",
    "SMSI", "PRA", "PCA", "DRP",
    # ── Other enterprise tools ───────────────────────────────────────────────
    "Salesforce", "ServiceNow", "Workday", "Oracle ERP", "Microsoft 365",
    "Power BI", "Tableau", "SharePoint", "Active Directory", "LDAP",
    "Nginx", "Apache", "IIS", "VMware", "Hyper-V", "ITIL",
]

# ── Human language map ─────────────────────────────────────────────────────────
# keyword → canonical name  (covers EN + FR CV vocabulary)
HUMAN_LANGUAGES: Dict[str, str] = {
    "english": "English",  "anglais": "English",
    "french": "French",    "français": "French",  "francais": "French",
    "arabic": "Arabic",    "arabe": "Arabic",
    "spanish": "Spanish",  "espagnol": "Spanish",
    "german": "German",    "allemand": "German",
    "italian": "Italian",  "italien": "Italian",
    "portuguese": "Portuguese", "portugais": "Portuguese",
    "chinese": "Chinese",  "mandarin": "Chinese",
    "japanese": "Japanese",
    "korean": "Korean",
    "dutch": "Dutch",
    "russian": "Russian",
}

PROFICIENCY_KEYWORDS: Dict[str, str] = {
    "native": "Native",        "natif": "Native",       "bilingue": "Native",
    "fluent": "Fluent",        "courant": "Fluent",
    "proficient": "Proficient", "professional": "Proficient",
    "advanced": "Advanced",    "avancé": "Advanced",
    "intermediate": "Intermediate", "intermédiaire": "Intermediate",
    "basic": "Basic",          "notions": "Basic",
    "débutant": "Basic",       "beginner": "Basic",
}

# ── Section header keywords (for context extraction sent to Ollama) ────────────
_SECTION_MAP: Dict[str, List[str]] = {
    "experience":     ["work experience", "professional experience", "employment history",
                       "experience professionnelle", "experiences professionnelles",
                       "experiences", "postes occupes", "parcours professionnel",
                       "career history", "work history"],
    "education":      ["education", "academic background", "academic history",
                       "formation", "formations", "etudes", "diplomes",
                       "qualifications", "parcours academique", "scolarite"],
    "certifications": ["certifications", "certificates", "licences",
                       "diplomes professionnels", "certifications et formations"],
    "summary":        ["summary", "profile", "objective", "about me", "about",
                       "profil", "resume", "presentation", "objectif", "a propos"],
}


class SpacyExtractor:
    """Extract structured data from CV text using spaCy NLP."""

    _nlp = None  # shared across requests (lazy-loaded once)

    # ── Public API ────────────────────────────────────────────────────────────

    @classmethod
    def load_model(cls):
        """
        Lazy-load spaCy model.
        Priority: en_core_web_sm → fr_core_news_sm → blank English model.
        """
        if cls._nlp is None:
            for model_name in ("en_core_web_sm", "fr_core_news_sm"):
                try:
                    cls._nlp = spacy.load(model_name)
                    logger.info("Loaded spaCy model: %s", model_name)
                    return cls._nlp
                except OSError:
                    continue
            logger.warning("No spaCy model installed — using blank model (NER disabled)")
            cls._nlp = spacy.blank("en")
        return cls._nlp

    @classmethod
    def extract(cls, text: str) -> Dict:
        """
        Parse CV text and return all extractable fields.

        Returns:
            {
              "name":      str | None,
              "email":     str | None,
              "phone":     str | None,
              "skills":    [{"name": ..., "confidence": ...}],
              "languages": [{"name": ..., "proficiency": ..., "confidence": ...}],
              "sections":  {"experience": ..., "education": ..., ...},
            }
        """
        nlp = cls.load_model()
        # Cap input to 100 k chars to keep inference fast
        doc = nlp(text[:100_000])

        return {
            "name":      cls._extract_name(doc, text),
            "headline":  cls._extract_headline(text),
            "email":     cls._extract_email(text),
            "phone":     cls._extract_phone(text),
            "city":      cls._extract_city(text),
            "country":   cls._extract_country(text),
            "summary":   cls._extract_summary(text),
            "skills":    cls._extract_skills(text),
            "languages": cls._extract_languages(text),
            "sections":  cls._extract_sections(text),
        }

    # ── Private helpers ───────────────────────────────────────────────────────

    @staticmethod
    def _extract_headline(text: str) -> Optional[str]:
        """
        Extract headline from the 2nd non-empty line of the CV.
        E.g. "Consultant SAP BASIS Senior | S/4HANA | ..."
        """
        lines = [l.strip() for l in text.splitlines() if l.strip()]
        # Skip first line (name), return second non-empty line if it looks like a title
        if len(lines) >= 2:
            candidate = lines[1]
            # Accept if it's reasonably short and not contact info
            if len(candidate) < 200 and '@' not in candidate and not re.match(r'^\+?\d', candidate):
                return candidate
        return None

    @staticmethod
    def _extract_city(text: str) -> Optional[str]:
        """
        Extract city from patterns like "City, Country |" or "City | phone | email".
        """
        m = re.search(
            r'^([A-ZÀ-Ÿa-zà-ÿ][A-ZÀ-Ÿa-zà-ÿ\s\-]+),\s*([A-ZÀ-Ÿa-zà-ÿ][A-ZÀ-Ÿa-zà-ÿ\s]+?)\s*[|,]',
            text,
            re.MULTILINE,
        )
        return m.group(1).strip() if m else None

    @staticmethod
    def _extract_country(text: str) -> Optional[str]:
        """
        Extract country from patterns like "City, Country |".
        """
        m = re.search(
            r'^[A-ZÀ-Ÿa-zà-ÿ][\w\s\-]+,\s*([A-ZÀ-Ÿa-zà-ÿ][A-ZÀ-Ÿa-zà-ÿ\s]+?)\s*[|,\n]',
            text,
            re.MULTILINE,
        )
        return m.group(1).strip() if m else None

    @staticmethod
    def _extract_summary(text: str) -> Optional[str]:
        """
        Extract the profile summary section text.
        """
        # Find PROFIL / SUMMARY / ABOUT section header and grab text until next header
        header_re = re.compile(
            r'^(PROFIL|SUMMARY|PROFILE|ABOUT|PR[EÉ]SENTATION|OBJECTIF)\s*$',
            re.MULTILINE | re.IGNORECASE,
        )
        next_header_re = re.compile(
            r'^[A-ZÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞ\s]{4,}\s*$',
            re.MULTILINE,
        )
        m = header_re.search(text)
        if not m:
            return None
        after = text[m.end():].strip()
        m2 = next_header_re.search(after)
        summary = after[:m2.start()].strip() if m2 else after[:800].strip()
        return summary if len(summary) > 20 else None

    @staticmethod
    def _extract_email(text: str) -> Optional[str]:
        m = re.search(r'\b[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}\b', text)
        return m.group() if m else None

    @staticmethod
    def _extract_phone(text: str) -> Optional[str]:
        """
        Try multiple phone patterns; require at least 9 actual digits.
        Patterns ordered from most-specific to most-general.
        """
        patterns = [
            r'(?:\+33|0033|0)\s*[67]\s*(?:\d{2}\s*){4}',   # FR mobile
            r'(?:\+1[-.\s]?)?\(?\d{3}\)?[-.\s]?\d{3}[-.\s]?\d{4}',  # NA
            r'\+?[\d\s\-\(\)\.]{10,20}',                    # generic intl
        ]
        for pattern in patterns:
            m = re.search(pattern, text)
            if m:
                phone = re.sub(r'\s+', ' ', m.group()).strip()
                if len(re.sub(r'\D', '', phone)) >= 9:
                    return phone
        return None

    @staticmethod
    def _extract_name(doc, text: str) -> Optional[str]:
        """
        Names via NER PERSON first; fall back to first capitalised 2–4 word line.
        """
        for ent in doc.ents:
            if ent.label_ == "PERSON":
                name = ent.text.strip()
                if 2 <= len(name.split()) <= 5:
                    return name

        for line in text.split("\n")[:8]:
            clean = re.sub(r'[^a-zA-ZÀ-ÿ\s\-]', '', line).strip()
            words = clean.split()
            if 2 <= len(words) <= 4 and all(w[0].isupper() for w in words if w):
                if len(clean) < 60:
                    return clean
        return None

    @staticmethod
    def _extract_skills(text: str) -> List[Dict]:
        """
        Keyword-match against TECH_SKILLS.
        Confidence = 0.70 base + 0.05 per extra mention, capped at 0.95.
        """
        text_lower = text.lower()
        found: List[Dict] = []
        seen: set = set()

        for skill in TECH_SKILLS:
            key = skill.lower()
            if key in text_lower and key not in seen:
                seen.add(key)
                count = text_lower.count(key)
                confidence = round(min(0.95, 0.70 + (count - 1) * 0.05), 2)
                found.append({"name": skill, "confidence": confidence})

        return found

    @staticmethod
    def _extract_languages(text: str) -> List[Dict]:
        """
        Detect human languages and proficiency.
        Priority: structured bullet pattern "• Language : Level",
        then keyword matching (preserves original language name from CV).
        """
        found: List[Dict] = []
        seen: set = set()

        # Pattern 1: "• Français : Courant" / "- English : Fluent"
        bullet_re = re.compile(
            r'[•\-\*]\s*([A-ZÀ-ŸA-Za-zà-ÿ][a-zà-ÿA-ZÀ-Ÿ]+(?:\s+[A-Za-zÀ-ÿ]+)?)\s*:\s*([A-Za-zÀ-ÿ]+)',
            re.MULTILINE,
        )
        # Only accept if the word before ":" is a known language keyword
        all_lang_keys = set(HUMAN_LANGUAGES.keys())
        for m in bullet_re.finditer(text):
            name_raw  = m.group(1).strip()
            level_raw = m.group(2).strip()
            norm_name = _norm(name_raw)
            if norm_name in all_lang_keys and norm_name not in seen:
                seen.add(norm_name)
                found.append({
                    "name":        name_raw,   # keep original ("Français" not "French")
                    "proficiency": level_raw,  # keep original ("Courant" not "Fluent")
                    "confidence":  0.90,
                })

        if found:
            return found

        # Pattern 2: keyword scan (fallback)
        text_lower = text.lower()
        for keyword, language in HUMAN_LANGUAGES.items():
            if keyword in text_lower and language not in seen:
                seen.add(language)
                m = re.search(rf'{re.escape(keyword)}.{{0,40}}', text_lower)
                proficiency = None
                if m:
                    context = m.group()
                    for pk, label in PROFICIENCY_KEYWORDS.items():
                        if pk in context:
                            proficiency = label
                            break
                found.append({
                    "name":        language,
                    "proficiency": proficiency,
                    "confidence":  0.80,
                })
        return found

    @staticmethod
    def _extract_sections(text: str) -> Dict:
        """
        Split CV text into named sections by detecting header lines.
        Uses accent-stripped comparison so "EXPERIENCE PROFESSIONNELLE" matches.
        """
        sections: Dict[str, str] = {k: "" for k in _SECTION_MAP}
        current: Optional[str] = None

        for line in text.split("\n"):
            ll = _norm(line.strip())
            matched = False
            for section, headers in _SECTION_MAP.items():
                if any(h in ll for h in headers) and len(ll) < 60:
                    current = section
                    matched = True
                    break
            if not matched and current and line.strip():
                sections[current] += line + "\n"

        return sections
