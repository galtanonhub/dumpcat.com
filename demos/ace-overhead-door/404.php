<?php require __DIR__ . '/lib/content.php'; $PAGE = '404'; http_response_code(404); ?>
<?php require __DIR__ . '/lib/head.php'; ?>

<section class="section error-page">
  <div class="container error-page__inner">
    <h1>Page Not Found</h1>
    <p>The page you're looking for doesn't exist or may have moved.</p>
    <a class="btn btn--brand" href="index.php">Back to Home</a>
  </div>
</section>

<?php require __DIR__ . '/lib/foot.php'; ?>
