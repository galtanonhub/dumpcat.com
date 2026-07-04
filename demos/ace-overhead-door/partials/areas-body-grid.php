<?php /* SERVICE AREAS body — checkmark city grid + optional per-city blurbs.
   Reads areas.list[] and areas.detail[]. Pairs with areas-header.
   Keeps .areas-page class so existing slice.css applies. */ ?>
<section class="section areas-page">
  <div class="container">
    <ul class="areas-page__grid">
      <?php foreach (c('areas.list', []) as $i => $city): ?>
      <li><?= e($city) ?><?= kit_list_remove_btn('areas.list', $i) ?></li>
      <?php endforeach; ?>
    </ul>
    <?= kit_list_add_btn('areas.list', '+ Add a city') ?>

    <?php $detail = c('areas.detail', []); if ($detail): ?>
    <div class="areas-detail-list">
      <?php foreach ($detail as $i => $item): ?>
      <div class="areas-detail-item">
        <?= kit_list_remove_btn('areas.detail', $i) ?>
        <h2 data-edit="areas.detail.<?= $i ?>.city"><?= e($item['city'] ?? '') ?></h2>
        <p data-edit="areas.detail.<?= $i ?>.blurb"><?= e($item['blurb'] ?? '') ?></p>
      </div>
      <?php endforeach; ?>
    </div>
    <?= kit_list_add_btn('areas.detail', '+ Add a city detail') ?>
    <?php endif; ?>

    <p class="areas-page__note">Don't see your city? <a href="contact.php<?= edit_mode() ? '?edit=1' : '' ?>">Contact us</a> — if you're within driving distance, we'll make it work.</p>
  </div>
</section>
