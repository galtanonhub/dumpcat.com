<?php /* HOMEPAGE gallery — before/after. Paired photo cards proving the
   work: a labeled "Before" / "After" photo side by side per job, with a
   short caption underneath. New home.gallery.* content namespace
   (eyebrow/heading/intro + items[]{before,after,caption}) — mirrors the
   reviews.items[] / services.items[] indexed-array + data-edit-img
   pattern already used everywhere else, so the buyer's editor "just
   works" here with no new editor code. */ ?>
<section class="section gallery gallery--before-after">
  <div class="container">
    <div class="section-head center">
      <span class="eyebrow" data-edit="home.gallery.eyebrow"><?= e(c('home.gallery.eyebrow', 'Our Work')) ?></span>
      <h2 data-edit="home.gallery.heading"><?= e(c('home.gallery.heading', 'See the Difference')) ?></h2>
      <p data-edit="home.gallery.intro"><?= e(c('home.gallery.intro', 'Real jobs, real results.')) ?></p>
    </div>
    <div class="gallery--before-after__grid">
      <?php foreach (c('home.gallery.items', []) as $i => $item): ?>
      <figure class="gallery--before-after__card">
        <?= kit_list_remove_btn('home.gallery.items', $i) ?>
        <div class="gallery--before-after__photos">
          <span class="gallery--before-after__half">
            <img src="<?= e($item['before'] ?? '') ?>" alt="Before" data-edit-img="home.gallery.items.<?= $i ?>.before">
            <span class="gallery--before-after__tag">Before</span>
          </span>
          <span class="gallery--before-after__half">
            <img src="<?= e($item['after'] ?? '') ?>" alt="After" data-edit-img="home.gallery.items.<?= $i ?>.after">
            <span class="gallery--before-after__tag gallery--before-after__tag--after">After</span>
          </span>
        </div>
        <figcaption data-edit="home.gallery.items.<?= $i ?>.caption"><?= e($item['caption'] ?? '') ?></figcaption>
      </figure>
      <?php endforeach; ?>
    </div>
    <?= kit_list_add_btn('home.gallery.items', '+ Add a before/after pair') ?>
  </div>
</section>
