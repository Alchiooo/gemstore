<?php
// public/process/register_process.php
require_once('../../config/db.php'); // Sesuaikan path ke file db.php
session_start(); // Mulai sesi

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $growid = $_POST['growid'] ?? ''; // Opsional
    $world_name = $_POST['world_name'] ?? ''; // Opsional

    // Validasi input
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $_SESSION['message'] = "Semua kolom wajib diisi kecuali GrowID dan World Name.";
        $_SESSION['message_type'] = "error";
        header("Location: /growtopia_gems_project/public/auth/register.php");
        exit();
    }

    if ($password !== $confirm_password) {
        $_SESSION['message'] = "Konfirmasi password tidak cocok.";
        $_SESSION['message_type'] = "error";
        header("Location: /growtopia_gems_project/public/auth/register.php");
        exit();
    }

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Cek apakah username atau email sudah terdaftar
    $stmt_check = $conn->prepare("SELECT user_id FROM users WHERE username = ? OR email = ?");
    $stmt_check->bind_param("ss", $username, $email);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows > 0) {
        $_SESSION['message'] = "Username atau Email sudah terdaftar. Silakan gunakan yang lain.";
        $_SESSION['message_type'] = "error";
        header("Location: /growtopia_gems_project/public/auth/register.php");
        exit();
    }
    $stmt_check->close();

    // Masukkan data pengguna baru ke database
    $stmt_insert = $conn->prepare("INSERT INTO users (username, password, email, growid, world_name, role, balance) VALUES (?, ?, ?, ?, ?, 'pelanggan', 0)");
    $stmt_insert->bind_param("sssss", $username, $hashed_password, $email, $growid, $world_name);

    if ($stmt_insert->execute()) {
        $_SESSION['message'] = "Registrasi berhasil! Silakan login.";
        $_SESSION['message_type'] = "success";
        header("Location: /growtopia_gems_project/public/auth/login.php");
        exit();
    } else {
        $_SESSION['message'] = "Terjadi kesalahan saat registrasi: " . $stmt_insert->error;
        $_SESSION['message_type'] = "error";
        header("Location: /growtopia_gems_project/public/auth/register.php");
        exit();
    }
    $stmt_insert->close();
    $conn->close();
} else {
    // Jika akses bukan dari POST, redirect ke halaman registrasi
    header("Location: /growtopia_gems_project/public/auth/register.php");
    exit();
}
?>