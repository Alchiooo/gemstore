<?php
// generate_admin_hash.php

$admin_password = 'admin123'; // GANTI dengan password yang Anda inginkan untuk admin
$hashed_admin_password = password_hash($admin_password, PASSWORD_DEFAULT);

echo "Password asli admin: " . $admin_password . "<br>";
echo "Hash password admin: " . $hashed_admin_password . "<br><br>";
echo "Salin hash ini ke database Anda untuk user 'admin'.";

?>