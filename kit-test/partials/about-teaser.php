<?php /* HOMEPAGE about teaser — split photo + copy with checklist.
   Links to about.php using theme.nav.about label for the CTA.
   Checklist items are looped; photo is editable. */ ?>
<section class="section about about--split-photo">
  <div class="container split">
    <figure class="about__photo">
      <img src="<?= e(c('about.teaser.image', 'https://picsum.photos/seed/team7/900/700')) ?>" alt="<?= e(c('business.name', '')) ?> team" data-edit-img="about.teaser.image">
    </figure>
    <div class="about__copy">
      <span class="eyebrow" data-edit="about.teaser.eyebrow"><?= e(c('about.teaser.eyebrow', 'Why Choose Us')) ?></span>
      <h2 data-edit="about.teaser.heading"><?= e(c('about.teaser.heading', "We've done this for years. We know what matters.")) ?></h2>
      <p data-edit="about.teaser.text"><?= e(c('about.teaser.text', 'A short paragraph about who you are and what you stand for.')) ?></p>
      <?php $checklist = c('about.teaser.checklist', []); if ($checklist): ?>
      <ul class="checklist">
        <?php foreach ($checklist as $i => $item): ?>
        <li data-edit="about.teaser.checklist.<?= $i ?>"><?= e($item) ?></li>
        <?php endforeach; ?>
      </ul>
      <?php endif; ?>
      <a href="about.php<?= edit_mode() ? '?edit=1' : '' ?>" class="link-arrow" data-edit="about.teaser.cta"><?= e(c('about.teaser.cta', 'More about us')) ?></a>
    </div>
  </div>
</section>
