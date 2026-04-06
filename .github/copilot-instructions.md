# Persomy — Copilot Instructions

This file is read automatically by GitHub Copilot for **every contributor** in this repo.
It defines the coding conventions, design system, and architecture rules that must be followed.

---

## Stack & Environment

| Item | Value |
|---|---|
| Framework | CodeIgniter 4 (CI4) 4.4.8 |
| PHP target | 8.1 (prod) / 8.0 (local XAMPP) |
| Database | MariaDB — database name `automoni` (local), `pers_persomy` (prod) |
| CSS framework | Bootstrap 5.3.3 + Bootstrap Icons 1.11.3 |
| Auth | Session-based — `session()->get('user_id')`, `session()->get('logged_in')` |
| File uploads | `writable/uploads/` served via `UploadsController` |
| i18n | 3 locales: `en`, `fr`, `ar` (RTL for Arabic) |

---

## Directory Structure

```
app/
  Config/         # CI4 config (Routes.php is the single routing file)
  Controllers/    # One controller per domain
  Database/
    Migrations/   # Raw SQL migrations (see Migration Rules below)
    Seeds/
  Filters/        # AuthFilter, RoleFilter
  Libraries/      # AlertMailer, CvParser
  Models/         # One model per table (see Model conventions)
  Views/
    layouts/      # main.php — the ONLY layout template
    profile/      # show.php, edit.php
    connections/  # index.php, search.php
    organizations/# index.php, show.php, form.php
    home/         # index.php, coaching.php
    jobs/         # index.php, show.php, create.php, edit.php
    auth/         # login.php, register.php
    dashboard/    # recruiter.php, seeker.php
    alerts/       # index.php
    company/      # show.php, form.php
public/           # Document root — only public/index.php here
writable/
  uploads/        # User-uploaded files (avatars, CVs, org logos, post media)
```

---

## Routing Rules (`app/Config/Routes.php`)

- **One file** — all routes live in `Routes.php`. Never create separate route files.
- Public routes (no auth) go before the auth groups.
- Auth-protected routes go inside `$routes->group('', ['filter' => 'auth'], ...)`.
- Role-restricted routes use `['filter' => 'role:job_seeker']` or `['filter' => 'role:recruiter,admin']`.
- **Always use `(:num)` for ID segments**, never `(:segment)` — prevents route collisions (e.g., `/organizations/create` matching before `/:id`).
- When a literal route and a parameterized route share the same prefix (e.g., `organizations/create` and `organizations/(:num)`), declare the **literal route first**.

```php
// CORRECT
$routes->get('organizations/create',      'OrganizationController::create');
$routes->get('organizations/(:num)',       'OrganizationController::show/$1');

// WRONG — "create" matches (:segment) and throws TypeError on int param
$routes->get('organizations/(:segment)',   'OrganizationController::show/$1');
```

---

## Migration Rules

- **Never use `$forge->addPrimaryKey()` followed by `$forge->createTable()`** — causes silent failures on the production MariaDB version.
- **Always write migrations as raw SQL** using `$this->db->query('...')`.
- Filename format: `YYYY-MM-DD-NNNNNN_DescriptiveName.php`
- The `up()` method uses `CREATE TABLE IF NOT EXISTS`.
- The `down()` method uses `DROP TABLE IF EXISTS`.

```php
// CORRECT pattern
public function up(): void
{
    $this->db->query('
        CREATE TABLE IF NOT EXISTS my_table (
            id   INT UNSIGNED AUTO_INCREMENT,
            name VARCHAR(255) NOT NULL,
            PRIMARY KEY (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ');
}
```

---

## Model Conventions

- Extend `CodeIgniter\Model`.
- Set `$useTimestamps = true` on every model.
- `UserModel` has soft deletes enabled (`$useSoftDeletes = true`) — always filter `deleted_at IS NULL` in raw queries.
- For complex queries (JOINs, aggregates), write named methods using `$this->db->query()`.
- Never use `$this->db->query()` for simple CRUD — use the model's built-in `insert()`, `update()`, `find()`.
- Password hashing: `password_hash($plain, PASSWORD_DEFAULT)` / `password_verify()` — never store plain text.

---

## Controller Conventions

- All controllers extend `App\Controllers\BaseController`.
- Read authenticated user ID as: `(int) session()->get('user_id')` — always cast to int.
- For services/libraries that are not CI4 services, instantiate directly: `new OrganizationService()`, **not** `service('OrganizationService')` — the service locator is unreliable for custom classes.
- AJAX-only actions (called by JS `fetch`) must return `$this->response->setJSON([...])`.
- Never output HTML from a controller — use views.
- Validate with CI4's `$this->validate($rules)` before touching the database.

---

## View Conventions

### Layout

All views **must** use the shared layout:

```php
<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<!-- your HTML here -->

<?= $this->endSection() ?>
```

Never embed `<html>`, `<head>`, `<body>`, Bootstrap CDN `<link>`, or navbar HTML directly in a view. The layout handles all of this.

### CSS Design Tokens

Use only these CSS variables (defined in `layouts/main.php`) — **do not hardcode colors or shadows**:

| Variable | Value |
|---|---|
| `--brand` | `#6366f1` (indigo) |
| `--brand-dark` | `#4f46e5` |
| `--brand-light` | `#eef2ff` |
| `--text` | `#0f172a` |
| `--muted` | (muted grey) |
| `--border` | `#e2e8f0` |
| `--bg` | page background |
| `--radius` | `12px` |
| `--shadow` | standard card shadow |
| `--shadow-lg` | brand-tinted large shadow |

### Icons

Use **Bootstrap Icons** exclusively: `<i class="bi bi-*"></i>`.
Never use Font Awesome, Heroicons, or inline SVG icons.

### Card Pattern

Every content block uses this card structure:

```html
<div style="background:#fff; border:1px solid var(--border); border-radius:var(--radius); padding:1.25rem; margin-bottom:1rem;">
    <!-- content -->
</div>
```

Or use the named CSS class if the view already defines one (e.g., `.lp-card` in profile views).

### Page Title

Pass `$title` to the view for the `<title>` tag:

```php
return view('my/view', ['title' => 'My Page', ...]);
```

---

## AJAX / Fetch Pattern

All JS `fetch` POST calls must include the CSRF token:

```js
const CSRF_NAME = '<?= csrf_token() ?>';
const CSRF_HASH = '<?= csrf_hash() ?>';

const body = new URLSearchParams({ [CSRF_NAME]: CSRF_HASH, ...otherFields });
fetch(BASE + 'route/action', {
    method: 'POST',
    headers: { 'X-Requested-With': 'XMLHttpRequest' },
    body
})
.then(r => r.json())
.then(data => { if (data.success) { /* update UI */ } });
```

Controllers return: `{ "success": true|false, "message": "...", "new_status": "..." }`.

---

## Authentication & Roles

```php
session()->get('logged_in')   // bool — is user logged in?
session()->get('user_id')     // int — current user's ID
session()->get('user_role')   // string — 'job_seeker' | 'recruiter' | 'admin'
session()->get('user_name')   // string — full name
session()->get('user_email')  // string
```

Roles:
- `job_seeker` — can apply to jobs, set alerts, manage profile, connections
- `recruiter` — can post jobs, manage company
- `admin` — full access

---

## File Upload Pattern

- Uploaded files go to `writable/uploads/`.
- Store only the **filename** (not the full path) in the database.
- Serve files via `UploadsController` at `/uploads/{filename}` — never expose `writable/` directly.
- Avatars are stored in `users.avatar` (filename string).

---

## Key Tables

| Table | Description |
|---|---|
| `users` | Auth + roles, soft deletes, `avatar` filename |
| `profiles` | 1-to-1 with users, headline/summary/city/country/LinkedIn/GitHub/portfolio |
| `skills` | belongs to user |
| `experiences`, `education`, `certifications`, `languages`, `projects`, `volunteering` | profile sections |
| `jobs` | posted by recruiters, belongs to company |
| `applications`, `job_alerts` | job seeker activity |
| `posts`, `post_reactions`, `post_comments` | social feed |
| `organizations`, `organization_members` | company/org pages |
| `user_connections` | social graph — `requester_id`, `receiver_id`, status `pending/accepted/rejected` |

---

## Naming Conventions

| Element | Convention | Example |
|---|---|---|
| Controllers | PascalCase + `Controller` suffix | `ProfileController` |
| Models | PascalCase + `Model` suffix | `ConnectionModel` |
| Views | snake_case folders + filename | `profile/show.php` |
| DB tables | snake_case plural | `user_connections` |
| DB columns | snake_case | `first_name`, `created_at` |
| Routes | kebab-case | `connections/send/(:num)` |
| CSS classes | kebab-case with view prefix | `.cn-card`, `.lp-header-card` |
| JS variables | camelCase | `const connectionsCount` |

---

## What NOT to Do

- ❌ Do not use `$forge->addPrimaryKey()` in migrations
- ❌ Do not use `(:segment)` for ID route params — use `(:num)`
- ❌ Do not call `service('MyCustomClass')` for non-CI4 services
- ❌ Do not hardcode colors — always use CSS vars
- ❌ Do not build custom `<html>/<head>/<nav>` in a view — use the layout
- ❌ Do not store plain-text passwords
- ❌ Do not expose `writable/` folder directly — use UploadsController
- ❌ Do not add Bootstrap CDN or icon CDN to individual views
- ❌ Do not cast `session()->get('user_id')` without `(int)` — it can be null
- ❌ Do not use `empty($obj->property)` when `$obj` may be null — this throws a TypeError; use `empty($obj) || empty($obj->property)`
