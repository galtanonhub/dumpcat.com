<?php /* SOCIAL section — a standalone band of social buttons. Reads the shared
   roster (kit_social_roster) + business.social.<platform> URLs (same data as the
   footer). Live: one button per platform that has a URL; if none, the whole band
   hides. Edit: a URL field per platform (fill = show, clear = hide), matching the
   footer's social-editor convention. Heading is editable. */ ?>
<?php
$SOCIAL = kit_social_roster();
$has = false; foreach ($SOCIAL as $k => $m) { if (c('business.social.' . $k, '') !== '') { $has = true; break; } }
if (!edit_mode() && !$has) return;   // nothing to show on the live site → hide the band
?>
<section class="section social social--buttons section--alt">
  <div class="container">
    <div class="section-head center">
      <span class="eyebrow" data-edit="social.eyebrow"><?= e(c('social.eyebrow', 'Stay Connected')) ?></span>
      <h2 data-edit="social.heading"><?= e(c('social.heading', 'Follow us')) ?></h2>
    </div>

    <?php if (edit_mode()): ?>
    <div class="social-editor">
      <p class="social-editor__hint">Social links — paste your page URL to show a button, leave blank to hide it.</p>
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
    <div class="social-buttons">
      <?php foreach ($SOCIAL as $key => $meta): $url = c('business.social.' . $key, ''); if ($url === '') continue; ?>
      <a class="social-buttons__btn" href="<?= e($url) ?>" target="_blank" rel="noopener" aria-label="<?= e($meta['label']) ?>">
        <span class="social-buttons__icon" aria-hidden="true"><?= $meta['icon'] ?></span>
        <span class="social-buttons__label"><?= e($meta['label']) ?></span>
      </a>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>
</section>
