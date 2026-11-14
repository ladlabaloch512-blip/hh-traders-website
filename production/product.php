<?php
require __DIR__ . '/config.php';
require __DIR__ . '/partials/icons.php';
require __DIR__ . '/partials/card.php';
$d = hh_load();
$page = 'shop';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$p = hh_product($d, $id);
if (!$p) { header('Location: 404.php'); exit; }
$rfq = hh_is_rfq($p);
$mode = $d['settings']['cta_mode'];
$catName = hh_cat_name($d, $p['cat']);
$title = $p['name'] . ' - Buy in Rawalpindi | H.H Traders';
$desc = $p['name'] . ' available at H.H Traders, Saddar Rawalpindi. ' . $p['blurb'] . ' Wholesale & retail, delivery across Pakistan.';
$ld = array('@context' => 'https://schema.org', '@type' => 'Product', 'name' => $p['name'], 'image' => rtrim($d['settings']['base_url'], '/') . '/' . $p['img'], 'description' => $p['blurb'], 'sku' => $p['part_no'], 'category' => $catName);
if (!$rfq) { $ld['offers'] = array('@type' => 'Offer', 'priceCurrency' => 'PKR', 'price' => (int)$p['price'], 'availability' => 'https://schema.org/InStock'); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
<?php hh_head($d, $title, $desc, '/product.php?id=' . $id, $p['img'], 'product'); ?>
<script type="application/ld+json"><?php echo json_encode($ld, JSON_UNESCAPED_SLASHES); ?></script>
</head>
<body>
<?php include __DIR__ . '/partials/header.php'; ?>
<main><div class="wrap">
  <nav class="crumbs" aria-label="Breadcrumb"><a href="index.php">Home</a> / <a href="shop.php">Shop</a> / <a href="shop.php?cat=<?php echo hh_e($p['cat']); ?>"><?php echo hh_e($catName); ?></a> / <span><?php echo hh_e($p['name']); ?></span></nav>
  <div class="pdgrid">
    <div class="pdimg"><img src="<?php echo hh_e($p['img']); ?>" alt="<?php echo hh_e($p['name']); ?> - H.H Traders Rawalpindi"><?php if (!empty($p['is_new'])): ?><span class="newb">New</span><?php endif; ?></div>
    <div class="pdinfo">
      <span class="ccat"><?php echo hh_e($catName); ?></span>
      <h1 class="pdname"><?php echo hh_e($p['name']); ?></h1>
      <span class="pdstars"><?php echo str_repeat('<span class="st">' . hh_star() . '</span>', 5); ?> <b><?php echo hh_e(number_format((float)$p['rating'], 1)); ?> rating</b></span>
      <?php if ($rfq): ?>
      <span class="rfq big">Request for Quotation</span>
      <?php else: ?>
      <span class="pdprice"><?php echo hh_money($p['price']); ?></span>
      <?php endif; ?>
      <p class="pdblurb"><?php echo hh_e($p['blurb']); ?></p>
      <div class="qtyrow">
        <?php if (!$rfq && $mode === 'shop'): ?>
        <div class="qty"><button id="qMinus" aria-label="Decrease quantity">-</button><span id="qVal">1</span><button id="qPlus" aria-label="Increase quantity">+</button></div>
        <button class="addbtn" id="pdAdd" data-addqty="<?php echo (int)$p['id']; ?>">Add to Cart</button>
        <?php elseif (!$rfq && $mode === 'whatsapp'): ?>
        <a class="addbtn wabtn" href="<?php echo hh_e(hh_wa_link($d, 'Assalam o Alaikum, I want to order: ' . $p['name'] . ' (' . hh_money($p['price']) . ')')); ?>" target="_blank" rel="noopener"><?php echo hh_wa_svg(); ?> Order on WhatsApp</a>
        <?php else: ?>
        <a class="addbtn rfqbtn" href="<?php echo hh_e(hh_wa_link($d, 'Assalam o Alaikum, please send me a quotation for: ' . $p['name'] . ' (Part no. ' . $p['part_no'] . ')')); ?>" target="_blank" rel="noopener"><?php echo hh_icon('doc'); ?> Request for Quotation</a>
        <?php endif; ?>
        <button class="ghost" id="pdWish" data-wish="<?php echo (int)$p['id']; ?>"><?php echo hh_icon('heart'); ?> Wishlist</button>
      </div>
      <a class="waline" href="<?php echo hh_e(hh_wa_link($d, 'Assalam o Alaikum, I want to ask about: ' . $p['name'] . ' (Part no. ' . $p['part_no'] . ')')); ?>" target="_blank" rel="noopener"><?php echo hh_wa_svg(); ?> Ask on WhatsApp - <?php echo hh_e($d['settings']['phone1']); ?></a>
      <table class="spectab">
        <tr><td>Part no.</td><td><?php echo hh_e($p['part_no']); ?></td></tr>
        <tr><td>Category</td><td><?php echo hh_e($catName); ?></td></tr>
        <tr><td>Delivery</td><td>2-3 working days</td></tr>
        <tr><td>Payment</td><td>Cash on Delivery / Bank Transfer</td></tr>
        <tr><td>Stock</td><td>In Stock</td></tr>
      </table>
    </div>
  </div>
  <div class="sechead" style="margin-top:34px"><h2>Related Products</h2></div>
  <div class="fgrid">
    <?php $n = 0; foreach ($d['products'] as $rp): if ($rp['cat'] === $p['cat'] && $rp['id'] !== $p['id'] && $n < 4): echo hh_card($d, $rp, $n * 60); $n++; endif; endforeach; ?>
  </div>
</div></main>
<?php include __DIR__ . '/partials/footer.php'; ?>
</body>
</html>
