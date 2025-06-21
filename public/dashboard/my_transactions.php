<?php
// public/dashboard/my_transactions.php
$page_title = "Riwayat Transaksi - Growtopia Gems";
require_once('../../config/db.php');
require_once('../includes/auth_check.php');

// Ambil semua transaksi beli dan jual untuk pengguna saat ini
$all_transactions = [];

// Query untuk transaksi beli
$stmt_buy = $conn->prepare("SELECT 'Beli' as type, buy_id as transaction_id, gems_amount, price, status, transaction_date FROM buy_transactions WHERE user_id = ?");
$stmt_buy->bind_param("i", $current_user_id);
$stmt_buy->execute();
$result_buy = $stmt_buy->get_result();
while ($row = $result_buy->fetch_assoc()) {
    $all_transactions[] = $row;
}
$stmt_buy->close();

// Query untuk transaksi jual
$stmt_sell = $conn->prepare("SELECT 'Jual' as type, sell_id as transaction_id, gems_amount, price, status, transaction_date, proof_image FROM sell_transactions WHERE user_id = ?");
$stmt_sell->bind_param("i", $current_user_id);
$stmt_sell->execute();
$result_sell = $stmt_sell->get_result();
while ($row = $result_sell->fetch_assoc()) {
    $all_transactions[] = $row;
}
$stmt_sell->close();

// Urutkan transaksi berdasarkan tanggal secara descending (terbaru di atas)
usort($all_transactions, function($a, $b) {
    return strtotime($b['transaction_date']) - strtotime($a['transaction_date']);
});

include('../includes/header.php');
?>

<div class="dashboard-layout">
    <?php include('../includes/sidebar.php'); ?>

    <div class="main-content">
        <h2>Riwayat Transaksi Saya</h2>

        <?php
        if (isset($_SESSION['message'])) {
            echo '<div class="message ' . $_SESSION['message_type'] . '">' . $_SESSION['message'] . '</div>';
            unset($_SESSION['message']);
            unset($_SESSION['message_type']);
        }
        ?>

        <?php if (!empty($all_transactions)): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID Transaksi</th>
                        <th>Tipe</th>
                        <th>Jumlah Gems</th>
                        <th>Harga/Terima</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($all_transactions as $transaction): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($transaction['transaction_id']); ?></td>
                            <td><?php echo htmlspecialchars($transaction['type']); ?></td>
                            <td><?php echo number_format($transaction['gems_amount'], 0, ',', '.'); ?></td>
                            <td>Rp <?php echo number_format($transaction['price'], 0, ',', '.'); ?></td>
                            <td><?php echo htmlspecialchars(ucfirst($transaction['status'])); ?></td>
                            <td><?php echo date('d-m-Y H:i', strtotime($transaction['transaction_date'])); ?></td>
                            <td>
                                <?php if ($transaction['type'] == 'Jual' && !empty($transaction['proof_image'])): ?>
                                    <a href="/growtopia_gems_project/public/assets/images/<?php echo htmlspecialchars($transaction['proof_image']); ?>" target="_blank" class="btn btn-secondary" style="padding: 5px 10px; font-size: 0.9em;">Lihat Bukti</a>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Anda belum memiliki riwayat transaksi.</p>
        <?php endif; ?>
    </div>
</div>

<?php include('../includes/footer.php'); ?>
