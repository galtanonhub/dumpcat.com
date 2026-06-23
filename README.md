# dumpcat.com — DragonWorkflows Sample Sites

Holding domain for the demo websites we build to sell. The root serves a
**coming-soon page**; each sample site lives under `/samples/<niche>/` until it sells
and graduates to its own domain.

Built with [Eleventy](https://www.11ty.dev/) — each sample is a self-contained static
site. Deployed to cPanel (`dumpcat`) via git (`.cpanel.yml` → `public_html`).

## Structure

```
dumpcat.com/                     # git repo root = dumpcat.com docroot
├── index.html                   # coming-soon page (root)
├── robots.txt                   # Disallow: / (nothing here should be indexed)
├── favicon.svg
├── .cpanel.yml                  # deploy rules
└── samples/
    └── garage-door-repair/      # "Your Name Garage Door Repair" — Central Florida
```

Each sample is independent (own `package.json` / `node_modules`). To work on one:

```bash
cd samples/garage-door-repair
npm install      # first time only
npm start        # preview at localhost:8080/samples/garage-door-repair/
npm run build    # outputs static HTML to _site/ (committed for deploy)
```

## SEO baseline — every sample ships with this

The contract. Any site we produce includes, out of the box:

- **Per-page `<title>` + `<meta description>`** with site-level fallbacks (`src/_data/site.json`)
- **Open Graph + Twitter Card** tags
- **`LocalBusiness` JSON-LD** — name, address, phone, geo, hours, area-served,
  aggregate rating, offered services. Drives Google's local pack, rich results, and AI Overviews.
- **Auto-generated `sitemap.xml`**
- **`robots.txt`** + a **`noindex`** meta tag while the site is an unsold sample (in `base.njk`)
- **Canonical URL**, favicon, theme-color, breadcrumbs, a11y skip-link

> SEO plumbing lives in each sample's `src/_includes/layouts/base.njk`. To add a niche,
> copy a sample, edit `src/_data/site.json` + page copy, set `pathPrefix` in `eleventy.config.js`.

## When a sample sells / goes live on its own domain

1. Move the sample's folder to its **own repo**.
2. In that copy: change `pathPrefix` in `eleventy.config.js` from `/samples/<niche>/` to `/`.
3. Remove the `noindex, nofollow` meta tag in `src/_includes/layouts/base.njk`.
4. Update `src/_data/site.json` with the buyer's real name, phone, address, URL.
5. Find-and-replace the "Your Name" placeholder business name.
6. `npm run build` and deploy `_site/` to the new domain.
7. **Back in this repo: delete the sample's folder AND remove its copy task in
   `.cpanel.yml`**, so dumpcat.com only ever holds samples still parked here.
