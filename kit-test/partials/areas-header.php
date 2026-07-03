<?php /* SERVICE AREAS page header — eyebrow + h1 + intro. Reads areas.page.*.
   Inner-page section: pairs with an areas-body below it. */ ?>
<section class="section inner-header areas-header">
  <div class="container">
    <div class="section-head">
      <span class="eyebrow" data-edit="areas.page.eyebrow"><?= e(c('areas.page.eyebrow', 'Service Area')) ?></span>
      <h1 data-edit="areas.page.heading"><?= e(c('areas.page.heading', 'Where we work')) ?></h1>
      <p class="lead" data-edit="areas.page.intro"><?= e(c('areas.page.intro', 'We come to you across the region.')) ?></p>
    </div>
  </div>
</section>
