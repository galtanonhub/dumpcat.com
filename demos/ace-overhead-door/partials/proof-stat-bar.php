<?php /* HOMEPAGE proof bar — loops home.proof.stats[].
   Each stat has an editable value (big number/word) and label. */ ?>
<section class="section proof proof--stat-bar">
  <div class="container">
    <ul class="proof--stat-bar__row">
      <?php foreach (c('home.proof.stats', []) as $i => $stat): ?>
      <li>
        <span class="proof__num" data-edit="home.proof.stats.<?= $i ?>.value"><?= e($stat['value'] ?? '') ?></span>
        <span class="proof__label" data-edit="home.proof.stats.<?= $i ?>.label"><?= e($stat['label'] ?? '') ?></span>
      </li>
      <?php endforeach; ?>
    </ul>
  </div>
</section>
