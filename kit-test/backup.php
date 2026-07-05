<?php
/* ------------------------------------------------------------------
   backup.php — nightly-cron-friendly snapshot of this site's live/mutable
   data: content.json, uploads/, and the per-site secrets (auth-secret.php,
   mail-config.php — losing those means losing the edit password / mail
   config, not just content). Table stakes once a site is sold on the
   hosted/ongoing plan rather than handed off one-time.

   Trigger: a cPanel Cron Job running `php backup.php` (e.g. nightly at
   3am). PHP is the one thing guaranteed on shared hosting — Node or a
   particular shell toolchain isn't — so this is plain PHP, no dependency
   on anything else in this kit's build tooling.

   Backups land in backups/ under this same directory, since shared hosting
   rarely offers storage outside the webroot. backups/.htaccess denies all
   web access the first time this runs — same reasoning as protecting
   uploads/ from being served/executed directly.
   ------------------------------------------------------------------ */

$root = __DIR__;
$backupDir = $root . '/backups';
if (!is_dir($backupDir)) {
  mkdir($backupDir, 0755, true);
  file_put_contents($backupDir . '/.htaccess', "Require all denied\n");
}

if (!class_exists('ZipArchive')) {
  fwrite(STDERR, "ZipArchive extension not available — cannot create a backup.\n");
  exit(1);
}

$stamp = date('Ymd-His');
$zipPath = $backupDir . "/backup-$stamp.zip";
$zip = new ZipArchive();
if ($zip->open($zipPath, ZipArchive::CREATE) !== true) {
  fwrite(STDERR, "Could not create $zipPath\n");
  exit(1);
}

foreach (['content.json', 'lib/auth-secret.php', 'lib/mail-config.php'] as $rel) {
  $full = $root . '/' . $rel;
  if (is_file($full)) $zip->addFile($full, $rel);
}

$uploadsDir = $root . '/uploads';
if (is_dir($uploadsDir)) {
  $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($uploadsDir, FilesystemIterator::SKIP_DOTS));
  foreach ($files as $file) {
    $zip->addFile($file->getPathname(), 'uploads/' . substr($file->getPathname(), strlen($uploadsDir) + 1));
  }
}
$zip->close();

/* keep the last 14 nightly backups, prune older ones — two weeks of restore
   points is plenty without the folder growing forever unattended */
$existing = glob($backupDir . '/backup-*.zip');
sort($existing);
while (count($existing) > 14) unlink(array_shift($existing));

echo "Backed up to $zipPath\n";
