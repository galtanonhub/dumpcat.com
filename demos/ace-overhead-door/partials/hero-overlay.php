<?php /* HOMEPAGE hero — full-bleed overlay with background image.
   Primary CTA links to contact.php; secondary button dials business.phone. */ ?>
<section class="hero hero--overlay">
  <img class="hero--overlay__bg" src="<?= e(c('home.hero.image', 'https://picsum.photos/seed/trades/1600/900')) ?>" alt="" data-edit-img="home.hero.image">
  <div class="hero--overlay__scrim"></div>
  <?php kit_shape_divider($SECTION_OPTS['edge'] ?? 'none'); ?>
  <div class="container hero--overlay__inner">
    <span class="eyebrow" data-edit="home.hero.eyebrow"><?= e(c('home.hero.eyebrow', 'Licensed & Insured')) ?></span>
    <h1 class="display" data-edit="home.hero.headline"><?= e(c('home.hero.headline', 'We get it done right.')) ?></h1>
    <p class="lead" data-edit="home.hero.sub"><?= e(c('home.hero.sub', 'Fast, reliable service across the area.')) ?></p>
    <div class="hero__actions">
      <a href="contact.php<?= edit_mode() ? '?edit=1' : '' ?>" class="btn btn--brand btn--lg" data-edit="home.hero.cta_primary"><?= e(c('home.hero.cta_primary', 'Get a Free Estimate')) ?></a>
      <a href="tel:<?= e(preg_replace('/[^+\d]/', '', c('business.phone', ''))) ?>" class="btn btn--ghost-invert btn--lg"><?= e(c('business.phone', '')) ?></a>
    </div>
    <?php $badges = c('home.hero.badges', []); if ($badges): ?>
    <ul class="hero--overlay__badges">
      <?php foreach ($badges as $i => $badge): ?>
      <li data-edit="home.hero.badges.<?= $i ?>"><?= e($badge) ?></li>
      <?php endforeach; ?>
    </ul>
    <?php endif; ?>
  </div>
</section>
