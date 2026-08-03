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

- Theme System
- Plugin System
- Blog
- Downloads
- Team Members
- Announcements
- Form Builder
- Newsletter
- Multi-language
- Multi-site
- API
- GraphQL
- Mobile App
- CLI Installer
- Auto Update
- Marketplace

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
