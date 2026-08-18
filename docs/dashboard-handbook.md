# AfraaCMS Dashboard Handbook

This handbook walks through every screen in your admin dashboard — what each one is for, exactly what to fill in, and the small behaviors that aren't obvious the first time you see them. Keep it open in a second tab while you work.

---

## Getting around the dashboard

Everything lives behind one login. Once you're in, the left-hand menu is your map — it's grouped the same way this handbook is.

### Logging in

Go to `/admin/login` on your site's address, sign in with the email and password your administrator gave you, and you'll land on the Dashboard — a quick overview of recent activity.

> **Good to know**
> - What you can see and edit depends on your **Role**. If a screen mentioned in this handbook doesn't appear in your menu, your role doesn't include it — ask a Super Admin to grant it under [Users & Roles](#users--roles).
> - Forgot your password? Use the "Forgot password" link on the login screen rather than asking someone else to reset it for you.

---

## Patterns you'll see everywhere

Almost every module in the dashboard is built from the same handful of building blocks. Learn these once and every screen from here on will feel familiar.

- **Draft vs. Published** — Content saved as Draft only you can see in the dashboard; it never appears on the live site. Switch it to Published when it's ready for visitors.
- **Trash & Restore** — Deleting a page, project, news post, story, team member, or a few other content types doesn't erase it right away — it moves to that module's Trash screen, where you can Restore it or delete it permanently.
- **Active / Featured toggles** — An Active toggle switched off hides something from the public site without deleting it, handy for retiring a team member or album temporarily. Featured just means "show this first."
- **The image picker** — Any Image field opens your Media Library — pick an existing photo or upload a new one on the spot. If the spot has a fixed shape (like a banner), you'll crop it right there before it saves.
- **Drag to reorder** — Lists with a small grip handle (⠿) on the left — menu items, sections, gallery photos — reorder by dragging. The new order saves automatically; there's no separate Save button for it.
- **The SEO panel** — Pages, Projects, News, Stories, and Galleries each have a collapsible SEO section for the page title/description search engines show. Every field is optional — leave it blank and your sitewide defaults (set once in [Settings](#settings)) are used instead.

---

## Building your site

The four modules that shape the pages visitors actually see: what pages exist, what's on them, how people navigate between them, and the images behind it all.

### Pages & Sections

A **Page** is one URL on your site (Home, About, Contact...). It's built from a stack of **Sections** — reusable content blocks you arrange in order, like building with blocks:

```
Hero          — "Building a Poverty-Free, Educated & Peaceful Society"
Cards         — "What We Do"
Projects      — auto-lists your Projects module
Gallery Albums — recent photos
Contact       — the contact form
```

**Creating a page**

| # | Field | Required? | Notes |
|---|-------|-----------|-------|
| 1 | Title | Required | The name shown in menus and browser tabs, e.g. "About Us" |
| 2 | Slug | Required | The web address, auto-filled from the Title but editable, e.g. `about` → yoursite.com/about |
| 3 | Page Banner | Optional | A banner image and short eyebrow text shown at the top of this page only |
| 4 | SEO panel | Optional | Search-result title/description for this page |
| 5 | Status | Required | Draft or Published |
| 6 | Template | Required | Default or Full Width |
| 7 | Publish Date | Optional | Leave blank to publish immediately |

**Adding sections to a page**

Open a page and go to its Sections screen. Each section has a **Type** (Hero, Rich Text, Cards, Gallery, Call to Action, FAQ, Timeline, Stats, Image + Text, Contact, Projects, Gallery Albums, Latest News, Success Stories, Photo Slider, Team...) — the type decides which fields you'll see below it. Most types share: Heading, Subheading, an optional Button, and an **Anchor ID** (so a menu link can jump straight to it, e.g. `/about#history`).

> **Good to know**
> - **Cards** and **Contact** sections have a **Background** choice — Dark or Light. If a dark card block feels out of place sitting between two light sections on a page, open that section and switch it to Light Background. It defaults to Dark, matching how it's always looked.
> - Sections marked "All Active Albums / Members" update themselves automatically as you add new galleries or team members — no need to revisit the page. Switch to "Specific" only when you want to hand-pick exactly what shows.
> - Sections and their photo/card items drag-to-reorder and save instantly.
> - Deleting a section deletes everything inside it (its cards, its items) — there's no undo, so double-check before confirming.
> - Deleted pages go to Trash first (a separate screen) before they're gone for good.

### Menus

Controls the Header and Footer navigation links across your whole site.

**Adding a menu item**

1. **Label** (required) — the text visitors click, e.g. "Get Involved"
2. **Icon** (optional)
3. **Link Type** (required) — Internal (one of your own Pages) or External (any URL)
4. **Page** — for Internal links, pick from a dropdown; the Label and URL fill in for you
5. **Parent Item** (optional) — nest this under another top-level item to create a dropdown, like About's sub-menu
6. **Visible / New Tab** (toggles)

> **Good to know**
> - Drag items to reorder or nest them under a parent — it saves as you drop it, no Save button needed.
> - To rename a menu itself (not its items), use the "Edit Menu" button on its item-management screen — it opens in a small popup.

### Banners

The large promotional image strips shown at fixed spots: Homepage, standard Page tops, Call-to-Action blocks, and Popups.

1. **Placement** (required) — which of the four spots this banner belongs to
2. **Title / Subtitle / Button** (optional)
3. **Image** (required) — Homepage and Page banners crop to a wide letterbox shape automatically
4. **Starts At / Ends At** (optional) — schedule a banner to run only within a date range
5. **Priority** (required) — the lowest number wins if more than one banner is active for the same spot at once

> **Good to know**
> Only **one** banner shows per placement at any moment — the active, in-schedule one with the lowest Priority number. Deleting a banner is permanent, so if you just want to pause one, turn its Active toggle off instead.

### Media Library

Every image you've ever uploaded, in one searchable place — independent of which page or module it's used on.

> **Good to know**
> - Use **Replace** on an image to swap the file while keeping the same record — anything using that image site-wide (a banner, a team photo) updates instantly, without you having to re-attach it everywhere.
> - You can drag up to 20 files in at once to bulk-upload.
> - This same library is what opens any time you click an Image field elsewhere in the dashboard.

---

## Content

The modules for your ongoing content: what you're working on, what's happening, who's involved, and the photos to prove it.

### Projects *(+ Project Categories)*

Your programs and initiatives — shown on the public Projects listing and linkable from anywhere on the site.

1. **Title** (required)
2. **Slug** (required) — auto-filled, editable
3. **Excerpt** (optional) — short summary shown on the listing grid
4. **Content** (optional) — the full write-up, rich text editor
5. **SEO panel** (optional)
6. **Category** (optional) — for filtering on the public site
7. **Status** (required) — Draft / Published
8. **Photo/Video Gallery** (optional) — link one of your Gallery albums
9. **Cover Image** (required)
10. **Featured** (toggle) — featured projects list first

> **Good to know**
> - Project Categories are managed right on the Projects list via a small popup — no separate page. Deleting a category never deletes its projects; they just lose that label.
> - Deleted projects go to Trash first, with Restore available.

### News *(+ News Categories)*

Dated announcements and updates — same shape as Projects, plus a Published Date that controls display order.

1. **Title, Slug, Excerpt, Content, SEO panel** — same as Projects, above
2. **Category** (optional)
3. **Status** (required)
4. **Published Date** (required) — newest shows first on the public News page
5. **Cover Image** (required)
6. **Featured** (toggle)

### Stories

Success stories and case studies — the same shape as News, with an optional link back to the Project the story came from, so visitors can browse stories by program.

### Galleries

Photo (and video) albums. These power the public Gallery page and also act as the photo source for Hero backgrounds and Photo Slider sections elsewhere.

1. **Title, Slug, Description, SEO panel**
2. **Cover Image**
3. **Active** (toggle) — off disables it everywhere, including as a section's photo source
4. **Show in Public Gallery** (toggle) — turn off if this album is only meant to feed a Hero/Slider section, not have its own public page

> **Good to know**
> Adding photos one at a time gets slow — use **Bulk Add Photos** instead: pick several existing library images or upload new ones together, with an optional caption on each, in one go.

### Team *(+ Team Categories)*

Your board, staff, and volunteers.

1. **Name** (required)
2. **Role / Position, Country, Service Period** (optional)
3. **Bio** (optional)
4. **Category** (optional) — group into e.g. "Board" vs "Volunteers" so a page section can show just one group
5. **Photo, Active toggle, Display Order**

### Featured Visitors

Notable guests *you* choose to showcase — different from the Visitor Book below, which is submitted by the public.

1. **Name** (required)
2. **Organization** (optional)
3. **Country, Visit Date** (required)
4. **Photo, Active toggle, Display Order**

---

## Engagement

What comes in from your visitors — nothing to create here, only to read and act on.

### Contact Inbox

Every submission from your site's contact form lands here.

> **Good to know**
> - Unread messages are highlighted; opening one to view it automatically marks it read.
> - "Reply by Email" opens the message in *your own* email app (via a `mailto:` link) — there's no reply box inside the dashboard.
> - Deleting a message is permanent — there's no Trash for the inbox.

### Visitor Book

Opinions and comments visitors submit publicly about a project or their visit — you moderate them before (or instead of) showing them anywhere.

> **Good to know**
> Each entry shows Pending, Approved, or Rejected. Use the **Approve** / **Reject** buttons to change its status; deleting is permanent.

---

## Programs

If your site runs training courses, these five modules connect: a **Student** takes a **Course** through an **Enrollment**, which can lead to an issued **Certificate**. **Donations** is a separate, standalone ledger.

### Courses

Your catalog of training programs.

1. **Course Name** (required)
2. **Description** (optional)
3. **Duration** (required) — free text, e.g. "02 Years"
4. **Status** (required) — only Active courses appear when creating an Enrollment

### Students

Enrollee records.

1. **Full Name, Father's Name, Mother's Name, Date of Birth** (required)
2. **Address** (required)
3. **Phone, Email** (optional)

> **Good to know**
> A **Student Code** is generated for you automatically once saved — you don't set it.

### Enrollments

Links one Student to one Course and tracks their result.

1. **Student, Course** (required) — pick from dropdowns
2. **Session** (required) — e.g. "2024–2025"
3. **Roll Number, Registration Number, Admission/Completion Dates** (optional)
4. **Result Status** (required) — Pending / Passed / Failed
5. **Grade, Grade Point, Grade Scale** (optional)

> **Good to know**
> The **Issue Certificate** button only appears once Result Status is saved as Passed — set the result, save, then reopen the enrollment to issue it.

### Certificates

Two ways a certificate gets created: automatically from a Passed Enrollment (above), or manually here for a one-off certificate not tied to a course.

1. **Recipient Name** (required)
2. **Program/Course** (optional, free text)
3. **Related Project** (optional)
4. **Issue Date** (required)
5. **Status** (required) — Valid / Revoked

> **Good to know**
> Every certificate gets a public verification link and a downloadable QR code, so anyone can confirm it's genuine. Revoking a certificate immediately breaks that public verification.

### Donations

A manual ledger for donations received offline — there's no online payment gateway built in, this is for recording what came in by bank transfer, cash, etc.

1. **Donor Name** (required)
2. **Donor Email** (optional) — a receipt only gets emailed if this is filled in
3. **Amount, Currency, Payment Method, Donation Date** (required)
4. **Related Project** (optional)
5. **Status** (required) — Completed / Refunded

---

## Site setup

The two screens that shape the whole site at once, rather than one piece of content.

### Settings

One page, organized into tabs down the left. Anything here affects the entire site immediately on save.

- **General & Branding** — Site Name, Tagline, Homepage (which Page shows at your root address), Logo, Favicon, and one **Brand Color** — every accent color across your public site derives from this single value.
- **Contact & Social** — Email, Phone, Address, Google Map link, and your social profile URLs — these feed the footer and Contact sections sitewide.
- **Footer, Gallery & News** — Copyright text, how many footer links/projects show, gallery display style, and how many News items appear on the homepage.
- **Analytics & System** — Google Analytics/Tag Manager/Facebook Pixel IDs, Maintenance Mode, and reCAPTCHA keys (both keys are required together to activate spam protection on your contact form).

> **Good to know**
> - The **Developer** tab is visible but locked to Super Admin only — everyone else can look but not edit it.
> - If you see a banner saying "You have view-only access," your role can see Settings but not save changes — ask a Super Admin.

### SEO & Sitemap

Separate from Settings — this is the sitewide default for search engines and your sitemap, not per-page SEO (that lives on each page/project/etc.'s own SEO panel).

1. **Default Meta Robots** (required) — whether search engines are allowed to index the site by default
2. **Include Projects / Include Galleries** (toggles) — whether those show up in your sitemap
3. **robots.txt Contents** (optional)

---

## Administration

Who can log in, and what they're allowed to touch.

### Users & Roles

**Users** are the people who can log into this dashboard. Each one has exactly one **Role**, and a Role is just a named set of permission checkboxes grouped by module.

1. **Name, Email** (required)
2. **Password** (required on create) — leave blank when editing to keep the current one
3. **Role** (required)
4. **Active** (toggle)

> **Good to know**
> - Use the **Deactivate** button on the users list to lock someone out temporarily without deleting their account — Delete is permanent everywhere here, there's no Trash for Users.
> - To give someone access to a module missing from their menu, edit their **Role** and check that module's permission — not the individual user.

---

*AfraaCMS Dashboard Handbook — covers every admin module as of this build. If a screen you're looking at doesn't match this description, your site's version may have moved on since this was written.*
