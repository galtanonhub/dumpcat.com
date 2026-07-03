<?php /* SERVICES detail body — alternating media/copy rows (the "upgrade" view).
   Reads services.items[] (name, description, bullets, image). Pairs with
   services-header. Keeps .services-page class so existing slice.css applies. */ ?>
<section class="section services-page">
  <div class="container">
    <div class="svc-detail-list">
      <?php foreach (c('services.items', []) as $i => $item): ?>
      <article class="svc-detail" id="<?= e($item['id'] ?? '') ?>">
        <div class="svc-detail__media">
          <img src="<?= e($item['image'] ?? '') ?>" alt="<?= e($item['name'] ?? '') ?>" data-edit-img="services.items.<?= $i ?>.image">
        </div>
        <div class="svc-detail__body">
          <h2 data-edit="services.items.<?= $i ?>.name"><?= e($item['name'] ?? '') ?></h2>
          <p data-edit="services.items.<?= $i ?>.description"><?= e($item['description'] ?? '') ?></p>
          <ul class="svc-detail__list">
            <?php foreach (($item['bullets'] ?? []) as $b => $bullet): ?>
            <li data-edit="services.items.<?= $i ?>.bullets.<?= $b ?>"><?= e($bullet) ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>
