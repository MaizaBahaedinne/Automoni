# 📊 Schéma Relationnel - Automoni

## Entity Relationship Diagram (Simplifié)

```
                          ┌──────────────┐
                          │   USERS      │
                          │──────────────│
                          │ id (PK)      │
                          │ first_name   │
                          │ last_name    │
                          │ email        │
                          │ password     │
                          │ role         │◄─── Rôles: job_seeker, recruiter, admin
                          │ avatar       │
                          │ linkedin_id  │
                          │ status       │
                          └────┬─────────┘
                               │ (1:1)
                               │
                ┌──────────────┴──────────────┬─────────────────────┬─────────────────────┐
                │                             │                     │                     │
                ▼ (0:1)           (1:N)       ▼ (0:1)      (1:N)   ▼ (0:1)      (1:N)    ▼
          ┌─────────────┐   ┌────────────┐   ┌──────────┐    ┌─────────────────┐  ┌────────────┐
          │  PROFILES   │   │   SKILLS   │   │ COMPANY  │    │  ORGANIZATIONS  │  │   POSTS    │
          │─────────────│   │────────────│   │──────────│    │─────────────────│  │────────────│
          │ id          │   │ id         │   │ id       │    │ id              │  │ id         │
          │ user_id (FK)├──►│ user_id(FK)│   │ user_id(FK) │ id (PK)         │  │ user_id(FK)│
          │ headline    │   │ skill_name │   │ name     │    │ parent_id (FK) ◄──┤ content    │
          │ position    │   │ level      │   │ logo     │    │ type_id (FK)    │  │ visibility │
          │ cv_file     │   │ endorsed   │   │ website  │    │ name            │  │ created_at │
          │ avatar      │   │            │   │ email    │    │ logo            │  │            │
          │ completeness│   └────────────┘   │ phone    │    │ website         │  └────┬───────┘
          │ ...         │                    │ status   │    │ members_count   │       │ (1:N)
          └─────────────┘                    └───┬──────┘    │ ...             │       │
                                                 │           └─────────────────┘       │
                                                 │                   │                 │
              ┌──────────────┬────────────┬──────┴──┬────────┬──────┴──────┬─────────┴─────┐
              │              │            │         │        │             │               │
              ▼ (1:N)        ▼ (1:N)      ▼ (1:N) ▼ (1:N) ▼ (1:N)      ▼ (1:N)          ▼
          ┌────────────┐  ┌────────────┐  ┌───────────────┐  ┌──────────────────┐  ┌──────────────┐
          │   JOBS     │  │APPLICATIONS│  │  EXPERIENCES  │  │ORG_MEMBERS ◄─────┼──│ SKILLS       │
          │────────────│  │────────────│  │───────────────│  │──────────────────│  │──────────────│
          │ id         │  │ id         │  │ id            │  │ id               │  │ (Post detail)│
          │ company(FK)├─►│ job_id(FK) │  │ user_id (FK)  │  │ org_id (FK)      │  └──────────────┘
          │ user_id(FK)│  │ user_id(FK)│  │ company_id(FK)│  │ user_id (FK)     │
          │ title      │  │ status     │  │ title         │  │ role: owner      │
          │ description│  │ applied_at │  │ dates         │  │ manager / viewer │
          │ contract   │  │            │  │ description   │  │ joined_at        │
          │ salary     │  │            │  │               │  │                  │
          │ status     │  └────────────┘  └───────────────┘  └──────────────────┘
          │ views      │
          │ expires_at │     ┌──────────────┐  ┌──────────────┐  ┌──────────────────┐
          └────────────┘     │  EDUCATION   │  │CERTIFICATIONS│  │ORGANIZATION_TYPES │
                             │──────────────│  │──────────────│  │──────────────────│
              ┌────────────────│ id          │  │ id           │  │ id               │
              │ (1:N) │ user_id(FK)│       │  │ user_id(FK)  │  │ name (Company,  │
              │       └──────────────┘       │ issued_at    │  │ NGO, etc)        │
              │            ▲                 │ expires_at   │  └──────────────────┘
              │            │(1:N)            └──────────────┘
              ▼            │
          ┌────────────┐   │
          │ JOB_ALERTS │   │
          │────────────│   │
          │ id         │   │
          │ user_id(FK)├───►
          │ keywords   │
          │ location   │
          │ is_active  │
          └────────────┘

              ┌────────────────────────────────────────────────────────────────┐
              │             ORGANIZATION RELATIONS (NEW MODULE)                │
              ├────────────────────────────────────────────────────────────────┤
              │                                                                │
              │  Organizations (self-reference for hierarchy):                │
              │  ┌─────────────────────────────────────────────────────────┐  │
              │  │              ORGANIZATIONS (20+ fields)                 │  │
              │  ├─────────────────────────────────────────────────────────┤  │
              │  │┌ id (PK)                     ┌ parent_id (FK → self)   │  │
              │  ││ type_id (FK)  ◄─────────────┤    [For subsidiaries]   │  │
              │  ││ name / slug                 └ ... hierarchy support   │  │
              │  ││ logo / website / phone                                │  │
              │  ││ address / GPS (lat/lng)     ┌ ORG_SOCIAL_LINKS       │  │
              │  ││ employee_count              │ (social media urls)    │  │
              │  ││ industry / founded_at       └ 1:N relationship       │  │
              │  ││ status (active/inactive/)   ┌ ORG_CERTIFICATIONS     │  │
              │  ││ is_verified                 │ (audit certifications) │  │
              │  ││                             └ 1:N relationship       │  │
              │  │└ Created_at / Updated_at     ┌ ORG_PARTNERS (N:M)     │  │
              │  │                              │ (partnership links)    │  │
              │  └─────────────────────────────┘ └ Symmetric relationship│  │
              │                                                                │
              │  ORG_MEMBERS (N:M Users ↔ Organizations):                    │
              │  ┌─────────────────────────────────────────────────────────┐  │
              │  │ organization_id (FK) ──► Organizations                  │  │
              │  │ user_id (FK) ─────────► Users                          │  │
              │  │ role: owner/manager/viewer ──► Permission hierarchy    │  │
              │  │ joined_at                                              │  │
              │  └─────────────────────────────────────────────────────────┘  │
              │                                                                │
              └────────────────────────────────────────────────────────────────┘
```

---

## 📋 Tables et Relations - Détail

### **Core Entities**

```
┌─ USERS
│  └─ 1:1 ─────────► PROFILES
│  └─ 1:N ─────────► SKILLS
│  ├─ 1:N ─────────► EXPERIENCES
│  ├─ 1:N ─────────► EDUCATION
│  ├─ 1:N ─────────► CERTIFICATIONS
│  ├─ 1:N ─────────► LANGUAGES
│  ├─ 1:N ─────────► PROJECTS
│  ├─ 1:N ─────────► VOLUNTEERING
│  │
│  ├─ (Recruiter) ─► COMPANY (1:1 or 1:N owner)
│  │              ├─► JOBS (1:N)
│  │              └─► APPLICATIONS (indirect)
│  │
│  ├─ 1:N ─────────► APPLICATIONS
│  ├─ 1:N ─────────► JOB_ALERTS
│  ├─ 1:N ─────────► POSTS (social)
│  ├─ 1:N ─────────► POST_REACTIONS
│  ├─ 1:N ─────────► POST_COMMENTS
│  │
│  ├─ N:M ─────────► ORGANIZATIONS (via ORG_MEMBERS)
│  └─ 1:N ─────────► LINKEDIN_DATA (OAuth)
│
└─ COMPANY
   └─ 1:N ─────────► JOBS
```

### **Business Flow**

```
┌─────────────────┐
│  User (Login)   │
└────────┬────────┘
         │
    ┌────▼────────────────────────────┐
    │                                 │
┌───▼──────────────────┐      ┌──────▼──────────────┐
│   JOB_SEEKER Role    │      │  RECRUITER Role     │
├──────────────────────┤      ├─────────────────────┤
│ Browse Jobs          │      │ Create Company      │
│ Apply to Jobs        │      │ Post Jobs           │
│ Create Alerts        │      │ View Applications   │
│ Update Profile       │      │ Manage Candidates   │
│ View Company Info    │      │ Post to Feed        │
│ View Organizations   │      │ Manage Organization │
└─────────────────────┘      └────────────────────┘
```

---

## 🔄 Data Flow Examples

### **Example 1: Job Application Flow**
```
1. User (job_seeker)
   ├─ Browse /jobs
   ├─ View /jobs/{id}
   └─ POST /jobs/{id}/apply
       ├─ Create APPLICATION record
       │  └─ status: "pending"
       │
       ├─ Notify RECRUITER (email)
       │
       └─ Recruiter reviews at
           /company/applications
           ├─ SHORTLIST
           ├─ ACCEPT
           └─ REJECT
```

### **Example 2: Organization Hierarchy**
```
1. Recruiter creates:
   ORGANIZATION[1] "Global Tech"
   ├─ owner_user_id: 5 (recruiter)
   ├─ parent_id: NULL
   │
   ├─ Users add as members:
   │  ├─ User 5 → "owner" (permissions: all)
   │  ├─ User 8 → "manager" (permissions: edit org, view members)
   │  └─ User 12 → "viewer" (permissions: read-only)
   │
   └─ Create SUBSIDIARIES:
      ├─ ORGANIZATION[2] "Global Tech - France"
      │  ├─ parent_id: 1
      │  └─ owner_user_id: 5
      │
      └─ ORGANIZATION[3] "Global Tech - Germany"
         ├─ parent_id: 1
         └─ owner_user_id: 5

   Hierarchy Tree:
   Global Tech (level 0)
   ├─ Global Tech - France (level 1)
   └─ Global Tech - Germany (level 1)
```

### **Example 3: Social Feed Interaction**
```
1. User A creates POST
   ├─ POST[1]
   │  ├─ user_id: A
   │  ├─ content: "Excited to join Automoni!"
   │  └─ visibility: "public"
   │
   ├─ User B REACTS (like)
   │  └─ POST_REACTION
   │     ├─ post_id: 1
   │     ├─ user_id: B
   │     └─ reaction_type: "like"
   │
   ├─ User C COMMENTS
   │  └─ POST_COMMENT
   │     ├─ post_id: 1
   │     ├─ user_id: C
   │     └─ comment_text: "Great news!"
   │
   └─ User A REPLIES to User C's comment
      └─ POST_COMMENT
         ├─ post_id: 1
         ├─ user_id: A
         └─ reply_to: [POST_COMMENT from C]
```

---

## 🔐 Permission Matrix

```
┌──────────────────┬──────────────┬──────────────┬──────────────┐
│ Resource         │ job_seeker   │ recruiter    │ admin        │
├──────────────────┼──────────────┼──────────────┼──────────────┤
│ /jobs            │ LIST, VIEW   │ CRUD, MANAGE │ FULL ACCESS  │
│ /jobs/apply      │ CREATE       │ N/A          │ FULL ACCESS  │
│ /profile         │ CRUD (own)   │ CRUD (own)   │ FULL ACCESS  │
│ /company         │ VIEW         │ CRUD (own)   │ FULL ACCESS  │
│ /alerts          │ CRUD         │ N/A          │ FULL ACCESS  │
│ /posts           │ CRUD (own)   │ CRUD (own)   │ FULL ACCESS  │
│ /organizations   │ VIEW LIST    │ CRUD (own)   │ FULL ACCESS  │
│ ORG_MEMBERS      │ VIEW (own)   │ MANAGE (own) │ FULL ACCESS  │
└──────────────────┴──────────────┴──────────────┴──────────────┘
```

---

## 💾 Data Integrity Rules

```
┌─────────────────────────────────────────────────────────┐
│ Constraints & Business Rules                           │
├─────────────────────────────────────────────────────────┤
│ • Users.email                   → UNIQUE               │
│ • Jobs.company_id → Companies   → NOT NULL, CASCADE    │
│ • APPLICATIONS.job_id → Jobs    → CASCADE DELETE       │
│ • JOB_ALERT.user_id → Users     → CASCADE DELETE       │
│ • ORG_MEMBERS(org_id, user_id)  → UNIQUE constraint   │
│ • ORGANIZATIONS.parent_id (self)→ No cycles allowed   │
│ • ORGANIZATIONS.type_id         → NOT NULL, CASCADE   │
│ • Users.role                    → Must be job_seeker  │
│                                   recruiter, or admin  │
│ • Applications.status           → pending/accepted/... │
│ • Soft deletes                  → deleted_at field    │
└─────────────────────────────────────────────────────────┘
```

---

## 📈 Scaling & Future Relations

```
Potential Future Entities:
├─ NOTIFICATIONS        (user notifications center)
├─ MESSAGES             (direct messaging)
├─ RECOMMENDATIONS      (similar jobs/candidates)
├─ ANALYTICS            (job views, application rate)
├─ COMPANY_FOLLOWERS    (users following companies)
├─ SAVED_JOBS           (job bookmarks)
├─ SAVED_CANDIDATES     (candidate bookmarks)
├─ TEAM_MEMBERS         (organization teams)
├─ ROLES                (custom organization roles)
├─ CONTENT_MODERATION   (flagged content)
└─ AUDIT_LOGS           (system activity logging)
```

---

## 🔍 Query Patterns

### **Common Queries**

```sql
-- User with full profile
SELECT u.*, p.* FROM users u
LEFT JOIN profiles p ON u.id = p.user_id
WHERE u.id = ?;

-- Jobs with company details
SELECT j.*, c.name as company_name, c.logo 
FROM jobs j
JOIN companies c ON j.company_id = c.id
WHERE j.status = 'active'
ORDER BY j.created_at DESC;

-- Organization hierarchy
SELECT * FROM organizations
WHERE parent_id = ? AND status = 'active';

-- Organization members with users
SELECT om.*, u.first_name, u.last_name, u.email
FROM organization_members om
JOIN users u ON om.user_id = u.id
WHERE om.organization_id = ?;

-- User's organizations
SELECT DISTINCT o.* FROM organizations o
JOIN organization_members om ON o.id = om.organization_id
WHERE om.user_id = ? AND om.role IN ('owner', 'manager');
```

---

**Last Updated:** 2024-01-16  
**Version:** 1.0 (with Organizations module)
