# Handoff — dumpcat.com sample sites

Last updated: 2026-06-23. Read this first when resuming, especially on a new computer.

## Where things stand (DONE)
- **Live site:** https://dumpcat.com/ (coming-soon page) and
  https://dumpcat.com/samples/garage-door-repair/ (first sample) are both deployed and working.
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

## How to work on a sample
```bash
cd samples/garage-door-repair
npm install      # first time on a machine
npm start        # preview at localhost:8080/samples/garage-door-repair/
npm run build    # rebuild _site/ (committed, used for deploy)
```
After editing: `npm run build`, commit, push, then manual-copy (see above) until the queue is fixed.

## Adding a new sample niche
Copy `samples/garage-door-repair/`, then: edit `src/_data/site.json` + page copy, set
`pathPrefix` in `eleventy.config.js` to `/samples/<new-niche>/`, add a copy task in
`.cpanel.yml`. The SEO baseline comes along automatically (it's in `src/_includes/layouts/base.njk`).

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
