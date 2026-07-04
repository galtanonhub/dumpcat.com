<?php
/* ------------------------------------------------------------------
   content.php — the data layer.
   Loads content.json (the live, buyer-editable content) and exposes
   tiny helpers the templates use to read it. The buyer never touches
   markup; they only ever change the data this file loads.
   ------------------------------------------------------------------ */

require_once __DIR__ . '/auth.php';

define('SLICE_DIR', dirname(__DIR__));
define('CONTENT_LIVE', SLICE_DIR . '/content.json');
define('CONTENT_ORIG', SLICE_DIR . '/content.original.json');

/* Load live content; fall back to the shipped original if it's missing
   (e.g. fresh deploy before the first save). */
function kit_load_content() {
  $file = file_exists(CONTENT_LIVE) ? CONTENT_LIVE : CONTENT_ORIG;
  $raw  = file_exists($file) ? file_get_contents($file) : '{}';
  $data = json_decode($raw, true);
  return is_array($data) ? $data : [];
}

$CONTENT = kit_load_content();

/* c('services.teaser.heading', 'fallback') — dot-path getter with a
   placeholder fallback so a template always renders, even mid-edit. */
function c($path, $fallback = '') {
  global $CONTENT;
  $node = $CONTENT;
  foreach (explode('.', $path) as $key) {
    if (is_array($node) && array_key_exists($key, $node)) {
      $node = $node[$key];
    } else {
      return $fallback;
    }
  }
  return $node;
}

/* escape for safe HTML output */
function e($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

/* Favicon — no image asset needed: a monogram (business name's first letter)
   rendered as an inline SVG data URI, so every stamped site gets a distinct
   tab icon for free with zero build-time asset generation. Colors are fixed
   (not skin-aware) since head.php has no access to the chosen skin's actual
   hex values — those live in a separate CSS file the browser hasn't fetched
   yet at the point <link rel="icon"> is parsed. */
function kit_favicon_data_uri() {
  $letter = strtoupper(substr(trim((string) c('business.name', '')), 0, 1));
  if ($letter === '') $letter = 'S';
  $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64">'
       . '<rect width="64" height="64" rx="14" fill="#1a1d23"/>'
       . '<text x="32" y="45" font-family="system-ui,sans-serif" font-size="34" font-weight="700" text-anchor="middle" fill="#ffffff">' . e($letter) . '</text>'
       . '</svg>';
  return 'data:image/svg+xml,' . rawurlencode($svg);
}

/* Open Graph + Twitter card tags — so a shared link gets a real title/
   description/image instead of a blank preview. Reuses the exact same
   title/description fields already in <title>/<meta name="description">
   (see head.php) plus home.hero.image, so there's nothing new for the
   buyer to fill in. */
function kit_og_tags() {
  $title = c('business.name', 'Your Business');
  $desc  = c('home.hero.sub', c('services.teaser.intro', ''));
  $image = c('home.hero.image', '');
  $tags  = '<meta property="og:type" content="website">' . "\n"
         . '<meta property="og:title" content="' . e($title) . '">' . "\n"
         . '<meta property="og:description" content="' . e($desc) . '">' . "\n";
  if ($image !== '') $tags .= '<meta property="og:image" content="' . e($image) . '">' . "\n";
  $tags .= '<meta name="twitter:card" content="summary_large_image">';
  return $tags;
}

/* has the buyer asked for edit mode? (?edit=1 in the URL) — may still be locked */
function edit_requested() { return isset($_GET['edit']); }

/* are we ACTUALLY editing? requested AND the session is unlocked (auth.php).
   Everything that renders editor chrome keys off this, so a logged-out visitor
   who guesses ?edit=1 sees the normal site (plus a password prompt), never the
   editing UI — and save.php refuses their writes regardless. */
function edit_mode() { return edit_requested() && edit_unlocked(); }

/* Social roster — STRUCTURE (locked): which platforms exist + their emoji and
   label. The buyer never edits this; they only set the URL (data) per platform
   at business.social.<key>. Shared by the footer AND the social section so the
   roster can never drift between them. */
function kit_social_roster() {
  return [
    'facebook'  => ['icon' => '📘',  'label' => 'Facebook'],
    'instagram' => ['icon' => '📷',  'label' => 'Instagram'],
    'x'         => ['icon' => '🐦',  'label' => 'X (Twitter)'],
    'youtube'   => ['icon' => '▶️', 'label' => 'YouTube'],
    'linkedin'  => ['icon' => '💼',  'label' => 'LinkedIn'],
    'tiktok'    => ['icon' => '🎵',  'label' => 'TikTok'],
  ];
}

/* LocalBusiness JSON-LD — built from the same business.* fields the footer
   and header already show, so there's nothing new for the buyer to fill in.
   Drops any field that's empty (a buyer who never fills in email/social
   shouldn't emit "" into the schema) and skips openingHours entirely: the
   business.hours field is free text ("Mon–Sat 7am–7pm"), and schema.org's
   openingHours wants ISO day/time tokens ("Mo-Sa 07:00-19:00") — emitting
   the free text there would just fail Google's Rich Results validator. */
function kit_local_business_ldjson() {
  $social = array_values(array_filter(array_map(
    fn($k) => c("business.social.$k", ''),
    array_keys(kit_social_roster())
  )));
  $data = array_filter([
    '@context'   => 'https://schema.org',
    '@type'      => 'LocalBusiness',
    'name'       => c('business.name', ''),
    'image'      => c('home.hero.image', ''),
    'telephone'  => c('business.phone', ''),
    'email'      => c('business.email', ''),
    'address'    => c('business.address', ''),
    'areaServed' => c('business.area', ''),
    'sameAs'     => $social,
  ], fn($v) => $v !== '' && $v !== []);
  return json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
}

/* Add/remove controls for repeating content arrays (services.items,
   reviews.items, areas.list, faq.items, gallery items…). Buyer-facing:
   lets a non-technical buyer change how MANY items exist, not just their
   text/images — save.php's add_item/remove_item actions do the actual
   array mutation, then editor.js reloads the page so PHP re-renders with
   correct, freshly-reindexed data-edit paths. No-ops outside edit mode. */
function kit_list_remove_btn($path, $i) {
  if (!edit_mode()) return '';
  return '<button type="button" class="kit-list__remove" data-list-remove="' . e($path) . '" data-list-index="' . (int)$i . '" title="Remove this item" aria-label="Remove this item">&times;</button>';
}
function kit_list_add_btn($path, $label = '+ Add') {
  if (!edit_mode()) return '';
  return '<button type="button" class="kit-list__add" data-list-add="' . e($path) . '">' . e($label) . '</button>';
}

/* Shape divider — echoes the `.shape-divider` SVG (see base.css) for a
   variant's `edge` option (none/wave/triangle). STRUCTURE (locked): the
   caller only ever passes a value from $SECTION_OPTS, never buyer content.
   Only call this inside a section that unconditionally renders a photo
   (hero--overlay, inner-header--banner, the feature-band photo band) — the
   effect is meaningless without one, so it's never offered as an option on
   variants that don't guarantee an image. */
function kit_shape_divider($edge) {
  $paths = [
    /* two full repeating sine-like cycles (down-up-down-up), mirror-symmetric
       so the pattern reads consistently across the whole width rather than
       one lopsided dip on one side — each quarter-cycle uses BOTH cubic
       control points pulled to the same extreme (0 or 100) rather than just
       one, which pulls the curve's actual peak/trough much closer to the
       full 0–100 range without any point exceeding it (exceeding would clip
       against .shape-divider's overflow:hidden, as an earlier version did). */
    'wave'     => 'M0,50 C62.5,100 187.5,100 250,50 C312.5,0 437.5,0 500,50 C562.5,100 687.5,100 750,50 C812.5,0 937.5,0 1000,50 L1000,100 L0,100 Z',
    /* photo dips to a point at center (background recedes there) and the
       background fully covers the corners — not the other way around */
    'triangle' => 'M0,0 L500,85 L1000,0 L1000,100 L0,100 Z',
  ];
  if (!isset($paths[$edge])) return;
  ?>
  <div class="shape-divider" aria-hidden="true">
    <svg viewBox="0 0 1000 100" preserveAspectRatio="none"><path d="<?= $paths[$edge] ?>"/></svg>
  </div>
  <?php
}
