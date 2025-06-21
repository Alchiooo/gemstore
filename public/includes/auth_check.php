<?php
// public/includes/auth_check.php

session_start();

// Periksa apakah pengguna sudah login (ada user_id di session)
if (!isset($_SESSION['user_id'])) {
    // Jika belum login, arahkan kembali ke halaman login
    header("Location: /growtopia_gems_project/public/auth/login.php");
    exit();
}

// Dapatkan informasi pengguna dari sesi
$current_user_id = $_SESSION['user_id'];
$current_username = $_SESSION['username'];
$current_user_role = $_SESSION['role'];


?>