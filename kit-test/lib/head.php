<?php /* shared page shell. Expects $PAGE set before include. */ ?>
<?php
$eq = edit_mode() ? '?edit=1' : '';
$bizName = c('business.name', "kit-test");
$PAGE_META = [
  'home'          => ['title' => $bizName, 'desc' => c('home.hero.sub', c('services.teaser.intro', ''))],
  'services'      => ['title' => c('services.page.heading', '') . ' | ' . $bizName, 'desc' => c('services.page.intro', '')],
  'service-areas' => ['title' => c('areas.page.heading', '') . ' | ' . $bizName, 'desc' => c('areas.page.intro', '')],
  'about'         => ['title' => c('about.page.heading', '') . ' | ' . $bizName, 'desc' => c('about.page.intro', '')],
  'contact'       => ['title' => c('contact.page.heading', '') . ' | ' . $bizName, 'desc' => c('contact.page.intro', '')],
];
$pageMeta = $PAGE_META[$PAGE] ?? $PAGE_META['home'];
$pageFile = $PAGE === 'home' ? 'index.php' : $PAGE . '.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($pageMeta['title']) ?></title>
<meta name="description" content="<?= e($pageMeta['desc']) ?>">
<link rel="canonical" href="<?= e(kit_absolute_url($pageFile)) ?>">
<link rel="icon" href="<?= kit_favicon_data_uri() ?>">
<?= kit_og_tags($pageMeta['title'], $pageMeta['desc']) ?>
<script type="application/ld+json"><?= kit_local_business_ldjson() ?></script>
<link rel="stylesheet" href="css/base.css">
<link rel="stylesheet" href="css/sections.css">
<link rel="stylesheet" href="css/skin.css">
<link rel="stylesheet" href="css/site.css">
<?php if (edit_requested()): ?><link rel="stylesheet" href="editor.css"><?php endif; ?>
</head>
<body class="<?= edit_mode() ? 'is-editing' : '' ?>">

<header class="nav nav--centered slice-nav">
  <div class="container nav__inner">
    <a class="nav__logo" href="index.php<?= $eq ?>"><?= e(c('business.name', "kit-test")) ?></a>
    <nav class="nav__links">
      <a href="index.php<?= $eq ?>"<?= $PAGE==='home' ? ' class="is-current"' : '' ?>><?= e(c('theme.nav.home', 'Home')) ?></a>
      <a href="services.php<?= $eq ?>"<?= $PAGE==='services' ? ' class="is-current"' : '' ?>><?= e(c('theme.nav.services', 'What We Do')) ?></a>
      <a href="service-areas.php<?= $eq ?>"<?= $PAGE==='service-areas' ? ' class="is-current"' : '' ?>><?= e(c('theme.nav.service-areas', 'Where We Work')) ?></a>
      <a href="about.php<?= $eq ?>"<?= $PAGE==='about' ? ' class="is-current"' : '' ?>><?= e(c('theme.nav.about', 'Our Story')) ?></a>
      <a class="btn btn--brand" href="contact.php<?= $eq ?>"<?= $PAGE==='contact' ? ' aria-current="page"' : '' ?>><?= e(c('theme.nav.contact', 'Get a Quote')) ?></a>
    </nav>
    <button class="nav__toggle" type="button" aria-label="Menu" aria-expanded="false"><span></span><span></span><span></span></button>
  </div>
  <nav class="nav__mobile" aria-label="Mobile">
    <a href="index.php<?= $eq ?>"<?= $PAGE==='home' ? ' class="is-current"' : '' ?>><?= e(c('theme.nav.home', 'Home')) ?></a>
    <a href="services.php<?= $eq ?>"<?= $PAGE==='services' ? ' class="is-current"' : '' ?>><?= e(c('theme.nav.services', 'What We Do')) ?></a>
    <a href="service-areas.php<?= $eq ?>"<?= $PAGE==='service-areas' ? ' class="is-current"' : '' ?>><?= e(c('theme.nav.service-areas', 'Where We Work')) ?></a>
    <a href="about.php<?= $eq ?>"<?= $PAGE==='about' ? ' class="is-current"' : '' ?>><?= e(c('theme.nav.about', 'Our Story')) ?></a>
    <a class="btn btn--brand" href="contact.php<?= $eq ?>"<?= $PAGE==='contact' ? ' aria-current="page"' : '' ?>><?= e(c('theme.nav.contact', 'Get a Quote')) ?></a>
  </nav>
</header>

<main>
