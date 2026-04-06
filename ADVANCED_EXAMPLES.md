# Exemples d'Utilisation Avancée - Module Organisations

## Exemples d'Utilisation dans des Controllers

### Exemple 1: Utiliser le Service dans un Controller personnalisé

```php
<?php
namespace App\Controllers;

use App\Services\OrganizationService;
use App\Models\OrganizationModel;

class CustomReportController extends BaseController
{
    private OrganizationService $orgService;
    private OrganizationModel $orgModel;

    public function __construct()
    {
        $this->orgService = service('OrganizationService');
        $this->orgModel = model(OrganizationModel::class);
    }

    /**
     * Générer un rapport d'organisations avec hiérarchie
     */
    public function organizationHierarchyReport()
    {
        // Récupérer l'arborescence complète d'une org
        $hierarchy = $this->orgService->getHierarchyTree(1);

        // Obtenir les statistics
        $stats = $this->orgService->getStats(1);

        // Breadcrumbs pour navigation
        $breadcrumbs = $this->orgService->getBreadcrumbs(1);

        return $this->response->setJSON([
            'status' => 'success',
            'hierarchy' => $hierarchy,
            'statistics' => $stats,
            'breadcrumbs' => $breadcrumbs,
        ]);
    }

    /**
     * Récupérer tous les descendants d'une org
     */
    public function getOrgDescendants(int $orgId)
    {
        $descendants = $this->orgService->getAllDescendants($orgId);
        
        return $this->response->setJSON([
            'status' => 'success',
            'count' => count($descendants),
            'data' => $descendants,
        ]);
    }

    /**
     * Obtenir la profondeur hiérarchique
     */
    public function getOrgDepth(int $orgId)
    {
        $depth = $this->orgService->getTreeDepth($orgId);
        
        return $this->response->setJSON([
            'org_id' => $orgId,
            'hierarchy_depth' => $depth,
        ]);
    }
}
?>
```

---

## Exemples API cURL

### 1. Créer une hiérarchie d'organisations

```bash
# Créer parent
PARENT_ID=$(curl -s -X POST http://localhost:8080/organizations \
  -H "Content-Type: application/json" \
  -d '{
    "type_id": 1,
    "name": "Global Corporation",
    "website": "https://global.example.com",
    "email": "contact@global.example.com",
    "industry": "Technology",
    "employee_count": 10000,
    "founded_at": "2000-01-15"
  }' | jq -r '.data.id')

echo "Parent created: $PARENT_ID"

# Créer filiale 1
FILIALE_1=$(curl -s -X POST http://localhost:8080/organizations \
  -H "Content-Type: application/json" \
  -d "{
    \"type_id\": 1,
    \"name\": \"Global - France\",
    \"parent_id\": $PARENT_ID,
    \"industry\": \"Technology\",
    \"employee_count\": 500
  }" | jq -r '.data.id')

echo "Filiale 1 created: $FILIALE_1"

# Créer filiale 2
FILIALE_2=$(curl -s -X POST http://localhost:8080/organizations \
  -H "Content-Type: application/json" \
  -d "{
    \"type_id\": 1,
    \"name\": \"Global - Germany\",
    \"parent_id\": $PARENT_ID,
    \"industry\": \"Technology\",
    \"employee_count\": 800
  }" | jq -r '.data.id')

echo "Filiale 2 created: $FILIALE_2"

# Récupérer hiérarchie complète
echo "Full hierarchy:"
curl -s "http://localhost:8080/organizations/$PARENT_ID/hierarchy" | jq .
```

---

### 2. Upload de logo avec validation

```bash
# Créer organisation avec logo
curl -X POST http://localhost:8080/organizations \
  -F "type_id=1" \
  -F "name=Company with Logo" \
  -F "industry=IT" \
  -F "logo=@/path/to/logo.png" \
  -F "social_platform_0=linkedin" \
  -F "social_url_0=https://linkedin.com/company/..." \
  | jq .

# Récupérer l'organisation (voir le logo_url)
curl "http://localhost:8080/organizations/1" | jq '.data | {name, logo_url}'
```

**Réponse:**
```json
{
  "data": {
    "name": "Company with Logo",
    "logo_url": "/uploads/organizations/org_1_1705416000.png"
  }
}
```

---

### 3. Gestion des membres avec rôles

```bash
# Ajouter un member comme owner
curl -X POST "http://localhost:8080/organizations/1/members" \
  -H "Content-Type: application/json" \
  -d '{
    "user_id": 5,
    "role": "owner"
  }' | jq .

# Ajouter un member comme manager
curl -X POST "http://localhost:8080/organizations/1/members" \
  -H "Content-Type: application/json" \
  -d '{
    "user_id": 6,
    "role": "manager"
  }' | jq .

# Ajouter un member comme viewer
curl -X POST "http://localhost:8080/organizations/1/members" \
  -H "Content-Type: application/json" \
  -d '{
    "user_id": 7,
    "role": "viewer"
  }' | jq .

# Lister tous les members
curl "http://localhost:8080/organizations/1/members" | jq .

# Changer le rôle d'un member (user 6 → owner)
curl -X POST "http://localhost:8080/organizations/1/members/6/role" \
  -H "Content-Type: application/json" \
  -d '{"role": "owner"}' | jq .

# Supprimer un member
curl -X DELETE "http://localhost:8080/organizations/1/members/7" | jq .
```

---

### 4. Recherche et filtrage avancé

```bash
# Recherche par keyword
curl "http://localhost:8080/organizations?keyword=tech&per_page=10" | jq '.data[] | {id, name, industry}'

# Filtrer par type
curl "http://localhost:8080/organizations?type_id=1&per_page=5" | jq .

# Filtrer par industrie
curl "http://localhost:8080/organizations?industry=Technology&per_page=5" | jq .

# Organisations vérifiées seulement
curl "http://localhost:8080/organizations?is_verified=1" | jq .

# Combinaisons
curl "http://localhost:8080/organizations?keyword=tech&industry=IT&is_verified=1&per_page=10&page=1" | jq .

# Dans le code PHP avec API JSON:
$filters = [
    'keyword' => 'tech',
    'type_id' => 1,
    'industry' => 'Technology',
    'is_verified' => true,
];
$result = $this->organizationModel->search($filters, 15);
```

---

## Exemples dans les Vues

### Afficher la hiérarchie dans un menu

```php
<?php
// Dans une vue
$service = service('OrganizationService');
$hierarchy = $service->getHierarchyTree($organizationId);

function renderHierarchy($items, $level = 0) {
    $indent = str_repeat('&nbsp;&nbsp;', $level);
    foreach ($items as $item) {
        $node = $item['node'];
        $nextLevel = $item['level'];
        echo $indent . "└─ <a href='/organizations/{$node->id}'>{$node->name}</a><br>";
    }
}
renderHierarchy($hierarchy);
?>
```

**Résultat:**
```
└─ Global Tech Solutions
   └─ Global Tech - Europe
   └─ Global Tech - APAC
```

---

### Breadcrumbs Navigation

```php
<?php
$service = service('OrganizationService');
$breadcrumbs = $service->getBreadcrumbs($organizationId);
?>

<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <?php foreach ($breadcrumbs as $crumb): ?>
            <li class="breadcrumb-item">
                <a href="/organizations/<?= $crumb->id ?>">
                    <?= esc($crumb->name) ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ol>
</nav>
```

---

## Exemples de Logique Métier

### Validation de mouvement hiérarchique

```php
<?php
namespace App\Controllers;

use App\Services\OrganizationService;

class OrganizationMoveController extends BaseController
{
    public function moveOrganization()
    {
        $service = service('OrganizationService');
        
        $orgId = 2;           // Global Tech - Europe
        $newParentId = 3;     // Global Tech - APAC
        
        try {
            // Le service empêche les cycles
            $service->moveToParent($orgId, $newParentId);
            
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Organization moved successfully',
            ]);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(400)->setJSON([
                'status' => 'error',
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Cas d'erreur: Essayer de déplacer vers un enfant
     * Résultat: Exception "Cannot move organization to its own child"
     */
    public function failureExample()
    {
        $service = service('OrganizationService');
        
        // Global Tech (1) → Global Tech - Europe (2) → France (4)
        // Essayer de déplacer 1 (parent) vers 4 (petit-enfant)
        
        try {
            $service->moveToParent(1, 4);
            // Les erreurs: Exception levée!
        } catch (\Exception $e) {
            echo $e->getMessage();
            // Output: "Cannot move organization to its own child"
        }
    }
}
?>
```

---

### Gestion des permissions

```php
<?php
namespace App\Services;

use App\Models\OrganizationMemberModel;

class PermissionService
{
    private OrganizationMemberModel $memberModel;

    public function __construct()
    {
        $this->memberModel = model(OrganizationMemberModel::class);
    }

    /**
     * Vérifier accès multi-niveaux
     */
    public function canAccess(int $orgId, int $userId, string $action): bool
    {
        $role = $this->memberModel->getUserRole($orgId, $userId);
        
        if (!$role) {
            return false;
        }

        // Matrice de permissions
        $permissions = [
            'owner' => ['view', 'edit', 'delete', 'manage_members', 'manage_partners'],
            'manager' => ['view', 'edit', 'manage_partners'],
            'viewer' => ['view'],
        ];

        return in_array($action, $permissions[$role] ?? []);
    }

    /**
     * Exemple d'utilisation dans Controller
     */
    public function exampleUsage($orgId, $userId)
    {
        if ($this->canAccess($orgId, $userId, 'edit')) {
            // Permettre édition
        } else {
            // Rejeter
            http_response_code(403);
            echo json_encode(['error' => 'Unauthorized']);
        }
    }
}
?>
```

---

## Exemples Test Unitaires (PHPUnit)

```php
<?php
namespace Tests\Unit;

use Tests\Support\TestCase;
use App\Models\OrganizationModel;
use App\Services\OrganizationService;

class OrganizationTest extends TestCase
{
    private OrganizationModel $orgModel;
    private OrganizationService $orgService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->orgModel = model(OrganizationModel::class);
        $this->orgService = service('OrganizationService');
    }

    /**
     * Test: Créer une organisation
     */
    public function testCreateOrganization()
    {
        $data = [
            'type_id' => 1,
            'name' => 'Test Company',
            'industry' => 'IT',
            'status' => 'active',
        ];

        $orgId = $this->orgModel->insert($data);

        $org = $this->orgModel->find($orgId);
        $this->assertNotNull($org);
        $this->assertEquals('Test Company', $org->name);
        $this->assertStringContainsString('test-company', $org->slug);
    }

    /**
     * Test: Hiérarchie parent-enfant
     */
    public function testHierarchy()
    {
        // Créer parent
        $parentId = $this->orgModel->insert([
            'type_id' => 1,
            'name' => 'Parent Org',
            'status' => 'active',
        ]);

        // Créer enfant
        $childId = $this->orgModel->insert([
            'type_id' => 1,
            'name' => 'Child Org',
            'parent_id' => $parentId,
            'status' => 'active',
        ]);

        // Vérifier parent
        $child = $this->orgModel->find($childId);
        $this->assertEquals($parentId, $child->parent_id);

        // Vérifier enfants
        $children = $this->orgModel->getSubsidiaries($parentId);
        $this->assertCount(1, $children);
        $this->assertEquals($childId, $children[0]->id);
    }

    /**
     * Test: Prévention des cycles
     */
    public function testCyclePrevention()
    {
        $this->expectException(\Exception::class);

        // Créer hiérarchie: A → B → C
        $a = $this->orgModel->insert(['type_id' => 1, 'name' => 'A', 'status' => 'active']);
        $b = $this->orgModel->insert(['type_id' => 1, 'name' => 'B', 'parent_id' => $a, 'status' => 'active']);
        $c = $this->orgModel->insert(['type_id' => 1, 'name' => 'C', 'parent_id' => $b, 'status' => 'active']);

        // Essayer de créer cycle: A → C (qui est déjà sous A)
        $this->orgService->moveToParent($a, $c);
    }

    /**
     * Test: Recherche avec filtres
     */
    public function testSearchWithFilters()
    {
        // Créer données de test
        for ($i = 1; $i <= 5; $i++) {
            $this->orgModel->insert([
                'type_id' => 1,
                'name' => "Tech Company $i",
                'industry' => 'IT',
                'status' => 'active',
            ]);
        }

        // Rechercher
        $result = $this->orgModel->search([
            'keyword' => 'Tech',
            'industry' => 'IT',
        ], 10);

        $this->assertGreaterThanOrEqual(5, $result['total']);
    }

    /**
     * Test: Permissions membres
     */
    public function testMemberPermissions()
    {
        $orgId = $this->orgModel->insert(['type_id' => 1, 'name' => 'Org', 'status' => 'active']);
        $memberModel = model('OrganizationMemberModel');

        // Ajouter owner
        $memberModel->addMember($orgId, 1, 'owner');

        // Vérifier permissions
        $this->assertTrue($memberModel->hasPermission($orgId, 1, 'owner'));
        $this->assertTrue($memberModel->hasPermission($orgId, 1, 'manager'));
        $this->assertTrue($memberModel->hasPermission($orgId, 1, 'viewer'));
    }
}
?>
```

---

## Commandes Utiles

```bash
# Exécuter les tests
php vendor/bin/phpunit tests/Unit/OrganizationTest.php

# Voir schema
php spark db:table organizations

# Export données
mysqldump -u user -p database organizations > backup.sql

# Rollback et refaire
php spark migrate:rollback
php spark migrate

# Cache (si implémenté)
php spark cache:clear
php spark cache:info

# Logs
tail -f writable/logs/*.log

# Debug
php spark db:seed OrganizationSeeder --dry-run
```

---

## Intégration avec Events

```php
<?php
// app/Events/OrganizationCreated.php
namespace App\Events;

class OrganizationCreated
{
    public $organization;

    public function __construct($organization)
    {
        $this->organization = $organization;
    }
}

// app/Listeners/SendWelcomeEmail.php
namespace App\Listeners;

class SendWelcomeEmail
{
    public function handle($event)
    {
        // Envoyer email au créateur
        $email = \Config\Services::email();
        $email->setTo($event->organization->email);
        $email->setSubject('Organization Created');
        $email->send('Organization created successfully');
    }
}

// Usage:
events()->trigger('organizationCreated', new OrganizationCreated($org));
?>
```

---

## Conclusion

Ce module est extrêmement flexible et peut être adapté à vos besoins spécifiques.

**Consult ez la documentation principale pour plus d'informations.**

---

**Bon développement! 🚀**
