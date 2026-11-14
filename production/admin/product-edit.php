<?php
require __DIR__ . '/auth.php';
hh_admin_require();
$d = hh_load();
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$p = $id ? hh_product($d, $id) : null;
if ($id && !$p) { $_SESSION['hh_err'] = 'Product not found.'; header('Location: products.php'); exit; }
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    hh_admin_csrf_check();
    $name = trim($_POST['name'] ?? '');
    $err = '';
    $imgPath = $p ? $p['img'] : '';
    $up = hh_admin_upload('image', 'product', $err);
    if ($up) $imgPath = $up;
    if ($name === '') $err = 'Product name is required.';
    if (!$imgPath && !$err) $err = 'Please upload a product image.';
    if ($err) {
        $_SESSION['hh_err'] = $err;
        header('Location: product-edit.php' . ($id ? '?id=' . $id : '')); exit;
    }
    $priceRaw = trim($_POST['price'] ?? '');
    $row = array(
        'cat' => $_POST['cat'] ?? 'general',
        'name' => $name,
        'price' => $priceRaw === '' ? 0 : (int)$priceRaw,
        'rfq' => !empty($_POST['rfq']),
        'is_new' => !empty($_POST['is_new']),
        'part_no' => trim($_POST['part_no'] ?? ''),
        'img' => $imgPath,
        'blurb' => trim($_POST['blurb'] ?? ''),
        'rating' => max(0, min(5, (float)($_POST['rating'] ?? 4.5)))
    );
    if ($p) {
        foreach ($d['products'] as $i => $ex) { if ((int)$ex['id'] === $id) { $d['products'][$i] = array_merge($ex, $row); } }
        $_SESSION['hh_msg'] = 'Product updated.';
    } else {
        $maxId = 0; foreach ($d['products'] as $ex) { $maxId = max($maxId, (int)$ex['id']); }
        $row['id'] = $maxId + 1;
        if ($row['part_no'] === '') $row['part_no'] = 'HH-' . (100 + $row['id']);
        $d['products'][] = $row;
        $_SESSION['hh_msg'] = 'Product added.';
    }
    hh_save($d);
    header('Location: products.php'); exit;
}
hh_admin_head($p ? 'Edit Product' : 'Add Product');
hh_flash();
?>
<h1><?php echo $p ? 'Edit Product' : 'Add Product'; ?></h1>
<form method="post" enctype="multipart/form-data" class="aform">
<input type="hidden" name="csrf" value="<?php echo hh_admin_csrf(); ?>">
<label>Product name<input name="name" type="text" required value="<?php echo hh_e($p['name'] ?? ''); ?>"></label>
<label>Category
  <select name="cat"><?php foreach ($d['categories'] as $c): ?><option value="<?php echo hh_e($c['id']); ?>"<?php echo (($p['cat'] ?? '') === $c['id']) ? ' selected' : ''; ?>><?php echo hh_e($c['name']); ?></option><?php endforeach; ?></select>
</label>
<label>Price in Rs (leave empty for quotation-only)
  <input name="price" type="number" min="0" step="1" value="<?php echo ($p && (int)$p['price'] > 0) ? (int)$p['price'] : ''; ?>" placeholder="e.g. 5850">
</label>
<label class="acheck"><input name="rfq" type="checkbox" value="1"<?php echo (!empty($p['rfq'])) ? ' checked' : ''; ?>> Show "Request for Quotation" instead of price &amp; buying</label>
<label class="acheck"><input name="is_new" type="checkbox" value="1"<?php echo (!empty($p['is_new'])) ? ' checked' : ''; ?>> Mark as "New" on the website</label>
<label>Part no.<input name="part_no" type="text" value="<?php echo hh_e($p['part_no'] ?? ''); ?>" placeholder="e.g. HH-101"></label>
<label>Rating (0 to 5)<input name="rating" type="number" min="0" max="5" step="0.1" value="<?php echo hh_e($p['rating'] ?? '4.5'); ?>"></label>
<label>Short description<textarea name="blurb" rows="3"><?php echo hh_e($p['blurb'] ?? ''); ?></textarea></label>
<label>Product image <?php if ($p): ?>(current: <?php echo hh_e($p['img']); ?>)<?php endif; ?>
  <input name="image" type="file" accept="image/*"><?php if ($p): ?> <img class="athumb lg" src="../<?php echo hh_e($p['img']); ?>" alt=""><?php endif; ?>
</label>
<button type="submit" class="abtn big">Save Product</button>
<a class="abtn ghost" href="products.php">Back to Products</a>
</form>
<?php hh_admin_foot(); ?>
