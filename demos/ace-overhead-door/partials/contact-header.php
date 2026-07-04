<?php /* CONTACT page header — eyebrow + h1 + intro. Reads contact.page.*.
   Pairs with a contact-body below it. */ ?>
<section class="section inner-header contact-header">
  <div class="container">
    <div class="section-head">
      <span class="eyebrow" data-edit="contact.page.eyebrow"><?= e(c('contact.page.eyebrow', 'Contact')) ?></span>
      <h1 data-edit="contact.page.heading"><?= e(c('contact.page.heading', 'Get a free estimate')) ?></h1>
      <p class="lead" data-edit="contact.page.intro"><?= e(c('contact.page.intro', 'Fill out the form and we\'ll get back to you quickly.')) ?></p>
    </div>
  </div>
</section>
