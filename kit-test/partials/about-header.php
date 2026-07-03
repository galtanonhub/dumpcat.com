<?php /* ABOUT page header — eyebrow + h1 (no lead, matches original about page).
   Reads about.page.*. Pairs with an about-body below it. */ ?>
<section class="section inner-header about-header">
  <div class="container">
    <div class="section-head">
      <span class="eyebrow" data-edit="about.page.eyebrow"><?= e(c('about.page.eyebrow', 'Our Story')) ?></span>
      <h1 data-edit="about.page.heading"><?= e(c('about.page.heading', 'Built on doing it right')) ?></h1>
    </div>
  </div>
</section>
