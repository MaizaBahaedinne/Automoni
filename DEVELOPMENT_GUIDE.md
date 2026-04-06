# 🛠️ Development Guide - Automoni

## Table of Contents
1. [Environment Setup](#environment-setup)
2. [Project Structure](#project-structure)
3. [Development Workflow](#development-workflow)
4. [Coding Standards](#coding-standards)
5. [Common Tasks](#common-tasks)
6. [Troubleshooting](#troubleshooting)
7. [Performance Tips](#performance-tips)

---

## Environment Setup

### Prerequisites
- PHP 8.1 or higher
- Composer
- MySQL/MariaDB
- Git

### Initial Setup
```bash
# 1. Clone and navigate
cd /Users/gouiaaepmaizamalak/Automoni

# 2. Install dependencies
composer install

# 3. Configure environment
cp .env.example .env
# Edit .env with your database credentials

# 4. Generate application key (if needed)
php spark key:generate

# 5. Run migrations
php spark migrate

# 6. Seed database (optional)
php spark db:seed OrganizationTypeSeeder
php spark db:seed OrganizationSeeder

# 7. Start development server
php spark serve
# Access at http://localhost:8080
```

### Database Configuration
Located in `.env`:
```
database.default.hostname = localhost
database.default.database = automoni
database.default.username = root
database.default.password = 
database.default.DBDriver = MySQLi
```

---

## Project Structure

### Directory Organization
```
Automoni/
├── app/
│   ├── Config/              # Configuration files
│   │   ├── App.php          # Base URL, environment
│   │   ├── Routes.php       # All route definitions ⭐
│   │   ├── Database.php     # DB connection settings
│   │   ├── Filters.php      # Filter configuration
│   │   └── ...
│   ├── Controllers/         # Request handlers (14 controllers)
│   │   ├── BaseController.php
│   │   ├── AuthController.php
│   │   ├── OrganizationController.php    # NEW
│   │   ├── OrganizationMemberController.php  # NEW
│   │   └── ...
│   ├── Models/              # Data access layer (24 models)
│   │   ├── UserModel.php
│   │   ├── OrganizationModel.php    # NEW
│   │   ├── OrganizationMemberModel.php  # NEW
│   │   └── ...
│   ├── Filters/             # Middleware (3 filters)
│   │   ├── AuthFilter.php   # Authentication check
│   │   ├── RoleFilter.php   # Authorization check
│   │   └── LangFilter.php   # Language selection
│   ├── Libraries/           # Utility classes
│   │   ├── AlertMailer.php
│   │   └── CvParser.php
│   ├── Services/            # Business logic layer
│   │   └── OrganizationService.php  # NEW
│   ├── Views/               # View templates
│   │   ├── organizations/   # NEW
│   │   │   ├── index.php
│   │   │   ├── show.php
│   │   │   └── form.php
│   │   └── ...
│   └── Database/
│       ├── Migrations/      # Database schema (24 migrations)
│       │   ├── 2024-01-16-000001_CreateOrganizationTypesTable.php  # NEW
│       │   ├── 2024-01-16-000002_CreateOrganizationsTable.php  # NEW
│       │   └── ...
│       └── Seeds/           # Test data
│           ├── OrganizationTypeSeeder.php  # NEW
│           └── OrganizationSeeder.php  # NEW
├── public/
│   ├── index.php            # Entry point
│   ├── uploads/             # User-generated content
│   └── robots.txt
├── writable/
│   ├── cache/               # Cache files
│   ├── logs/                # Application logs
│   ├── session/             # Session data
│   └── uploads/             # Uploaded files
├── tests/
│   ├── unit/                # Unit tests
│   └── database/            # Database tests
├── .env                     # Environment variables ⚠️ DO NOT COMMIT
├── composer.json            # PHP dependencies
├── phpunit.xml.dist         # Test configuration
└── README.md
```

### Key Configuration Files

#### `app/Config/Routes.php`
```php
// Public routes (no auth required)
$routes->get('jobs', 'JobController::index');
$routes->get('jobs/(:num)', 'JobController::show/$1');
$routes->get('organizations', 'OrganizationController::index');

// Protected routes (require login)
$routes->group('', ['filter' => 'auth'], function($routes) {
    $routes->get('dashboard', 'DashboardController::index');
    $routes->post('jobs', 'JobController::store');
    $routes->post('organizations', 'OrganizationController::store');
});

// Recruiter-only routes
$routes->group('', ['filter' => 'role:recruiter'], function($routes) {
    $routes->post('company/create', 'CompanyController::create');
});
```

#### `app/Config/Filters.php`
```php
public $aliases = [
    'csrf'       => \CodeIgniter\Filters\CSRF::class,
    'toolbar'    => \CodeIgniter\Filters\DebugBar::class,
    'debug'      => \CodeIgniter\Filters\Debug::class,
    'auth'       => \App\Filters\AuthFilter::class,
    'role'       => \App\Filters\RoleFilter::class,
    'lang'       => \App\Filters\LangFilter::class,
];
```

---

## Development Workflow

### Adding a New Feature

#### 1. Database Migration (if needed)
```bash
# Create migration file
php spark make:migration CreateMyTableTable

# Edit app/Database/Migrations/YYYY-MM-DD-HHMMSS_CreateMyTableTable.php
# Run migrations
php spark migrate
```

#### 2. Create Model
```bash
# Generate model
php spark make:model MyModel

# Edit app/Models/MyModel.php with:
# - Table name
# - Allowed fields
# - Validation rules
# - Relationships
```

#### 3. Create Controller
```bash
# Generate controller
php spark make:controller MyController

# Edit app/Controllers/MyController.php with:
# - Request handling
# - Authorization checks
# - Response formatting (HTML + JSON)
```

#### 4. Add Routes
Edit `app/Config/Routes.php`:
```php
$routes->get('my-feature', 'MyController::index');
$routes->post('my-feature', 'MyController::store');
```

#### 5. Create Views (if needed)
Create in `app/Views/my-feature/`:
- `index.php` - List view
- `show.php` - Detail view
- `form.php` - Create/Edit form

#### 6. Write Tests
Create in `tests/unit/` or `tests/database/`

### Typical Model Structure
```php
<?php

namespace App\Models;

use CodeIgniter\Model;

class MyModel extends Model
{
    protected $table            = 'my_table';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';
    protected $deletedField     = 'deleted_at';

    // Allowed fields for mass assignment
    protected $allowedFields = [
        'name',
        'description',
        'user_id',
        'status',
    ];

    // Validation rules
    protected $validationRules = [
        'name'        => 'required|string|max_length[255]|is_unique[my_table.name]',
        'description' => 'required|string',
        'user_id'     => 'required|integer|ext_isValidUserId',
        'status'      => 'required|in_list[active,inactive]',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo('App\Models\UserModel', 'user_id', 'id');
    }
}
```

### Typical Controller Structure
```php
<?php

namespace App\Controllers;

class MyController extends BaseController
{
    protected $model;

    public function __construct()
    {
        $this->model = new MyModel();
    }

    /**
     * Display list of resources
     */
    public function index()
    {
        $items = $this->model->paginate(15);
        
        // Support both HTML and JSON
        if ($this->request->getHeaderLine('Accept') === 'application/json') {
            return $this->response->setJSON([
                'status'  => 200,
                'message' => 'Items retrieved',
                'data'    => $items
            ]);
        }

        return view('my_feature/index', [
            'items' => $items,
            'pager' => $this->model->pager,
        ]);
    }

    /**
     * Show single resource
     */
    public function show($id = null)
    {
        $item = $this->model->find($id);
        
        if (!$item) {
            return $this->response->setStatusCode(404);
        }

        return view('my_feature/show', [
            'item' => $item,
        ]);
    }

    /**
     * Store new resource
     */
    public function store()
    {
        if (!$this->validate($this->model->validationRules)) {
            return redirect()->back()->withInput();
        }

        $this->model->save([
            'name'        => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
            'user_id'     => user_id(), // Helper function
        ]);

        return redirect()->to('my-feature')->with('success', 'Created');
    }

    /**
     * Delete resource
     */
    public function delete($id = null)
    {
        $this->model->delete($id);
        return redirect()->to('my-feature')->with('success', 'Deleted');
    }
}
```

---

## Coding Standards

### PHP Coding Style
- **PSR-12** compliance (Code Style Guide)
- 4 spaces indentation (no tabs)
- Maximum line length: 120 characters (soft recommendation)

### Naming Conventions
```
Controllers:   MyFeatureController
Models:        MyFeatureModel
Services:      MyFeatureService
Views:         my_feature/index.php
Migrations:    YYYY-MM-DD-HHMMSS_CreateMyFeatureTable.php
Filters:       MyFeatureFilter
```

### Comments & Documentation
```php
/**
 * Short description of what this method does.
 * 
 * Long description if needed, explaining the intent
 * and any important behaviors.
 *
 * @param  int    $id        The resource ID
 * @param  string $status    The status filter
 * @return array|bool        The results or failure flag
 * @throws RuntimeException  If database connection fails
 */
public function myMethod(int $id, string $status = 'active')
{
    // Implementation
}
```

### Error Handling
```php
// ✅ Good
try {
    $result = $this->model->save($data);
    if (!$result) {
        throw new \Exception('Failed to save record');
    }
} catch (\Exception $e) {
    log_message('error', $e->getMessage());
    return redirect()->back()->with('error', 'An error occurred');
}

// ❌ Avoid
$this->model->save($data); // Assumes success
```

### Security Practices
```php
// ✅ Always escape user input
$name = esc($this->request->getPost('name'));

// ✅ Use prepared statements (CodeIgniter handles this)
$user = $this->userModel->where('email', $email)->first();

// ✅ Validate all input
if (!$this->validate($rules)) {
    return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
}

// ✅ Use CSRF protection (automatic in CodeIgniter)
echo csrf_field(); // In forms

// ❌ Never trust user input directly
// ❌ Don't expose SQL queries to users
// ❌ Never store passwords in plain text
```

### Database Conventions
```php
// Primary key: id (auto-increment)
// Foreign keys: snake_case with _id (e.g., user_id, company_id)
// Timestamps: created_at, updated_at (DATETIME)
// Soft deletes: deleted_at (DATETIME)
// Booleans: stored as TINYINT(1) with is_ prefix (is_verified)
// Enums: ENUM if database supports, VARCHAR if not
// Indexes: On foreign keys and frequently queried fields
```

---

## Common Tasks

### Task 1: Add a New Route
```php
// In app/Config/Routes.php

// Before other routes or in appropriate group
$routes->get('feature-name', 'FeatureController::index');
$routes->get('feature-name/(:num)', 'FeatureController::show/$1');
$routes->post('feature-name', 'FeatureController::store');
$routes->put('feature-name/(:num)', 'FeatureController::update/$1');
$routes->delete('feature-name/(:num)', 'FeatureController::delete/$1');
```

### Task 2: Create a Migration
```bash
php spark make:migration CreateFeaturesTable
```
```php
<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateFeaturesTable extends Migration
{
    public function up()
    {
        $this->forge->createTable('features', function($table) {
            $table->id();
            $table->string('name', 255);
            $table->text('description');
            $table->unsignedInt('user_id');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate();
            $table->softDeletes();

            $table->index('user_id');
            $table->unique('name');
            $table->foreignKey('user_id', 'users', 'id', 'restrict', 'cascade');
        });
    }

    public function down()
    {
        $this->forge->dropTable('features', true);
    }
}
```

### Task 3: Add Authentication to a Route
```php
// In app/Config/Routes.php
$routes->group('', ['filter' => 'auth'], function($routes) {
    $routes->post('my-account/update', 'AccountController::update');
    $routes->get('dashboard', 'DashboardController::index');
});

// The AuthFilter checks session and redirects to login if not authenticated
```

### Task 4: Add Role-Based Authorization
```php
// In app/Config/Routes.php
$routes->group('', ['filter' => 'role:recruiter,admin'], function($routes) {
    $routes->post('jobs', 'JobController::store');
    $routes->put('jobs/(:num)', 'JobController::update/$1');
    $routes->delete('jobs/(:num)', 'JobController::delete/$1');
});

// The RoleFilter checks user_role and allows only specified roles
```

### Task 5: Query Data Efficiently
```php
// Bad: N+1 query problem
$users = $this->userModel->findAll();
foreach ($users as $user) {
    $profile = $this->profileModel->where('user_id', $user['id'])->first();
    // Query runs for each user
}

// Good: Join in single query
$users = $this->userModel
    ->select('users.*, profiles.headline')
    ->join('profiles', 'users.id = profiles.user_id', 'left')
    ->findAll();
```

### Task 6: Implement Soft Deletes
```php
// In Migration
$table->softDeletes(); // Adds deleted_at column

// In Model
protected $useSoftDeletes = true;
protected $deletedField = 'deleted_at';

// Usage:
$this->model->delete($id);  // Sets deleted_at, doesn't remove row
$this->model->find($id);    // Skips soft-deleted rows
$this->model->withDeleted()->find($id);  // Includes deleted rows
$this->model->onlyDeleted()->findAll();  // Only deleted rows
```

### Task 7: Upload Files
```php
// In Controller
$file = $this->request->getFile('avatar');

if (!$file->isValid()) {
    throw \RuntimeException($file->getErrorString());
}

$allowed = ['image/jpeg', 'image/png', 'image/webp'];
if (!in_array($file->getMimeType(), $allowed)) {
    throw \RuntimeException('Invalid file type');
}

$newName = $file->getRandomName();
$file->move(WRITEPATH . 'uploads', $newName);

$this->userModel->update(user_id(), [
    'avatar' => $newName
]);
```

### Task 8: Send Email
```php
// Using AlertMailer library
$alertMailer = new \App\Libraries\AlertMailer();

$alertMailer->send(
    'user@example.com',
    'Welcome to Automoni',
    'welcome_email',
    [
        'user_name' => $user['first_name'],
        'activation_link' => base_url('activate/' . $token)
    ]
);
```

### Task 9: Write a Unit Test
```php
// In tests/unit/MyTest.php
<?php

namespace Tests\Unit;

use CodeIgniter\Test\CIUnitTestCase;
use App\Models\MyModel;

class MyTest extends CIUnitTestCase
{
    protected $model;

    protected function setUp(): void
    {
        parent::setUp();
        $this->model = new MyModel();
    }

    public function testCanCreateRecord()
    {
        $data = [
            'name' => 'Test Item',
            'status' => 'active',
        ];

        $id = $this->model->insert($data);
        $this->assertIsInt($id);

        $retrieved = $this->model->find($id);
        $this->assertEquals('Test Item', $retrieved['name']);
    }
}
```

Run tests:
```bash
php spark test
# Or specific test:
php spark test tests/unit/MyTest.php
```

---

## Troubleshooting

### Issue: Routes not working
**Symptoms:** 404 error on valid routes

**Solutions:**
1. Check if route is defined in `app/Config/Routes.php`
2. Verify controller exists: `app/Controllers/MyController.php`
3. Check if method exists in controller
4. Ensure base URL in `.env` matches your domain

```bash
# Debug routes:
php spark routes
```

### Issue: Filter not applied
**Symptoms:** Authentication not working, unauthorized access

**Solutions:**
1. Check `app/Config/Filters.php` for filter definition
2. Verify filter is applied to route group
3. Check filter logic matches intent

```php
// Debug in controller
log_message('debug', 'Current user: ' . user_id());
log_message('debug', 'Current role: ' . user_role());
```

### Issue: Database query failing
**Symptoms:** Exception or empty results

**Solutions:**
1. Check table exists: `php spark db:exec "SHOW TABLES;"`
2. Verify column names: `php spark db:exec "DESCRIBE table_name;"`
3. Check validation rules
4. Use query builder for debugging

```php
// See SQL being run:
$query = $this->model->where('status', 'active')->getCompiledSelect();
log_message('debug', $query);
```

### Issue: File upload failing
**Symptoms:** File not saved, no error shown

**Solutions:**
1. Check `writable/uploads/` exists and is writable: `chmod 755`
2. Verify file size: Check `php.ini` `post_max_size` and `upload_max_filesize`
3. Check MIME type whitelist
4. Enable debug mode to see errors

```php
// Check file validity:
if ($file->isValid()) {
    // Safe to use
} else {
    throw new \Exception($file->getErrorString()); // Get error details
}
```

### Issue: Slow queries
**Symptoms:** Page loading takes >1 second

**Solutions:**
1. Add indexes to frequently queried columns
2. Avoid N+1 queries - use joins
3. Paginate large result sets
4. Cache results

```php
// Enable query logging to see slow queries:
// In .env: CI_ENVIRONMENT = development
// Use Query Builder with db->showLastQuery()
```

### Issue: Session not persisting
**Symptoms:** User_id lost after page load

**Solutions:**
1. Check session driver in `app/Config/Session.php`
2. Verify browser accepts cookies
3. Check session file permissions in `writable/session/`

```php
// Debug session:
log_message('debug', print_r(session()->all(), true));
```

---

## Performance Tips

### Database Optimization
```php
// 1. Use indexes on foreign keys and frequently queried columns
$table->index('user_id');
$table->index(['status', 'created_at']);

// 2. Avoid selecting unnecessary columns
$users = $this->userModel
    ->select('id, first_name, last_name, email') // Not all columns
    ->where('status', 'active')
    ->limit(100)
    ->get();

// 3. Use joins instead of multiple queries
$jobs = $this->jobModel
    ->select('jobs.*, companies.name as company_name')
    ->join('companies', 'jobs.company_id = companies.id')
    ->limit(20)
    ->get();

// 4. Paginate large result sets
$jobs = $this->jobModel->paginate(15);
```

### Query Results Caching
```php
// Cache query results for 1 hour
$jobs = $this->jobModel
    ->cache(3600) // 3600 seconds = 1 hour
    ->where('status', 'active')
    ->findAll();
```

### View Optimization
```php
// 1. Minimize HTTP requests - combine CSS/JS
// 2. Lazy load images with 'loading=lazy'
<img src="logo.png" alt="Logo" loading="lazy" />

// 3. Remove unnecessary whitespace in production
// 4. Minify CSS and JavaScript
// 5. Use responsive images with srcset
<img src="small.png" srcset="medium.png 640w, large.png 1280w" />
```

### General Best Practices
```
• Use prepared statements (CodeIgniter does this)
• Avoid SELECT * - specify needed columns
• Set appropriate cache headers
• Enable gzip compression in web server
• Use CDN for static assets
• Monitor slow queries in development
• Profile code with built-in debugger
```

---

## Helper Functions

Commonly used helper functions in Automoni:

```php
// User & Session
user_id()              // Get current user ID
user_role()            // Get current user role
user()                 // Get current user data
is_admin()             // Check if admin role
is_recruiter()         // Check if recruiter role
check_user_auth()      // Redirect if not authenticated

// Security
csrf_field()           // Output CSRF token in forms
esc($text)             // Escape HTML
hash_password($pass)   // Hash password with BCRYPT

// Routing
base_url($path)        // Get base URL
site_url($path)        // Get site URL
route_to('route.name') // Get URL for named route

// Validation
validate($rules)       // Validate data

// Response
view($path, $data)     // Load and render view
redirect($path)        // Redirect with optional data
json_response($data)   // Return JSON response

// Logging
log_message($level, $message)  // Log message
```

---

## Resources

- [CodeIgniter 4 Documentation](https://codeigniter.com/user_guide/)
- [Database Design Guide](./DATABASE_SCHEMA.md)
- [Organizations Module Documentation](./ORGANISATIONS_MODULE.md)
- [Project Context Overview](./CONTEXT_OVERVIEW.md)
- [Advanced Examples](./ADVANCED_EXAMPLES.md)

---

**Last Updated:** 2024-01-16  
**Maintained By:** Development Team
