<?php
// Admin auth + helpers. Every admin page includes this first.
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/credentials.php';
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params(array('httponly' => true, 'samesite' => 'Lax'));
    session_start();
}
function hh_admin_logged_in() { return !empty($_SESSION['hh_admin']); }
function hh_admin_require() {
    if (!hh_admin_logged_in()) { header('Location: login.php'); exit; }
}
function hh_admin_csrf() {
    if (empty($_SESSION['hh_csrf'])) { $_SESSION['hh_csrf'] = bin2hex(random_bytes(16)); }
    return $_SESSION['hh_csrf'];
}
function hh_admin_csrf_check() {
    if (!isset($_POST['csrf']) || !hash_equals($_SESSION['hh_csrf'] ?? '', $_POST['csrf'])) {
        http_response_code(403); exit('Invalid session token. Go back and try again.');
    }
}
// Upload an image into /uploads and return its relative path, or null + error in $err.
function hh_admin_upload($field, $prefix, &$err) {
    if (!isset($_FILES[$field]) || $_FILES[$field]['error'] === UPLOAD_ERR_NO_FILE) return null;
    $f = $_FILES[$field];
    if ($f['error'] !== UPLOAD_ERR_OK) { $err = 'Upload failed (code ' . $f['error'] . ').'; return null; }
    if ($f['size'] > 5 * 1024 * 1024) { $err = 'Image is larger than 5 MB.'; return null; }
    $ext = strtolower(pathinfo($f['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, array('jpg', 'jpeg', 'png', 'webp'), true)) { $err = 'Only JPG, PNG or WEBP images are allowed.'; return null; }
    $mime = mime_content_type($f['tmp_name']);
    if (strpos((string)$mime, 'image/') !== 0) { $err = 'That file is not an image.'; return null; }
    $name = $prefix . '-' . date('YmdHis') . '-' . bin2hex(random_bytes(3)) . '.' . $ext;
    $dest = HH_ROOT . '/uploads/' . $name;
    if (!move_uploaded_file($f['tmp_name'], $dest)) { $err = 'Could not save the uploaded image.'; return null; }
    return 'uploads/' . $name;
}
function hh_admin_head($title) {
    echo '<!DOCTYPE html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">';
    echo '<title>' . hh_e($title) . ' - H.H Traders Admin</title><link rel="stylesheet" href="admin.css?v=20261114"></head><body>';
    echo '<div class="abar"><b>H.H Traders - Admin</b><nav>'
      . '<a href="index.php">Dashboard</a><a href="products.php">Products</a><a href="sliders.php">Sliders</a>'
      . '<a href="settings.php">Settings</a><a href="orders.php">Orders</a><a href="../index.php" target="_blank">View Site</a>'
      . '<a href="logout.php">Logout</a></nav></div><main class="awrap">';
}
function hh_admin_foot() { echo '</main></body></html>'; }
function hh_flash() {
    if (!empty($_SESSION['hh_msg'])) { echo '<div class="amsg">' . hh_e($_SESSION['hh_msg']) . '</div>'; unset($_SESSION['hh_msg']); }
    if (!empty($_SESSION['hh_err'])) { echo '<div class="aerr">' . hh_e($_SESSION['hh_err']) . '</div>'; unset($_SESSION['hh_err']); }
}
