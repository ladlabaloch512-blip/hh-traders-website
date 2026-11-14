<?php
require __DIR__ . '/auth.php';
hh_admin_require();
$d = hh_load();
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['act'] ?? '') === 'delete') {
    hh_admin_csrf_check();
    $id = (int)$_POST['id'];
    $d['products'] = array_values(array_filter($d['products'], function ($p) use ($id) { return (int)$p['id'] !== $id; }));
    $d['featured'] = array_values(array_filter($d['featured'], function ($f) use ($id) { return (int)$f !== $id; }));
    hh_save($d);
    $_SESSION['hh_msg'] = 'Product deleted.';
    header('Location: products.php'); exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['act'] ?? '') === 'feature') {
    hh_admin_csrf_check();
    $id = (int)$_POST['id'];
    if (in_array($id, $d['featured'], true) || in_array($id, array_map('intval', $d['featured']), true)) {
        $d['featured'] = array_values(array_filter($d['featured'], function ($f) use ($id) { return (int)$f !== $id; }));
        $_SESSION['hh_msg'] = 'Removed from Featured on the home page.';
    } else {
        $d['featured'][] = $id;
        $_SESSION['hh_msg'] = 'Added to Featured on the home page.';
    }
    hh_save($d);
    header('Location: products.php'); exit;
}
$d = hh_load();
$feat = array_map('intval', $d['featured']);
hh_admin_head('Products');
hh_flash();
?>
<h1>Products <a class="abtn" href="product-edit.php">+ Add Product</a></h1>
<table class="atable">
<tr><th>Image</th><th>Product</th><th>Category</th><th>Part no.</th><th>Price</th><th>New</th><th>Featured</th><th>Actions</th></tr>
<?php foreach ($d['products'] as $p): ?>
<tr>
  <td><img class="athumb" src="../<?php echo hh_e($p['img']); ?>" alt=""></td>
  <td><b><?php echo hh_e($p['name']); ?></b></td>
  <td><?php echo hh_e(hh_cat_name($d, $p['cat'])); ?></td>
  <td><?php echo hh_e($p['part_no']); ?></td>
  <td><?php echo hh_is_rfq($p) ? '<span class="arfq">Request for Quotation</span>' : hh_money($p['price']); ?></td>
  <td><?php echo !empty($p['is_new']) ? 'Yes' : '-'; ?></td>
  <td>
    <form method="post" class="ainline"><input type="hidden" name="csrf" value="<?php echo hh_admin_csrf(); ?>"><input type="hidden" name="act" value="feature"><input type="hidden" name="id" value="<?php echo (int)$p['id']; ?>">
    <button type="submit" class="amini"><?php echo in_array((int)$p['id'], $feat, true) ? 'Unfeature' : 'Feature'; ?></button></form>
  </td>
  <td class="aacts">
    <a class="abtn sm" href="product-edit.php?id=<?php echo (int)$p['id']; ?>">Edit</a>
    <form method="post" class="ainline" onsubmit="return confirm('Delete this product?');"><input type="hidden" name="csrf" value="<?php echo hh_admin_csrf(); ?>"><input type="hidden" name="act" value="delete"><input type="hidden" name="id" value="<?php echo (int)$p['id']; ?>">
    <button type="submit" class="abtn sm danger">Delete</button></form>
  </td>
</tr>
<?php endforeach; ?>
</table>
<?php hh_admin_foot(); ?>
