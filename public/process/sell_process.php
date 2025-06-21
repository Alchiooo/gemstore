<?php
// public/process/sell_process.php
require_once('../../config/db.php');
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: /growtopia_gems_project/public/auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $gems_amount = $_POST['gems_amount'] ?? 0;

    // Pastikan gems_amount adalah kelipatan 1000 dan lebih dari 0
    if ($gems_amount <= 0 || $gems_amount % 10000 !== 0) {
        $_SESSION['message'] = "Jumlah Gems harus kelipatan 1000 dan lebih dari 0.";
        $_SESSION['message_type'] = "error";
        header("Location: /growtopia_gems_project/public/dashboard/sell_gems.php");
        exit();
    }

    // Ambil harga jual per k dari database
    $price_settings = get_price_settings($conn);
    $sell_price_per_k = $price_settings['sell_price_per_k'];

    if ($sell_price_per_k <= 0) {
        $_SESSION['message'] = "Harga Gems belum diatur oleh admin. Silakan coba lagi nanti.";
        $_SESSION['message_type'] = "error";
        header("Location: /growtopia_gems_project/public/dashboard/sell_gems.php");
        exit();
    }

    $total_price = ($gems_amount / 1000) * $sell_price_per_k;

    // Handle file upload
    $proof_image = '';
    if (isset($_FILES['proof_image']) && $_FILES['proof_image']['error'] == UPLOAD_ERR_OK) {
        $target_dir = "../assets/images/";
        // Buat nama file unik
        $file_extension = pathinfo($_FILES['proof_image']['name'], PATHINFO_EXTENSION);
        $file_name = uniqid('proof_') . '.' . $file_extension;
        $target_file = $target_dir . $file_name;
        $imageFileType = strtolower($file_extension);

        // Validasi tipe file
        $allowed_types = ['jpg', 'png', 'jpeg', 'gif'];
        if (!in_array($imageFileType, $allowed_types)) {
            $_SESSION['message'] = "Maaf, hanya file JPG, JPEG, PNG & GIF yang diizinkan.";
            $_SESSION['message_type'] = "error";
            header("Location: /growtopia_gems_project/public/dashboard/sell_gems.php");
            exit();
        }

        // Validasi ukuran file (misal: max 2MB)
        if ($_FILES['proof_image']['size'] > 2000000) { // 2MB
            $_SESSION['message'] = "Ukuran file terlalu besar. Maksimal 2MB.";
            $_SESSION['message_type'] = "error";
            header("Location: /growtopia_gems_project/public/dashboard/sell_gems.php");
            exit();
        }

        if (move_uploaded_file($_FILES['proof_image']['tmp_name'], $target_file)) {
            $proof_image = $file_name; // Simpan hanya nama file untuk database
        } else {
            $_SESSION['message'] = "Terjadi kesalahan saat mengupload gambar bukti.";
            $_SESSION['message_type'] = "error";
            header("Location: /growtopia_gems_project/public/dashboard/sell_gems.php");
            exit();
        }
    } else {
        $_SESSION['message'] = "Silakan upload gambar bukti transfer.";
        $_SESSION['message_type'] = "error";
        header("Location: /growtopia_gems_project/public/dashboard/sell_gems.php");
        exit();
    }

    // Masukkan transaksi ke database
    $stmt = $conn->prepare("INSERT INTO sell_transactions (user_id, gems_amount, price, status, proof_image) VALUES (?, ?, ?, 'pending', ?)");
    $stmt->bind_param("iids", $user_id, $gems_amount, $total_price, $proof_image);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Permintaan penjualan Gems Anda berhasil diajukan! Anda akan menerima Rp " . number_format($total_price, 0, ',', '.') . ". Admin akan segera memverifikasi.";
        $_SESSION['message_type'] = "success";
        header("Location: /growtopia_gems_project/public/dashboard/sell_gems.php");
        exit();
    } else {
        $_SESSION['message'] = "Terjadi kesalahan saat mengajukan penjualan: " . $stmt->error;
        $_SESSION['message_type'] = "error";
        header("Location: /growtopia_gems_project/public/dashboard/sell_gems.php");
        exit();
    }
    $stmt->close();
    $conn->close();
} else {
    header("Location: /growtopia_gems_project/public/dashboard/sell_gems.php");
    exit();
}
?>
