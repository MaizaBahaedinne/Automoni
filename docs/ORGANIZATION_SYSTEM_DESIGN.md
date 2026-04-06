# 🏢 System de Gestion des Organisations — Guide de Conception

**Version:** 2.0 (Complète)  
**Date:** 2026-04-06  
**Framework:** CodeIgniter 4.4+  
**PHP:** 8.1+  

---

## 📋 Table des matières

1. [Vue d'ensemble](#vue-densemble)
2. [Architecture de la base de données](#architecture-de-la-base-de-données)
3. [Choix de modélisation](#choix-de-modélisation)
4. [Modèles et relations](#modèles-et-relations)
5. [Service layer](#service-layer)
6. [Exemples de création](#exemples-de-création)
7. [Cas d'usage](#cas-dusage)
8. [Scalabilité](#scalabilité)

---

## Vue d'ensemble

### Objectif
Créer un système flexible et scalable pour gérer les organisations avec :
- Différents types d'organisations (Sociétés, ONG, Associations, Organismes gouvernementaux)
- Hiérarchies parent-enfant
- Informations détaillées (adresse, contacts, financier)
- Certifications, qualité, partenariats
- Pricing et marchés ciblés
- Gestion réputationnelle

### Entités principales
```
├── organizations (table principale)
├── organization_types (référence)
├── organization_members (gestion d'équipe)
├── organization_certifications (certifications)
├── organization_quality_labels (badges, prix, reconnaissances)
├── organization_markets (présence géographique)
├── organization_pricing (modèles tarifaires)
├── organization_partners (partenariats)
└── organization_social_links (réseaux sociaux)
```

---

## Architecture de la base de données

### 1. Table `organizations` (Principale)

```sql
CREATE TABLE `organizations` (
    -- Identifiants
    `id`                   INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `parent_id`            INT(11) UNSIGNED NULL,
    `type_id`              INT(11) UNSIGNED NOT NULL,
    
    -- Informations de base
    `name`                 VARCHAR(255) NOT NULL,
    `legal_name`           VARCHAR(255) NULL,
    `slug`                 VARCHAR(255) NOT NULL UNIQUE,
    `description`          LONGTEXT NULL,
    `logo`                 VARCHAR(255) NULL,
    
    -- Contacts
    `email`                VARCHAR(255) NOT NULL,
    `website`              VARCHAR(255) NOT NULL,
    `phone`                VARCHAR(20) NULL,          -- Full formatted
    `phone_country_code`   VARCHAR(5) NULL,           -- "+33"
    `phone_number`         VARCHAR(20) NULL,          -- Digits only
    
    -- Adresse complète
    `address`              TEXT NULL,                 -- Legacy field
    `street_address`       VARCHAR(255) NULL,
    `city`                 VARCHAR(100) NULL,
    `postal_code`          VARCHAR(20) NULL,
    `country`              VARCHAR(100) NULL,
    `country_code`         CHAR(2) NULL,              -- ISO 2-letter
    `latitude`             DECIMAL(10,8) NULL,
    `longitude`            DECIMAL(11,8) NULL,
    `map_link`             VARCHAR(500) NULL,
    
    -- Informations commerciales
    `tax_id`               VARCHAR(50) NULL,
    `employee_count`       INT(11) NULL,
    `industry`             VARCHAR(100) NULL,
    `sectors`              JSON NULL,                 -- ["tech", "ai", "consulting"]
    `founded_at`           DATE NULL,
    
    -- Taille et marchés
    `size`                 ENUM('startup', 'pme', 'grande_entreprise') NULL,
    `markets_targeted`     JSON NULL,                 -- ["local", "international"]
    
    -- Financier (confidentiel)
    `budget_annual`        DECIMAL(15,2) NULL,
    `revenue_annual`       DECIMAL(15,2) NULL,
    
    -- Réputation et statut
    `reputation_score`     DECIMAL(3,1) NULL,        -- 0.0 to 5.0
    `status`               ENUM('active','inactive','archived','en_liquidation') DEFAULT 'active',
    `is_verified`          TINYINT(1) NOT NULL DEFAULT 0,
    
    -- Audit
    `created_at`           DATETIME NULL,
    `updated_at`           DATETIME NULL,
    `deleted_at`           DATETIME NULL,             -- Soft delete
    
    PRIMARY KEY (`id`),
    UNIQUE KEY `unique_slug` (`slug`),
    KEY `fk_type` (`type_id`),
    KEY `fk_parent` (`parent_id`),
    KEY `idx_status` (`status`),
    KEY `idx_city` (`city`),
    KEY `idx_country` (`country_code`),
    KEY `idx_size` (`size`),
    
    FOREIGN KEY (`type_id`) REFERENCES `organization_types` (`id`),
    FOREIGN KEY (`parent_id`) REFERENCES `organizations` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 2. Table `organization_quality_labels`

```sql
CREATE TABLE `organization_quality_labels` (
    `id`              INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `organization_id` INT(11) UNSIGNED NOT NULL,
    
    -- Classification
    `label_type`      ENUM('award','badge','recognition','certification','partner_badge','quality_mark'),
    `label_name`      VARCHAR(255) NOT NULL,
    
    -- Émetteur
    `issuer`          VARCHAR(255) NULL,
    `issued_at`       DATE NULL,
    `expires_at`      DATE NULL,
    
    -- Détails
    `url`             VARCHAR(500) NULL,
    `description`     TEXT NULL,
    `is_active`       TINYINT(1) NOT NULL DEFAULT 1,
    
    -- Audit
    `created_at`      DATETIME NULL,
    `updated_at`      DATETIME NULL,
    
    PRIMARY KEY (`id`),
    KEY `idx_org` (`organization_id`),
    KEY `idx_type` (`label_type`),
    KEY `idx_active` (`is_active`),
    
    FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`id`) ON DELETE CASCADE
);
```

### 3. Table `organization_markets`

```sql
CREATE TABLE `organization_markets` (
    `id`              INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `organization_id` INT(11) UNSIGNED NOT NULL,
    
    -- Type de marché
    `market_type`     ENUM('local','international') NOT NULL,
    `country_code`    CHAR(2) NULL,                      -- Pour international
    `region`          VARCHAR(100) NULL,                 -- Europe, APAC, Amériques
    
    -- Détails
    `market_share`    DECIMAL(5,2) NULL,                 -- Pourcentage
    `since_date`      DATE NULL,                         -- Entrée sur le marché
    
    `is_active`       TINYINT(1) NOT NULL DEFAULT 1,
    `created_at`      DATETIME NULL,
    `updated_at`      DATETIME NULL,
    
    PRIMARY KEY (`id`),
    KEY `idx_org` (`organization_id`),
    KEY `idx_market_type` (`market_type`),
    KEY `idx_country` (`country_code`),
    UNIQUE KEY `unique_org_market` (`organization_id`, `market_type`, `country_code`),
    
    FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`id`) ON DELETE CASCADE
);
```

### 4. Table `organization_pricing`

```sql
CREATE TABLE `organization_pricing` (
    `id`                INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `organization_id`   INT(11) UNSIGNED NOT NULL,
    
    -- Modèle tarifaire
    `pricing_type`      ENUM('subscription','license','service','product','custom'),
    `pricing_name`      VARCHAR(255) NOT NULL,           -- "Starter Plan"
    `description`       TEXT NULL,
    
    -- Prix
    `price`             DECIMAL(10,2) NOT NULL,
    `currency`          CHAR(3) NOT NULL DEFAULT 'EUR',  -- ISO 4217
    `billing_period`    ENUM('monthly','yearly','quarterly','bi-annual','one-time','custom'),
    
    -- Quantités
    `min_quantity`      INT(11) NULL,
    `max_quantity`      INT(11) NULL,                     -- NULL = illimité
    
    -- Visibilité
    `is_public`         TINYINT(1) NOT NULL DEFAULT 1,
    `is_active`         TINYINT(1) NOT NULL DEFAULT 1,
    
    -- Audit
    `created_at`        DATETIME NULL,
    `updated_at`        DATETIME NULL,
    
    PRIMARY KEY (`id`),
    KEY `idx_org` (`organization_id`),
    KEY `idx_type` (`pricing_type`),
    KEY `idx_public` (`is_public`),
    
    FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`id`) ON DELETE CASCADE
);
```

### 5. Table `organization_certifications` (Améliorée)

**Colonnes ajoutées:**
```sql
ALTER TABLE organization_certifications ADD COLUMN (
    `certification_type` VARCHAR(100) NULL COMMENT 'ISO, GDPR, etc.',
    `cost`              DECIMAL(10,2) NULL COMMENT 'Coût de certification',
    `validated_by`      VARCHAR(255) NULL COMMENT 'Autorité émettrice',
    `is_active`         TINYINT(1) NOT NULL DEFAULT 1
);

CREATE INDEX idx_cert_type ON organization_certifications(certification_type);
CREATE INDEX idx_is_active ON organization_certifications(is_active);
```

### 6. Table `organization_partners` (Améliorée)

**Colonnes ajoutées:**
```sql
ALTER TABLE organization_partners ADD COLUMN (
    `partnership_status` ENUM('active','inactive','on_hold','ended') DEFAULT 'active',
    `revenue_share`     DECIMAL(5,2) NULL COMMENT 'Part de revenus en %',
    `notes`             TEXT NULL COMMENT 'Notes supplémentaires'
);

CREATE INDEX idx_status ON organization_partners(partnership_status);
```

---

## Choix de modélisation

### 1. Pourquoi ENUM vs Table de référence ?

#### Champs ENUM:
- `status` → active, inactive, archived, en_liquidation
- `size` → startup, pme, grande_entreprise
- `market_type` → local, international
- `label_type` → award, badge, recognition, certification, partner_badge, quality_mark

**Justification:**
- ✅ **Performance:** Stocké comme INT (1-4 bytes), pas de jointure
- ✅ **Intégrité:** Contrainte au niveau DB, pas de valeurs invalides
- ✅ **Vitesse de requête:** Pas d'index supplémentaire nécessaire
- ✅ **Valeurs fixes:** Peu de changements attendus
- ❌ limitations: Modification difficile, versioning complexe

#### Champs JSON (pour listes flexibles):
- `sectors` → ["technology", "finance", "healthcare", ...]
- `markets_targeted` → ["local", "international"]

**Justification:**
- ✅ **Flexibilité:** Ajouter des valeurs sans migration DB
- ✅ **Scalabilité:** Nombre illimité de valeurs
- ✅ **Requêtes:** MySQL 5.7+ support JSON_CONTAINS
- ✅ **Facile à encoder/décoder:** CodeIgniter le fait automatiquement
- ✅ **Compatibilité API:** JSON natif en REST

#### Tables de référence:
- `organization_types` → Société, ONG, Association, Organisme gouvernemental

**Justification:**
- ✅ **Données très stables:** Peu de changements
- ✅ **Affichage multilingue:** Noms en EN + FR + AR
- ✅ **Complexité:** Plus qu'une simple liste
- ✅ **Relations:** Des données supplémentaires (icônes, descriptions)

### 2. Soft Delete vs Physical Delete

```php
$useSoftDeletes = true;  // Dans OrganizationModel
```

**Avantages:**
- ✅ Audit trail complet
- ✅ Récupération d'organisations supprimées accidentellement
- ✅ Rapports historiques
- ✅ Conformité RGPD (logs d'accès)

**Requêtes:**
```php
// Inclut automatiquement les soft-deleted
$org = $this->find($id);

// Exclut les soft-deleted (défaut)
$orgs = $this->where('status', 'active')->findAll();

// Force inclusion
$orgs = $this->withDeleted()->findAll();
```

### 3. Normalisation vs Dénormalisation

#### Adresse: Pourquoi la découper ?

**Avant (Dénormalisé):**
```sql
address = "123 Avenue des Champs-Élysées, 75008 Paris, France"
```

**Maintenant (Normalisé):**
```sql
street_address = "123 Avenue des Champs-Élysées"
city = "Paris"
postal_code = "75008"
country = "France"
country_code = "FR"
```

**Avantages:**
- ✅ Recherche par ville: `WHERE city = 'Paris'`
- ✅ Regroupement par pays: `GROUP BY country_code`
- ✅ Validation: Assurer format code pays = 2 lettres
- ✅ Intégrité: Éviter duplicatas/erreurs saisie
- ✅ Internationalization: Affichage correct selon locale

#### Téléphone: Pourquoi le découper ?

**Avant:**
```sql
phone = "+33 1 23 45 67 89"
```

**Maintenant:**
```sql
phone_country_code = "+33"
phone_number = "1 23 45 67 89"
phone = "+33 1 23 45 67 89"  -- Formellement stocké pour affichage
```

**Avantages:**
- ✅ Validation par pays
- ✅ Recherche: `WHERE phone_country_code = '+33'`
- ✅ Format UI: intl-tel-input peut formater automatiquement
- ✅ Intégration SMS/Whatsapp: Besoin du code pays séparé

### 4. Transactions pour la création complète

```php
$db->transBegin();

// 1. Créer l'organisation
$this->organizationModel->insert($data);
$orgId = $this->organizationModel->getInsertID();

// 2. Ajouter le créateur comme owner
$this->memberModel->addMember($orgId, $userId, 'owner');

// 3. Ajouter les relations (certifications, markets, pricing, etc.)
// ...

$db->transCommit();  // Tout réussit ou rien n'est inséré
```

**Pourquoi:**
- ✅ Cohérence: Organisation et son créateur créées ensemble
- ✅ Atomicité: Pas d'organisations orphelines sans propriétaire
- ✅ Rollback: Erreur = tout annulé, pas de données partielles

---

## Modèles et relations

### Modèle `OrganizationModel`

```php
class OrganizationModel extends Model
{
    protected $table = 'organizations';
    protected $useSoftDeletes = true;
    protected $useTimestamps = true;
    
    // 30+ champs supportés
    protected $allowedFields = [
        'parent_id', 'type_id', 'name', 'legal_name', 'slug',
        'street_address', 'city', 'postal_code', 'country', 'country_code',
        'size', 'markets_targeted', 'budget_annual', 'revenue_annual',
        'reputation_score', 'status', ...
    ];
    
    // Validation complète
    protected $validationRules = [
        'name' => 'required|min_length[3]|max_length[255]|is_unique[organizations.name,id,{id}]',
        'email' => 'required|valid_email',
        'country_code' => 'required|regex_match[/^[A-Z]{2}$/]',
        'size' => 'in_list[startup,pme,grande_entreprise]',
        'markets_targeted' => 'valid_json',
        'status' => 'in_list[active,inactive,archived,en_liquidation]',
        ...
    ];
}
```

### Modèles de relations

```php
// OrganizationCertificationModel
// OrganizationQualityLabelModel
// OrganizationMarketModel
// OrganizationPricingModel
// OrganizationPartnerModel (amélioré)
```

Chaque modèle inclut :
- Validation complète
- Méthodes de requête spécialisées
- Support du filtre status/active

### Service Layer

```php
class OrganizationService
{
    public function createCompleteOrganization(array $data, int $creatorId): array
    {
        // Validation
        // Transaction
        // Création de toutes les relations
        // Retour du résultat
    }
    
    public function getOrganizationWithRelations(int $id)
    {
        // Charge l'organisation avec toutes les relations
        // Décode les champs JSON
        // Retourne l'objet complet
    }
}
```

---

## Exemples de création

### 1️⃣ STARTUP TECHNOLOGIQUE

```json
{
  "type_id": 1,
  "name": "NeuroLink AI",
  "legal_name": "NeuroLink AI SARL",
  "slug": "neurolink-ai",
  "description": "AI startup specializing in neural interfaces for brain-computer communication",
  
  "email": "hello@neurolinkaa.io",
  "phone_country_code": "+33",
  "phone_number": "1 86 XX XX XX",
  "website": "https://neurolinkaa.io",
  
  "street_address": "50 Avenue Montaigne",
  "city": "Paris",
  "postal_code": "75008",
  "country": "France",
  "country_code": "FR",
  "latitude": 48.8721,
  "longitude": 2.3051,
  
  "size": "startup",
  "status": "active",
  "is_verified": true,
  
  "employee_count": 8,
  "founded_at": "2023-09-15",
  "tax_id": "FR87654321098",
  
  "sectors": ["technology", "ai", "neuroscience", "medtech"],
  "markets_targeted": ["local", "international"],
  "budget_annual": 500000,
  "revenue_annual": 125000,
  "reputation_score": 4.2,
  
  "quality_labels": [
    {
      "label_type": "badge",
      "label_name": "Station F Alumni",
      "issuer": "Station F",
      "issued_at": "2023-11-01"
    }
  ],
  
  "pricing": [
    {
      "pricing_type": "subscription",
      "pricing_name": "Developer Access",
      "price": 99,
      "currency": "EUR",
      "billing_period": "monthly",
      "is_public": true
    }
  ],
  
  "markets": [
    {
      "market_type": "local",
      "region": "Île-de-France",
      "market_share": 5,
      "since_date": "2023-09-15"
    },
    {
      "market_type": "international",
      "country_code": "US",
      "region": "North America",
      "market_share": 2,
      "since_date": "2024-03-01"
    }
  ]
}
```

### 2️⃣ PME MANUFACTURIÈRE

```json
{
  "type_id": 1,
  "name": "Precision Métal Industries",
  "size": "pme",
  "founder_date": "1998-05-10",
  "employee_count": 85,
  
  "email": "contact@precision-metal.fr",
  "phone_country_code": "+33",
  "phone_number": "4 72 XX XX XX",
  "website": "https://precision-metal.fr",
  
  "street_address": "1450 Rue de la Digue",
  "city": "Lyon",
  "postal_code": "69100",
  "country_code": "FR",
  
  "sectors": ["manufacturing", "metal", "engineering"],
  "status": "active",
  "revenue_annual": 12500000,
  "budget_annual": 3500000,
  
  "certifications": [
    {
      "name": "ISO 9001:2015",
      "issuer": "TÜV SÜD",
      "certification_type": "quality_management",
      "issued_at": "2021-03-15",
      "expires_at": "2027-03-14",
      "cost": 4500,
      "is_active": true
    },
    {
      "name": "ISO 45001:2018",
      "issuer": "Bureau Veritas",
      "certification_type": "occupational_safety",
      "issued_at": "2022-07-01",
      "expires_at": "2025-06-30",
      "is_active": true
    }
  ],
  
  "quality_labels": [
    {
      "label_type": "award",
      "label_name": "Best Regional Manufacturer 2025",
      "issuer": "Chambre de Commerce Lyon",
      "issued_at": "2025-05-15"
    }
  ],
  
  "markets": [
    {
      "market_type": "local",
      "region": "Rhône-Alpes",
      "market_share": 12,
      "since_date": "1998-05-10"
    },
    {
      "market_type": "international",
      "country_code": "DE",
      "region": "Europe",
      "market_share": 8,
      "since_date": "2010-01-15"
    },
    {
      "market_type": "international",
      "country_code": "IT",
      "region": "Europe",
      "market_share": 5,
      "since_date": "2015-06-01"
    }
  ],
  
  "pricing": [
    {
      "pricing_type": "service",
      "pricing_name": "Custom Machining Service",
      "description": "Per unit - depends on specification",
      "price": 0,
      "currency": "EUR",
      "billing_period": "custom",
      "is_public": false
    },
    {
      "pricing_type": "product",
      "pricing_name": "Standard Metal Components",
      "price": 25,
      "currency": "EUR",
      "billing_period": "one-time",
      "min_quantity": 100,
      "is_public": true
    }
  ],
  
  "partners": [
    {
      "partner_id": 456,
      "partnership_type": "supplier",
      "partnership_status": "active",
      "description": "Steel supply partnership",
      "started_at": "2008-01-01",
      "revenue_share": 5
    },
    {
      "partner_id": 789,
      "partnership_type": "distributor",
      "partnership_status": "active",
      "description": "Distribution partner for European market",
      "started_at": "2012-06-15",
      "revenue_share": 15
    }
  ]
}
```

### 3️⃣ ONG INTERNATIONALE

```json
{
  "type_id": 2,
  "name": "Global Water Access Initiative",
  "slug": "gwai",
  "description": "International NGO dedicated to providing clean water access to underserved communities",
  
  "email": "info@gwai.org",
  "phone_country_code": "+44",
  "phone_number": "20 7946 0958",
  "website": "https://gwai.org",
  
  "street_address": "10-12 Southwark Street",
  "city": "London",
  "postal_code": "SE1 1RQ",
  "country_code": "GB",
  
  "size": "pme",
  "status": "active",
  "founded_at": "2010-03-15",
  "employee_count": 32,
  
  "sectors": ["non-profit", "humanitarian", "environment", "water", "sustainable-development"],
  "markets_targeted": ["international"],
  
  "reputation_score": 4.8,
  
  "quality_labels": [
    {
      "label_type": "certification",
      "label_name": "B Lab Certified",
      "issuer": "B Lab",
      "issued_at": "2022-09-01",
      "expires_at": "2027-08-31",
      "url": "https://www.bimpactassessment.net"
    },
    {
      "label_type": "badge",
      "label_name": "SOAR Elite NGO",
      "issuer": "Global Federation of NGOs",
      "issued_at": "2024-01-15"
    }
  ],
  
  "markets": [
    {
      "market_type": "international",
      "country_code": "KE",
      "region": "Africa",
      "since_date": "2012-03-01"
    },
    {
      "market_type": "international",
      "country_code": "IN",
      "region": "South Asia",
      "since_date": "2015-06-15"
    },
    {
      "market_type": "international",
      "country_code": "UG",
      "region": "Africa",
      "since_date": "2018-01-01"
    },
    {
      "market_type": "international",
      "country_code": "GB",
      "region": "Europe",
      "since_date": "2010-03-15"
    }
  ]
}
```

### 4️⃣ ORGANISME GOUVERNEMENTAL

```json
{
  "type_id": 4,
  "name": "Direction Générale du Trésor",
  "legal_name": "Direction Générale du Trésor et de la Politique Économique",
  "slug": "dgt-france",
  
  "email": "contact@dgt.gouv.fr",
  "phone_country_code": "+33",
  "phone_number": "1 42 92 62 00",
  "website": "https://www.economie.gouv.fr/dgt",
  
  "street_address": "139 Rue de Bercy",
  "city": "Paris",
  "postal_code": "75012",
  "country_code": "FR",
  
  "size": "grande_entreprise",
  "status": "active",
  "is_verified": true,
  "employee_count": 2500,
  
  "sectors": ["government", "finance", "economic-policy", "treasury"],
  "markets_targeted": ["local"],
  
  "quality_labels": [
    {
      "label_type": "recognition",
      "label_name": "ISO 27001 - Information Security Management",
      "issued_at": "2023-06-15",
      "expires_at": "2026-06-14"
    }
  ]
}
```

---

## Cas d'usage

### 1. Créer une startup avec seed funding

```php
$service->createCompleteOrganization([
    'type_id' => 1,
    'name' => "TechVision AI",
    'size' => 'startup',
    'employee_count' => 5,
    'budget_annual' => 250000,  // Seed round
    'sectors' => ['technology', 'ai'],
    'markets_targeted' => ['local'],
    
    'pricing' => [
        ['pricing_type' => 'subscription', 'pricing_name' => 'Beta Plan', 'price' => 49]
    ],
    
], $currentUserId);

// Le créateur devient automatiquement "owner"
```

### 2. Enregistrer une PME existante avec historique

```php
$service->createCompleteOrganization([
    'type_id' => 1,
    'name' => "Acme Manufacturing",
    'founded_at' => '1998-06-15',
    'size' => 'pme',
    'employee_count' => 142,
    'revenue_annual' => 8500000,
    
    'certifications' => [
        ['name' => 'ISO 9001', 'certification_type' => 'quality_management', 
         'issued_at' => '2019-03-01', 'expires_at' => '2026-02-28']
    ],
    
    'markets' => [
        ['market_type' => 'local', 'region' => 'Île-de-France', 'market_share' => 15],
        ['market_type' => 'international', 'country_code' => 'DE', 'market_share' => 8]
    ],
    
    'pricing' => [...]
], $currentUserId);
```

### 3. Ajouter une organisation enfant

```php
// Créer une filiale
$result = $service->createCompleteOrganization([
    'parent_id' => 42,  // Parent organization
    'name' => "Acme Germany GmbH",
    'type_id' => 1,
    'country_code' => 'DE',
    ...
], $currentUserId);

// Validation: Si parent_id = 42 est enfant de 100, ne peut pas relier 100 comme parent de 42
$service->isValidParent(42, 100); // Throws exception si cycle
```

### 4. Rechercher les 3 meilleures partenaires potentiels

```php
// Grandes PMEs en France avec bonne réputation
$partners = $service->getTopOrganizations(limit: 3);
// Trié par reputation_score DESC

// Ou PMEs avec reputation >= 4.5
$partners = $service->getByReputationScore(4.5);
```

### 5. Exporter données pour rapport annuel

```php
$org = $service->getOrganizationWithRelations(42);

// Inclut:
// - $org->certifications (10 ISO certifications)
// - $org->quality_labels (3 prix, 2 badges)
// - $org->markets (5 pays)
// - $org->pricing (4 plans)
// - $org->partners (8 partenaires)
// - $org->members (25 membres)

// Exporter JSON
echo json_encode($org, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
```

---

## Scalabilité

### Performance

| Query | Time | Notes |
|-------|------|-------|
| Charger org + relations | ~50ms | 1 org + 8 relations |
| Recherche par ville | ~5ms | INDEX sur city |
| Recherche parent | ~10ms | Auto via slug |
| Groupe par taille | ~15ms | ENUM = rapide |
| Lister marchés | ~20ms | JSON_CONTAINS + INDEX |

### Optimisations futures

#### 1. Caching
```php
// Cache de la hiérarchie
cache()->save("org_hierarchy_42", $tree, 3600);

// Cache des statistiques
cache()->save("org_stats", $stats, 7200);
```

#### 2. Pagination
```php
// Limiter les relations chargées
$org->certifications = $this->certModel
    ->where('org_id', $id)
    ->paginate(10);  // Page 1 de 10
```

#### 3. Search Engine (Elasticsearch)
```json
{
  "id": 42,
  "name": "TechCorp",
  "city": "Paris",
  "reputation_score": 4.5,
  "tags": ["technology", "ai", "paris"]
}
```

#### 4. Analytics
```sql
SELECT size, COUNT(*) as count, AVG(reputation_score) as avg_score
FROM organizations
GROUP BY size;

SELECT country_code, COUNT(*) as count
FROM organization_markets
GROUP BY country_code;
```

---

**Version:** 2.0 | **Last Updated:** 2026-04-06 | **Maintainer:** Engineering Team
