<?php
// Shared product card - used on Home (featured) and Shop.
// Price is OPTIONAL: an RFQ product shows "Request for Quotation" instead of a price.
// The main button follows the admin setting: shop = Add to Cart, whatsapp = Order on
// WhatsApp, rfq = Request for Quotation. A WhatsApp icon button is always available.
function hh_card($d, $p, $delay = 0) {
    $rfq = hh_is_rfq($p);
    $mode = $d['settings']['cta_mode'];
    $waAsk = hh_wa_link($d, 'Assalam o Alaikum, I want to ask about: ' . $p['name'] . ' (Part no. ' . $p['part_no'] . ')');
    $h = '<article class="card rv" style="--d:' . (int)$delay . 'ms">';
    $h .= '<div class="cimg"><a href="product.php?id=' . (int)$p['id'] . '"><img src="' . hh_e($p['img']) . '" alt="' . hh_e($p['name']) . ' - H.H Traders Rawalpindi" loading="lazy"></a>';
    if (!empty($p['is_new'])) { $h .= '<span class="newb">New</span>'; }
    $h .= '<button class="specbtn" data-qv="' . (int)$p['id'] . '" aria-label="Quick view ' . hh_e($p['name']) . '">' . hh_icon('search') . ' Specs</button></div>';
    $h .= '<div class="cbody"><span class="ccat">' . hh_e(hh_cat_name($d, $p['cat'])) . '</span>';
    $h .= '<a class="cname" href="product.php?id=' . (int)$p['id'] . '">' . hh_e($p['name']) . '</a>';
    $h .= '<span class="part">Part no. ' . hh_e($p['part_no']) . '</span>';
    $h .= '<span class="cstars">' . str_repeat('<span class="st">' . hh_star() . '</span>', 5) . ' <b>' . hh_e(number_format((float)$p['rating'], 1)) . '</b></span>';
    if ($rfq) {
        $h .= '<span class="rfq">Request for Quotation</span>';
    } else {
        $h .= '<span class="price">' . hh_money($p['price']) . '</span>';
    }
    $h .= '<div class="cacts">';
    if ($rfq || $mode === 'rfq') {
        $h .= '<a class="ctabtn rfqb" href="' . hh_e(hh_wa_link($d, 'Assalam o Alaikum, please send me a quotation for: ' . $p['name'] . ' (Part no. ' . $p['part_no'] . ')')) . '" target="_blank" rel="noopener">' . hh_icon('doc') . ' Request for Quotation</a>';
    } elseif ($mode === 'whatsapp') {
        $h .= '<a class="ctabtn wab" href="' . hh_e(hh_wa_link($d, 'Assalam o Alaikum, I want to order: ' . $p['name'] . ' (' . hh_money($p['price']) . ')')) . '" target="_blank" rel="noopener">' . hh_wa_svg() . ' Order on WhatsApp</a>';
    } else {
        $h .= '<button class="ctabtn addb" data-add="' . (int)$p['id'] . '">Add to Cart</button>';
    }
    $h .= '<a class="waic" href="' . hh_e($waAsk) . '" target="_blank" rel="noopener" aria-label="Ask about ' . hh_e($p['name']) . ' on WhatsApp">' . hh_wa_svg() . '</a></div>';
    $h .= '<div class="clinks"><button class="wlink" data-wish="' . (int)$p['id'] . '">' . hh_icon('heart') . ' Wishlist</button><button class="wlink" data-qv="' . (int)$p['id'] . '">' . hh_icon('eye') . ' Quick View</button></div>';
    $h .= '</div></article>';
    return $h;
}
