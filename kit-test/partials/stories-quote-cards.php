<?php /* HOMEPAGE reviews — static quote-card grid (no JS). Reads reviews.items[]
   (quote/name/role/image), same source as stories-spotlight. Avatar uses the
   item image; quote + name + role shown per card. */ ?>
<?php $items = c('reviews.items', []); if (!$items) return; ?>
<section class="section stories stories--quote-cards section--alt">
  <div class="container">
    <div class="section-head center">
      <h2 data-edit="reviews.heading"><?= e(c('reviews.heading', 'What our customers say')) ?></h2>
    </div>
    <div class="grid grid-3">
      <?php foreach ($items as $i => $item): ?>
      <figure class="stories--quote-cards__card card">
        <?= kit_list_remove_btn('reviews.items', $i) ?>
        <div class="card__body">
          <blockquote class="stories--quote-cards__quote" data-edit="reviews.items.<?= $i ?>.quote"><?= e($item['quote'] ?? '') ?></blockquote>
          <figcaption class="stories--quote-cards__meta">
            <?php if (!empty($item['image']) || edit_mode()): ?><img class="stories--quote-cards__avatar" src="<?= e($item['image'] ?? '') ?>" alt="<?= e($item['name'] ?? '') ?>" data-edit-img="reviews.items.<?= $i ?>.image"><?php endif; ?>
            <span class="stories--quote-cards__person">
              <strong data-edit="reviews.items.<?= $i ?>.name"><?= e($item['name'] ?? '') ?></strong>
              <span data-edit="reviews.items.<?= $i ?>.role"><?= e($item['role'] ?? '') ?></span>
            </span>
          </figcaption>
        </div>
      </figure>
      <?php endforeach; ?>
    </div>
    <?= kit_list_add_btn('reviews.items', '+ Add a review') ?>
  </div>
</section>
