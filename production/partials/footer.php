<?php $s = $d['settings']; $waHome = hh_wa_link($d, 'Assalam o Alaikum, I have a question about your products.'); ?>
<footer class="sitefoot"><div class="wrap"><div class="cols">
  <div><h4>H.H Traders</h4><p>Fire safety and safety equipment supplier in Rawalpindi, serving businesses, contractors and factories across Pakistan.</p></div>
  <div><h4>Quick Links</h4><a href="index.php">Home</a><a href="shop.php">Shop</a><a href="cart.php">Cart</a><a href="checkout.php">Checkout</a></div>
  <div><h4>Categories</h4>
    <?php foreach ($d['categories'] as $c): ?><a href="shop.php?cat=<?php echo hh_e($c['id']); ?>"><?php echo hh_e($c['name']); ?></a><?php endforeach; ?>
  </div>
  <div><h4>Contact Us</h4>
    <div class="frow"><?php echo hh_icon('pin'); ?><span><?php echo hh_e($s['address']); ?></span></div>
    <div class="frow"><?php echo hh_icon('phone'); ?><span><?php echo hh_e($s['phone1']); ?><br><?php echo hh_e($s['phone2']); ?></span></div>
    <div class="frow"><?php echo hh_icon('mail'); ?><span><?php echo hh_e($s['email']); ?></span></div>
    <a href="<?php echo hh_e($waHome); ?>" target="_blank" rel="noopener" style="color:#25d366;font-weight:700">Chat on WhatsApp</a>
  </div>
</div></div>
<div class="footbar"><div class="wrap"><span>&copy; H.H Traders. All rights reserved.</span><span>Fire Safety &amp; Safety Solutions - Rawalpindi</span></div></div>
</footer>

<div class="overlay" id="drawerRoot"><div class="scrim" data-close></div>
  <aside class="drawer" role="dialog" aria-label="Shopping cart"><header><h3>Your Cart</h3><button class="x" data-close aria-label="Close cart">&times;</button></header><div class="dbody" id="drawerBody"></div></aside>
</div>
<div class="overlay" id="menuRoot"><div class="scrim" data-close></div>
  <aside class="drawer left" role="dialog" aria-label="Menu"><header><h3>Menu</h3><button class="x" data-close aria-label="Close menu">&times;</button></header>
  <div class="dbody"><div class="mnav">
    <a href="index.php">Home</a><a href="shop.php">Shop All Products</a>
    <?php foreach ($d['categories'] as $c): ?><a href="shop.php?cat=<?php echo hh_e($c['id']); ?>"><?php echo hh_e($c['name']); ?></a><?php endforeach; ?>
    <a href="cart.php">Cart</a><a href="checkout.php">Checkout</a>
  </div>
  <p style="margin-top:16px;font-size:13.5px;color:var(--mut)"><?php echo hh_e($s['address']); ?><br><?php echo hh_e($s['phone1']); ?> | <?php echo hh_e($s['phone2']); ?><br><?php echo hh_e($s['email']); ?></p>
  </div></aside>
</div>
<div class="overlay" id="qvRoot"><div class="scrim" data-close></div>
  <div class="qvbox" role="dialog" aria-label="Quick view"><button class="x" data-close aria-label="Close quick view">&times;</button>
    <img id="qvImg" src="" alt=""><div class="qvin"><span class="ccat" id="qvCat"></span><h3 id="qvName"></h3><span id="qvStars"></span><span class="price" id="qvPrice"></span><p id="qvBlurb" style="color:#33383f;font-size:14px"></p>
    <div class="qtyrow"><button class="addbtn" id="qvAdd">Add to Cart</button><a class="ghost" id="qvView" href="#">View Product</a></div></div>
  </div>
</div>
<div class="toast" id="toast" role="status"></div>

<nav class="mbar" aria-label="Mobile navigation">
  <a class="mb<?php echo ($page === 'home') ? ' on' : ''; ?>" href="index.php"><?php echo hh_icon('home'); ?><span>Home</span></a>
  <button class="mb" id="mbCats"><?php echo hh_icon('grid'); ?><span>Categories</span></button>
  <a class="mb wa" href="<?php echo hh_e($waHome); ?>" target="_blank" rel="noopener"><span class="wacirc"><?php echo hh_wa_svg(); ?></span><span>H.H Traders</span></a>
  <button class="mb" id="mbCart"><?php echo hh_icon('cart'); ?><span class="badge" data-cart-count style="display:none">0</span><span>Cart</span></button>
  <button class="mb" id="mbAcct"><?php echo hh_icon('user'); ?><span>Account</span></button>
</nav>

<a class="wafloat" href="<?php echo hh_e($waHome); ?>" target="_blank" rel="noopener" aria-label="Chat on WhatsApp"><?php echo hh_wa_svg(); ?></a>
<?php hh_js_data($d); ?>
<script src="assets/store.js?v=20261114"></script>
