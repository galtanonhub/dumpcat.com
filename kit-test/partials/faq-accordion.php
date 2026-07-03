<?php /* HOMEPAGE FAQ accordion — looped q/a items.
   JS handles open/close (existing site.js handles .faq__q clicks).
   q and a are both editable. */ ?>
<section class="section faq faq--accordion section--alt">
  <div class="container">
    <div class="section-head center">
      <span class="eyebrow">Common Questions</span>
      <h2 data-edit="faq.heading"><?= e(c('faq.heading', 'Before you call')) ?></h2>
    </div>
    <div class="faq__list">
      <?php foreach (c('faq.items', []) as $i => $item): ?>
      <div class="faq__item">
        <?= kit_list_remove_btn('faq.items', $i) ?>
        <button class="faq__q" type="button">
          <span data-edit="faq.items.<?= $i ?>.q"><?= e($item['q'] ?? '') ?></span>
          <span class="faq__icon" aria-hidden="true"></span>
        </button>
        <div class="faq__a">
          <div class="faq__a-inner">
            <p data-edit="faq.items.<?= $i ?>.a"><?= e($item['a'] ?? '') ?></p>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <?= kit_list_add_btn('faq.items', '+ Add a question') ?>
  </div>
</section>
