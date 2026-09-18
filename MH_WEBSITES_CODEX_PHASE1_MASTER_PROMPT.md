# MH Websites Phase 1 — Codex Master Implementation Brief

You are the implementation and testing developer for the project located at:

`C:\xampp\htdocs\MH_Websites`

This is the official company website and Phase 1 quotation catalogue for **MH WEBSITES (Pty) Ltd**.

## 1. Non-negotiable project boundary

- Work only inside `C:\xampp\htdocs\MH_Websites`.
- Never access, inspect or modify `C:\xampp\htdocs\ABA_EduFlow_v2_Upgrade`.
- ABA EduFlow is a separate project with a separate Git history and Codex session.
- Do not run destructive Git commands.
- Preserve the current clean baseline commit.
- Before implementation, verify `git status` is clean and create a branch named:
  `redesign/software-first-phase1`
- Commit coherent checkpoints after each implementation phase.
- Do not push to a remote repository unless the user explicitly requests it.

## 2. Product and business decisions already approved

### Website model

MH Websites is a **hybrid business website**:

- Digital services use consultation and quotation workflows.
- Physical products use a Phase 1 product quotation catalogue and quote basket.
- Real e-commerce payments, live inventory and order fulfilment are deferred to Phase 2.

### Company positioning

Position MH Websites as a **software company first**. The technology-products store is an important but secondary division.

Primary audience:

- Small and growing South African businesses.

Secondary audience:

- Schools, colleges and training organisations.

Broader audience:

- NGOs, public-sector teams and other organisations needing practical digital solutions.

The core message should communicate:

> Custom software, websites and digital systems built around how your organisation works.

Do not use vague claims, invented client results or fake performance statistics.

## 3. Approved information architecture

Use the following main navigation:

1. Home
2. Services
3. Systems
4. Technology Store
5. About
6. Contact

Include a visible primary navigation CTA: **Discuss Your Project**.

Replace the existing overlapping “Solutions” concept with **Systems**.

### Required pages

- Home
- Services
- Systems overview
- Individual page for every approved system
- Technology Store catalogue
- Product details
- Quote Basket
- Quote Request
- Quote Confirmation
- About
- Contact
- Privacy and POPIA Notice
- Website Terms
- Product Quotation Terms
- Delivery Information
- Accessible 404 page

The four listed legal/trust pages are the only legal pages required in Phase 1. Do not add extra legal pages without approval.

## 4. Approved service categories

The Services page must use these five primary categories:

1. **Custom Software Development**
   - Business systems
   - Portals
   - Workflow tools
   - Internal platforms

2. **Website & E-commerce Development**
   - Company websites
   - Online catalogues
   - E-commerce development
   - Hosting and maintenance

3. **Learning Platforms & Moodle**
   - Moodle installation
   - Branding
   - Course structures
   - LMS support

4. **Dashboards, Data & Automation**
   - KPI dashboards
   - Capture tools
   - Reporting
   - Exports
   - Workflow automation

5. **IT Support & Systems Integration**
   - Technical support
   - Configuration
   - System integration
   - Ongoing assistance

The physical-product store is a separate business division, not a sixth software service.

## 5. Approved Systems portfolio

Create `systems.php` plus an individual page for each system:

- `systems/eduflow.php`
- `systems/clinicflow.php`
- `systems/peopleflow.php`
- `systems/learnhub.php`
- `systems/ai-assistant.php`
- `systems/insighthub.php`

Approved public names and honest statuses:

| System | Public description | Status |
|---|---|---|
| EduFlow | Education Management Platform | Pilot |
| ClinicFlow | Clinic Management System | In Development |
| PeopleFlow | HR Management System | Prototype |
| LearnHub LMS | Learning Management Platform | In Development |
| Business AI Assistant | AI-enabled Business Support | Prototype |
| InsightHub | Performance and Reporting Dashboard | In Development |

Each page must contain:

- System purpose
- Business problem addressed
- Intended users
- Core features
- Honest status badge
- Technology/capability summary without unsupported claims
- Consistent conceptual visual area
- CTA: **Request a Similar Solution**
- Optional future link location for **Watch Demo**

Do not use client names, client logos, real student/patient/employee data, email addresses, ID numbers or confidential records.

### System imagery rule

Use a consistent conceptual visual system for all six systems. The visuals should be premium branded 3D product concepts, not fabricated screenshots. Clearly and discreetly identify them as **Product concept visual**.

Do not mix real client screenshots with generated concepts in Phase 1. Build a predictable asset contract such as:

`assets/images/systems/<system-slug>/concept.webp`

If final concept assets are not yet present, build polished, accessible fallback frames that preserve layout without showing the word “placeholder” publicly. Do not invent client data inside fallback UI.

## 6. Visual design direction

Use a **premium 3D SaaS style** with controlled motion.

Approved brand colours:

- Primary blue: `#1F51FF`
- Accent orange: `#FF4800`
- White: `#FFFFFF`
- Add dark navy and neutral greys only as supporting accessible colours.

Design requirements:

- Dark-blue dimensional hero.
- Clean white content sections.
- Premium device/product mockup presentation.
- Strong editorial spacing and hierarchy.
- Controlled shadows and depth.
- Selective orange calls to action.
- Realistic, professional South African business tone.
- Avoid repetitive grids where a narrative layout is stronger.
- Avoid excessive glass panels, gradients, rounded cards, hover lifts and floating decorations.
- The result must not look like a generic Bootstrap template.

Do not use Bootstrap in Phase 1. Use custom modular CSS and vanilla JavaScript.

### Motion

Use controlled premium motion only:

- Subtle scroll entrances
- Small hover depth
- Restrained 3D tilt where appropriate
- No continuous distracting movement
- No heavy parallax
- No excessive glowing or rotating objects
- Fully honour `prefers-reduced-motion`

### Accessibility

Meet WCAG AA for practical Phase 1 implementation:

- Never use white normal-sized text on `#FF4800` where it fails contrast.
- Do not use low-contrast muted text.
- Include a skip link.
- Use semantic landmarks.
- All inputs require associated labels and names.
- Keyboard-accessible navigation, search, filters, quote basket and dialogs.
- Proper focus management for mobile navigation and filter drawers.
- Escape-to-close, backdrop, body-scroll lock and focus return.
- Use ARIA only where native semantics are insufficient.
- Announce dynamic search counts, basket changes and submission states with live regions.
- Use `lang="en-ZA"` consistently.

## 7. Homepage requirements

The homepage order must be:

1. Software-company hero
2. Business problems MH Websites solves
3. Core services
4. Systems showcase
5. Development process
6. Founder/company credibility
7. Collaborations
8. Technology Store promotion
9. Final consultation CTA

Hero actions:

- Primary: **Discuss Your Project**
- Secondary: **Explore Our Systems**

Do not make the store compete with the software-company positioning in the first viewport.

## 8. Founder and collaboration content

Use the heading **Meet the Founder**, not “Meet Our Team.”

Approved founder content only:

- **Mthokozisi Hlomela Mnyobe**
- Founder and Managing Director
- IT Graduate
- CompTIA Network+ certified
- Software and web developer
- Experience in custom systems, Moodle, dashboards and IT support
- Existing professional photograph
- Reserved future LinkedIn link location
- CTA: **Discuss Your Project**

Do not invent an institution, degree title, employment history, awards or additional qualifications.

### Collaborations

Heading: **Organisations We Collaborate With**

Display company names with professional monograms only:

- MH — MH Websites (Pty) Ltd
- BB — Basic Blue Trading 773 CC
- HG — Halisi Group (Pty) Ltd

Do not display “placeholder logo.” Do not invent descriptions, relationship claims or external links. Keep the asset structure easy to replace with approved logos later.

## 9. Phase 1 Technology Store

The store is a **quotation catalogue**, not a payment store.

Remove from public presentation:

- Sample prices
- Fake discounts
- Public stock quantities
- Fake stock badges
- Same-day dispatch claims
- Fake checkout/payment fields
- Simulated payment success
- Unverified product compatibility claims

Product cards and details should show:

- Product name
- Category
- Brand
- Useful specifications
- Representative image
- Clear “Representative product image” wording where required
- CTA: **Add to Quote Basket**
- CTA: **Request Compatibility Help**
- Message: **Price and availability confirmed on quotation**

Keep these categories:

- Toner & Ink
- Drum Units
- Printers
- Accessories

Refactor the sample catalogue so content cannot be mistaken for live inventory. Preserve useful sample structure only where it supports the interface.

## 10. Search, filters and URL state

Implement an accessible catalogue-search experience:

- Search product name, product code, brand, category and relevant specifications.
- Use token-based ranking rather than catalogue-order “relevance.”
- Accessible combobox/listbox behaviour.
- Arrow-key navigation and Escape handling.
- Announce search results.
- Synchronise category, search, filter and sort state with URL query parameters.
- Make filtered views shareable and restorable with browser Back/Forward.
- Prevent category chips and sidebar filters from drifting out of sync.
- Show active filters and a clear reset action.
- Responsive mobile filter drawer with proper focus management.

Useful Phase 1 filters:

- Category
- Brand
- Product type
- Printer type where relevant
- Colour where relevant
- Page-yield band where verified

Do not claim typo-tolerant AI search unless it is actually implemented.

## 11. Manual compatibility confirmation

Do not provide automatic compatibility guarantees in Phase 1.

Create a compatibility-assistance workflow that collects:

- Printer brand
- Exact printer model
- Current cartridge/drum code if known
- Customer notes
- Optional future upload field location for a printer/cartridge photograph

Add the submitted printer details to the quotation request. Use wording that MH Websites will manually confirm compatibility before issuing the final quotation.

## 12. Quote Basket workflow

Replace Cart/Checkout terminology and pages with:

- Quote Basket
- Request Quote
- Quote Confirmation

Approved flow:

`Browse → Compatibility help if needed → Add items → Review quantities → Enter contact/delivery details → Submit request → Receive quotation reference`

Requirements:

- No card fields.
- No payment tabs.
- No fake payment or order-success copy.
- Generate a server-side quotation reference such as `MHQ-YYYYMMDD-XXXX`.
- Save quotations and items in MySQL.
- Send an email notification when configured.
- Provide an optional WhatsApp action with a concise prefilled message and quotation reference.
- Provide a printable quotation-request summary.
- Validate product IDs and quantities on the server.
- Do not trust localStorage values as authoritative.
- Do not accept prices from the browser because Phase 1 has no public prices.

Collect only necessary customer data:

- Full name
- Organisation (optional)
- Email
- Phone
- Delivery town/city
- Province
- Postal code (optional)
- Preferred contact method
- Additional notes
- POPIA/privacy consent

## 13. Technical architecture

Migrate the prototype from duplicated static HTML to maintainable PHP while preserving clean URLs where practical.

Use:

- PHP 8.2 compatible code
- Reusable PHP includes/components for header, footer and shared content
- Modular CSS rather than one fragile override-heavy file
- Vanilla JavaScript modules for interactions
- MySQL with PDO and prepared statements
- Server-side validation
- CSRF protection
- Output escaping
- Session hardening appropriate to the quote basket
- Honeypot and basic rate limiting for public forms
- Environment-based configuration
- `.env.example` with no secrets
- Never commit real database or email credentials

Suggested structure (adapt if inspection reveals a better equivalent):

```text
assets/
  css/
    tokens.css
    base.css
    components.css
    utilities.css
    pages/
  js/
    navigation.js
    store.js
    quote-basket.js
    search.js
    motion.js
  images/
  logos/
  systems/
config/
includes/
services/
systems/
store/
legal/
database/
  migrations/
```

Create migration(s) for quotation requests, quotation items and any minimal supporting data. Do not build the Phase 2 payment, customer-account, live-stock or order-fulfilment schema yet.

Email notification must fail safely: the quote must remain saved even if email delivery is unavailable. Never expose mail errors or credentials to the customer.

WhatsApp is optional and must not be the only durable quote-submission route.

## 14. Contact and service enquiries

Implement a real service-enquiry endpoint using the same security standards as quote requests.

Service enquiry fields:

- Full name
- Organisation
- Email
- Phone
- Selected service
- Project summary
- Preferred contact method
- Privacy consent

Store enquiries durably and send a configurable notification. Do not display a fake success message if persistence fails.

## 15. Metadata, performance and resilience

- Unique page titles and descriptions.
- Canonical URL support configured for `mhwebsites.co.za` without breaking localhost.
- Open Graph metadata.
- Organisation structured data using only verified company facts.
- Product structured data only when the information is truthful and appropriate for a quotation catalogue.
- Sitemap and robots file.
- Responsive images with dimensions.
- Lazy-load below-the-fold imagery.
- Avoid CSS `@import` for fonts.
- Prevent layout shift from injected navigation.
- Essential navigation and footer must render server-side.
- Graceful behaviour when JavaScript is unavailable where practical.
- Prepare deployment notes for compression, caching, CSP and security headers; do not assume Apache production configuration can be changed automatically.

## 16. Implementation phases and Git checkpoints

Work in the following order.

### Checkpoint 0 — Preparation

- Verify clean repository.
- Create `redesign/software-first-phase1`.
- Inventory existing assets and pages.
- Write a short implementation plan before editing.

### Checkpoint 1 — Architecture and design system

- PHP includes/layout
- Routing/page structure
- Tokens, base styles and shared components
- Accessible navigation/footer
- No major page left broken

Commit message:

`Phase 1A: establish software-first architecture and design system`

### Checkpoint 2 — Corporate experience

- Homepage
- Services
- Founder
- Collaborations
- About
- Contact interface

Commit message:

`Phase 1B: rebuild corporate website experience`

### Checkpoint 3 — Systems portfolio

- Systems overview
- Six individual system pages
- Consistent concept-visual asset slots
- Honest statuses and system CTAs

Commit message:

`Phase 1C: add client-safe systems portfolio`

### Checkpoint 4 — Quote catalogue UX

- Store catalogue
- Search/filter/URL state
- Product detail
- Manual compatibility assistance
- Quote basket UI
- Mobile catalogue and basket

Commit message:

`Phase 1D: rebuild store as accessible quote catalogue`

### Checkpoint 5 — Secure quotation and enquiry backend

- Database migrations
- Quote submission
- Quote reference
- Quote confirmation/print summary
- Email notification abstraction
- WhatsApp option
- Contact/service enquiry persistence
- CSRF, validation, escaping and safe failures

Commit message:

`Phase 1E: implement secure quotation and enquiry workflows`

### Checkpoint 6 — Legal, accessibility and hardening

- Four approved legal pages
- Accessibility remediation
- Metadata/sitemap/robots
- Responsive fixes
- Documentation and deployment notes

Commit message:

`Phase 1F: complete accessibility legal and launch readiness`

## 17. Verification requirements

Before claiming completion:

- Run PHP syntax checks on every PHP file.
- Run JavaScript syntax/lint checks available without adding unnecessary dependencies.
- Run `git diff --check`.
- Verify every internal link and asset path.
- Verify all required pages return expected HTTP status on XAMPP.
- Test desktop widths around 1440 and 1280 pixels.
- Test tablet widths around 1024 and 768 pixels.
- Test mobile widths around 430, 390 and 360 pixels.
- Test keyboard-only navigation.
- Test reduced-motion mode.
- Test search suggestions and URL restoration.
- Test quote-basket quantity boundaries.
- Test invalid product IDs and tampered client data.
- Test CSRF rejection.
- Test database failure and email failure states.
- Confirm no real secrets, client names or private records are committed.

If browser automation is unavailable, explicitly report the limitation and provide a manual test checklist instead of claiming rendered visual verification.

## 18. Required documentation

Create or update:

- `README.md`
- `.env.example`
- `documentation/PHASE1_SETUP.md`
- `documentation/PHASE1_TEST_CHECKLIST.md`
- `documentation/PHASE2_ECOMMERCE_ROADMAP.md`

The Phase 2 roadmap should cover real pricing, supplier-approved product imagery, live inventory, secure payments, orders, invoices/receipts, delivery, customer notifications and fulfilment. Do not implement Phase 2 during this task.

## 19. Stop conditions

Stop and ask before proceeding if:

- A decision would change the approved business model.
- Real SMTP, database or payment credentials are required.
- Client-sensitive data is found.
- A destructive migration or file deletion is proposed.
- An unapproved framework or dependency would materially alter the project.
- The requested visual assets do not exist and a truthful fallback cannot be created.

## 20. First response required from Codex

Do not start editing immediately.

First respond with:

1. Confirmation that the repository is clean.
2. Confirmation that the current directory is `C:\xampp\htdocs\MH_Websites`.
3. The proposed branch command.
4. A concise implementation plan mapped to Checkpoints 1–6.
5. Any genuine blockers or contradictions in this brief.
6. A list of files expected to be created, moved or replaced.

Wait for explicit approval before making the first source-code change.
