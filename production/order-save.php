<?php
// Saves a checkout order into data.json (visible in the admin panel, Orders page)
// and returns the order number as JSON. Called by the checkout page.
require __DIR__ . '/config.php';
header('Content-Type: application/json');
$raw = file_get_contents('php://input');
$in = json_decode($raw, true);
if (!is_array($in) || empty($in['items']) || !is_array($in['items'])) {
    http_response_code(400); echo json_encode(array('ok' => false)); exit;
}
$d = hh_load();
$items = array(); $total = 0; $count = 0;
foreach ($in['items'] as $it) {
    $p = hh_product($d, (int)($it['id'] ?? 0));
    if (!$p || hh_is_rfq($p)) continue;
    $qty = max(1, (int)($it['qty'] ?? 1));
    $line = (int)$p['price'] * $qty;
    $items[] = array('id' => (int)$p['id'], 'name' => $p['name'], 'part_no' => $p['part_no'], 'price' => (int)$p['price'], 'qty' => $qty, 'line' => $line);
    $total += $line; $count += $qty;
}
if (!$items) { http_response_code(400); echo json_encode(array('ok' => false)); exit; }
$no = 'HH-' . date('Y') . '-' . str_pad((string)random_int(1000, 9999), 4, '0', STR_PAD_LEFT);
$order = array(
    'no' => $no, 'date' => date('Y-m-d H:i'),
    'name' => mb_substr(trim((string)($in['name'] ?? '')), 0, 80),
    'phone' => mb_substr(trim((string)($in['phone'] ?? '')), 0, 30),
    'city' => mb_substr(trim((string)($in['city'] ?? '')), 0, 60),
    'address' => mb_substr(trim((string)($in['address'] ?? '')), 0, 300),
    'note' => mb_substr(trim((string)($in['note'] ?? '')), 0, 300),
    'items' => $items, 'total' => $total, 'count' => $count
);
$d['orders'][] = $order;
hh_save($d);
echo json_encode(array('ok' => true, 'no' => $no, 'total' => $total, 'count' => $count));
