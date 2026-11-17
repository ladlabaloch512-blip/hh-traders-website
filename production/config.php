<?php
// H.H Traders - shared config and helpers. All site content lives in data.json
// and is managed from the admin panel. Nothing is hardcoded in the pages.

define('HH_ROOT', __DIR__);
define('HH_DATA', HH_ROOT . '/data.json');
define('HH_DATA_BAK', HH_ROOT . '/data.json.bak');

function hh_load() {
    $raw = @file_get_contents(HH_DATA);
    $d = $raw ? json_decode($raw, true) : null;
    if (!is_array($d)) { $d = array('settings' => array(), 'sliders' => array(), 'categories' => array(), 'tiles' => array(), 'featured' => array(), 'products' => array(), 'orders' => array()); }
    return $d;
}

function hh_save($d) {
    if (is_file(HH_DATA)) { @copy(HH_DATA, HH_DATA_BAK); }
    $tmp = HH_DATA . '.tmp';
    file_put_contents($tmp, json_encode($d, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), LOCK_EX);
    rename($tmp, HH_DATA);
}

function hh_e($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

function hh_money($n) { return 'Rs ' . number_format((int)$n); }

function hh_product($d, $id) {
    foreach ($d['products'] as $p) { if ((int)$p['id'] === (int)$id) return $p; }
    return null;
}

function hh_cat_name($d, $id) {
    foreach ($d['categories'] as $c) { if ($c['id'] === $id) return $c['name']; }
    return '';
}

// A product is quotable (no online price) when RFQ is on or the price is empty/zero.
function hh_is_rfq($p) {
    return !empty($p['rfq']) || empty($p['price']) || (int)$p['price'] <= 0;
}

function hh_wa_link($d, $text) {
    return 'https://wa.me/' . preg_replace('/\D/', '', $d['settings']['whatsapp']) . '?text=' . rawurlencode($text);
}

// ---------- SEO ----------
function hh_head($d, $title, $desc, $path = '/', $img = null, $type = 'website') {
    $base = rtrim($d['settings']['base_url'], '/');
    $url = $base . $path;
    $imgUrl = $img ? $base . '/' . ltrim($img, '/') : $base . '/uploads/slide-1.jpg';
    echo '<meta charset="utf-8">' . "\n";
    echo '<meta name="viewport" content="width=device-width, initial-scale=1">' . "\n";
    echo '<title>' . hh_e($title) . '</title>' . "\n";
    echo '<meta name="description" content="' . hh_e($desc) . '">' . "\n";
    echo '<link rel="canonical" href="' . hh_e($url) . '">' . "\n";
    echo '<meta name="robots" content="index, follow">' . "\n";
    echo '<meta name="geo.region" content="PK-PB"><meta name="geo.placename" content="Rawalpindi">' . "\n";
    echo '<meta property="og:type" content="' . hh_e($type) . '"><meta property="og:site_name" content="H.H Traders">' . "\n";
    echo '<meta property="og:title" content="' . hh_e($title) . '"><meta property="og:description" content="' . hh_e($desc) . '">' . "\n";
    echo '<meta property="og:url" content="' . hh_e($url) . '"><meta property="og:image" content="' . hh_e($imgUrl) . '">' . "\n";
    echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
    echo '<link rel="icon" href="assets/favicon.png" type="image/png">' . "\n";
    echo '<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
    echo '<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Oswald:wght@500;600;700&display=swap" rel="stylesheet">' . "\n";
    echo '<link rel="stylesheet" href="assets/style.css?v=20261114">' . "\n";
}

function hh_jsonld_store($d) {
    $s = $d['settings'];
    $ld = array(
        '@context' => 'https://schema.org',
        '@type' => 'LocalBusiness',
        'name' => 'H.H Traders',
        'description' => 'Fire fighting equipment, safety items, power tools and general order supplies in Rawalpindi, Pakistan.',
        'url' => rtrim($s['base_url'], '/') . '/',
        'email' => $s['email'],
        'telephone' => '+92' . substr(preg_replace('/\D/', '', $s['phone1']), 1),
        'priceRange' => 'Rs',
        'address' => array(
            '@type' => 'PostalAddress',
            'streetAddress' => 'Office #14, Mumtaz Market, City Saddar Road, Jinnah Road',
            'addressLocality' => 'Rawalpindi',
            'addressRegion' => 'Punjab',
            'addressCountry' => 'PK'
        ),
        'areaServed' => array('Rawalpindi', 'Islamabad', 'Pakistan'),
        'sameAs' => array()
    );
    echo '<script type="application/ld+json">' . json_encode($ld, JSON_UNESCAPED_SLASHES) . '</script>' . "\n";
}

// Front-end data for the cart engine (prices live here so admin changes apply at once).
function hh_js_data($d) {
    $out = array(
        'wa' => preg_replace('/\D/', '', $d['settings']['whatsapp']),
        'cta_mode' => $d['settings']['cta_mode'],
        'products' => array()
    );
    foreach ($d['products'] as $p) {
        $out['products'][] = array(
            'id' => (int)$p['id'], 'name' => $p['name'], 'cat' => $p['cat'],
            'price' => hh_is_rfq($p) ? 0 : (int)$p['price'], 'rfq' => hh_is_rfq($p),
            'img' => $p['img'], 'part_no' => $p['part_no'], 'blurb' => $p['blurb'],
            'rating' => (float)$p['rating'], 'cat' => $p['cat']
        );
    }
    echo '<script>window.HH = ' . json_encode($out, JSON_UNESCAPED_SLASHES) . ';</script>' . "\n";
}
