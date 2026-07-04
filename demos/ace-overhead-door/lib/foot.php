</main>

<?php
/* Social roster (shared, locked) — see kit_social_roster() in lib/content.php.
   A platform renders on the live site only if its URL is non-empty. */
$SOCIAL = kit_social_roster();
?>
<footer class="slice-foot">
  <div class="container">

    <?php if (edit_mode()): ?>
    <!-- social editor: fill a URL to show that icon on the site; clear it to hide -->
    <div class="social-editor">
      <p class="social-editor__hint">Social links — paste your page URL to show an icon, leave blank to hide it.</p>
      <div class="social-editor__rows">
        <?php foreach ($SOCIAL as $key => $meta): ?>
        <label class="social-editor__row">
          <span class="social-editor__icon"><?= $meta['icon'] ?></span>
          <span class="social-editor__name"><?= e($meta['label']) ?></span>
          <input type="url" class="social-editor__input"
                 data-edit-field="business.social.<?= $key ?>"
                 value="<?= e(c('business.social.' . $key, '')) ?>"
                 placeholder="https://…">
        </label>
        <?php endforeach; ?>
      </div>
    </div>
    <?php else: ?>
    <!-- live social icons: only platforms with a URL -->
    <?php $hasSocial = false; foreach ($SOCIAL as $key => $meta) { if (c('business.social.' . $key, '') !== '') { $hasSocial = true; break; } } ?>
    <?php if ($hasSocial): ?>
    <ul class="social-links" aria-label="Social media">
      <?php foreach ($SOCIAL as $key => $meta): $url = c('business.social.' . $key, ''); if ($url === '') continue; ?>
      <li><a href="<?= e($url) ?>" target="_blank" rel="noopener" aria-label="<?= e($meta['label']) ?>" title="<?= e($meta['label']) ?>"><?= $meta['icon'] ?></a></li>
      <?php endforeach; ?>
    </ul>
    <?php endif; ?>
    <?php endif; ?>

    <ul class="footer-legal-links">
      <li><a href="privacy.php<?= $eq ?>">Privacy Policy</a></li>
      <li><a href="terms.php<?= $eq ?>">Terms of Service</a></li>
    </ul>

    <p><?= e(c('business.name', 'Your Business')) ?> · <?= e(c('business.area', '')) ?> · <?= e(c('business.phone', '')) ?></p>

    <?php if (!edit_requested()): ?>
    <!-- quiet site-owner entry point into edit mode — still password-gated by lib/auth.php,
         so it's safe to show to any visitor; a buyer who doesn't know it's here can't use it. -->
    <p class="footer-owner-link">
      <a href="<?= e(strtok($_SERVER['REQUEST_URI'], '?')) ?>?edit=1">Site owner? Edit this page</a>
    </p>
    <?php endif; ?>
  </div>
</footer>

<?php if (!edit_mode()):
  /* Mobile-only sticky call bar. foot.php ships verbatim to both single- and
     multi-page stamped sites (see new-template.js — head.php is generated
     per-mode, foot.php is not), so it can't be told which mode it's in at
     build time. Detect it at request time instead: multi-page sites always
     have a contact.php sibling, single-page sites don't. Falls back to the
     on-page #cta anchor (see cta-*.php) when there's no contact page to link to. */
  $hasContactPage = file_exists(__DIR__ . '/../contact.php');
  $quoteHref = $hasContactPage ? ('contact.php' . $eq) : '#cta';
?>
<div class="sticky-call-bar">
  <a class="sticky-call-bar__btn sticky-call-bar__btn--call" href="tel:<?= e(preg_replace('/[^+\d]/', '', c('business.phone', ''))) ?>">
    <span aria-hidden="true">📞</span> Call Now
  </a>
  <a class="sticky-call-bar__btn sticky-call-bar__btn--quote" href="<?= e($quoteHref) ?>">Get a Quote</a>
</div>
<?php endif; ?>

<script src="site.js"></script>

<?php if (edit_mode()): ?>
<!-- buyer editor toolbar -->
<div class="editor-bar" id="editor-bar">
  <span class="editor-bar__title">Edit mode</span>
  <span class="editor-bar__hint">Click any text to edit · click a photo to replace it</span>
  <span class="editor-bar__status" id="editor-status"></span>
  <button class="editor-bar__btn" id="editor-revert">Revert to original</button>
  <a class="editor-bar__btn" href="<?= e(strtok($_SERVER['REQUEST_URI'], '?')) ?>">Exit</a>
  <button class="editor-bar__btn editor-bar__btn--save" id="editor-save">Save changes</button>
</div>
<input type="file" id="editor-file" accept="image/*" hidden>
<script src="editor.js"></script>
<?php endif; ?>

<?php if (edit_requested() && !edit_unlocked()): ?>
<!-- edit requested but session locked: ask for the password, then reload into edit mode -->
<div class="edit-login" id="edit-login">
  <form class="edit-login__card" id="edit-login-form">
    <h2 class="edit-login__title">Edit mode</h2>
    <p class="edit-login__hint">Enter your edit password to make changes.</p>
    <input type="password" class="edit-login__input" id="edit-login-pass" placeholder="Password" autocomplete="current-password" autofocus>
    <p class="edit-login__error" id="edit-login-error" hidden>Wrong password — try again.</p>
    <div class="edit-login__actions">
      <a class="edit-login__cancel" href="<?= e(strtok($_SERVER['REQUEST_URI'], '?')) ?>">Cancel</a>
      <button type="submit" class="edit-login__btn">Unlock</button>
    </div>
  </form>
</div>
<script>
(function () {
  var form = document.getElementById('edit-login-form');
  var pass = document.getElementById('edit-login-pass');
  var err  = document.getElementById('edit-login-error');
  form.addEventListener('submit', function (e) {
    e.preventDefault();
    err.hidden = true;
    fetch('save.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ action: 'login', password: pass.value })
    })
    .then(function (r) { return r.json().catch(function () { return { ok: false }; }); })
    .then(function (d) {
      if (d && d.ok) { location.reload(); }
      else { err.hidden = false; pass.value = ''; pass.focus(); }
    })
    .catch(function () { err.hidden = false; });
  });
})();
</script>
<?php endif; ?>

</body>
</html>
