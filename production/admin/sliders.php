<?php
require __DIR__ . '/auth.php';
hh_admin_require();
$d = hh_load();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    hh_admin_csrf_check();
    $i = (int)($_POST['idx'] ?? -1);
    if (isset($d['sliders'][$i])) {
        $err = '';
        $up = hh_admin_upload('image', 'slide', $err);
        if ($up) $d['sliders'][$i]['img'] = $up;
        if ($err) { $_SESSION['hh_err'] = $err; header('Location: sliders.php'); exit; }
        $d['sliders'][$i]['kicker'] = trim($_POST['kicker'] ?? '');
        $d['sliders'][$i]['l1'] = trim($_POST['l1'] ?? '');
        $d['sliders'][$i]['l2'] = trim($_POST['l2'] ?? '');
        $d['sliders'][$i]['text'] = trim($_POST['text'] ?? '');
        $d['sliders'][$i]['cta'] = trim($_POST['cta'] ?? 'Shop Now');
        $d['sliders'][$i]['link'] = trim($_POST['link'] ?? 'shop.php');
        hh_save($d);
        $_SESSION['hh_msg'] = 'Slider ' . ($i + 1) . ' updated. It is live on the home page now.';
    }
    header('Location: sliders.php'); exit;
}
hh_admin_head('Sliders');
hh_flash();
?>
<h1>Home Page Sliders</h1>
<p>Change a slider image or its text here - the home page updates at once. Wide photos (about 1600 x 700) look best.</p>
<?php foreach ($d['sliders'] as $i => $sl): ?>
<form method="post" enctype="multipart/form-data" class="aform slideform">
<input type="hidden" name="csrf" value="<?php echo hh_admin_csrf(); ?>">
<input type="hidden" name="idx" value="<?php echo $i; ?>">
<h2>Slider <?php echo $i + 1; ?></h2>
<img class="aslide" src="../<?php echo hh_e($sl['img']); ?>" alt="">
<label>Replace image<input name="image" type="file" accept="image/*"></label>
<label>Top heading (small line)<input name="kicker" type="text" value="<?php echo hh_e($sl['kicker']); ?>"></label>
<label>Main heading - line 1 (white)<input name="l1" type="text" value="<?php echo hh_e($sl['l1']); ?>"></label>
<label>Main heading - line 2 (yellow)<input name="l2" type="text" value="<?php echo hh_e($sl['l2']); ?>"></label>
<label>Small text<input name="text" type="text" value="<?php echo hh_e($sl['text']); ?>"></label>
<label>Button text<input name="cta" type="text" value="<?php echo hh_e($sl['cta']); ?>"></label>
<label>Button link<input name="link" type="text" value="<?php echo hh_e($sl['link']); ?>"></label>
<button type="submit" class="abtn big">Save Slider <?php echo $i + 1; ?></button>
</form>
<?php endforeach; ?>
<?php hh_admin_foot(); ?>
