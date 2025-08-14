<?php
require 'inc/koneksi.php';
session_start();
$err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $u = trim($_POST['username'] ?? '');
    $p = trim($_POST['password'] ?? '');
    
    if ($u === '' || $p === '') {
        $err = 'Username dan password wajib diisi';
    } else {
        $u = mysqli_real_escape_string($koneksi, $u);
        $p = mysqli_real_escape_string($koneksi, $p);
        $q = mysqli_query($koneksi, "SELECT * FROM users WHERE username='$u' AND password='$p' LIMIT 1");
        
        if ($q && mysqli_num_rows($q)) {
            $r = mysqli_fetch_assoc($q);
            $_SESSION['user_id'] = $r['id'];
            $_SESSION['username'] = $r['username'];
            header('Location: index.php');
            exit;
        } else {
                  }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Login Inventaris</title>
<link rel="stylesheet" href="css/login.css">
</head>
<body>
<div class="login-card">
        <div style="display:flex;flex-direction:column;align-items:center;margin-bottom:24px;">
            <img src="assets/Logo.png" alt="Logo Universitas Nias" style="width:64px;margin-bottom:16px;">
            <span style="font-size:1.3em;font-weight:bold;color:#000;text-align:center;">SELAMAT DATANG</span>
            <span style="display:block;height:18px;"></span>
            <span style="font-size:1.1em;color:#000;text-align:center;">UNIVERSITAS NIAS</span>
        </div>
    <?php if($err): ?>
        <div class="error"><?= $err ?></div>
    <?php endif; ?>
    <form method="post" action="">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
    </form>
    <small>Not a member? Sign Up</small>
</div>
</body>
</html>
