<?php
// public/process/buy_process.php
require_once('../../config/db.php');
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: /growtopia_gems_project/public/auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $gems_amount = $_POST['gems_amount'] ?? 0;
    $payment_method = $_POST['payment_method'] ?? '';

    // Pastikan gems_amount adalah kelipatan 10000 dan lebih dari 0
    if ($gems_amount <= 0 || $gems_amount % 10000 !== 0) {
        $_SESSION['message'] = "Jumlah Gems harus kelipatan 1000 dan lebih dari 0.";
        $_SESSION['message_type'] = "error";
        header("Location: /growtopia_gems_project/public/dashboard/buy_gems.php");
        exit();
    }

    // Ambil harga beli per k dari database
    $price_settings = get_price_settings($conn);
    $buy_price_per_k = $price_settings['buy_price_per_k'];

    if ($buy_price_per_k <= 0) {
        $_SESSION['message'] = "Harga Gems belum diatur oleh admin. Silakan coba lagi nanti.";
        $_SESSION['message_type'] = "error";
        header("Location: /growtopia_gems_project/public/dashboard/buy_gems.php");
        exit();
    }

    $total_price = ($gems_amount / 1000) * $buy_price_per_k;

    // Masukkan transaksi ke database
    $stmt = $conn->prepare("INSERT INTO buy_transactions (user_id, gems_amount, price, status, payment_method) VALUES (?, ?, ?, 'pending', ?)");
    $stmt->bind_param("iids", $user_id, $gems_amount, $total_price, $payment_method);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Permintaan pembelian Gems Anda berhasil diajukan! Total: Rp " . number_format($total_price, 0, ',', '.') . ". Silakan lanjutkan pembayaran.";
        $_SESSION['message_type'] = "success";
        header("Location: /growtopia_gems_project/public/dashboard/buy_gems.php");
        exit();
    } else {
        $_SESSION['message'] = "Terjadi kesalahan saat mengajukan pembelian: " . $stmt->error;
        $_SESSION['message_type'] = "error";
        header("Location: /growtopia_gems_project/public/dashboard/buy_gems.php");
        exit();
    }
    $stmt->close();
    $conn->close();
} else {
    header("Location: /growtopia_gems_project/public/dashboard/buy_gems.php");
    exit();
}
?>