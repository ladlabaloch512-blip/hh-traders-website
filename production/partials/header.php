<?php
// Shared header: top bar, logo + search + account actions, red category nav.
// Expects $d (site data) and $page (current page key).
$s = $d['settings'];
$counts = array();
foreach ($d["products"] as $xp) { $counts[$xp["cat"]] = ($counts[$xp["cat"]] ?? 0) + 1; }
?>
<script>document.documentElement.classList.add('js')</script>
<div class="topbar"><div class="wrap">
  <span class="tb addr"><?php echo hh_icon('pin'); ?> <?php echo hh_e($s['address']); ?></span>
  <span class="tb"><?php echo hh_icon('mail'); ?> <?php echo hh_e($s['email']); ?></span>
  <span class="tb"><?php echo hh_icon('phone'); ?> <?php echo hh_e($s['phone1']); ?></span>
  <span class="sp"></span><span class="modes">B2B<i>|</i>Retail<i>|</i>Bulk Orders</span>
</div></div>
<header class="sitehead"><div class="wrap">
  <button class="burger" id="menuBtn" aria-label="Open menu"><?php echo hh_icon('menu'); ?></button>
  <a class="logo" href="index.php" aria-label="H.H Traders home"><?php echo hh_logo_svg(); ?><span class="lw"><b>H<em>.</em>H Traders</b><small><?php echo hh_e($s['tagline']); ?></small></span></a>
  <form class="searchbox" action="shop.php" method="get" role="search"><div class="sin">
    <input id="searchInput" name="q" type="search" placeholder="Search for products..." aria-label="Search products" autocomplete="off">
    <button class="sbtn" id="searchBtn" type="submit" aria-label="Search"><?php echo hh_icon('search'); ?></button>
  </div><div class="suggest" id="suggest"></div></form>
  <div class="hacts">
    <button class="hact" id="acctBtn"><?php echo hh_icon('user'); ?><span>My Account</span></button>
    <a class="hact" href="shop.php"><?php echo hh_icon('heart'); ?><span>Wishlist</span><span class="badge" data-wish-count style="display:none">0</span></a>
    <button class="hact" id="cartBtn"><?php echo hh_icon('cart'); ?><span>Cart</span><span class="badge" data-cart-count style="display:none">0</span></button>
  </div>
</div></header>
<nav class="mainnav"><div class="wrap">
  <div class="allcats"><button id="allCatsBtn" aria-haspopup="true"><?php echo hh_icon('menu'); ?> All Categories <?php echo hh_icon('chevD'); ?></button>
    <div class="panel" id="allCatsPanel">
      <?php foreach ($d['categories'] as $c): ?>
      <a href="shop.php?cat=<?php echo hh_e($c['id']); ?>"><?php echo hh_e($c['name']); ?><span class="n"><?php echo (int)($counts[$c['id']] ?? 0); ?> items</span></a>
      <?php endforeach; ?>
    </div>
  </div>
  <div class="navlinks">
    <a href="shop.php?cat=fire">Fire Fighting Equipment</a>
    <a href="shop.php?cat=safety">Safety Items</a>
    <a href="shop.php?cat=support">Hanging Supports System</a>
    <a href="shop.php?cat=hvac">HVAC &amp; Adhesive</a>
    <a href="shop.php?cat=anchor">Epoxy Products</a>
    <a href="shop.php?cat=tools">Power Tools &amp; More</a>
  </div>
</div></nav>
