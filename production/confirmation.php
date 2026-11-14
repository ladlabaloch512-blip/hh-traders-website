<?php
require __DIR__ . '/config.php';
require __DIR__ . '/partials/icons.php';
$d = hh_load();
$page = 'checkout';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<?php hh_head($d, 'Order Confirmed - H.H Traders', 'Your order has been placed with H.H Traders, Rawalpindi.', '/confirmation.php'); ?>
<meta name="robots" content="noindex, follow">
</head>
<body>
<?php include __DIR__ . '/partials/header.php'; ?>
<main><div class="wrap"><div class="confirm" id="confRoot">
  <svg class="cksvg" viewBox="0 0 52 52"><circle cx="26" cy="26" r="24"/><path d="M15 27l7.5 7.5L37 19"/></svg>
  <h1>Order placed. Thank you!</h1>
  <p id="confText">Our team will call to confirm your order shortly.</p>
  <p><a id="confWa" class="addbtn wabtn" href="#" target="_blank" rel="noopener" hidden><?php echo hh_wa_svg(); ?> Send order on WhatsApp</a></p>
  <p><a class="shopnow" href="shop.php">Continue Shopping</a></p>
</div></div></main>
<?php include __DIR__ . '/partials/footer.php'; ?>
</body>
</html>
