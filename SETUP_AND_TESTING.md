# ✅ Setup Checklist & Testing Guide

## 🚀 Pre-Deployment Checklist

### Phase 1: Environment & Dependencies
- [ ] PHP version 8.1+ installed (`php -v`)
- [ ] Composer installed (`composer --version`)
- [ ] MySQL/MariaDB running (`mysql --version`)
- [ ] Git installed (`git --version`)
- [ ] Working directory: `/Users/gouiaaepmaizamalak/Automoni`

### Phase 2: Project Setup
- [ ] Dependencies installed: `composer install`
- [ ] Environment file configured: `.env` created with DB credentials
- [ ] Database created in MySQL
- [ ] Application key generated (if needed)
- [ ] Writable directories have correct permissions:
  - [ ] `writable/cache/` - `755`
  - [ ] `writable/logs/` - `755`
  - [ ] `writable/session/` - `755`
  - [ ] `writable/uploads/` - `755`

### Phase 3: Database Setup
- [ ] All migrations executed: `php spark migrate`
- [ ] Database tables created (15 core + 6 Organizations = 21 tables)
- [ ] Optional: Seed test data: `php spark db:seed OrganizationTypeSeeder`

### Phase 4: Verify Core Systems
- [ ] Development server starts: `php spark serve`
- [ ] Home page loads: `http://localhost:8080`
- [ ] All controllers are accessible
- [ ] Error pages display correctly

### Phase 5: Organizations Module Verification
- [ ] Migration files exist (6 files)
- [ ] Model files exist (6 files)
- [ ] Controller files exist (2 files)
- [ ] Routes configured in `app/Config/Routes.php`
- [ ] Views exist (3 files)

### Phase 6: Authentication Test
- [ ] User registration works
- [ ] User login works
- [ ] Session persists across pages
- [ ] Logout clears session

### Phase 7: Final Checks
- [ ] No PHP errors in logs
- [ ] No database connection errors
- [ ] All routes responding with 200 or appropriate status codes
- [ ] CSRF protection active on forms

---

## 🧪 Quick Testing Guide

### Test 1: Server Startup
```bash
cd /Users/gouiaaepmaizamalak/Automoni
php spark serve

# Expected output:
# CodeIgniter v4.x.x Command Line Tool - Server
# Server running on http://127.0.0.1:8080
# Press Control+C to stop
```

### Test 2: Database Connection
```bash
php spark db:exec "SELECT 1"

# Expected output:
# Row 1: 1
```

### Test 3: List All Routes
```bash
php spark routes

# Look for organization routes:
# - GET        organizations
# - GET        organizations/(:num)
# - GET        organizations/hierarchy
# - POST       organizations
# - POST       organizations/(:num)/members
# - PUT        organizations/(:num)
# - DELETE     organizations/(:num)
```

### Test 4: Check Migrations
```bash
php spark migrate:status

# All migrations should show "Batch 1" or higher (green checkmark)
```

### Test 5: API Testing (Using curl)

#### 5a. Get Organizations List (Public)
```bash
curl -X GET http://localhost:8080/organizations \
  -H "Accept: application/json"

# Expected response:
# {
#   "status": 200,
#   "message": "Organizations retrieved",
#   "data": [...]
# }
```

#### 5b. Get Organization Details (Public)
```bash
curl -X GET http://localhost:8080/organizations/1 \
  -H "Accept: application/json"

# Expected response:
# {
#   "status": 200,
#   "data": {
#     "id": 1,
#     "name": "Organization Name",
#     "slug": "organization-name",
#     ...
#   }
# }
```

#### 5c. Create Organization (Requires Auth)
```bash
# First, get authentication token/session
# Then:
curl -X POST http://localhost:8080/organizations \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -H "Cookie: PHPSESSID=your_session_id" \
  -d '{
    "name": "Test Org",
    "slug": "test-org",
    "type_id": 1,
    "email": "org@example.com"
  }'

# Expected response:
# {
#   "status": 201,
#   "message": "Organization created",
#   "data": { "id": 1, ... }
# }
```

#### 5d. Get Organization Hierarchy (Public)
```bash
curl -X GET http://localhost:8080/organizations/hierarchy \
  -H "Accept: application/json"

# Expected response:
# {
#   "status": 200,
#   "data": [
#     {
#       "id": 1,
#       "name": "Parent Org",
#       "children": [
#         {
#           "id": 2,
#           "name": "Subsidiary",
#           "children": []
#         }
#       ]
#     }
#   ]
# }
```

### Test 6: View Testing (Browser)

#### 6a. Organizations List Page
```
Navigate to: http://localhost:8080/organizations
Expected:
✓ Page loads without errors
✓ Organization cards display
✓ Filter/search functionality works
✓ Pagination controls present
```

#### 6b. Organization Details Page
```
Navigate to: http://localhost:8080/organizations/1
Expected:
✓ Organization details display
✓ Logo displays (if exists)
✓ Members list visible
✓ Social links present
✓ Hierarchy breadcrumbs show
```

### Test 7: Permission Testing

#### 7a. Owner Permissions
```
As organization owner:
- [ ] Can edit organization details
- [ ] Can add/remove members
- [ ] Can change member roles
- [ ] Can access organization settings
```

#### 7b. Manager Permissions
```
As organization manager:
- [ ] Can edit organization details
- [ ] Can add members
- [ ] Cannot remove members (owner only)
- [ ] Cannot delete organization
```

#### 7c. Viewer Permissions
```
As organization viewer:
- [ ] Can view organization details
- [ ] Cannot edit anything
- [ ] Cannot add members
- [ ] Cannot delete organization
```

### Test 8: File Upload Testing

#### 8a. Upload Organization Logo
```
Steps:
1. Navigate to organization edit form
2. Select image file (PNG, JPEG, WebP, SVG)
3. Name, email, other fields...
4. Click Save

Expected:
✓ File uploaded successfully
✓ Logo displays on organization page
✓ File stored in writable/uploads/
✓ File name format: org_[ID]_[timestamp].[ext]
```

#### 8b. Invalid File Handling
```
Steps:
1. Try uploading oversized file (>5MB)
2. Try uploading invalid format (.pdf, .docx)

Expected:
✓ Error message displayed
✓ Form doesn't submit
✓ User can retry with valid file
```

### Test 9: Hierarchy Cycle Prevention

#### 9a. Prevent Self-Reference
```
Steps:
1. Create organization "Parent"
2. Try to set parent_id = 1 (itself)

Expected:
✓ Error: "Cannot move organization to itself"
✓ Organization not updated
```

#### 9b. Prevent Child-as-Parent
```
Steps:
1. Create "Parent" (ID: 1)
2. Create "Child" with parent_id = 1 (ID: 2)
3. Try to set Parent's parent_id = 2 (creating cycle)

Expected:
✓ Error: "Cannot move organization to its own child"
✓ Parent remains unchanged
```

### Test 10: Search & Filter

#### 10a. Search by Name
```
Navigate to: http://localhost:8080/organizations?q=tech
Expected:
✓ Only organizations with "tech" in name/description display
✓ Results update in real-time (if JS enabled)
✓ Clear button resets filter
```

#### 10b. Filter by Type
```
Navigate to: http://localhost:8080/organizations?type=1
Expected:
✓ Only organizations of type 1 display
✓ Total count updates
```

#### 10c. Filter by Industry
```
Navigate to: http://localhost:8080/organizations?industry=technology
Expected:
✓ Only organizations in tech industry display
```

---

## 🐛 Debugging Checklist

### If Organizations Page Returns 404
```bash
# 1. Check route exists
php spark routes | grep organization

# 2. Check controller file exists
ls -la app/Controllers/OrganizationController.php

# 3. Check method exists
grep -n "public function index" app/Controllers/OrganizationController.php

# 4. Check namespace
head -20 app/Controllers/OrganizationController.php | grep namespace
```

### If Database Query Fails
```bash
# 1. Check table exists
php spark db:exec "SHOW TABLES LIKE 'organizations%';"

# 2. Check table structure
php spark db:exec "DESCRIBE organizations;"

# 3. Check migrations ran
php spark migrate:status | grep -i organization

# 4. Check for errors
tail -50 writable/logs/log-*.log | grep -i "error\|exception"
```

### If Upload Fails
```bash
# 1. Check directory writable
ls -ld writable/uploads/
# Should show: drwxr-xr-x (755)

# 2. Check PHP upload limits
php -r "echo ini_get('upload_max_filesize') . PHP_EOL; echo ini_get('post_max_size');"
# Should be >= 5MB for org logos

# 3. Check uploaded files
ls -la writable/uploads/ | head -20
```

### If Authentication Fails
```bash
# 1. Check session driver
grep "sessionDriver" app/Config/Session.php

# 2. Check session directory
ls -ld writable/session/

# 3. Check session data
php -r "session_start(); print_r(\$_SESSION);"

# 4. Check AuthFilter
tail -20 writable/logs/log-*.log | grep -i "auth"
```

---

## 📊 Performance Baseline

### Expected Response Times (Development)

```
Route                           Expected Time    DB Queries
─────────────────────────────────────────────────────────
GET /organizations              < 100ms          1-2
GET /organizations/1            < 100ms          2-3
GET /organizations/hierarchy    < 200ms          5-10
POST /organizations             < 200ms          2-3
PUT /organizations/1            < 200ms          2-3
DELETE /organizations/1         < 150ms          2-3
GET /organizations/1/members    < 100ms          2
POST /organizations/1/members   < 150ms          3-4
```

If times are higher:
- [ ] Check database indexes: `SHOW INDEX FROM organizations;`
- [ ] Review slow query log: `mysql -u root -p -e "SHOW PROCESSLIST;"`
- [ ] Check if queries running in loop (N+1 problem)
- [ ] Consider query caching

---

## 🔐 Security Verification

### CSRF Protection
```
Navigate to any form page:
Expected:
✓ Form contains hidden CSRF token
✓ `csrf_field()` output present in HTML
✓ POST/PUT/DELETE requests include token
```

### Input Validation
```
Try invalid data submission:
Expected:
✓ Form field validation errors display
✓ Data not saved to database
✓ User can correct and resubmit
```

### Authorization Checks
```
Try accessing with insufficient permissions:
Expected:
✓ 403 Forbidden response
✓ No sensitive data exposed
✓ Error message appropriate
```

### XSS Prevention
```
Try submitting: <script>alert('xss')</script>
Expected:
✓ Script tags escaped in output
✓ Alert doesn't trigger
✓ Text displays literally
```

### SQL Injection Prevention
```
Try submitting: ' OR '1'='1
Expected:
✓ Treated as literal string value
✓ No unexpected data returned
✓ No database errors exposed
```

---

## 📋 Test Cases by Feature

### Organizations CRUD
- [ ] Create new organization
- [ ] Read organization details
- [ ] Update organization info
- [ ] Delete organization (soft delete)
- [ ] List organizations with pagination
- [ ] Search organizations by name
- [ ] Filter by type, industry, verification status

### Organization Hierarchy
- [ ] Set parent organization
- [ ] View subsidiaries
- [ ] Get organization breadcrumbs
- [ ] View full hierarchy tree
- [ ] Move organization to different parent
- [ ] Prevent circular hierarchies

### Members Management
- [ ] Add member to organization
- [ ] Remove member
- [ ] Change member role (owner/manager/viewer)
- [ ] Only owners can manage members
- [ ] Members see appropriate permissions
- [ ] Member list shows join date

### File Management
- [ ] Upload organization logo
- [ ] View uploaded logo
- [ ] Replace logo with new file
- [ ] Delete logo
- [ ] Validate file type/size
- [ ] Generate secure file paths

### Partner Relationships
- [ ] Create partnership between organizations
- [ ] Delete partnership
- [ ] View partner organizations
- [ ] Symmetric relationship (A-B and B-A)
- [ ] Prevent self-partnerships

### Social Links
- [ ] Add social media links
- [ ] Display social links on profile
- [ ] Update social links
- [ ] Remove social links
- [ ] Validate URL format

### Certifications
- [ ] Add certification to organization
- [ ] Show certification status (active/expired)
- [ ] Update certification details
- [ ] Remove certification
- [ ] Filter by certification type

---

## 📝 Test Results Template

```markdown
## Test Session: [Date/Time]
Environment: Development
Tester: [Your Name]

### Environment Tests
- Server Status: ✅ / ❌
- Database Connection: ✅ / ❌
- File Permissions: ✅ / ❌

### Organizations Module Tests
- List Page: ✅ / ❌
- Detail Page: ✅ / ❌
- Create Form: ✅ / ❌
- Update: ✅ / ❌
- Delete: ✅ / ❌
- Hierarchy: ✅ / ❌
- Members: ✅ / ❌
- Upload: ✅ / ❌

### Security Tests
- CSRF Protection: ✅ / ❌
- Input Validation: ✅ / ❌
- Authorization: ✅ / ❌
- XSS Prevention: ✅ / ❌

### Performance Tests
- List Page Load: [XXXms]
- Detail Page Load: [XXXms]
- Create Page Load: [XXXms]
- Average Response Time: [XXXms]

### Issues Found
1. [Description]
   - Severity: High/Medium/Low
   - Status: Open/Resolved
   - Notes: [Additional info]

### Sign-Off
Overall Status: Ready for Production / Needs Fixes
Tester Signature: ________________
Date: ________________
```

---

## 🚦 Go/No-Go Decision Matrix

### Ready for Production (Green Light ✅)
All of the following are true:
- [ ] All core tests passing
- [ ] No critical bugs found
- [ ] Performance acceptable
- [ ] Security checks passed
- [ ] Database integrity verified
- [ ] All permissions working correctly
- [ ] File uploads secure
- [ ] Documentation complete
- [ ] Team approval obtained

### Needs More Work (Red Light ❌)
Any of the following are true:
- [ ] Critical bugs present
- [ ] Performance unacceptable
- [ ] Security vulnerabilities found
- [ ] Database issues
- [ ] Authorization/permission issues
- [ ] File upload issues
- [ ] Missing functionality

### Conditional (Yellow Light ⚠️)
- [ ] Minor issues found but documented
- [ ] Workarounds available
- [ ] Known limitations documented
- [ ] Team consensus on proceeding

---

## 📚 Related Documentation

- [CONTEXT_OVERVIEW.md](./CONTEXT_OVERVIEW.md) - Project architecture
- [DATABASE_SCHEMA.md](./DATABASE_SCHEMA.md) - Data model
- [DEVELOPMENT_GUIDE.md](./DEVELOPMENT_GUIDE.md) - Coding standards
- [ORGANISATIONS_MODULE.md](./docs/ORGANISATIONS_MODULE.md) - Module details
- [ADVANCED_EXAMPLES.md](./docs/ADVANCED_EXAMPLES.md) - Advanced usage

---

**Last Updated:** 2024-01-16  
**Version:** 1.0  
**Status:** Active
