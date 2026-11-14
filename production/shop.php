<?php
require __DIR__ . '/config.php';
require __DIR__ . '/partials/icons.php';
require __DIR__ . '/partials/card.php';
$d = hh_load();
$page = 'shop';
$cat = isset($_GET['cat']) ? $_GET['cat'] : '';
$q = isset($_GET['q']) ? trim($_GET['q']) : '';
$catName = $cat ? hh_cat_name($d, $cat) : '';
$maxPrice = 0;
foreach ($d['products'] as $p) { if (!hh_is_rfq($p)) $maxPrice = max($maxPrice, (int)$p['price']); }
$title = $catName ? $catName . ' in Rawalpindi - H.H Traders' : 'Shop Fire Safety & Safety Equipment in Rawalpindi - H.H Traders';
$desc = $catName
  ? 'Buy ' . $catName . ' at wholesale prices from H.H Traders, Saddar Rawalpindi. Genuine products, bulk rates and delivery across Pakistan.'
  : 'Shop fire extinguishers, safety gear, hanging supports, HVAC, epoxy anchoring and power tools at H.H Traders, Rawalpindi. Wholesale & retail prices.';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<?php hh_head($d, $title, $desc, '/shop.php' . ($cat ? '?cat=' . urlencode($cat) : '')); ?>
</head>
<body>
<?php include __DIR__ . '/partials/header.php'; ?>
<main><div class="wrap">
  <h1 class="ptitle" id="shopTitle"><?php echo $catName ? hh_e($catName) : 'Shop'; ?></h1>
  <p class="pcount"><span id="shopCount"><?php echo count($d['products']); ?></span> products</p>
  <div class="toolbar">
    <div class="catdrop"><button id="catBtn" class="catbtn" aria-haspopup="listbox" aria-expanded="false"><span id="catBtnLabel"><?php echo $catName ? hh_e($catName) : 'All Products'; ?></span> <?php echo hh_icon('chevD'); ?></button>
      <div class="catmenu" id="catMenu" role="listbox">
        <button data-catpick="all">All Products</button>
        <?php foreach ($d['categories'] as $c): ?><button data-catpick="<?php echo hh_e($c['id']); ?>"><?php echo hh_e($c['name']); ?></button><?php endforeach; ?>
      </div>
    </div>
    <label class="fld">Sort <select id="sortSel"><option value="feat">Featured</option><option value="price-asc">Price: Low to High</option><option value="price-desc">Price: High to Low</option><option value="name">Name A-Z</option><option value="rating">Top Rated</option></select></label>
    <label class="fld grow">Max price <input type="range" id="priceRange" min="300" max="<?php echo $maxPrice; ?>" step="50" value="<?php echo $maxPrice; ?>"><b id="priceVal"><?php echo hh_money($maxPrice); ?></b></label>
    <label class="fld">Rating <select id="rateSel"><option value="0">Any rating</option><option value="4.5">4.5 &amp; up</option><option value="4.7">4.7 &amp; up</option></select></label>
  </div>
  <div class="grid" id="shopGrid">
    <?php foreach ($d['products'] as $i => $p): ?>
    <div class="cell" data-cat="<?php echo hh_e($p['cat']); ?>" data-price="<?php echo hh_is_rfq($p) ? 0 : (int)$p['price']; ?>" data-rfq="<?php echo hh_is_rfq($p) ? 1 : 0; ?>" data-rating="<?php echo hh_e($p['rating']); ?>" data-name="<?php echo hh_e(strtolower($p['name'])); ?>" data-idx="<?php echo $i; ?>">
      <?php echo hh_card($d, $p, ($i % 4) * 50); ?>
    </div>
    <?php endforeach; ?>
  </div>
  <div class="nores" id="noRes" hidden><p>No products match your filters.</p><button class="shopnow" id="clearF">Clear filters</button></div>
</div></main>
<?php include __DIR__ . '/partials/footer.php'; ?>
<script>
window.HH_INIT_SHOP = { cat: <?php echo json_encode($cat); ?>, q: <?php echo json_encode($q); ?> };
</script>
</body>
</html>
