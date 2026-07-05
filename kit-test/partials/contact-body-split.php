<?php /* CONTACT body — two columns: contact info (left) + form (right).
   Form POSTs to form.php which redirects back with ?sent=1. Reads
   business.{phone,email,address,hours}. Pairs with contact-header.
   Keeps .contact-page class so existing slice.css applies. */ ?>
<section class="section contact-page">
  <div class="container">
    <?php if (isset($_GET['sent'])): ?>
    <div class="contact-success">
      <p>Thanks! We received your message and will follow up shortly.</p>
    </div>
    <?php else: ?>

    <div class="contact-page__body">
      <div class="contact-page__info">
        <h2>How to reach us</h2>
        <ul class="contact-info-list">
          <?php if ($ph = c('business.phone', '')): ?>
          <li><strong>Phone</strong><a href="tel:<?= e(preg_replace('/[^+\d]/', '', $ph)) ?>"><?= e($ph) ?></a></li>
          <?php endif; ?>
          <?php if ($em = c('business.email', '')): ?>
          <li><strong>Email</strong><a href="mailto:<?= e($em) ?>"><?= e($em) ?></a></li>
          <?php endif; ?>
          <?php if ($addr = c('business.address', '')): ?>
          <li><strong>Area</strong><span><?= e($addr) ?></span></li>
          <?php endif; ?>
          <?php if ($hrs = c('business.hours', '')): ?>
          <li><strong>Hours</strong><span><?= e($hrs) ?></span></li>
          <?php endif; ?>
        </ul>
      </div>

      <div class="contact-page__form">
        <form class="contact-form" method="post" action="form.php<?= edit_mode() ? '?edit=1' : '' ?>">
          <label>Your name<input type="text" name="name" required placeholder="Jane Smith"></label>
          <label>Best way to reach you<input type="text" name="contact" required placeholder="Phone or email"></label>
          <label>How can we help?<textarea name="message" rows="5" required placeholder="Describe what's happening, or ask anything…"></textarea></label>
          <div class="hp-field" aria-hidden="true"><label>Leave this field blank<input type="text" name="hp_website" tabindex="-1" autocomplete="off"></label></div>
          <input type="hidden" name="csrf" value="<?= e(kit_csrf_token()) ?>">
          <button class="btn btn--brand btn--lg" type="submit" data-edit="contact.page.button"><?= e(c('contact.page.button', 'Send My Request')) ?></button>
        </form>
      </div>
    </div>

    <?php endif; ?>
  </div>
</section>
