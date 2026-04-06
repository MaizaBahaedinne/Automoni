# 📚 Automoni Documentation Index

## Quick Links by Topic

### 🎯 Getting Started (New to Automoni?)
**Start here if you're new to the project:**
1. [README.md](./README.md) - Project overview (5 min read)
2. [CONTEXT_OVERVIEW.md](./CONTEXT_OVERVIEW.md) - Complete project architecture (15 min read)
3. [DEVELOPMENT_GUIDE.md](./DEVELOPMENT_GUIDE.md#environment-setup) - Environment setup (10 min read)
4. [SETUP_AND_TESTING.md](./SETUP_AND_TESTING.md) - Run the project locally (20 min)

**Total onboarding time:** ~50 minutes

---

### 👨‍💻 For Developers

#### Learning the Codebase
1. [CONTEXT_OVERVIEW.md](./CONTEXT_OVERVIEW.md) - System architecture & components
2. [DATABASE_SCHEMA.md](./DATABASE_SCHEMA.md) - Entity relationships & data model
3. [DEVELOPMENT_GUIDE.md](./DEVELOPMENT_GUIDE.md) - Coding standards & workflows
4. [ORGANISATIONS_MODULE.md](./docs/ORGANISATIONS_MODULE.md) - Example of a complete module

#### Adding Features
1. [DEVELOPMENT_GUIDE.md#adding-a-new-feature](./DEVELOPMENT_GUIDE.md#adding-a-new-feature) - Step-by-step guide
2. [ORGANISATIONS_MODULE.md](./docs/ORGANISATIONS_MODULE.md) - Reference implementation
3. [ADVANCED_EXAMPLES.md](./docs/ADVANCED_EXAMPLES.md) - Complex patterns

#### Debugging & Troubleshooting
1. [DEVELOPMENT_GUIDE.md#troubleshooting](./DEVELOPMENT_GUIDE.md#troubleshooting) - Common issues
2. [SETUP_AND_TESTING.md#-debugging-checklist](./SETUP_AND_TESTING.md#-debugging-checklist) - Debug steps
3. Project logs: `writable/logs/log-*.log`

#### Code Examples
- [ADVANCED_EXAMPLES.md](./docs/ADVANCED_EXAMPLES.md) - Complex queries & patterns
- [ORGANISATIONS_MODULE.md#api-endpoints](./docs/ORGANISATIONS_MODULE.md#api-endpoints) - REST API examples

---

### 👥 For Team Leads/Managers

#### Project Status
1. [PROJECT_ROADMAP.md](./PROJECT_ROADMAP.md#current-status-q1-2024) - Where we are
2. [PROJECT_ROADMAP.md#-next-vision-phase-3-proposed](./PROJECT_ROADMAP.md#-next-vision-phase-3-proposed) - What's next
3. [CONTEXT_OVERVIEW.md#statistics](./CONTEXT_OVERVIEW.md#statistics) - Project metrics

#### Resource Planning
1. [PROJECT_ROADMAP.md#📊-development-resource-allocation](./PROJECT_ROADMAP.md#📊-development-resource-allocation) - Team sizing
2. [PROJECT_ROADMAP.md#-immediate-action-items-next-2-weeks](./PROJECT_ROADMAP.md#-immediate-action-items-next-2-weeks) - Short-term tasks
3. [PROJECT_ROADMAP.md#📅-high-level-timeline](./PROJECT_ROADMAP.md#📅-high-level-timeline) - Timeline

#### Quality & Launch
1. [SETUP_AND_TESTING.md](./SETUP_AND_TESTING.md) - Testing framework
2. [PROJECT_ROADMAP.md#🚀-go-live-preparation](./PROJECT_ROADMAP.md#🚀-go-live-preparation) - Launch checklist

---

### 🧪 For QA/Testers

#### Testing Strategy
1. [SETUP_AND_TESTING.md](./SETUP_AND_TESTING.md) - Complete testing guide
2. [DATABASE_SCHEMA.md#🔄-data-flow-examples](./DATABASE_SCHEMA.md#🔄-data-flow-examples) - Using data flows
3. [ORGANISATIONS_MODULE.md#testing](./docs/ORGANISATIONS_MODULE.md#testing) - Module-specific tests

#### Test Cases
1. [SETUP_AND_TESTING.md#📋-test-cases-by-feature](./SETUP_AND_TESTING.md#📋-test-cases-by-feature) - All test cases
2. [SETUP_AND_TESTING.md#-security-verification](./SETUP_AND_TESTING.md#-security-verification) - Security tests
3. [SETUP_AND_TESTING.md#🐛-debugging-checklist](./SETUP_AND_TESTING.md#🐛-debugging-checklist) - Debugging

#### Test Results
- Use template in [SETUP_AND_TESTING.md#📝-test-results-template](./SETUP_AND_TESTING.md#📝-test-results-template)
- Create test reports in project wiki

---

### 🎨 For UX/Frontend Developers

#### Understanding the Design
1. [CONTEXT_OVERVIEW.md#-ui-framework--styling](./CONTEXT_OVERVIEW.md#-ui-framework--styling) - Current stack
2. [ORGANISATIONS_MODULE.md#views](./docs/ORGANISATIONS_MODULE.md#views) - View examples
3. [DATABASE_SCHEMA.md](./DATABASE_SCHEMA.md) - Understand data structure

#### Available Views
1. [ORGANISATIONS_MODULE.md#views](./docs/ORGANISATIONS_MODULE.md#views) - Organizations views
2. Current views in `app/Views/`

#### Styling & Customization
1. [DEVELOPMENT_GUIDE.md#view-optimization](./DEVELOPMENT_GUIDE.md#view-optimization) - Performance tips
2. Bootstrap 5 documentation (external)

---

### 📊 For Product Managers

#### Feature Understanding
1. [CONTEXT_OVERVIEW.md](./CONTEXT_OVERVIEW.md) - Core features
2. [ORGANISATIONS_MODULE.md](./docs/ORGANISATIONS_MODULE.md) - Organizations feature
3. [PROJECT_ROADMAP.md#-next-vision-phase-3-proposed](./PROJECT_ROADMAP.md#-next-vision-phase-3-proposed) - Planned features

#### Success Metrics
1. [PROJECT_ROADMAP.md#🎯-success-metrics](./PROJECT_ROADMAP.md#🎯-success-metrics) - KPIs
2. [DATABASE_SCHEMA.md#-scaling--future-relations](./DATABASE_SCHEMA.md#-scaling--future-relations) - Analytics tables

#### User Documentation
- [ORGANISATIONS_QUICKSTART.md](./docs/ORGANISATIONS_QUICKSTART.md) - User guide

---

### 🔒 For Security/DevOps

#### Security Review
1. [DEVELOPMENT_GUIDE.md#security-practices](./DEVELOPMENT_GUIDE.md#security-practices) - Security guidelines
2. [SETUP_AND_TESTING.md#-security-verification](./SETUP_AND_TESTING.md#-security-verification) - Security tests
3. [DATABASE_SCHEMA.md#💾-data-integrity-rules](./DATABASE_SCHEMA.md#💾-data-integrity-rules) - Data rules

#### Infrastructure
1. [DEVELOPMENT_GUIDE.md#environment-setup](./DEVELOPMENT_GUIDE.md#environment-setup) - Local setup
2. [PROJECT_ROADMAP.md#🔌-technology-stack-review](./PROJECT_ROADMAP.md#🔌-technology-stack-review) - Tech stack
3. Environment variables in `.env` file

#### Monitoring & Performance
1. [DEVELOPMENT_GUIDE.md#performance-tips](./DEVELOPMENT_GUIDE.md#performance-tips) - Tuning
2. [PROJECT_ROADMAP.md#💡-technical-debt--improvements](./PROJECT_ROADMAP.md#💡-technical-debt--improvements) - Known issues
3. [SETUP_AND_TESTING.md#📊-performance-baseline](./SETUP_AND_TESTING.md#📊-performance-baseline) - Expected performance

---

## 📂 File Locations Reference

### Documentation Files (Root)
```
├── README.md                          ← Project overview
├── PROJECT_ROADMAP.md                 ← Roadmap & future
├── CONTEXT_OVERVIEW.md                ← Architecture & systems
├── DATABASE_SCHEMA.md                 ← Data model & relationships
├── DEVELOPMENT_GUIDE.md               ← Coding standards
├── SETUP_AND_TESTING.md               ← Setup & verification
└── DOCUMENTATION_INDEX.md             ← You are here 📍
```

### Module Documentation (docs/)
```
docs/
├── ORGANISATIONS_MODULE.md            ← Organizations module guide
├── ORGANISATIONS_QUICKSTART.md        ← User quick start
└── ADVANCED_EXAMPLES.md               ← Advanced patterns
```

### Source Code
```
app/
├── Config/                            ← Configuration
│   ├── Routes.php                      # Route definitions
│   ├── Filters.php                     # Filter configuration
│   └── ...
├── Controllers/                       ← HTTP request handlers
│   ├── OrganizationController.php      # NEW
│   ├── OrganizationMemberController.php # NEW
│   └── ...
├── Models/                            ← Data access layer
│   ├── OrganizationModel.php           # NEW
│   ├── OrganizationMemberModel.php     # NEW
│   └── ...
├── Services/                          ← Business logic
│   └── OrganizationService.php         # NEW
├── Views/                             ← Templates
│   ├── organizations/                 # NEW
│   │   ├── index.php
│   │   ├── show.php
│   │   └── form.php
│   └── ...
└── Database/
    ├── Migrations/                    ← Schema changes
    │   ├── 2024-01-16-000001_CreateOrganizationTypesTable.php
    │   ├── 2024-01-16-000002_CreateOrganizationsTable.php
    │   └── ... (6 new migrations)
    └── Seeds/
        ├── OrganizationTypeSeeder.php # NEW
        └── OrganizationSeeder.php     # NEW
```

### Generated Files
```
writable/
├── cache/                             ← Cache files
├── logs/                              ← Application logs
├── session/                           ← Session data
└── uploads/                           ← User uploads
```

### Configuration
```
.env                                   ← Environment variables
composer.json                          ← PHP dependencies
phpunit.xml.dist                       ← Test configuration
```

---

## 🔍 Finding What You Need

### By Topic

**I want to understand the database...**
→ [DATABASE_SCHEMA.md](./DATABASE_SCHEMA.md)

**I want to understand how authentication works...**
→ [CONTEXT_OVERVIEW.md#-authentication-system](./CONTEXT_OVERVIEW.md#-authentication-system)

**I want to add a new route...**
→ [DEVELOPMENT_GUIDE.md#task-1-add-a-new-route](./DEVELOPMENT_GUIDE.md#task-1-add-a-new-route)

**I want to create a new model...**
→ [DEVELOPMENT_GUIDE.md#adding-a-new-feature](./DEVELOPMENT_GUIDE.md#adding-a-new-feature)

**I want to understand the Organizations module...**
→ [ORGANISATIONS_MODULE.md](./docs/ORGANISATIONS_MODULE.md)

**I want to test something...**
→ [SETUP_AND_TESTING.md](./SETUP_AND_TESTING.md)

**I want to see an example of complex code...**
→ [ADVANCED_EXAMPLES.md](./docs/ADVANCED_EXAMPLES.md)

**I want to understand the project roadmap...**
→ [PROJECT_ROADMAP.md](./PROJECT_ROADMAP.md)

**I want to see the API endpoints...**
→ [ORGANISATIONS_MODULE.md#api-endpoints](./docs/ORGANISATIONS_MODULE.md#api-endpoints)

**I want to troubleshoot an issue...**
→ [DEVELOPMENT_GUIDE.md#troubleshooting](./DEVELOPMENT_GUIDE.md#troubleshooting)

**I want to know what's deployed...**
→ [PROJECT_ROADMAP.md#-completed-phases](./PROJECT_ROADMAP.md#-completed-phases)

**I want to know the coding standards...**
→ [DEVELOPMENT_GUIDE.md#coding-standards](./DEVELOPMENT_GUIDE.md#coding-standards)

**I want to understand the permission system...**
→ [DATABASE_SCHEMA.md#-permission-matrix](./DATABASE_SCHEMA.md#-permission-matrix)

**I want to see performance benchmarks...**
→ [SETUP_AND_TESTING.md#📊-performance-baseline](./SETUP_AND_TESTING.md#📊-performance-baseline)

---

## 📊 Document Relationship Map

```
START HERE
    ↓
README.md (5 min)
    ↓
    ├─→ CONTEXT_OVERVIEW.md (15 min)
    │       ↓
    │       ├─→ DATABASE_SCHEMA.md (detailed data model)
    │       ├─→ DEVELOPMENT_GUIDE.md (coding standards)
    │       └─→ ORGANISATIONS_MODULE.md (example module)
    │
    ├─→ DEVELOPMENT_GUIDE.md (20 min)
    │       ↓
    │       ├─→ SETUP_AND_TESTING.md (verify setup)
    │       ├─→ ADVANCED_EXAMPLES.md (complex patterns)
    │       └─→ PROJECT_ROADMAP.md (future features)
    │
    ├─→ PROJECT_ROADMAP.md (planning)
    │       ↓
    │       └─→ SETUP_AND_TESTING.md (launch prep)
    │
    └─→ ORGANISATIONS_QUICKSTART.md (user guide)
```

---

## ⚡ Quick Reference Card

### Common Commands
```bash
# Start development server
php spark serve

# Run migrations
php spark migrate

# Seed test data
php spark db:seed OrganizationTypeSeeder

# List all routes
php spark routes

# Run tests
php spark test

# Clear cache
php spark cache:clear

# Check migrations status
php spark migrate:status

# Create new migration
php spark make:migration CreateMyTable

# Create new model
php spark make:model MyModel

# Create new controller
php spark make:controller MyController
```

### Common Helpers
```php
user_id()              // Current user ID
user_role()            // Current user role
base_url($path)        // Get base URL
site_url($path)        // Get site URL
view($path, $data)     // Load view
redirect($path)        // Redirect
csrf_field()           // CSRF token
esc($text)             // Escape HTML
```

### Common Routes Pattern
```php
// List
$routes->get('resource', 'Controller::index');

// Show
$routes->get('resource/(:num)', 'Controller::show/$1');

// Create form
$routes->get('resource/create', 'Controller::create');

// Store
$routes->post('resource', 'Controller::store');

// Edit form
$routes->get('resource/(:num)/edit', 'Controller::edit/$1');

// Update
$routes->put('resource/(:num)', 'Controller::update/$1');

// Delete
$routes->delete('resource/(:num)', 'Controller::delete/$1');
```

---

## 📧 Support & Questions

### If you have questions about:
- **Architecture** → Refer to CONTEXT_OVERVIEW.md, then ask in team chat
- **Coding** → Refer to DEVELOPMENT_GUIDE.md, then pair program
- **Database** → Refer to DATABASE_SCHEMA.md, then ask database team
- **Testing** → Refer to SETUP_AND_TESTING.md, then ask QA
- **Organizations module** → Refer to ORGANISATIONS_MODULE.md & ADVANCED_EXAMPLES.md
- **Future features** → Refer to PROJECT_ROADMAP.md, then discuss with PM

---

## 🎓 Learning Path

### For Complete Beginners (2-3 days)
1. Day 1: README.md + CONTEXT_OVERVIEW.md (2 hours)
2. Day 2: DEVELOPMENT_GUIDE.md + setup (4 hours)
3. Day 3: Run SETUP_AND_TESTING.md (3 hours)

### For Experienced Backend Developers (1-2 days)
1. CONTEXT_OVERVIEW.md (1 hour)
2. DEVELOPMENT_GUIDE.md (1 hour)
3. DATABASE_SCHEMA.md (1 hour)
4. Hands-on: Setup & explore code (3-4 hours)

### For Frontend Developers (1 day)
1. CONTEXT_OVERVIEW.md (1 hour)
2. ORGANISATIONS_MODULE.md Views section (30 min)
3. Hands-on: Setup & customize views (5-6 hours)

### For New Team Lead/Manager (2-3 hours)
1. README.md (15 min)
2. CONTEXT_OVERVIEW.md (30 min)
3. PROJECT_ROADMAP.md (1 hour)
4. SETUP_AND_TESTING.md (1 hour)

---

## 📈 Documentation Maintenance

### Keep Updated
- Update PROJECT_ROADMAP.md quarterly
- Update CONTEXT_OVERVIEW.md when adding major features
- Update DEVELOPMENT_GUIDE.md when standards change
- Update DATABASE_SCHEMA.md when adding tables

### Version Info
```
Last Updated: 2024-01-16
Maintained By: Development Team
Review Schedule: Quarterly
Current Phase: Organizations Module Complete
```

---

## 🚀 Next Steps

**Just starting?**
1. Start with [README.md](./README.md)
2. Read [CONTEXT_OVERVIEW.md](./CONTEXT_OVERVIEW.md)
3. Follow [DEVELOPMENT_GUIDE.md#environment-setup](./DEVELOPMENT_GUIDE.md#environment-setup)

**Ready to code?**
1. Review [DEVELOPMENT_GUIDE.md](./DEVELOPMENT_GUIDE.md)
2. Study [DATABASE_SCHEMA.md](./DATABASE_SCHEMA.md)
3. Read [ORGANISATIONS_MODULE.md](./docs/ORGANISATIONS_MODULE.md) for examples

**Need to test?**
1. Follow [SETUP_AND_TESTING.md](./SETUP_AND_TESTING.md)
2. Use templates for test cases

**Planning next phase?**
1. Review [PROJECT_ROADMAP.md](./PROJECT_ROADMAP.md)
2. Sync with team on priorities

---

**Happy developing! 🚀**

For more help, check the relevant documentation file or ask your team lead.
