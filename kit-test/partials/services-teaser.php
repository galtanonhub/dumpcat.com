<?php /* HOMEPAGE services teaser — short blurbs only, links to the full page.
   Shares name + image with the services page; uses the SHORT "blurb"
   field (the page uses "description" + "bullets"). One source, two views. */ ?>
<section class="section services services--cards-3"<?= !empty($SECTION_OPTS['arrival']) ? ' data-arrival="expand"' : '' ?>>
  <div class="container">
    <div class="section-head center">
      <span class="eyebrow" data-edit="services.teaser.eyebrow"><?= e(c('services.teaser.eyebrow', 'What We Do')) ?></span>
      <h2 data-edit="services.teaser.heading"><?= e(c('services.teaser.heading', 'Our Services')) ?></h2>
      <p data-edit="services.teaser.intro"><?= e(c('services.teaser.intro', 'A short line about the work you do.')) ?></p>
    </div>
    <div class="grid grid-3">
      <?php foreach (c('services.items', []) as $i => $item): ?>
      <article class="card">
        <?= kit_list_remove_btn('services.items', $i) ?>
        <div class="card__media">
          <img src="<?= e($item['image'] ?? '') ?>" alt="<?= e($item['name'] ?? '') ?>" data-edit-img="services.items.<?= $i ?>.image">
        </div>
        <div class="card__body">
          <h3 data-edit="services.items.<?= $i ?>.name"><?= e($item['name'] ?? '') ?></h3>
          <p data-edit="services.items.<?= $i ?>.blurb"><?= e($item['blurb'] ?? '') ?></p>
          <a href="services.php<?= edit_mode() ? '?edit=1' : '' ?>#<?= e($item['id'] ?? '') ?>" class="link-arrow">Learn more</a>
        </div>
      </article>
      <?php endforeach; ?>
    </div>
    <?= kit_list_add_btn('services.items', '+ Add a service') ?>
  </div>
</section>
