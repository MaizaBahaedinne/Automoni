# Module Organisations - Documentation Complète

## 📋 Table des matières
1. [Architecture](#architecture)
2. [Structure Base de Données](#structure-bdd)
3. [Installation](#installation)
4. [API Endpoints](#api-endpoints)
5. [Exemples JSON](#exemples-json)
6. [Gestion des Hiérarchies](#gestion-hiérarchies)
7. [Bonnes Pratiques](#bonnes-pratiques)
8. [Limitations et Améliorations](#limitations)

---

## Architecture

### Vue d'ensemble
Le module Organisations suit l'architecture MVC de CodeIgniter 4 avec une séparation claire des responsabilités :

```
app/
├── Controllers/
│   ├── OrganizationController.php      (CRUD principal)
│   └── OrganizationMemberController.php (Gestion des membres)
├── Models/
│   ├── OrganizationModel.php           (Entité principale)
│   ├── OrganizationTypeModel.php       (Types d'org)
│   ├── OrganizationMemberModel.php     (Membres & permissions)
│   ├── OrganizationSocialLinkModel.php (Réseaux sociaux)
│   ├── OrganizationCertificationModel.php (Certifications)
│   └── OrganizationPartnerModel.php    (Partenaires)
├── Services/
│   └── OrganizationService.php         (Logique métier complexe)
├── Database/
│   ├── Migrations/
│   │   ├── 2024-01-16-000001_CreateOrganizationTypesTable.php
│   │   ├── 2024-01-16-000002_CreateOrganizationsTable.php
│   │   ├── 2024-01-16-000003_CreateOrganizationMembersTable.php
│   │   ├── 2024-01-16-000004_CreateOrganizationSocialLinksTable.php
│   │   ├── 2024-01-16-000005_CreateOrganizationCertificationsTable.php
│   │   └── 2024-01-16-000006_CreateOrganizationPartnersTable.php
│   └── Seeds/
│       ├── OrganizationTypeSeeder.php
│       └── OrganizationSeeder.php
└── Views/
    └── organizations/
        ├── index.php       (Listing avec filtrage)
        ├── show.php        (Détails + hiérarchie)
        └── form.php        (Création/Édition)
```

### Flux de données
```
Request → Route → Controller → Model + Service → DB → Response
```

---

## Structure Base de Données

### Schéma relationnel

```sql
-- Types d'organisations (enum)
organization_types
├── id (PK)
├── name (VARCHAR)
├── slug (UNIQUE)
└── description (TEXT)

-- Organisations principales
organizations
├── id (PK)
├── type_id (FK → organization_types)
├── parent_id (FK → organizations) ← Hiérarchie
├── name
├── slug
├── description
├── logo
├── website
├── phone / email
├── address / GPS coordinates
├── employee_count
├── industry
├── founded_at
├── status (active/inactive/archived)
├── is_verified
├── timestamps + soft_delete
└── index: (type_id, parent_id, slug, status)

-- Membres et permissions (N:N avec rôles)
organization_members
├── id (PK)
├── organization_id (FK)
├── user_id (FK)
├── role (enum: owner/manager/viewer)
├── joined_at
└── timestamps
└── unique: (organization_id, user_id)

-- Réseaux sociaux
organization_social_links
├── id (PK)
├── organization_id (FK)
├── platform (facebook/twitter/linkedin/etc)
├── url
└── timestamps

-- Certifications
organization_certifications
├── id (PK)
├── organization_id (FK)
├── name
├── issuer
├── issued_at / expires_at
├── url
└── timestamps

-- Partenariats (N:M)
organization_partners
├── id (PK)
├── organization_id (FK)
├── partner_id (FK)
├── partnership_type
├── description
├── started_at / ended_at
├── is_active
└── timestamps
└── unique: (organization_id, partner_id)
```

### Relations
- **Parent-Enfant** : Self-join sur `parent_id` → Hiérarchie
- **Utilisateurs** : N:N via `organization_members` avec rôles
- **Partenaires** : N:M sym étrique
- **Social/Certif** : 1:N

---

## Installation

### 1. Exécuter les migrations
```bash
php spark migrate
```

### 2. Seeder les données
```bash
php spark db:seed OrganizationTypeSeeder
php spark db:seed OrganizationSeeder
```

### 3. Vérifier les uploads
```bash
mkdir -p writable/uploads/organizations
chmod 755 writable/uploads/organizations
```

### 4. Configuration (optionnelle)
Ajouter au `.env` si nécessaire :
```env
ORGANIZATION_LOGO_MAX_SIZE=5242880  # 5MB
ORGANIZATION_UPLOAD_PATH=uploads/organizations/
```

---

## API Endpoints

### Organisations (Public)

#### `GET /organizations`
**Lister organisations avec filtrage**

Paramètres query :
- `keyword` - Recherche en texte libre
- `type_id` - ID du type
- `industry` - Secteur d'activité
- `is_verified` - Vérifiées uniquement
- `per_page` - Pagination (défaut: 15)
- `page` - Numéro de page

**Réponse (JSON) :**
```json
{
  "status": "success",
  "data": [
    {
      "id": 1,
      "type_id": 1,
      "name": "Global Tech Solutions",
      "slug": "global-tech-solutions-xyz",
      "description": "...",
      "website": "https://...",
      "email": "contact@...",
      "phone": "+1-555-0100",
      "address": "123 Tech Blvd, SF",
      "latitude": 37.7749,
      "longitude": -122.4194,
      "industry": "IT",
      "employee_count": 5000,
      "founded_at": "2010-03-15",
      "status": "active",
      "is_verified": true,
      "logo_url": "/uploads/organizations/org_1_1234567890.png",
      "created_at": "2024-01-16T10:00:00Z",
      "updated_at": "2024-01-16T10:00:00Z"
    }
  ],
  "pagination": {
    "total": 50,
    "per_page": 15,
    "current_page": 1,
    "total_pages": 4
  }
}
```

---

#### `GET /organizations/:id`
**Récupérer détails d'une organisation**

**Réponse :**
```json
{
  "status": "success",
  "data": {
    "id": 1,
    "type_name": "Company",
    "name": "Global Tech Solutions",
    "...": "...",
    "logo_url": "/uploads/organizations/org_1_xxx.png",
    "stats": {
      "members_count": 12,
      "subsidiaries_count": 2,
      "descendants_count": 2,
      "certifications_count": 3,
      "partners_count": 5,
      "social_links_count": 4
    },
    "social_links": [
      {
        "id": 1,
        "platform": "linkedin",
        "url": "https://linkedin.com/company/..."
      }
    ],
    "certifications": [
      {
        "id": 1,
        "name": "ISO 27001",
        "issuer": "TÜV Süd",
        "issued_at": "2022-01-15",
        "expires_at": "2025-01-15"
      }
    ]
  }
}
```

---

#### `GET /organizations/:id/hierarchy`
**Récupérer l'arborescence complète**

**Réponse :**
```json
{
  "status": "success",
  "data": [
    {
      "level": 0,
      "node": {
        "id": 1,
        "name": "Global Tech Solutions",
        "slug": "global-tech-solutions",
        "logo_url": "/uploads/..."
      }
    },
    {
      "level": 1,
      "node": {
        "id": 2,
        "name": "Global Tech - Europe",
        "parent_id": 1
      }
    },
    {
      "level": 1,
      "node": {
        "id": 3,
        "name": "Global Tech - APAC",
        "parent_id": 1
      }
    }
  ]
}
```

---

### Organisations (Protégées - Authentifiées)

#### `GET /organizations/create`
Affiche le formulaire de création

#### `POST /organizations`
**Créer une nouvelle organisation**

**Payload :**
```json
{
  "type_id": 1,
  "name": "New Organization",
  "description": "Description...",
  "parent_id": null,
  "website": "https://example.com",
  "email": "contact@example.com",
  "phone": "+1-555-0100",
  "address": "123 Main St, City, Country",
  "latitude": 37.7749,
  "longitude": -122.4194,
  "industry": "Technology",
  "employee_count": 100,
  "founded_at": "2020-01-15"
}
```

**Upload avec formulaire multipart :**
```
POST /organizations
Content-Type: multipart/form-data

type_id=1
name=New Organization
logo=<file>
social_platform_0=linkedin
social_url_0=https://linkedin.com/company/...
social_platform_1=twitter
social_url_1=https://twitter.com/...
```

**Réponse (201 Created) :**
```json
{
  "status": "success",
  "message": "Organization created successfully",
  "data": {
    "id": 123
  }
}
```

---

#### `GET /organizations/:id/edit`
Affiche le formulaire d'édition

#### `PUT /organizations/:id` ou `POST /organizations/:id/update`
**Mettre à jour l'organisation**

Requête similaire à `POST /organizations` (sauf ID auto)

**Réponse :**
```json
{
  "status": "success",
  "message": "Organization updated successfully"
}
```

---

#### `DELETE /organizations/:id`
**Supprimer l'organisation (soft delete)**

**Réponse :**
```json
{
  "status": "success",
  "message": "Organization deleted"
}
```

---

### Gestion des Membres

#### `GET /organizations/:id/members`
**Lister les membres**

**Réponse :**
```json
{
  "status": "success",
  "data": [
    {
      "id": 1,
      "organization_id": 1,
      "user_id": 5,
      "role": "owner",
      "joined_at": "2024-01-01T10:00:00Z",
      "first_name": "John",
      "last_name": "Doe",
      "email": "john@example.com",
      "avatar": "/uploads/avatars/john.jpg"
    },
    {
      "id": 2,
      "organization_id": 1,
      "user_id": 6,
      "role": "manager",
      "joined_at": "2024-01-10T15:30:00Z",
      "first_name": "Jane",
      "last_name": "Smith",
      "email": "jane@example.com",
      "avatar": null
    }
  ]
}
```

---

#### `POST /organizations/:id/members`
**Ajouter un membre** (Owner only)

**Payload :**
```json
{
  "user_id": 10,
  "role": "manager"
}
```

**Réponse (201 Created) :**
```json
{
  "status": "success",
  "message": "Member added successfully"
}
```

---

#### `POST /organizations/:id/members/:userId/role`
**Changer le rôle d'un membre** (Owner only)

**Payload :**
```json
{
  "role": "viewer"
}
```

**Réponse :**
```json
{
  "status": "success",
  "message": "Member role updated"
}
```

---

#### `DELETE /organizations/:id/members/:userId`
**Supprimer un membre** (Owner only)

**Réponse :**
```json
{
  "status": "success",
  "message": "Member removed"
}
```

---

## Exemples JSON

### Exemple 1 : Créer une organisation parent

```bash
curl -X POST http://localhost:8080/organizations \
  -H "Content-Type: application/json" \
  -d '{
    "type_id": 1,
    "name": "Tech Innovations Global",
    "description": "Global technology innovation company",
    "website": "https://techinnovations.com",
    "email": "contact@techinnovations.com",
    "phone": "+33-1-42-86-82-00",
    "address": "42 Avenue des Champs-Élysées, 75008 Paris, France",
    "latitude": 48.8698,
    "longitude": 2.3076,
    "industry": "Information Technology",
    "employee_count": 3500,
    "founded_at": "2008-05-20"
  }'
```

### Exemple 2 : Créer une filiale

```bash
curl -X POST http://localhost:8080/organizations \
  -H "Content-Type: application/json" \
  -d '{
    "type_id": 1,
    "name": "Tech Innovations - Maroc",
    "description": "Moroccan subsidiary of Tech Innovations Global",
    "parent_id": 1,
    "website": "https://techinnovations.ma",
    "email": "contact@techinnovations.ma",
    "phone": "+212-5-37-77-88-99",
    "address": "Technopark, Casablanca, Morocco",
    "latitude": 33.5731,
    "longitude": -7.5898,
    "industry": "Information Technology",
    "employee_count": 250,
    "founded_at": "2015-03-10"
  }'
```

### Exemple 3 : Récupérer l'hiérarchie

```bash
curl -X GET "http://localhost:8080/organizations/1/hierarchy" \
  -H "Accept: application/json"
```

### Exemple 4 : Upload de logo

```bash
curl -X POST http://localhost:8080/organizations \
  -F "type_id=1" \
  -F "name=Company Name" \
  -F "logo=@/path/to/logo.png" \
  -F "social_platform_0=linkedin" \
  -F "social_url_0=https://linkedin.com/company/mycompany"
```

### Exemple 5 : Ajouter un membre

```bash
curl -X POST http://localhost:8080/organizations/1/members \
  -H "Content-Type: application/json" \
  -d '{
    "user_id": 5,
    "role": "manager"
  }'
```

### Exemple 6 : Chercher organisations

```bash
curl "http://localhost:8080/organizations?keyword=tech&industry=IT&per_page=10&page=1" \
  -H "Accept: application/json"
```

---

## Gestion des Hiérarchies

### Concepts clés

#### 1. Relation Parent-Enfant
```
Global Tech Solutions (ID: 1)
├── Global Tech - Europe (ID: 2, parent_id: 1)
│   └── Global Tech - France (ID: 4, parent_id: 2)
└── Global Tech - APAC (ID: 3, parent_id: 1)
```

#### 2. Méthodes disponibles dans OrganizationService

```php
// Récupérer l'arborescence JSON
$hierarchy = $this->organizationService->getHierarchyTree($organizationId);

// Récupérer les breadcrumbs
$breadcrumbs = $this->organizationService->getBreadcrumbs($organizationId);

// Récupérer tous les descendants
$allChildren = $this->organizationService->getAllDescendants($organizationId);

// Déplacer une organisation (changer le parent)
$this->organizationService->moveToParent($orgId, $newParentId);

// Obtenir la profondeur de l'arborescence
$depth = $this->organizationService->getTreeDepth($organizationId);
```

#### 3. Prévention des cycles
Le service empêche les mouvements qui créeraient des cycles (ex: déplacer une org vers ses enfants)

#### 4. Exemple d'utilisation

```php
// Dans un controller
public function moveSubsidiary()
{
    $newParentId = $this->request->getPost('new_parent_id');
    
    try {
        $this->organizationService->moveToParent($orgId, $newParentId);
        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Organization moved successfully'
        ]);
    } catch (\Exception $e) {
        return $this->response->setStatusCode(400)->setJSON([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }
}
```

---

## Bonnes Pratiques

### 1. Sécurité

#### Upload de fichiers
```php
// Service gère les validations
- Vérification du type MIME
- Limite de taille (5MB)
- Suppression de l'ancien fichier
- Sauvegarde avec nom unique (org_ID_timestamp.ext)
```

#### Permissions
```php
// Rôles et permissions
- Owner: Tous les droits (CRUD, gestion membres)
- Manager: Édition organisation, pas gestion membres
- Viewer: Lecture seule

// Vérification
if (!$service->canEdit($orgId, $userId)) {
    return error('Unauthorized');
}
```

#### Validation des données
```php
// Models possèdent des règles de validation intégrées
$validationRules = [
    'name' => 'required|min_length[3]|max_length[255]|is_unique[...]',
    'website' => 'valid_url_strict',
    'email' => 'valid_email',
    'latitude' => 'numeric|greater_than_equal_to[-90]|less_than_equal_to[90]',
];
```

---

### 2. Performance

#### Index base de données
```sql
-- Clés primaires et étrangères
CREATE INDEX idx_org_type ON organizations(type_id);
CREATE INDEX idx_org_parent ON organizations(parent_id);
CREATE INDEX idx_org_slug ON organizations(slug);
CREATE INDEX idx_org_status ON organizations(status);

-- Recherche et filtrage
CREATE INDEX idx_members_org_user ON organization_members(organization_id, user_id);
CREATE INDEX idx_partners_active ON organization_partners(organization_id, is_active);
```

#### Requêtes optimisées
```php
// Join avec types d'orgs (already done in getWithType)
$this->organizationModel->select('o.*, ot.name as type_name')
    ->from('organizations as o')
    ->join('organization_types as ot', 'ot.id = o.type_id');

// Pagination pour les listes
$this->search($filters, $perPage);
```

#### Cache (bonus)
```php
// À implémenter pour les hiérarchies fréquentes
$cache = cache();
$hierarchy = $cache->get("org_hierarchy_{$id}");
if (!$hierarchy) {
    $hierarchy = $this->getHierarchyTree($id);
    $cache->save("org_hierarchy_{$id}", $hierarchy, 3600);
}
```

---

### 3. Scalabilité

#### Structure pour croissance
- ✅ Self-join pour hiérarchies illimitées
- ✅ N:M pour partenaires symétriques
- ✅ Soft deletes pour audit
- ✅ Timestamps pour tracking

#### À considérer
- Denormalization si hiérarchies très profonds
- Materialized paths pour recherches optimisées
- Event sourcing pour audit complet

---

## Limitations et Améliorations

### Limitations actuelles

1. **Hiérarchie simple**
   - Max ~50 niveaux (limitation PHP récursion)
   - Solution: Implémenter un système de "paths" denormalisé

2. **Upload fichiers**
   - Stockage local uniquement
   - Solution: Adapter pour S3, Google Cloud Storage

3. **Pas de versioning**
   - Pas d'historique des modifications
   - Solution: Ajouter une table `organization_history`

4. **Pas de notifications**
   - Pas d'événements pour les changements
   - Solution: Utiliser Events/Listeners CI4

5. **Pas d'API rate limiting**
   - Pas de throttling
   - Solution: Implémenter middleware

---

### Améliorations suggérées

#### 1. Ajouter Events/Listeners
```php
// app/Events/OrganizationCreated.php
class OrganizationCreated {
    public function __construct(public Organization $org) {}
}

// app/Listeners/SendWelcomeEmail.php
// app/Listeners/LogOrganizationCreation.php
```

#### 2. Historique d'audit
```php
// Nueva tabla
CREATE TABLE organization_audit_logs (
    id INT PRIMARY KEY,
    organization_id INT,
    user_id INT,
    action ENUM('create', 'update', 'delete'),
    changes JSON,
    created_at DATETIME
);
```

#### 3. Support S3 pour logos
```php
// app/Services/FileStorageService.php
public function uploadToS3(int $orgId, $file) { ... }
```

#### 4. Recherche avancée avec Elasticsearch
```php
// Pour millions d'organisations, ajouter search engine
```

#### 5. API GraphQL
```graphql
query {
  organization(id: 1) {
    name
    subsidiaries {
      id
      name
    }
    members {
      user { name email }
      role
    }
  }
}
```

---

## Configuration du Service

### Enregistrer le Service (optionnel)

Ajouter à `app/Config/Services.php` :

```php
public static function organizationService($getShared = true)
{
    if ($getShared) {
        return static::getSharedInstance('organizationservice');
    }

    return new \App\Services\OrganizationService();
}
```

Utilisation :
```php
$service = service('organizationservice');
// ou
$service = new OrganizationService();
```

---

## Dépannage

### Erreur : "Cannot move organization to its own child"
- Vous essayez de déplacer une org vers un de ses descendants
- Solution: Choisir un autre parent

### Logo n'apparaît pas
- Vérifier permissions du dossier `/writable/uploads/organizations/`
- Vérifier le fichier existe à `$filename`
- Vérifier image valide (JPEG/PNG/WebP/SVG)

### Hiérarchie vide
- Vérifier `parent_id IS NULL` pour les parents
- Vérifier `status = 'active'` pour visibilité

### Erreur de permissions
- Vérifier le rôle de l'utilisateur: `organization_members.role`
- Owner > Manager > Viewer

---

## Support et Contributes

Pour des questions ou améliorations, consultez :
- READme projet
- Issues GitHub
- Documentation CodeIgniter 4: https://codeigniter.com/user_guide/

---

**Version:** 1.0.0  
**Dernière mise à jour:** 2024-01-16  
**Auteur:** Senior Developer (You)
