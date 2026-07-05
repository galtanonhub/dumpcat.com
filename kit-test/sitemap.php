<?php
require __DIR__ . '/lib/content.php';
header('Content-Type: application/xml; charset=UTF-8');
echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
  <url><loc><?= e(kit_absolute_url('index.php')) ?></loc></url>
  <url><loc><?= e(kit_absolute_url('services.php')) ?></loc></url>
  <url><loc><?= e(kit_absolute_url('service-areas.php')) ?></loc></url>
  <url><loc><?= e(kit_absolute_url('about.php')) ?></loc></url>
  <url><loc><?= e(kit_absolute_url('contact.php')) ?></loc></url>
</urlset>
