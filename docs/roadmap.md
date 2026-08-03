# AfraaCMS Development Roadmap

> Version: 1.0
> Company: AfraaWorld
> Product: AfraaCMS
> Tech Stack: Laravel + Blade + Tailwind CSS + Alpine.js + MySQL

---

# Project Goal

Build a lightweight, production-ready, reusable CMS using Laravel that can run efficiently on shared hosting while allowing administrators to manage all website content dynamically without modifying code.

---

# Development Rules

## Technology

- Laravel (Latest Stable)
- Blade
- Tailwind CSS
- Alpine.js
- MySQL

## Do NOT Use

- Livewire
- Filament
- Vue
- React
- Inertia

## Coding Standards

- PSR-12
- SOLID
- RESTful Controllers
- Form Request Validation
- Blade Components
- Service Layer
- Thin Controllers
- Reusable Code
- No Duplicate Logic

---

# Folder Structure

```
app/
    CMS/
        Services/
        Rendering/
        Cache/
        Helpers/
        Menu/
        SEO/

resources/
    views/
        admin/
        frontend/
        components/
        layouts/

routes/
database/
storage/
```

---

# Progress Status (as of 2026-08-03)

| Phase | Status | Notes |
|---|---|---|
| 1 — Project Foundation | ✅ Done | |
| 2 — Admin Framework | ✅ Done | Dashboard rebuilt with real stat cards, recent-activity feed, quick actions |
| 3 — Authentication & Permissions | ✅ Done | Spatie Permission, Super Admin/Editor roles, policies throughout |
| 4 — CMS Core | ✅ Done | `app/CMS/{Services,Rendering,Cache,Helpers,Menu,SEO}` |
| 5 — Settings Module | ✅ Done | |
| 6 — Media Library | ✅ Done | Spatie Media Library, Intervention Image, WebP |
| 7 — Menu Builder | ✅ Done | Includes nested/dropdown menu items (Parent Item selector) |
| 8 — Page Manager | ✅ Done | + Trash & Restore (soft deletes) |
| 9 — Section Engine | ✅ Done | |
| 10 — Blade Components | ✅ Done | |
| 11 — Homepage | ✅ Done | |
| 12 — Inner Pages | ✅ Done | |
| 13 — Banner Management | ✅ Done | |
| 14 — Gallery | ✅ Done | |
| 15 — Projects | ✅ Done | + Trash & Restore (soft deletes) |
| 16 — Contact | ✅ Done | |
| 17 — SEO | ✅ Done | Canonical, Open Graph, Twitter Card, robots.txt, sitemap.xml |
| 18 — Performance | ✅ Done | Frontend caching (pages/menus/settings/etc.), WebP conversion, lazy-loading on below-the-fold images |
| 19 — Security | ✅ Done except 2FA | CSRF, hardened password policy, rate limiting, hardened session cookies, Activity Log/audit trail, secure uploads. **2FA explicitly deferred by product decision**, not started |
| 20 — Shared Hosting Deployment | ⏭ Deferred | Explicitly skipped for now (no backup solution, cron, or deploy docs yet) — revisit when ready to deploy |
| 21 — Testing | ✅ Done | 184 automated feature tests passing across every module |
| 22 — Release v1.0 | ⏸ Blocked | Waiting on Phase 20 (`Shared hosting verified`, `Production deployed`) and a manual `Responsive verified` pass; all other checklist items are satisfied |

**Built beyond this original plan:** Trash & Restore for Pages/Projects, an Activity Log/audit trail (spatie/laravel-activitylog across all content models), a cache-service refactor (`CachesForFrontend` trait, removing duplicated cache logic), pagination + search on admin index pages, compact create/edit forms, collapsible SEO fields, and a sitemap.xml fix for a false-positive IDE parsing error.

---

# Phase 1 — Project Foundation

## Objective

Prepare the Laravel application.

## Tasks

- Install Laravel
- Configure environment
- Configure MySQL
- Configure Mail
- Configure Cache
- Configure Queue
- Configure Filesystem
- Configure Timezone
- Install Breeze (Blade)
- Install Tailwind CSS
- Install Alpine.js
- Verify project runs

## Deliverables

- Working Laravel application
- Authentication working
- Git repository initialized

---

# Phase 2 — Admin Framework

## Objective

Build reusable admin layout.

## Tasks

- Admin layout
- Sidebar
- Top navigation
- Breadcrumb
- Dashboard
- Cards
- Tables
- Alerts
- Modal component
- Dropdown component
- Responsive layout

## Deliverables

Reusable admin interface.

---

# Phase 3 — Authentication & Permissions

## Objective

Secure administration panel.

## Packages

- Spatie Permission

## Roles

- Super Admin
- Editor

## Tasks

- Role management
- Permission middleware
- Admin authorization
- Policies

## Deliverables

Secure admin area.

---

# Phase 4 — CMS Core

## Objective

Create CMS architecture.

## Create

```
app/CMS

Services

Rendering

Helpers

Cache

Menu

SEO
```

## Deliverables

CMS service layer.

---

# Phase 5 — Settings Module

## Database

settings

## Features

- Site Name
- Logo
- Favicon
- Contact
- Footer
- Social Links
- Email
- Analytics

## Deliverables

Settings management.

---

# Phase 6 — Media Library

## Packages

- Spatie Media Library
- Intervention Image

## Features

- Upload
- Replace
- Delete
- Search
- WebP
- Thumbnail
- Media Picker

## Deliverables

Reusable media system.

---

# Phase 7 — Menu Builder

## Tables

menus

menu_items

## Features

- Nested menu
- Drag & Drop
- Visibility
- External URL
- Internal URL
- Icons
- Ordering

## Deliverables

Dynamic menu system.

---

# Phase 8 — Page Manager

## Table

pages

## Features

- Title
- Slug
- Status
- Publish Date
- Template

## Deliverables

Dynamic pages.

---

# Phase 9 — Section Engine

## Tables

sections

section_items

## Section Types

- Hero
- Rich Text
- Cards
- Gallery
- CTA
- FAQ
- Timeline
- Stats
- Image + Text
- Contact

## Deliverables

Dynamic page builder.

---

# Phase 10 — Blade Components

## Create

Reusable components

Examples

- Hero
- Cards
- Gallery
- CTA
- FAQ
- Timeline
- Counter
- Slider

## Deliverables

Reusable frontend components.

---

# Phase 11 — Homepage

## Convert

Static homepage into CMS.

Everything editable.

## Deliverables

Homepage managed from admin.

---

# Phase 12 — Inner Pages

Convert

- About
- History
- Registration
- Contact
- Gallery
- Projects

## Deliverables

All pages dynamic.

---

# Phase 13 — Banner Management

## Features

- Homepage Banner
- Page Banner
- CTA Banner
- Popup Banner

## Deliverables

Banner module.

---

# Phase 14 — Gallery

## Features

- Albums
- Photos
- Videos
- Sorting
- Lightbox

## Deliverables

Gallery management.

---

# Phase 15 — Projects

## Features

- Category
- Project
- Featured
- Gallery
- Status

## Deliverables

Projects module.

---

# Phase 16 — Contact

## Features

- Contact Form
- Spam Protection
- Email Notification
- Inbox
- Read Status

## Deliverables

Contact management.

---

# Phase 17 — SEO

## Features

- Meta Title
- Description
- Canonical
- Open Graph
- Twitter
- Robots
- Sitemap

## Deliverables

SEO ready.

---

# Phase 18 — Performance

## Tasks

- Config Cache
- Route Cache
- View Cache
- Menu Cache
- Settings Cache
- Image Optimization
- Lazy Loading

## Deliverables

Optimized CMS.

---

# Phase 19 — Security

## Tasks

- CSRF
- Validation
- Authorization
- XSS Protection
- Activity Log
- Rate Limiting
- Secure Uploads

## Deliverables

Production security.

---

# Phase 20 — Shared Hosting Deployment

## Tasks

- Storage Link
- Queue
- Cron
- Optimize
- Composer
- SSL
- Error Logs
- Backup

## Deliverables

Production deployment.

---

# Phase 21 — Testing

## Test

- Authentication
- Permissions
- CRUD
- Uploads
- Menu
- Pages
- Gallery
- Contact
- SEO

Fix bugs.

---

# Phase 22 — Release v1.0

## Checklist

- Performance verified
- Security verified
- Responsive verified
- Shared hosting verified
- Documentation updated
- Production deployed

Release

AfraaCMS v1.0

---

# Future Roadmap (v2)

> **Direction confirmed 2026-08-03:** AfraaCMS is no longer just AfraaWorld's own site — it is being sold to other clients. First real client: an NGO whose reference site is https://demo1.rsufbd.com (a 6-page site: Home, About [with anchor sub-sections: History, Registration, Vision & Mission, Areas of Operation, What We Do, Success], Get Involved, News, Gallery, Contact Us — currently a placeholder/skeleton demo, no advanced features like search, multi-language, newsletter, or team profiles yet). This client's real needs now drive v2 prioritization below, instead of guessing.

## Tier 1 — Next up (drives real client delivery)

- **Team Members** — decided: build this now. Every NGO/org site (including this client's) eventually needs a Board of Directors / Founder / Staff page, even though it isn't on the reference demo yet. Small, well-scoped CRUD module — same shape as Projects/Gallery (name, designation, photo, bio, sort order). Low effort, immediate value for the first paying client.
- **Blog / News** — promoted here because the reference site has a "News" nav item. Reuses the existing Page/Section engine almost directly (title, slug, status, template, SEO already built).
- **Anchor sub-navigation for Pages** — a small gap found while reviewing the reference site: its About page jumps between sub-sections (History, Vision & Mission, etc.) via in-page anchors. AfraaCMS's Section engine doesn't yet support an anchor id + jump-nav pattern; needed to replicate that About-page structure.
- **Announcements** — same CRUD shape as Team Members, reuses Banner/Section display patterns.
- **Newsletter** — reuses the Contact module's pattern (spam protection, storage, admin inbox); only new piece is an outbound email/provider integration.
- **Form Builder** — hold until the above are done. A true generic form builder is bigger than it looks; consider scoping it to "one custom form per page" rather than a full builder unless a client has a concrete need for many different forms.

## Tier 2 — Decide deliberately per client, don't default into them

- **Multi-language** — do this early in whichever client engagement first needs it, not late. Retrofitting translations onto every content model after the fact is far more expensive than designing for it up front.
- **API** — build only once there's an actual consumer (mobile app, third-party integration). Laravel Sanctum + API Resources fits cleanly when that day comes.
- **Downloads** — small module (file + description + category), low priority until a client asks for it specifically.

## Tier 3 — Genuine multi-client/product infrastructure (deferred)

- **Theme System** — decided: **defer**. With only one real client so far, a swappable/multi-tenant theme system would be designed from guesses, not real requirements — this project's own coding standard is "don't design for hypothetical future requirements." Each new client is, for now, its own AfraaCMS install customized via Settings/Pages/Sections/Menus (the existing per-site content model already covers this). Revisit once 2–3 real client sites reveal what actually needs to vary between them (colors and logo only? full layout? whole page structures?).
- **Plugin System** — only valuable once third-party developers need to extend the CMS; needs a stable public API surface first.
- **Multi-site** — running many client sites off one codebase/tenant-isolated instance; substantial infra and security implications. Only pursue once the one-install-per-client model becomes a real bottleneck.
- **Marketplace** — depends on Plugin System and Theme System existing first; far downstream.
- **Mobile App** — depends on the API existing first; only pursue with a concrete mobile use case.
- **GraphQL** — an alternative/addition to the REST API; only worth it if a specific consumer needs it. Usually over-engineering for a CMS.
- **CLI Installer** — valuable once you're distributing AfraaCMS to many independent installs; low priority for a handful of manually-deployed clients.
- **Auto Update** — same; only matters once there are multiple independent installs to keep in sync.

---

# Git Workflow

```
main

develop

feature/admin

feature/auth

feature/settings

feature/media

feature/menu

feature/pages

feature/sections

feature/gallery

feature/projects

feature/contact

feature/seo

release/v1.0
```

---

# Commit Convention

```
feat(admin): create admin dashboard

feat(menu): build menu manager

feat(settings): implement system settings

feat(page): create dynamic pages

fix(menu): resolve nested ordering bug

refactor(section): move rendering to service
```

---

# Definition of Done

A phase is complete only when:

- Feature works correctly
- Validation exists
- Authorization exists
- Responsive UI
- No duplicated code
- Services used correctly
- Blade components reused
- Migrations successful
- No PHP errors
- No console errors
- Ready for production
