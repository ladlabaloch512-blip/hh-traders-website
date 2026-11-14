<?php
require __DIR__ . '/config.php';
require __DIR__ . '/partials/icons.php';
require __DIR__ . '/partials/card.php';
$d = hh_load();
$page = 'home';
$rail = array(
  array('fire', 'flame', 'Fire Fighting Equipment'),
  array('safety', 'shield', 'Safety Items'),
  array('safety', 'helmet', 'Personal Protective Gear'),
  array('tools', 'wrench', 'Tools & Hardware'),
  array('general', 'gear', 'Industrial Supplies')
);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<?php hh_head($d,
  'H.H Traders - Fire Safety & Safety Equipment Supplier in Rawalpindi',
  'H.H Traders is a general order supplier of fire fighting equipment, safety items, power tools, HVAC and anchoring in Saddar, Rawalpindi. Wholesale & retail, delivery across Pakistan.',
  '/'); ?>
<?php hh_jsonld_store($d); ?>
</head>
<body>
<?php include __DIR__ . '/partials/header.php'; ?>
<main>
  <div class="hero" id="heroSlider">
    <div class="slides">
      <?php foreach ($d['sliders'] as $i => $sl): ?>
      <div class="slide<?php echo $i === 0 ? ' on' : ''; ?>" style="background-image:url('<?php echo hh_e($sl['img']); ?>')">
        <div class="shade"></div>
        <div class="cap"><span class="kick"><?php echo hh_e($sl['kicker']); ?></span>
          <h1 class="big"><?php echo hh_e($sl['l1']); ?> <em><?php echo hh_e($sl['l2']); ?></em></h1>
          <p><?php echo hh_e($sl['text']); ?></p>
          <a class="shopnow" href="<?php echo hh_e($sl['link']); ?>"><?php echo hh_e($sl['cta']); ?> <?php echo hh_icon('arrowR'); ?></a>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <button class="arrow prev" aria-label="Previous slide"><?php echo hh_icon('chevL'); ?></button>
    <button class="arrow next" aria-label="Next slide"><?php echo hh_icon('chevR'); ?></button>
    <div class="dots"><?php foreach ($d['sliders'] as $i => $sl): ?><button class="dot<?php echo $i === 0 ? ' on' : ''; ?>" data-slide="<?php echo $i; ?>" aria-label="Slide <?php echo $i + 1; ?>"></button><?php endforeach; ?></div>
    <div class="herorail">
      <?php foreach ($rail as $r): ?>
      <a href="shop.php?cat=<?php echo $r[0]; ?>"><span class="ric"><?php echo hh_icon($r[1]); ?></span><?php echo hh_e($r[2]); ?></a>
      <?php endforeach; ?>
    </div>
  </div>

  <section><div class="wrap"><div class="sechead"><h2>Our Categories</h2></div>
    <div class="tiles">
      <?php foreach ($d['tiles'] as $i => $t): ?>
      <a class="tile rv" style="--d:<?php echo $i * 40; ?>ms" href="shop.php?cat=<?php echo hh_e($t['cat']); ?>"><img src="<?php echo hh_e($t['img']); ?>" alt="<?php echo hh_e($t['name']); ?> in Rawalpindi" loading="lazy"><span><?php echo hh_e($t['name']); ?></span></a>
      <?php endforeach; ?>
    </div></div>
  </section>

  <section><div class="wrap"><div class="sechead"><h2>Featured Products</h2></div>
    <div class="fgrid">
      <?php $i = 0; foreach ($d['featured'] as $fid): $p = hh_product($d, $fid); if (!$p) continue; echo hh_card($d, $p, ($i++ % 4) * 60); endforeach; ?>
    </div></div>
  </section>

  <div class="trust"><div class="wrap">
    <div class="ti"><?php echo hh_icon('badge'); ?> 100% Genuine Products</div>
    <div class="ti"><?php echo hh_icon('truck'); ?> Fast Delivery Across Pakistan</div>
    <div class="ti"><?php echo hh_icon('headset'); ?> Expert Support</div>
    <div class="ti"><?php echo hh_icon('users'); ?> Trusted by Businesses &amp; Individuals</div>
  </div></div>

  <section><div class="wrap"><div class="aboutgrid">
    <div class="aboutbox rv"><h2>About H.H Traders</h2>
      <p>H.H Traders is a leading supplier of fire safety and safety equipment in Pakistan. We provide high-quality products, expert advice and reliable service to keep you and your business safe.</p>
      <a class="shopnow" href="shop.php">Learn More <?php echo hh_icon('arrowR'); ?></a>
    </div>
    <div class="whybox rv"><div class="wbg" style="background-image:url(uploads/slide-1.jpg)"></div>
      <div class="wmain"><svg class="shield" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><?php echo $GLOBALS['HH_ICONS']['shield']; ?></svg>
      <h3>Protect What Matters Most <span>With H.H Traders</span></h3></div>
      <div class="wside"><h4>Why Choose Us?</h4><ul>
        <li><?php echo hh_icon('check'); ?> Premium Quality Products</li>
        <li><?php echo hh_icon('check'); ?> Competitive Prices</li>
        <li><?php echo hh_icon('check'); ?> Dedicated Support</li>
        <li><?php echo hh_icon('check'); ?> Wide Range of Safety Solutions</li>
      </ul></div>
    </div>
  </div></div></section>

  <div class="brands"><div class="wrap"><?php foreach ($d['settings']['brands'] as $b): ?><span><?php echo hh_e($b); ?></span><?php endforeach; ?></div></div>
</main>
<?php include __DIR__ . '/partials/footer.php'; ?>
</body>
</html>
