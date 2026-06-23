# Handoff — dumpcat.com sample sites

Last updated: 2026-06-23. Read this first when resuming, especially on a new computer.

## Where things stand (DONE)
- **Live site:** https://dumpcat.com/ (coming-soon page).
  Templates previewed at `/samples/template-01/` and `/samples/template-02/` once deployed.
- **Repo:** https://github.com/galtanonhub/dumpcat.com — **public**, branch `main`.
- Both templates are fully built and committed to GitHub including their `_site/` builds.

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
   - Template 01: contents of `samples/template-01/_site/` → `public_html/samples/template-01/`
   - Template 02: contents of `samples/template-02/_site/` → `public_html/samples/template-02/`

**Permanent fix:** open a ticket with the host — "Git Version Control deployments stay stuck
in 'queued'; please restart the deployment queue / queueprocd." After that, **Deploy HEAD
Commit** works normally and manual copying is no longer needed.

## How to work on a template
```bash
cd samples/template-01        # or template-02
npm install                   # first time on a machine
npm run preview               # local dev at localhost:8080 (links work)
npm run build                 # rebuild _site/ for deploy (never use npm start locally)
```

**Shortcut:** double-click `preview-template-01.bat` or `preview-template-02.bat` in the
repo root to start the preview server in its own window. Keep that window open while working.

After editing: `npm run build`, commit, push, then manual-copy to cPanel (see deploy workaround).

## Templates

**Template 01 — Clean Light** → copy `samples/template-01/`
- White/light bg · Navy + safety orange · Barlow Condensed + Inter
- **Hero:** Split layout — copy left, garage door photo right (fill height, object-fit cover)
  - Hero photo lives at `src/img/` — passthrough copied to `_site/img/`
  - Current photo: `garage-door-template-pic-01.jpg` (1080×720, 144 KB)
- **Nav:** Logo | frosted rectangle pill with links centered | phone button — Reviews link removed
- **Homepage flow:** Hero → Trust strip (orange bg) → Services (3-col card grid) → Why Us (checklist + emergency box, equal height) → Reviews (4 cards, 2×2) → FAQ (3-col numbered card grid, all visible) → Areas (navy bg section with frosted chips) → CTA band (centered, inward-pointing arrows flanking heading)
- **Services page:** 2-col card grid with emoji icon, number badge, full description, CTA button
- **About page:** 2-col — story + why-us cards left (orange left-border), credentials card right
- **Contact page:** Info card left (orange left-border style), quote form right — `method="POST"`, honeypot, action comment for Formspree/Web3Forms, email field added
- **Service Areas page:** Google Maps embed + 4 county cards (Orange, Osceola, Seminole, Lake)
- **Footer:** Social icons (FB, IG, YT, X, Google) — inert `#` links · Reviews link removed
- **Good for:** Plumbing, HVAC, pest control, general home services

**Template 02 — Bold Dark** → copy `samples/template-02/`
- Dark bg (#111827) · Electric yellow · DM Sans · Utility bar above nav
- **Hero:** Split layout — niche-specific widget/graphic left, content right (swap widget per client)
- **Homepage sections:** Trust strip → Service cards (yellow bg) → Why Us → Reviews (3) → FAQ accordion → Areas chips → CTA band
- **Services page:** 2-column card grid with icon, number, full description per service
- **About page:** 2-column card layout (story + how we work left, credentials right)
- **Contact page:** Info cards left (styled), quote form right — form prepped with `method="POST"`, honeypot, action comment
- **Service Areas page:** Google Maps iframe + 4 county cards
- **Footer:** Social icons (FB, IG, YT, X, Google) — inert `#` links
- ⚠️ Brand name in `nav.njk` + `footer.njk` is hardcoded HTML (`Your Name <em>Electric</em>`) — update manually alongside `site.json`
- **Good for:** Electrician, solar, security, tech-forward trades

To request a new sample: *"Build me a [niche] sample using Template 01"* (or 02).

## Adding a new sample niche
1. Copy the closest template folder into `samples/<new-niche>/`
2. Update `src/_data/site.json` — business name, city, phone, geo
3. Update `src/_data/services.json`, `areas.json`, `reviews.json`, `faq.json`
4. Swap hero image in `src/img/` (resize to ~1080px wide JPEG before committing)
5. Change colors in `style.css` `:root` if desired
6. Set `pathPrefix` in `eleventy.config.js` to `/samples/<new-niche>/`
7. Add a copy task in `.cpanel.yml`
8. `npm install` → `npm run preview` to test → `npm run build` before committing

The SEO baseline (JSON-LD, OG tags, sitemap, noindex) comes along automatically.

## Resuming on a NEW computer — setup checklist
1. Install: Node.js, Git, GitHub CLI (`gh`), Claude Code.
2. `gh auth login` → log in as **galtanonhub**.
3. Clone the repo:
   ```bash
   git clone https://github.com/galtanonhub/dumpcat.com.git
   cd dumpcat.com
   ```
4. Install dependencies for each template you'll work on:
   ```bash
   cd samples/template-01 && npm install
   cd ../template-02 && npm install
   ```
5. Double-click `preview-template-01.bat` (or 02) to start the local server.
6. **Bring Claude's memory over** (optional): copy
   `C:\Users\Chanel\.claude\projects\C--Users-Chanel-robotpets\memory\`
   from the old machine to the same path on the new one for full context continuity.
   Without it, this HANDOFF.md has all the essentials.
