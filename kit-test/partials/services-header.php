<?php /* SERVICES page header — eyebrow + h1 + intro. Reads services.page.*.
   Inner-page section: pairs with a services-detail body below it. */ ?>
<section class="section inner-header services-header">
  <div class="container">
    <div class="section-head">
      <span class="eyebrow" data-edit="services.page.eyebrow"><?= e(c('services.page.eyebrow', 'Our Services')) ?></span>
      <h1 data-edit="services.page.heading"><?= e(c('services.page.heading', 'What we do')) ?></h1>
      <p class="lead" data-edit="services.page.intro"><?= e(c('services.page.intro', 'The full range of work we handle.')) ?></p>
    </div>
  </div>
</section>
