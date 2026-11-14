<?php
require __DIR__ . '/auth.php';
if (hh_admin_logged_in()) { header('Location: index.php'); exit; }
$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $u = $_POST['user'] ?? '';
    $p = $_POST['pass'] ?? '';
    if (hash_equals(HH_ADMIN_USER, $u) && password_verify($p, HH_ADMIN_HASH)) {
        session_regenerate_id(true);
        $_SESSION['hh_admin'] = true;
        header('Location: index.php'); exit;
    }
    $err = 'Wrong username or password.';
}
?>
<!DOCTYPE html>
<html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Admin Login - H.H Traders</title><link rel="stylesheet" href="admin.css?v=20261114"></head>
<body><div class="loginbox">
<h1>H.H Traders</h1><p>Admin Panel</p>
<?php if ($err): ?><div class="aerr"><?php echo hh_e($err); ?></div><?php endif; ?>
<form method="post">
<label>Username<input name="user" type="text" autocomplete="username" required></label>
<label>Password<input name="pass" type="password" autocomplete="current-password" required></label>
<button type="submit">Login</button>
</form></div></body></html>
