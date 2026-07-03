<?php /* HOMEPAGE areas teaser — chip list of service cities.
   List items are a simple string array; chips are not individually editable
   (buyer edits areas.list[] in the JSON or we add a list editor later). */ ?>
<section class="section areas areas--chips section--deep">
  <div class="container">
    <div class="section-head center">
      <span class="eyebrow" data-edit="areas.teaser.eyebrow"><?= e(c('areas.teaser.eyebrow', 'Where We Work')) ?></span>
      <h2 data-edit="areas.teaser.heading"><?= e(c('areas.teaser.heading', 'Serving the Greater Area')) ?></h2>
      <p data-edit="areas.teaser.intro"><?= e(c('areas.teaser.intro', 'If you\'re in the area, we come to you.')) ?></p>
    </div>
    <?php $cities = c('areas.list', []); if ($cities): ?>
    <ul class="areas__chips">
      <?php foreach ($cities as $i => $city): ?><li><?= e($city) ?><?= kit_list_remove_btn('areas.list', $i) ?></li><?php endforeach; ?>
    </ul>
    <?php endif; ?>
    <?= kit_list_add_btn('areas.list', '+ Add a city') ?>
    <div class="areas__cta">
      <a href="service-areas.php<?= edit_mode() ? '?edit=1' : '' ?>" class="btn btn--brand btn--lg"><?= e(c('theme.nav.service-areas', 'Full Service Area')) ?></a>
    </div>
  </div>
</section>
