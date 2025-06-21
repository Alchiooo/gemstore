<?php
// public/process/update_profile_process.php
require_once('../../config/db.php');
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: /growtopia_gems_project/public/auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $growid = $_POST['growid'] ?? '';
    $world_name = $_POST['world_name'] ?? '';

    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_new_password = $_POST['confirm_new_password'] ?? '';

    // Ambil data user lama untuk validasi email/username unik
    $stmt_old_data = $conn->prepare("SELECT username, email, password FROM users WHERE user_id = ?");
    $stmt_old_data->bind_param("i", $user_id);
    $stmt_old_data->execute();
    $result_old_data = $stmt_old_data->get_result();
    $old_user_data = $result_old_data->fetch_assoc();
    $stmt_old_data->close();

    // Validasi apakah username atau email sudah digunakan oleh user lain
    $stmt_check_unique = $conn->prepare("SELECT user_id FROM users WHERE (username = ? OR email = ?) AND user_id != ?");
    $stmt_check_unique->bind_param("ssi", $username, $email, $user_id);
    $stmt_check_unique->execute();
    $result_check_unique = $stmt_check_unique->get_result();
    if ($result_check_unique->num_rows > 0) {
        $_SESSION['message'] = "Username atau Email sudah digunakan oleh pengguna lain.";
        $_SESSION['message_type'] = "error";
        header("Location: /growtopia_gems_project/public/dashboard/profile.php");
        exit();
    }
    $stmt_check_unique->close();

    $update_query_parts = [];
    $bind_types = "";
    $bind_params = [];

    // Tambahkan field yang akan diupdate
    $update_query_parts[] = "username = ?";
    $bind_types .= "s";
    $bind_params[] = $username;

    $update_query_parts[] = "email = ?";
    $bind_types .= "s";
    $bind_params[] = $email;

    $update_query_parts[] = "growid = ?";
    $bind_types .= "s";
    $bind_params[] = $growid;

    $update_query_parts[] = "world_name = ?";
    $bind_types .= "s";
    $bind_params[] = $world_name;

    // Update password jika diisi
    if (!empty($new_password)) {
        if (empty($current_password) || !password_verify($current_password, $old_user_data['password'])) {
            $_SESSION['message'] = "Password saat ini salah.";
            $_SESSION['message_type'] = "error";
            header("Location: /growtopia_gems_project/public/dashboard/profile.php");
            exit();
        }
        if ($new_password !== $confirm_new_password) {
            $_SESSION['message'] = "Konfirmasi password baru tidak cocok.";
            $_SESSION['message_type'] = "error";
            header("Location: /growtopia_gems_project/public/dashboard/profile.php");
            exit();
        }
        $hashed_new_password = password_hash($new_password, PASSWORD_DEFAULT);
        $update_query_parts[] = "password = ?";
        $bind_types .= "s";
        $bind_params[] = $hashed_new_password;
    }

    $sql = "UPDATE users SET " . implode(", ", $update_query_parts) . " WHERE user_id = ?";
    $bind_types .= "i";
    $bind_params[] = $user_id;

    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        $_SESSION['message'] = "Kesalahan persiapan query: " . $conn->error;
        $_SESSION['message_type'] = "error";
        header("Location: /growtopia_gems_project/public/dashboard/profile.php");
        exit();
    }

    $stmt->bind_param($bind_types, ...$bind_params);

    if ($stmt->execute()) {
        // Update username di sesi jika berubah
        $_SESSION['username'] = $username;
        $_SESSION['message'] = "Profil berhasil diperbarui!";
        $_SESSION['message_type'] = "success";
        header("Location: /growtopia_gems_project/public/dashboard/profile.php");
        exit();
    } else {
        $_SESSION['message'] = "Terjadi kesalahan saat memperbarui profil: " . $stmt->error;
        $_SESSION['message_type'] = "error";
        header("Location: /growtopia_gems_project/public/dashboard/profile.php");
        exit();
    }
    $stmt->close();
    $conn->close();
} else {
    header("Location: /growtopia_gems_project/public/dashboard/profile.php");
    exit();
}
?>