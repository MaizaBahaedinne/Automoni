# Guide d'Implémentation Rapide - Module Organisations

## Checklist Installation

- [ ] **Migrations exécutées**
  ```bash
  php spark migrate
  ```

- [ ] **Seeds exécutées**
  ```bash
  php spark db:seed OrganizationTypeSeeder
  php spark db:seed OrganizationSeeder
  ```

- [ ] **Dossier uploads créé**
  ```bash
  mkdir -p writable/uploads/organizations
  chmod 755 writable/uploads/organizations
  ```

- [ ] **Routes vérifiées** dans `app/Config/Routes.php` ✅ (déjà ajoutées)

- [ ] **Controllers présents** ✅
  - `app/Controllers/OrganizationController.php`
  - `app/Controllers/OrganizationMemberController.php`

- [ ] **Models présents** ✅
  - `app/Models/OrganizationModel.php`
  - `app/Models/OrganizationTypeModel.php`
  - `app/Models/OrganizationMemberModel.php`
  - `app/Models/OrganizationSocialLinkModel.php`
  - `app/Models/OrganizationCertificationModel.php`
  - `app/Models/OrganizationPartnerModel.php`

- [ ] **Service présent** ✅
  - `app/Services/OrganizationService.php`

---

## Test Rapide API

### 1. Lister organizations
```bash
curl "http://localhost:8080/organizations?Accept:application/json"
```

### 2. Créer organization
```bash
curl -X POST http://localhost:8080/organizations \
  -H "Content-Type: application/json" \
  -d '{"type_id":1,"name":"Test Co","industry":"IT","employee_count":100}'
```

### 3. Récupérer details
```bash
curl "http://localhost:8080/organizations/1"
```

### 4. Récupérer hiérarchie
```bash
curl "http://localhost:8080/organizations/1/hierarchy"
```

---

## Vues à créer (optionnel)

Créer les fichiers suivants dans `app/Views/organizations/` :

### Voir exemples de vues à la fin de ce fichier

---

## Structure Logique RBAC (Contrôle d'Accès)

### Permissions par Rôle

| Action | Owner | Manager | Viewer |
|--------|-------|---------|--------|
| Lire org | ✅ | ✅ | ✅ |
| Éditer org | ✅ | ✅ | ❌ |
| Supprimer org | ✅ | ❌ | ❌ |
| Ajouter membres | ✅ | ❌ | ❌ |
| Modifier rôles | ✅ | ❌ | ❌ |
| Supprimer membres | ✅ | ❌ | ❌ |

### Implémentation dans Model
```php
// Dans OrganizationMemberModel.php
public function hasPermission(int $orgId, int $userId, string $requiredRole): bool
{
    $roleHierarchy = ['owner' => 3, 'manager' => 2, 'viewer' => 1];
    $userRole = $this->getUserRole($orgId, $userId);
    
    return ($roleHierarchy[$userRole] ?? 0) >= ($roleHierarchy[$requiredRole] ?? 0);
}
```

### Utilisation dans Controller
```php
if (!$this->organizationService->canEdit($orgId, $userId)) {
    return redirect()->back()->with('error', 'Unauthorized');
}
```

---

## Configuration du Service d'Upload

### Sécurité de l'upload

File `app/Services/OrganizationService.php` - Méthode `uploadLogo()` :

✅ Validation MIME type : JPEG, PNG, WebP, SVG  
✅ Limite de taille : 5 MB  
✅ Nom fichier unique : `org_{ID}_{timestamp}.{ext}`  
✅ Suppression ancien fichier  

### Personnaliser les limites
```php
// Dans OrganizationService::uploadLogo()
$maxSize = 5 * 1024 * 1024; // Modifier ici

$allowedMimes = ['image/jpeg', 'image/png']; // Ajouter/retirer ici
```

---

## Flux Hiérarchie Organisations

### Créer une hiérarchie

```
        Parent (parent_id: NULL)
           |
         Enfant 1 (parent_id: 1)
           |
         Enfant 1.1 (parent_id: 2)

Enfant 2 (parent_id: 1)
```

### Créer filiale
```bash
curl -X POST http://localhost:8080/organizations \
  -H "Content-Type: application/json" \
  -d '{
    "type_id": 1,
    "name": "Tech Co - France",
    "parent_id": 1,
    "industry": "IT"
  }'
```

### Récupérer arborescence
```bash
curl "http://localhost:8080/organizations/1/hierarchy"
```

Réponse :
```json
{
  "status": "success",
  "data": [
    {"level": 0, "node": {"id": 1, "name": "Tech Co"}},
    {"level": 1, "node": {"id": 2, "name": "Tech Co - France", "parent_id": 1}},
    {"level": 1, "node": {"id": 3, "name": "Tech Co - Germany", "parent_id": 1}}
  ]
}
```

---

## Commandes Spark Utiles

```bash
# Voir statut migrations
php spark migrate:status

# Rollback migrations
php spark migrate:rollback

# Refresh base (réinitialiser)
php spark migrate:fresh
php spark db:seed OrganizationTypeSeeder
php spark db:seed OrganizationSeeder

# Générer Controller/Model automatiquement (pas nécessaire, déjà fait)
php spark make:controller OrganizationController --model=OrganizationModel
```

---

## Notes Importantes

1. **Cascade delete** : Suppression org = suppression membres associés
2. **Soft delete** : Organisations supprimées restent en DB avec `deleted_at`
3. **Timestamps** : `created_at`, `updated_at` gérés automatiquement
4. **Slugs** : Générés automatiquement du nom + timestamp
5. **Logo upload** : Stocké dans `writable/uploads/organizations/`

---

## Dépannage Courant

### "Table doesn't exist"
→ Exécuter migrations : `php spark migrate`

### "Call to undefined method"
→ Vérifier les Models sont utilisés via `model(ClassNameModel::class)`

### "Permission denied" sur uploads
→ Vérifier permissions : `chmod 755 writable/uploads/organizations/`

### Erreur 500 sur création
→ Vérifier logs : `tail -f writable/logs/log-*.log`

---

## Next Steps

1. ✅ Installer module (migrations + seeders)
2. ✅ Tester API endpoints
3. ⏭️ Créer vues (index.php, show.php, form.php)
4. ⏭️ Ajouter styles CSS/Tailwind
5. ⏭️ Implémenter notifications utilisateurs
6. ⏭️ Ajouter gestion des erreurs personnalisées
7. ⏭️ Tests unitaires (PHPUnit)
8. ⏭️ Documentation utilisateur frontend

