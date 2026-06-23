# Handoff — dumpcat.com sample sites

Last updated: 2026-06-23. Read this first when resuming, especially on a new computer.

## Where things stand (DONE)
- **Live site:** https://dumpcat.com/ (coming-soon page).
  Templates previewed at `/samples/template-01/` and `/samples/template-02/` once deployed.
- **Repo:** https://github.com/galtanonhub/dumpcat.com — **public**, branch `main`.
- **First sample built:** "Your Name Garage Door Repair" (placeholder name), Central Florida
  (Orlando metro, 407). Eleventy static site with the full SEO baseline (LocalBusiness JSON-LD,
  OG/Twitter, sitemap, robots, `noindex` while it's unsold inventory).

## What this project is
DragonWorkflows (agency) builds sample/demo websites to sell. **dumpcat.com is a temporary
holding domain**: coming-soon page at root, each demo at `/samples/<niche>/`. When a sample
sells, it moves to its own domain — and we **delete it from this repo** (and its `.cpanel.yml`
copy task) so dumpcat only ever holds samples still parked here.

## Hosting / cPanel
- cPanel user: `dumpcat`, docroot `/home/dumpcat/public_html/`.
- Git Version Control clone lives at `/home/dumpcat/repositories/dumpcat` (no `.com` suffix).
- Repo is public, so the cPanel clone URL is the plain
  `https://github.com/galtanonhub/dumpcat.com.git` — **no token, no SSH key.**

## ⚠️ Deploy workaround (important)
This host's cPanel **deploy queue is stalled** — "Deploy HEAD Commit" jobs sit in "queued"
forever. Until the host restarts the queue (`queueprocd`), **deploys are manual**:

1. Push changes to GitHub (agent does this).
2. In cPanel **File Manager**, copy the changed files from `repositories/dumpcat/` into
   `public_html/`:
   - Root files: `index.html`, `robots.txt`, `favicon.svg` → `public_html/`
   - Sample: contents of `samples/garage-door-repair/_site/` → `public_html/samples/garage-door-repair/`

**Permanent fix:** open a ticket with the host — "Git Version Control deployments stay stuck
in 'queued'; please restart the deployment queue / queueprocd." After that, **Deploy HEAD
Commit** works normally and manual copying is no longer needed.

## How to work on a template
```bash
cd samples/template-02        # or template-01
npm install                   # first time on a machine
npm run preview               # local dev at localhost:8080 (links work)
npm run build                 # rebuild _site/ for deploy (never use npm start locally)
```
After editing: `npm run build`, commit, push, then manual-copy (see deploy workaround above) until the queue is fixed.

## Templates

Each new sample should use a different template so they don't all look alike. Reference by name:

**Template 01 — Clean Light** → copy `samples/template-01/`
- White bg · Navy/orange · Barlow Condensed + Inter · Split hero · Card grid services
- Good for: plumbing, HVAC, pest control, general home services

**Template 02 — Bold Dark** → copy `samples/template-02/`
- Dark bg (#111827) · Electric yellow · DM Sans · Utility bar above nav
- **Hero:** Split layout — niche-specific widget/graphic left, content right (swap widget per client)
- **Homepage sections:** Trust strip → Service cards (yellow bg) → Why Us → Reviews → FAQ accordion → Areas → CTA band
- **Services page:** 2-column card grid with icon, number, full description per service
- **About page:** 2-column card layout (story + how we work left, credentials right)
- **Contact page:** Info cards left (styled), quote form right — form prepped with `method="POST"`, honeypot field, and action comment for Formspree/Web3Forms
- **Service Areas page:** Google Maps iframe + 4 county cards
- **Footer:** Social icons (Facebook, Instagram, YouTube, X, Google) — inert links, update per client
- ⚠️ Brand name in `nav.njk` and `footer.njk` is hardcoded HTML (`Your Name <em>Electric</em>`) — update manually alongside `site.json`
- Good for: electrician, solar, security, tech-forward trades

To request a new sample: *"Build me a [niche] sample using Template 01"* (or 02).

## Adding a new sample niche
1. Copy the closest template folder into `samples/<new-niche>/`
2. Update `src/_data/site.json` — business name, city, phone, geo
3. Update `src/_data/services.json`, `areas.json`, `reviews.json`
4. Change colors in `style.css` `:root` if desired
5. Set `pathPrefix` in `eleventy.config.js` to `/samples/<new-niche>/`
6. Add a copy task in `.cpanel.yml`
7. `npm install` → `npm run preview` to test → `npm run build` before committing

The SEO baseline (JSON-LD, OG tags, sitemap, noindex) comes along automatically.

## Resuming on a NEW computer — setup checklist
1. Install: Node.js, Git, GitHub CLI (`gh`), Claude Code.
2. `gh auth login` → log in as **galtanonhub**.
3. Recreate the sites folder at the **same path** `C:\Users\galta\sites` (keeping the path
   identical lets Claude's project memory match), then clone the repos into it:
   ```bash
   cd C:/Users/galta/sites
   git clone https://github.com/galtanonhub/dumpcat.com.git
   git clone https://github.com/galtanonhub/singledads.com.git
   git clone https://github.com/galtanonhub/doorproblems.com.git
   git clone https://github.com/galtanonhub/robotpets.com.git
   ```
4. **Bring Claude's memory over** (optional but recommended for full continuity): copy the
   folder `C:\Users\galta\.claude\projects\C--Users-galta-sites\memory\` from the old machine
   to the same path on the new one. That carries all project notes, preferences, and this
   project's history. Without it, this HANDOFF.md still has the dumpcat-specific essentials.
5. In each sample you'll work on: `npm install`.
