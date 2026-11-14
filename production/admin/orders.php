<?php
require __DIR__ . '/auth.php';
hh_admin_require();
$d = hh_load();
hh_admin_head('Orders');
hh_flash();
?>
<h1>Orders</h1>
<?php if (!$d['orders']): ?>
<p>No orders yet. When a customer checks out on the website, the order appears here and is also sent to the shop WhatsApp number.</p>
<?php else: ?>
<?php foreach (array_reverse($d['orders']) as $o): ?>
<div class="aorder">
  <div class="aohead"><b><?php echo hh_e($o['no']); ?></b><span><?php echo hh_e($o['date']); ?></span><b><?php echo hh_money($o['total']); ?></b></div>
  <p><?php echo hh_e($o['name']); ?> - <?php echo hh_e($o['phone']); ?> - <?php echo hh_e($o['city']); ?><br><?php echo hh_e($o['address']); ?><?php if (!empty($o['note'])): ?><br>Note: <?php echo hh_e($o['note']); ?><?php endif; ?></p>
  <table class="atable sm">
    <tr><th>Product</th><th>Part no.</th><th>Price</th><th>Qty</th><th>Line total</th></tr>
    <?php foreach ($o['items'] as $it): ?>
    <tr><td><?php echo hh_e($it['name']); ?></td><td><?php echo hh_e($it['part_no']); ?></td><td><?php echo hh_money($it['price']); ?></td><td><?php echo (int)$it['qty']; ?></td><td><?php echo hh_money($it['line']); ?></td></tr>
    <?php endforeach; ?>
  </table>
</div>
<?php endforeach; ?>
<?php endif; ?>
<?php hh_admin_foot(); ?>
