<?php
require __DIR__ . '/auth.php';
hh_admin_require();
$d = hh_load();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    hh_admin_csrf_check();
    $s = $d['settings'];
    $s['tagline'] = trim($_POST['tagline'] ?? $s['tagline']);
    $s['email'] = trim($_POST['email'] ?? $s['email']);
    $s['phone1'] = trim($_POST['phone1'] ?? $s['phone1']);
    $s['phone2'] = trim($_POST['phone2'] ?? $s['phone2']);
    $s['whatsapp'] = preg_replace('/\D/', '', $_POST['whatsapp'] ?? $s['whatsapp']);
    $s['address'] = trim($_POST['address'] ?? $s['address']);
    $s['base_url'] = rtrim(trim($_POST['base_url'] ?? $s['base_url']), '/');
    $mode = $_POST['cta_mode'] ?? 'shop';
    $s['cta_mode'] = in_array($mode, array('shop', 'whatsapp', 'rfq'), true) ? $mode : 'shop';
    $d['settings'] = $s;
    hh_save($d);
    $_SESSION['hh_msg'] = 'Settings saved. The website is updated.';
    header('Location: settings.php'); exit;
}
$s = $d['settings'];
hh_admin_head('Settings');
hh_flash();
?>
<h1>Site Settings</h1>
<form method="post" class="aform">
<input type="hidden" name="csrf" value="<?php echo hh_admin_csrf(); ?>">
<label>Product button style (whole website)
  <select name="cta_mode">
    <option value="shop"<?php echo $s['cta_mode'] === 'shop' ? ' selected' : ''; ?>>Shop Now - customers add to cart and check out</option>
    <option value="whatsapp"<?php echo $s['cta_mode'] === 'whatsapp' ? ' selected' : ''; ?>>WhatsApp - customers order on WhatsApp</option>
    <option value="rfq"<?php echo $s['cta_mode'] === 'rfq' ? ' selected' : ''; ?>>Request for Quotation - customers ask for a quote</option>
  </select>
</label>
<p class="ahint">Note: a product whose price is empty, or whose own "Request for Quotation" tick is on, always shows Request for Quotation, whatever is chosen here.</p>
<label>WhatsApp number (orders and chat go to this number)
  <input name="whatsapp" type="text" value="<?php echo hh_e($s['whatsapp']); ?>" placeholder="923125488700">
</label>
<label>Email<input name="email" type="email" value="<?php echo hh_e($s['email']); ?>"></label>
<label>Phone 1<input name="phone1" type="text" value="<?php echo hh_e($s['phone1']); ?>"></label>
<label>Phone 2<input name="phone2" type="text" value="<?php echo hh_e($s['phone2']); ?>"></label>
<label>Address<input name="address" type="text" value="<?php echo hh_e($s['address']); ?>"></label>
<label>Tagline (under the logo)<input name="tagline" type="text" value="<?php echo hh_e($s['tagline']); ?>"></label>
<label>Website address (for Google SEO links)<input name="base_url" type="text" value="<?php echo hh_e($s['base_url']); ?>"></label>
<button type="submit" class="abtn big">Save Settings</button>
</form>
<?php hh_admin_foot(); ?>
