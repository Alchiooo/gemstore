<?php
// public/process/login_process.php
require_once('../../config/db.php'); // Sesuaikan path ke file db.php
session_start(); // Mulai sesi

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Siapkan statement untuk mengambil data pengguna berdasarkan username atau email
    $stmt = $conn->prepare("SELECT user_id, username, password, role FROM users WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $username, $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        // Verifikasi password
        if (password_verify($password, $user['password'])) {
            // Password cocok, set session variables
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['message'] = "Selamat datang, " . $user['username'] . "!";
            $_SESSION['message_type'] = "success";

            // Redirect ke dashboard
            header("Location: /growtopia_gems_project/public/dashboard/home.php");
            exit();
        } else {
            // Password tidak cocok
            $_SESSION['message'] = "Username/Email atau password salah.";
            $_SESSION['message_type'] = "error";
            header("Location: /growtopia_gems_project/public/auth/login.php");
            exit();
        }
    } else {
        // Pengguna tidak ditemukan
        $_SESSION['message'] = "Username/Email atau password salah.";
        $_SESSION['message_type'] = "error";
        header("Location: /growtopia_gems_project/public/auth/login.php");
        exit();
    }
    $stmt->close();
    $conn->close();
} else {
    // Jika akses bukan dari POST, redirect ke halaman login
    header("Location: /growtopia_gems_project/public/auth/login.php");
    exit();
}
?>