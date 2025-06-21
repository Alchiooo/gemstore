<?php
// public/process/logout_process.php
session_start();
session_unset(); // Hapus semua variabel sesi
session_destroy(); // Hancurkan sesi
header("Location: /growtopia_gems_project/public/auth/login.php"); // Redirect ke halaman login
exit();
?>