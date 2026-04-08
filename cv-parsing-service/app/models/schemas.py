"""
Pydantic schemas for CV parsing
"""
from pydantic import BaseModel
from typing import Optional, List


class Profile(BaseModel):
    """Profile information"""
    name: Optional[str] = None
    headline: Optional[str] = None
    email: Optional[str] = None
    phone: Optional[str] = None
    city: Optional[str] = None
    country: Optional[str] = None
    summary: Optional[str] = None


class Skill(BaseModel):
    """Skill entry"""
    name: str
    confidence: float = 0.8


class Language(BaseModel):
    """Language proficiency"""
    name: str
    proficiency: Optional[str] = None
    confidence: float = 0.8


class Experience(BaseModel):
    """Work experience"""
    job_title: Optional[str] = None
    company_name: Optional[str] = None
    start_date: Optional[str] = None
    end_date: Optional[str] = None
    location: Optional[str] = None
    description: Optional[str] = None
    confidence: float = 0.7


class Education(BaseModel):
    """Education"""
    institution: Optional[str] = None
    degree: Optional[str] = None
    field_of_study: Optional[str] = None
    graduation_year: Optional[str] = None
    confidence: float = 0.8


class Certification(BaseModel):
    """Certification"""
    name: Optional[str] = None
    issuer: Optional[str] = None
    issue_date: Optional[str] = None
    expiration_date: Optional[str] = None
    confidence: float = 0.7


class ParsedCV(BaseModel):
    """Complete parsed CV"""
    profile: Profile
    skills: List[Skill] = []
    languages: List[Language] = []
    experiences: List[Experience] = []
    education: List[Education] = []
    certifications: List[Certification] = []


class CVParseResponse(BaseModel):
    """API response for CV parsing"""
    success: bool
    message: str
    data: Optional[ParsedCV] = None
    error: Optional[str] = None
