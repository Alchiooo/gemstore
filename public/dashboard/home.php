<?php
$page_title = "Dashboard - Growtopia Gems";
require_once('../../config/db.php');
require_once('../includes/auth_check.php'); // Memastikan pengguna sudah login

// Ambil saldo pengguna
$user_balance = 0;
$stmt = $conn->prepare("SELECT balance FROM users WHERE user_id = ?");
$stmt->bind_param("i", $current_user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $user_balance = $result->fetch_assoc()['balance'];
}
$stmt->close();

// Ambil riwayat transaksi terakhir (contoh, 5 transaksi terbaru)
$recent_transactions = [];
$stmt_transactions = $conn->prepare("
    SELECT 'Beli' as type, gems_amount, price, status, transaction_date FROM buy_transactions WHERE user_id = ?
    UNION ALL
    SELECT 'Jual' as type, gems_amount, price, status, transaction_date FROM sell_transactions WHERE user_id = ?
    ORDER BY transaction_date DESC LIMIT 5
");
$stmt_transactions->bind_param("ii", $current_user_id, $current_user_id);
$stmt_transactions->execute();
$result_transactions = $stmt_transactions->get_result();
while ($row = $result_transactions->fetch_assoc()) {
    $recent_transactions[] = $row;
}
$stmt_transactions->close();

include('../includes/header.php'); // Sertakan header
?>

<div class="dashboard-layout">
    <?php include('../includes/sidebar.php'); // Sertakan sidebar ?>

    <div class="main-content">
        <h2>Selamat Datang di Dashboard, <?php echo htmlspecialchars($current_username); ?>!</h2>

        <?php
        // Tampilkan pesan jika ada
        if (isset($_SESSION['message'])) {
            echo '<div class="message ' . $_SESSION['message_type'] . '">' . $_SESSION['message'] . '</div>';
            unset($_SESSION['message']);
            unset($_SESSION['message_type']);
        }
        ?>

        <div style="background-color: #e8f5e9; padding: 25px; border-radius: 10px; margin-bottom: 30px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
            <h3>Saldo Gems Anda:</h3>
            <p style="font-size: 2.2em; font-weight: bold; color: #2e7d32; margin: 0;"><?php echo number_format($user_balance, 0, ',', '.'); ?> Gems</p>
        </div>

        <h3>Transaksi Terbaru Anda:</h3>
        <?php if (!empty($recent_transactions)): ?>
            <table>
                <thead>
                    <tr>
                        <th>Tipe</th>
                        <th>Jumlah Gems</th>
                        <th>Harga</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recent_transactions as $transaction): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($transaction['type']); ?></td>
                            <td><?php echo number_format($transaction['gems_amount'], 0, ',', '.'); ?></td>
                            <td>Rp <?php echo number_format($transaction['price'], 0, ',', '.'); ?></td>
                            <td><?php echo htmlspecialchars(ucfirst($transaction['status'])); ?></td>
                            <td><?php echo date('d-m-Y H:i', strtotime($transaction['transaction_date'])); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <p style="margin-top: 20px; text-align: right;"><a href="/growtopia_gems_project/public/dashboard/my_transactions.php" class="btn btn-secondary">Lihat Semua Transaksi</a></p>
        <?php else: ?>
            <p>Belum ada transaksi terbaru.</p>
        <?php endif; ?>

    </div>
</div>

<?php include('../includes/footer.php'); // Sertakan footer ?>
