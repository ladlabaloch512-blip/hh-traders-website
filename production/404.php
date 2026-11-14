<?php
require __DIR__ . '/config.php';
require __DIR__ . '/partials/icons.php';
http_response_code(404);
$d = hh_load();
$page = '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<?php hh_head($d, 'Page Not Found - H.H Traders', 'The page you are looking for could not be found at H.H Traders, Rawalpindi.', '/404.php'); ?>
<meta name="robots" content="noindex, follow">
</head>
<body>
<?php include __DIR__ . '/partials/header.php'; ?>
<main><div class="wrap"><div class="confirm">
  <h1 style="font-size:64px">404</h1>
  <p>This page could not be found. Browse the shop instead.</p>
  <p><a class="shopnow" href="shop.php">Go to Shop</a></p>
</div></div></main>
<?php include __DIR__ . '/partials/footer.php'; ?>
</body>
</html>
