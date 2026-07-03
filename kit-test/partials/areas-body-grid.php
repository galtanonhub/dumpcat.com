<?php /* SERVICE AREAS body — checkmark city grid + optional per-city blurbs.
   Reads areas.list[] and areas.detail[]. Pairs with areas-header.
   Keeps .areas-page class so existing slice.css applies. */ ?>
<section class="section areas-page">
  <div class="container">
    <ul class="areas-page__grid">
      <?php foreach (c('areas.list', []) as $city): ?>
      <li><?= e($city) ?></li>
      <?php endforeach; ?>
    </ul>

    <?php $detail = c('areas.detail', []); if ($detail): ?>
    <div class="areas-detail-list">
      <?php foreach ($detail as $i => $item): ?>
      <div class="areas-detail-item">
        <h2 data-edit="areas.detail.<?= $i ?>.city"><?= e($item['city'] ?? '') ?></h2>
        <p data-edit="areas.detail.<?= $i ?>.blurb"><?= e($item['blurb'] ?? '') ?></p>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <p class="areas-page__note">Don't see your city? <a href="contact.php<?= edit_mode() ? '?edit=1' : '' ?>">Contact us</a> — if you're within driving distance, we'll make it work.</p>
  </div>
</section>
