# dumpcat.com — DragonWorkflows Template Library

Private holding domain for design templates. The root serves a **coming-soon page**;
each template lives under `/samples/template-XX/` as a live browsable preview.

Built with [Eleventy](https://www.11ty.dev/) — each template is a self-contained static
site. Deployed to cPanel (`dumpcat`) via git (`.cpanel.yml` → `public_html`).

## Structure

```
dumpcat.com/                     # git repo root = dumpcat.com docroot
├── index.html                   # coming-soon page (root)
├── robots.txt                   # Disallow: / (nothing here should be indexed)
├── favicon.svg
├── .cpanel.yml                  # deploy rules
└── samples/
    ├── template-01/             # Template 01 — Clean Light (garage door reference)
    └── template-02/             # Template 02 — Bold Dark (electrician reference)
```

Each template is independent (own `package.json` / `node_modules`). To work on one:

```bash
cd samples/template-02
npm install          # first time only
npm run preview      # local dev at localhost:8080 (links work correctly)
npm run build        # outputs static HTML to _site/ (committed for deploy)
```

Never use `npm start` locally — the production pathPrefix breaks internal links.

## Template library

| Folder | Name | Accent | Fonts | Reference niche |
|--------|------|--------|-------|-----------------|
| `template-01` | Clean Light | Safety orange | Barlow Condensed + Inter | Garage door repair |
| `template-02` | Bold Dark | Electric yellow | DM Sans | Electrician |

## SEO baseline — every template ships with this

- **Per-page `<title>` + `<meta description>`** with site-level fallbacks (`src/_data/site.json`)
- **Open Graph + Twitter Card** tags
- **`LocalBusiness` JSON-LD** — name, address, phone, geo, hours, area-served, aggregate rating, offered services
- **Auto-generated `sitemap.xml`**
- **`robots.txt`** + **`noindex`** meta tag while parked here (remove at launch)
- **Canonical URL**, favicon, theme-color, a11y skip-link

## Starting a new client site from a template

1. Copy the chosen template folder to a **new standalone repo** for the client.
2. In that copy: change `pathPrefix` in `eleventy.config.js` from `/samples/template-XX/` to `/`.
3. Remove the `noindex, nofollow` meta tag in `src/_includes/layouts/base.njk`.
4. Update `src/_data/site.json` with client's real name, phone, address, URL.
5. Update niche-specific copy, colors, and hero widget.
6. `npm run build` and deploy `_site/` to the client's domain.

## Adding a new template

1. Copy the closest existing template folder → `samples/template-XX/`
2. Update `pathPrefix` in `eleventy.config.js` to `/samples/template-XX/`
3. Update `package.json` name to `template-XX`
4. Add deploy task to `.cpanel.yml`
5. Customize design, `npm run preview` to test, `npm run build` before committing
