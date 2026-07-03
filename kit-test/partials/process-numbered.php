<?php /* HOMEPAGE process — numbered steps. Steps are looped; step number
   is auto-generated from index (no content field needed). */ ?>
<section class="section process process--numbered section--alt">
  <div class="container">
    <div class="section-head center">
      <span class="eyebrow" data-edit="home.process.eyebrow"><?= e(c('home.process.eyebrow', 'How It Works')) ?></span>
      <h2 data-edit="home.process.heading"><?= e(c('home.process.heading', 'Simple, start to finish')) ?></h2>
    </div>
    <ol class="process--numbered__grid">
      <?php foreach (c('home.process.steps', []) as $i => $step): ?>
      <li>
        <span class="process__num"><?= str_pad($i + 1, 2, '0', STR_PAD_LEFT) ?></span>
        <h3 data-edit="home.process.steps.<?= $i ?>.title"><?= e($step['title'] ?? '') ?></h3>
        <p data-edit="home.process.steps.<?= $i ?>.text"><?= e($step['text'] ?? '') ?></p>
      </li>
      <?php endforeach; ?>
    </ol>
  </div>
</section>
