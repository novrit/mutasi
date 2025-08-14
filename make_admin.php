<?php
require 'inc/koneksi.php';
// Run this once in browser to create/update admin password, then delete this file.
$username = 'admin';
$password = 'admin123'; // change if you want
$hash = password_hash($password, PASSWORD_DEFAULT);
$sql = "INSERT INTO users (username,password_hash) VALUES ('".mysqli_real_escape_string($koneksi,$username)."','".mysqli_real_escape_string($koneksi,$hash)."') ON DUPLICATE KEY UPDATE password_hash='".mysqli_real_escape_string($koneksi,$hash)."'";
if (mysqli_query($koneksi,$sql)) echo "Admin created/updated. Username: $username Password: $password";
else echo "Error: ".mysqli_error($koneksi);
?>