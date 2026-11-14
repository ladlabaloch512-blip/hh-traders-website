<?php
require __DIR__ . '/config.php';
require __DIR__ . '/partials/icons.php';
$d = hh_load();
$page = 'checkout';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<?php hh_head($d, 'Checkout - H.H Traders Rawalpindi', 'Place your order at H.H Traders, Rawalpindi. Your order is also sent to us on WhatsApp with the product list.', '/checkout.php'); ?>
<meta name="robots" content="noindex, follow">
</head>
<body>
<?php include __DIR__ . '/partials/header.php'; ?>
<main><div class="wrap">
  <h1 class="ptitle">Checkout</h1>
  <div class="cogrid">
    <form class="coform" id="coForm" novalidate>
      <label>Full name<input id="coName" type="text" autocomplete="name" placeholder="e.g. Muhammad Ali"></label>
      <label>Phone (WhatsApp)<input id="coPhone" type="tel" autocomplete="tel" placeholder="03xx xxxxxxx"></label>
      <label>City<input id="coCity" type="text" autocomplete="address-level2" placeholder="e.g. Rawalpindi"></label>
      <label>Full address<textarea id="coAddr" rows="3" placeholder="House, street, area"></textarea></label>
      <label>Note (optional)<input id="coNote" type="text" placeholder="Anything we should know?"></label>
      <div class="paybox"><b>Payment:</b> Cash on Delivery / Bank Transfer</div>
      <button class="addbtn big" id="coSubmit" type="submit">Place Order</button>
      <p class="conote">Placing the order also opens WhatsApp with your order and product list, so our team gets it instantly on <?php echo hh_e($d['settings']['phone1']); ?>.</p>
    </form>
    <aside class="cosum"><h3>Order Summary</h3><div id="coItems"></div></aside>
  </div>
</div></main>
<?php include __DIR__ . '/partials/footer.php'; ?>
</body>
</html>
