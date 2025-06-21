<?php
// public/process/admin_process.php
require_once('../../config/db.php');
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: /growtopia_gems_project/public/auth/login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {
    $action = $_POST['action'];

    switch ($action) {
        case 'update_user_role':
            $user_id = $_POST['user_id'] ?? 0;
            $new_role = $_POST['new_role'] ?? '';

            if ($user_id > 0 && ($new_role === 'pelanggan' || $new_role === 'admin')) {
                // Pastikan admin tidak bisa mengubah role dirinya sendiri agar tidak terkunci
                if ($user_id == $_SESSION['user_id'] && $new_role !== $_SESSION['role']) {
                    $_SESSION['message'] = "Anda tidak bisa mengubah peran Anda sendiri.";
                    $_SESSION['message_type'] = "error";
                    header("Location: /growtopia_gems_project/public/dashboard/admin/users.php");
                    exit();
                }

                $stmt = $conn->prepare("UPDATE users SET role = ? WHERE user_id = ?");
                $stmt->bind_param("si", $new_role, $user_id);
                if ($stmt->execute()) {
                    $_SESSION['message'] = "Peran pengguna berhasil diperbarui.";
                    $_SESSION['message_type'] = "success";
                } else {
                    $_SESSION['message'] = "Gagal memperbarui peran pengguna: " . $stmt->error;
                    $_SESSION['message_type'] = "error";
                }
                $stmt->close();
            } else {
                $_SESSION['message'] = "Data tidak valid untuk update peran pengguna.";
                $_SESSION['message_type'] = "error";
            }
            header("Location: /growtopia_gems_project/public/dashboard/admin/users.php");
            exit();

        case 'delete_user':
            $user_id = $_POST['user_id'] ?? 0;
            if ($user_id > 0) {
                // Pastikan admin tidak bisa menghapus dirinya sendiri
                if ($user_id == $_SESSION['user_id']) {
                    $_SESSION['message'] = "Anda tidak bisa menghapus akun Anda sendiri.";
                    $_SESSION['message_type'] = "error";
                    header("Location: /growtopia_gems_project/public/dashboard/admin/users.php");
                    exit();
                }
                $stmt = $conn->prepare("DELETE FROM users WHERE user_id = ?");
                $stmt->bind_param("i", $user_id);
                if ($stmt->execute()) {
                    $_SESSION['message'] = "Pengguna berhasil dihapus.";
                    $_SESSION['message_type'] = "success";
                } else {
                    $_SESSION['message'] = "Gagal menghapus pengguna: " . $stmt->error;
                    $_SESSION['message_type'] = "error";
                }
                $stmt->close();
            } else {
                $_SESSION['message'] = "ID pengguna tidak valid.";
                $_SESSION['message_type'] = "error";
            }
            header("Location: /growtopia_gems_project/public/dashboard/admin/users.php");
            exit();

        case 'approve_buy':
            $buy_id = $_POST['buy_id'] ?? 0;
            $user_id_affected = $_POST['user_id'] ?? 0;
            $gems_amount = $_POST['gems_amount'] ?? 0;

            if ($buy_id > 0 && $user_id_affected > 0 && $gems_amount > 0) {
                // Mulai transaksi database untuk memastikan atomisitas
                $conn->begin_transaction();
                try {
                    // Update status transaksi beli
                    $stmt_buy = $conn->prepare("UPDATE buy_transactions SET status = 'completed' WHERE buy_id = ? AND status = 'pending'");
                    $stmt_buy->bind_param("i", $buy_id);
                    $stmt_buy->execute();

                    if ($stmt_buy->affected_rows > 0) {
                        // Tambahkan gems ke balance pengguna
                        $stmt_user = $conn->prepare("UPDATE users SET balance = balance + ? WHERE user_id = ?");
                        $stmt_user->bind_param("di", $gems_amount, $user_id_affected);
                        $stmt_user->execute();

                        if ($stmt_user->affected_rows > 0) {
                            $conn->commit();
                            $_SESSION['message'] = "Pembelian berhasil disetujui dan Gems telah ditambahkan ke saldo pengguna.";
                            $_SESSION['message_type'] = "success";
                        } else {
                            throw new Exception("Gagal memperbarui saldo pengguna.");
                        }
                    } else {
                        throw new Exception("Transaksi beli sudah diproses atau tidak ditemukan.");
                    }
                } catch (Exception $e) {
                    $conn->rollback();
                    $_SESSION['message'] = "Error saat menyetujui pembelian: " . $e->getMessage();
                    $_SESSION['message_type'] = "error";
                } finally {
                    $stmt_buy->close();
                    if (isset($stmt_user)) $stmt_user->close();
                }
            } else {
                $_SESSION['message'] = "Data transaksi beli tidak valid.";
                $_SESSION['message_type'] = "error";
            }
            header("Location: /growtopia_gems_project/public/dashboard/admin/buy_requests.php");
            exit();

        case 'cancel_buy':
            $buy_id = $_POST['buy_id'] ?? 0;
            if ($buy_id > 0) {
                $stmt = $conn->prepare("UPDATE buy_transactions SET status = 'cancelled' WHERE buy_id = ? AND status = 'pending'");
                $stmt->bind_param("i", $buy_id);
                if ($stmt->execute()) {
                    $_SESSION['message'] = "Pembelian berhasil dibatalkan.";
                    $_SESSION['message_type'] = "success";
                } else {
                    $_SESSION['message'] = "Gagal membatalkan pembelian: " . $stmt->error;
                    $_SESSION['message_type'] = "error";
                }
                $stmt->close();
            } else {
                $_SESSION['message'] = "ID transaksi beli tidak valid.";
                $_SESSION['message_type'] = "error";
            }
            header("Location: /growtopia_gems_project/public/dashboard/admin/buy_requests.php");
            exit();

        case 'approve_sell':
            $sell_id = $_POST['sell_id'] ?? 0;
            $user_id_affected = $_POST['user_id'] ?? 0;
            $gems_amount = $_POST['gems_amount'] ?? 0;

            if ($sell_id > 0 && $user_id_affected > 0 && $gems_amount > 0) {
                // Mulai transaksi database
                $conn->begin_transaction();
                try {
                    // Cek apakah pengguna memiliki saldo yang cukup (opsional, tergantung alur bisnis Anda)
                    // Atau, ini adalah tahap setelah admin menerima gems di Growtopia, jadi hanya update status
                    $stmt_sell = $conn->prepare("UPDATE sell_transactions SET status = 'completed' WHERE sell_id = ? AND status = 'pending'");
                    $stmt_sell->bind_param("i", $sell_id);
                    $stmt_sell->execute();

                    if ($stmt_sell->affected_rows > 0) {
                        // Kurangi gems dari balance pengguna (jika Anda mengelola balance di sistem)
                        // Note: Untuk skenario riil, ini adalah proses setelah admin membayar pelanggan
                        $stmt_user = $conn->prepare("UPDATE users SET balance = balance - ? WHERE user_id = ?");
                        $stmt_user->bind_param("di", $gems_amount, $user_id_affected);
                        $stmt_user->execute();

                        if ($stmt_user->affected_rows > 0) {
                            $conn->commit();
                            $_SESSION['message'] = "Penjualan berhasil disetujui. Pastikan Anda sudah mentransfer pembayaran ke pelanggan.";
                            $_SESSION['message_type'] = "success";
                        } else {
                            throw new Exception("Gagal memperbarui saldo pengguna.");
                        }
                    } else {
                        throw new Exception("Transaksi jual sudah diproses atau tidak ditemukan.");
                    }
                } catch (Exception $e) {
                    $conn->rollback();
                    $_SESSION['message'] = "Error saat menyetujui penjualan: " . $e->getMessage();
                    $_SESSION['message_type'] = "error";
                } finally {
                    $stmt_sell->close();
                    if (isset($stmt_user)) $stmt_user->close();
                }
            } else {
                $_SESSION['message'] = "Data transaksi jual tidak valid.";
                $_SESSION['message_type'] = "error";
            }
            header("Location: /growtopia_gems_project/public/dashboard/admin/sell_requests.php");
            exit();

        case 'cancel_sell':
            $sell_id = $_POST['sell_id'] ?? 0;
            if ($sell_id > 0) {
                $stmt = $conn->prepare("UPDATE sell_transactions SET status = 'cancelled' WHERE sell_id = ? AND status = 'pending'");
                $stmt->bind_param("i", $sell_id);
                if ($stmt->execute()) {
                    $_SESSION['message'] = "Penjualan berhasil dibatalkan.";
                    $_SESSION['message_type'] = "success";
                } else {
                    $_SESSION['message'] = "Gagal membatalkan penjualan: " . $stmt->error;
                    $_SESSION['message_type'] = "error";
                }
                $stmt->close();
            } else {
                $_SESSION['message'] = "ID transaksi jual tidak valid.";
                $_SESSION['message_type'] = "error";
            }
            header("Location: /growtopia_gems_project/public/dashboard/admin/sell_requests.php");
            exit();

        case 'update_price_settings':
            $buy_price = $_POST['buy_price_per_k'] ?? 0;
            $sell_price = $_POST['sell_price_per_k'] ?? 0;

            if ($buy_price >= 0 && $sell_price >= 0) {
                // Update baris pertama di tabel price_settings (asumsi hanya ada satu baris)
                $stmt = $conn->prepare("UPDATE price_settings SET buy_price_per_k = ?, sell_price_per_k = ? WHERE setting_id = 1");
                $stmt->bind_param("dd", $buy_price, $sell_price);
                if ($stmt->execute()) {
                    $_SESSION['message'] = "Pengaturan harga berhasil diperbarui.";
                    $_SESSION['message_type'] = "success";
                } else {
                    $_SESSION['message'] = "Gagal memperbarui pengaturan harga: " . $stmt->error;
                    $_SESSION['message_type'] = "error";
                }
                $stmt->close();
            } else {
                $_SESSION['message'] = "Harga tidak valid.";
                $_SESSION['message_type'] = "error";
            }
            header("Location: /growtopia_gems_project/public/dashboard/admin/price_settings.php");
            exit();

        default:
            $_SESSION['message'] = "Aksi tidak dikenal.";
            $_SESSION['message_type'] = "error";
            header("Location: /growtopia_gems_project/public/dashboard/home.php");
            exit();
    }
} else {
    // Jika akses langsung ke file process tanpa POST atau action
    header("Location: /growtopia_gems_project/public/dashboard/home.php");
    exit();
}
$conn->close();
?>
