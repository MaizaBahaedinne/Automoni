# 📚 Organization Creation API Documentation

**Version:** 1.0  
**Date:** 2026-04-06  
**Framework:** CodeIgniter 4  

---

## 📋 Table of Contents

1. [Overview](#overview)
2. [Endpoints](#endpoints)
3. [Request Examples](#request-examples)
4. [Response Examples](#response-examples)
5. [Field Reference](#field-reference)
6. [Data Models](#data-models)
7. [Error Handling](#error-handling)

---

## Overview

The Organization API provides endpoints for creating, reading, updating, and managing organizations with all their relationships including:
- Basic information (name, type, description)
- Contact details (email, phone, website)
- Geographic location (address, coordinates)
- Business information (size, markets, sectors, pricing)
- Quality & reputation (certifications, labels, partnerships)

### API Base URL
```
https://persomy.com/api
```

### Authentication
- Protected endpoints require session authentication (`AuthFilter`)
- User must be logged in: `session()->get('user_id')`

---

## Endpoints

### 1. Create Organization
**POST /organizations**

**Required fields:**
- `type_id` (int) — Organization type ID
- `name` (string, 3-255 chars) — Organization name
- `email` (string) — Contact email
- `phone_number` (string) — Phone digits only
- `phone_country_code` (string) — e.g., "+33"
- `website` (string) — URL with http(s)://
- `street_address` (string) — Street address
- `city` (string) — City name
- `postal_code` (string) — ZIP code
- `country_code` (string) — ISO 2-letter code (FR, US, etc.)

**Optional fields:**
- `legal_name` — Official legal name
- `description` — Organization description
- `parent_id` — Parent organization ID
- `slug` — URL-friendly slug (auto-generated if not provided)
- `tax_id` — Tax/registration number
- `latitude`, `longitude` — GPS coordinates
- `size` — "startup", "pme", "grande_entreprise"
- `markets_targeted` — ["local", "international"]
- `employee_count` — Number of employees
- `founded_at` — Date in Y-m-d format
- `status` — "active", "inactive", "archived", "en_liquidation"
- `sectors` — JSON array of sector IDs
- `budget_annual`, `revenue_annual` — Financial info
- `reputation_score` — 0-5 decimal
- `logo` — Logo filename (via file upload)

**Related data (nested):**
- `certifications` — Array of certification objects
- `quality_labels` — Array of quality label objects
- `markets` — Array of market presence objects
- `pricing` — Array of pricing objects
- `partners` — Array of partnership objects

---

### 2. Get Organization
**GET /organizations/{id}**

Returns full organization object with all relations.

---

### 3. Update Organization
**PUT/PATCH /organizations/{id}**

Update any subset of organization fields. Same parameters as creation.

---

### 4. Search Parent Organizations
**GET /api/organizations/search?q=search_term**

**Query Parameters:**
- `q` (string, min 2 chars) — Search term
- `limit` (int, default 10) — Results limit

**Response:** Array of organizations matching the search term

---

## Request Examples

### Example 1: Minimal Organization Creation

**Request:**
```json
POST /organizations
Content-Type: application/json

{
  "type_id": 1,
  "name": "TechCorp France",
  "email": "contact@techcorp.fr",
  "phone_number": "1 23 45 67 89",
  "phone_country_code": "+33",
  "website": "https://techcorp.fr",
  "street_address": "123 Avenue des Champs-Élysées",
  "city": "Paris",
  "postal_code": "75008",
  "country_code": "FR"
}
```

**Response:**
```json
{
  "status": "success",
  "message": "Organization created successfully",
  "data": {
    "id": 42,
    "type_id": 1,
    "name": "TechCorp France",
    "slug": "techcorp-france-1712406000",
    "email": "contact@techcorp.fr",
    "website": "https://techcorp.fr",
    "status": "active",
    "created_at": "2026-04-06T10:30:00Z"
  }
}
```

---

### Example 2: Complete Organization with All Fields

**Request:**
```json
POST /organizations
Content-Type: application/json

{
  "type_id": 1,
  "name": "Innovate Solutions",
  "legal_name": "Innovate Solutions SARL",
  "slug": "innovate-solutions",
  "description": "Leading AI and blockchain solutions provider",
  "email": "info@innovate.com",
  "phone_country_code": "+33",
  "phone_number": "1 41 42 43 44",
  "website": "https://innovate.com",
  "street_address": "456 Boulevard Saint-Germain",
  "city": "Paris",
  "postal_code": "75006",
  "country": "France",
  "country_code": "FR",
  "latitude": 48.8507,
  "longitude": 2.3475,
  "tax_id": "FR12345678901",
  "employee_count": 150,
  "founded_at": "2015-06-15",
  "size": "pme",
  "status": "active",
  "is_verified": true,
  "markets_targeted": ["local", "international"],
  "sectors": ["technology", "consulting", "ai"],
  "budget_annual": 2500000.00,
  "revenue_annual": 3200000.00,
  "reputation_score": 4.5,
  
  "certifications": [
    {
      "name": "ISO 27001",
      "issuer": "TÜV SÜD",
      "certification_type": "information_security",
      "issued_at": "2023-06-15",
      "expires_at": "2026-06-14",
      "url": "https://example.com/iso27001",
      "cost": 5000,
      "validated_by": "TÜV SÜD",
      "is_active": true
    },
    {
      "name": "ISO 9001",
      "issuer": "Bureau Veritas",
      "certification_type": "quality_management",
      "issued_at": "2022-03-20",
      "expires_at": "2025-03-19",
      "url": "https://example.com/iso9001",
      "cost": 3000,
      "validated_by": "Bureau Veritas",
      "is_active": true
    }
  ],

  "quality_labels": [
    {
      "label_type": "award",
      "label_name": "Best Tech Startup 2025",
      "issuer": "TechCrunch Europe",
      "issued_at": "2025-10-15",
      "url": "https://techcrunch.com/awards/2025",
      "description": "Recognized as the most innovative startup in AI/ML"
    },
    {
      "label_type": "badge",
      "label_name": "LinkedIn Top Company",
      "issuer": "LinkedIn",
      "issued_at": "2025-01-01",
      "is_active": true
    },
    {
      "label_type": "certification",
      "label_name": "Great Place to Work",
      "issuer": "Great Place to Work Institute",
      "issued_at": "2025-01-10",
      "expires_at": "2026-01-09"
    }
  ],

  "markets": [
    {
      "market_type": "local",
      "region": "Île-de-France",
      "market_share": 15.5,
      "since_date": "2015-06-15"
    },
    {
      "market_type": "international",
      "country_code": "DE",
      "region": "Europe",
      "market_share": 8.2,
      "since_date": "2018-03-01"
    },
    {
      "market_type": "international",
      "country_code": "US",
      "region": "North America",
      "market_share": 12.7,
      "since_date": "2019-11-15"
    }
  ],

  "pricing": [
    {
      "pricing_type": "subscription",
      "pricing_name": "Starter Plan",
      "description": "Perfect for small teams and early-stage projects",
      "price": 99.00,
      "currency": "EUR",
      "billing_period": "monthly",
      "min_quantity": 1,
      "is_public": true,
      "is_active": true
    },
    {
      "pricing_type": "subscription",
      "pricing_name": "Professional Plan",
      "description": "For growing teams with advanced features",
      "price": 299.00,
      "currency": "EUR",
      "billing_period": "monthly",
      "min_quantity": 1,
      "is_public": true,
      "is_active": true
    },
    {
      "pricing_type": "subscription",
      "pricing_name": "Enterprise Plan",
      "description": "Custom pricing for enterprises",
      "price": 999.00,
      "currency": "EUR",
      "billing_period": "yearly",
      "is_public": false,
      "is_active": true
    },
    {
      "pricing_type": "license",
      "pricing_name": "Perpetual License",
      "description": "One-time purchase license",
      "price": 2500.00,
      "currency": "EUR",
      "billing_period": "one-time",
      "is_public": true,
      "is_active": true
    }
  ],

  "partners": [
    {
      "partner_id": 5,
      "partnership_type": "technology_partner",
      "partnership_status": "active",
      "description": "Technology integration partnership for AI services",
      "started_at": "2023-06-01",
      "revenue_share": 15.0,
      "is_active": true
    },
    {
      "partner_id": 12,
      "partnership_type": "reseller",
      "partnership_status": "active",
      "description": "Authorized reseller in Benelux region",
      "started_at": "2022-01-01",
      "revenue_share": 20.0,
      "is_active": true
    }
  ]
}
```

**Response:**
```json
{
  "status": "success",
  "message": "Organization created successfully with all relations",
  "data": {
    "id": 43,
    "type_id": 1,
    "name": "Innovate Solutions",
    "slug": "innovate-solutions",
    "status": "active",
    "size": "pme",
    "markets_targeted": ["local", "international"],
    "reputation_score": 4.5,
    "certifications": 2,
    "quality_labels": 3,
    "markets": 3,
    "pricing_plans": 4,
    "partnerships": 2,
    "created_at": "2026-04-06T10:30:00Z",
    "created_by": 1
  }
}
```

---

### Example 3: NGO Creation

```json
POST /organizations
Content-Type: application/json

{
  "type_id": 2,
  "name": "Global Education Initiative",
  "description": "Non-profit focused on education in developing countries",
  "email": "contact@globaledu.org",
  "phone_country_code": "+44",
  "phone_number": "20 7946 0958",
  "website": "https://globaledu.org",
  "street_address": "10-12 Southwark Street",
  "city": "London",
  "postal_code": "SE1 1RQ",
  "country_code": "GB",
  "status": "active",
  "size": "pme",
  "markets_targeted": ["international"],
  "sectors": ["education", "non-profit", "development"],
  "employee_count": 45,
  "founded_at": "2010-03-20"
}
```

---

### Example 4: Government Organization

```json
POST /organizations
Content-Type: application/json

{
  "type_id": 4,
  "name": "Ministry of Digital Economy",
  "legal_name": "Ministère de l'Économie Numérique",
  "description": "Government agency overseeing digital transformation",
  "email": "contact@economie-numerique.gouv.fr",
  "phone_country_code": "+33",
  "phone_number": "1 42 75 80 00",
  "website": "https://economie-numerique.gouv.fr",
  "street_address": "101 Rue de Grenelle",
  "city": "Paris",
  "postal_code": "75007",
  "country_code": "FR",
  "status": "active",
  "size": "grande_entreprise",
  "markets_targeted": ["local"],
  "sectors": ["government", "digital"],
  "employee_count": 5000,
  "founded_at": "2017-05-15"
}
```

---

## Response Examples

### Success Response (201 Created)
```json
{
  "status": "success",
  "message": "Organization created successfully",
  "data": {
    "id": 42,
    "type_id": 1,
    "name": "Organization Name",
    "slug": "organization-name",
    "description": null,
    "email": "contact@example.com",
    "website": "https://example.com",
    "phone": "+33 1 23 45 67 89",
    "status": "active",
    "size": "pme",
    "created_at": "2026-04-06T10:30:00Z"
  }
}
```

### Error Response (422 Unprocessable Entity)
```json
{
  "status": "error",
  "message": "Validation failed",
  "errors": {
    "email": "Email address is not valid",
    "phone_number": "Phone number must be between 7 and 15 characters",
    "country_code": "Country code must be 2 uppercase letters"
  }
}
```

### Error Response (400 Bad Request)
```json
{
  "status": "error",
  "message": "Invalid data",
  "errors": {
    "parent_id": "Parent organization must be different from the organization itself"
  }
}
```

---

## Field Reference

### Organization Types
```
1 = Société (Company)
2 = ONG (NGO)
3 = Association
4 = Organisme gouvernemental (Government Agency)
```

### Organization Size
```
startup = Startup (< 10 employees)
pme = PME (10-250 employees)
grande_entreprise = Large Enterprise (> 250 employees)
```

### Status Values
```
active = Active (operational)
inactive = Inactive (not currently operating)
archived = Archived (historical record)
en_liquidation = In liquidation (winding down)
```

### Markets Targeted
```
["local"] = Operating primarily in home country/region
["international"] = Operating in multiple countries
["local", "international"] = Both local and international
```

### Business Sectors (Examples)
```
technology, finance, healthcare, manufacturing, energy, 
education, consulting, non-profit, government, retail, 
real-estate, transportation, telecommunications
```

### Certification Types
```
iso - ISO certification (ISO 9001, ISO 27001)
gdpr - GDPR compliance
quality_management - Quality management certification
information_security - Information security certification
environmental - Environmental certification
```

### Quality Label Types
```
award - Industry award or recognition
badge - Professional badge or certification
recognition - Special recognition from authority
certification - Quality certification
partner_badge - Partner program badge
quality_mark - Quality mark or seal
```

### Market Types
```
local - Local/regional presence
international - International presence with specific country
```

### Pricing Types
```
subscription - Recurring subscription (monthly, yearly)
license - Purchase license (perpetual or time-limited)
service - Service-based pricing
product - Product-based pricing
custom - Custom pricing (contact for quote)
```

---

## Data Models

### Organization object
```json
{
  "id": "integer",
  "type_id": "integer",
  "parent_id": "integer|null",
  "name": "string",
  "legal_name": "string|null",
  "slug": "string",
  "description": "string|null",
  "email": "string",
  "phone": "string",
  "phone_country_code": "string",
  "phone_number": "string",
  "website": "string",
  "street_address": "string",
  "city": "string",
  "postal_code": "string",
  "country": "string",
  "country_code": "string",
  "latitude": "decimal(10,8)|null",
  "longitude": "decimal(11,8)|null",
  "tax_id": "string|null",
  "employee_count": "integer|null",
  "founded_at": "date|null",
  "size": "enum: startup|pme|grande_entreprise|null",
  "status": "enum: active|inactive|archived|en_liquidation",
  "is_verified": "boolean",
  "markets_targeted": "json array",
  "sectors": "json array",
  "budget_annual": "decimal(15,2)|null",
  "revenue_annual": "decimal(15,2)|null",
  "reputation_score": "decimal(3,1)|null",
  "created_at": "datetime",
  "updated_at": "datetime",
  "deleted_at": "datetime|null"
}
```

### Certification object
```json
{
  "id": "integer",
  "organization_id": "integer",
  "name": "string",
  "issuer": "string|null",
  "certification_type": "string",
  "issued_at": "date|null",
  "expires_at": "date|null",
  "cost": "decimal(10,2)|null",
  "validated_by": "string|null",
  "is_active": "boolean",
  "url": "string|null",
  "created_at": "datetime",
  "updated_at": "datetime"
}
```

### Quality Label object
```json
{
  "id": "integer",
  "organization_id": "integer",
  "label_type": "enum: award|badge|recognition|certification|partner_badge|quality_mark",
  "label_name": "string",
  "issuer": "string|null",
  "issued_at": "date|null",
  "expires_at": "date|null",
  "url": "string|null",
  "description": "string|null",
  "is_active": "boolean",
  "created_at": "datetime",
  "updated_at": "datetime"
}
```

### Market object
```json
{
  "id": "integer",
  "organization_id": "integer",
  "market_type": "enum: local|international",
  "country_code": "string(2)|null",
  "region": "string|null",
  "market_share": "decimal(5,2)|null",
  "since_date": "date|null",
  "is_active": "boolean",
  "created_at": "datetime",
  "updated_at": "datetime"
}
```

### Pricing object
```json
{
  "id": "integer",
  "organization_id": "integer",
  "pricing_type": "enum: subscription|license|service|product|custom",
  "pricing_name": "string",
  "description": "string|null",
  "price": "decimal(10,2)",
  "currency": "string(3)",
  "billing_period": "enum: monthly|yearly|quarterly|bi-annual|one-time|custom",
  "min_quantity": "integer|null",
  "max_quantity": "integer|null",
  "is_public": "boolean",
  "is_active": "boolean",
  "created_at": "datetime",
  "updated_at": "datetime"
}
```

---

## Error Handling

### HTTP Status Codes
- `201` Created — Organization successfully created
- `200` OK — Request successful
- `400` Bad Request — Circular hierarchy, logic error
- `422` Unprocessable Entity — Validation errors
- `401` Unauthorized — Not logged in
- `403` Forbidden — No permission
- `404` Not Found — Organization not found
- `500` Server Error — Database error

### Validation Rules

**type_id:** Required, must be valid organization type ID

**name:** Required, 3-255 characters, unique per instance

**slug:** 3-255 chars, lowercase alphanumeric + hyphens, auto-generated if empty

**email:** Required, valid email format

**phone_number:** Required, 7-15 digits/characters, may contain +, -, (), spaces

**website:** Required, must start with http:// or https://

**street_address:** Required, minimum 5 characters

**city:** Required, 2-100 characters

**postal_code:** Required, 2-20 characters (format varies by country)

**country_code:** Required, exactly 2 uppercase letters (ISO standard)

**latitude:** Optional, must be between -90 and 90

**longitude:** Optional, must be between -180 and 180

**size:** Optional, must be "startup", "pme", or "grande_entreprise"

**status:** Must be active, inactive, archived, or en_liquidation

**markets_targeted:** Must be valid JSON array with "local" and/or "international"

---

**Last Updated:** 2026-04-06  
**API Version:** 1.0  
**Maintainer:** Persomy Platform Team
