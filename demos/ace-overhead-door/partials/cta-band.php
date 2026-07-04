<?php /* HOMEPAGE CTA band — flat fields from home.cta.*.
   Phone comes from shared business.phone, not duplicated here. */ ?>
<section class="section cta cta--band" id="cta">
  <div class="container cta--band__inner">
    <div class="cta--band__text">
      <h2 data-edit="home.cta.heading"><?= e(c('home.cta.heading', 'Ready to get started?')) ?></h2>
      <p data-edit="home.cta.text"><?= e(c('home.cta.text', 'Get your free, no-obligation estimate today.')) ?></p>
    </div>
    <div class="cta--band__actions">
      <a href="contact.php<?= edit_mode() ? '?edit=1' : '' ?>" class="btn btn--ink btn--lg" data-edit="home.cta.button"><?= e(c('home.cta.button', 'Get a Free Quote')) ?></a>
      <a href="tel:<?= e(preg_replace('/[^+\d]/', '', c('business.phone', ''))) ?>" class="btn btn--outline btn--lg"><?= e(c('business.phone', '')) ?></a>
    </div>
  </div>
</section>
