<?php
require __DIR__ . '/config.php';
require __DIR__ . '/partials/icons.php';
$d = hh_load();
$page = 'cart';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<?php hh_head($d, 'Your Cart - H.H Traders Rawalpindi', 'Review your cart at H.H Traders, Rawalpindi. Fire safety and safety equipment with cash on delivery across Pakistan.', '/cart.php'); ?>
<meta name="robots" content="noindex, follow">
</head>
<body>
<?php include __DIR__ . '/partials/header.php'; ?>
<main><div class="wrap">
  <h1 class="ptitle">Your Cart</h1>
  <div id="cartRoot"></div>
</div></main>
<?php include __DIR__ . '/partials/footer.php'; ?>
</body>
</html>
