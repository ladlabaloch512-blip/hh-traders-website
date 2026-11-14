<?php
require __DIR__ . '/auth.php';
hh_admin_require();
$d = hh_load();
$rfqCount = 0;
foreach ($d['products'] as $p) { if (hh_is_rfq($p)) $rfqCount++; }
hh_admin_head('Dashboard');
hh_flash();
?>
<h1>Dashboard</h1>
<div class="acards">
  <div class="acard"><b><?php echo count($d['products']); ?></b><span>Products</span></div>
  <div class="acard"><b><?php echo $rfqCount; ?></b><span>Quotation-only products</span></div>
  <div class="acard"><b><?php echo count($d['sliders']); ?></b><span>Home sliders</span></div>
  <div class="acard"><b><?php echo count($d['orders']); ?></b><span>Orders received</span></div>
</div>
<h2>Quick actions</h2>
<p class="aquick">
  <a class="abtn" href="product-edit.php">+ Add Product</a>
  <a class="abtn" href="sliders.php">Change Slider Images</a>
  <a class="abtn" href="settings.php">Site Settings</a>
  <a class="abtn" href="orders.php">View Orders</a>
</p>
<h2>Latest orders</h2>
<?php if (!$d['orders']): ?>
<p>No orders yet. Orders placed on the website checkout appear here.</p>
<?php else: ?>
<table class="atable"><tr><th>Order</th><th>Date</th><th>Customer</th><th>Items</th><th>Total</th></tr>
<?php foreach (array_slice(array_reverse($d['orders']), 0, 5) as $o): ?>
<tr><td><?php echo hh_e($o['no']); ?></td><td><?php echo hh_e($o['date']); ?></td><td><?php echo hh_e($o['name']); ?> (<?php echo hh_e($o['city']); ?>)</td><td><?php echo (int)$o['count']; ?></td><td><?php echo hh_money($o['total']); ?></td></tr>
<?php endforeach; ?>
</table>
<?php endif; ?>
<?php hh_admin_foot(); ?>
