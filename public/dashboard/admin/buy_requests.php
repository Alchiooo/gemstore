<?php

$page_title = "Permintaan Beli - Growtopia Gems";
require_once('../../../config/db.php');
require_once('../../includes/auth_check.php');

if ($current_user_role !== 'admin') {
    header("Location: /growtopia_gems_project/public/dashboard/home.php");
    exit();
}

$buy_requests = [];
$stmt = $conn->prepare("
    SELECT bt.buy_id, u.username, bt.gems_amount, bt.price, bt.status, bt.transaction_date, bt.payment_method, u.growid, u.world_name, u.user_id
    FROM buy_transactions bt
    JOIN users u ON bt.user_id = u.user_id
    ORDER BY bt.transaction_date DESC
");
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $buy_requests[] = $row;
}
$stmt->close();

include('../../includes/header.php');
?>

<div class="dashboard-layout">
    <?php include('../../includes/sidebar.php'); ?>

    <div class="main-content">
        <h2>Permintaan Pembelian Gems</h2>

        <?php
        if (isset($_SESSION['message'])) {
            echo '<div class="message ' . $_SESSION['message_type'] . '">' . $_SESSION['message'] . '</div>';
            unset($_SESSION['message']);
            unset($_SESSION['message_type']);
        }
        ?>

        <?php if (!empty($buy_requests)): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID Beli</th>
                        <th>Pengguna</th>
                        <th>GrowID/World</th>
                        <th>Jumlah Gems</th>
                        <th>Total Harga</th>
                        <th>Metode Bayar</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($buy_requests as $request): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($request['buy_id']); ?></td>
                            <td><?php echo htmlspecialchars($request['username']); ?></td>
                            <td><?php echo htmlspecialchars($request['growid'] ?: '-') . ' / ' . htmlspecialchars($request['world_name'] ?: '-'); ?></td>
                            <td><?php echo number_format($request['gems_amount'], 0, ',', '.'); ?></td>
                            <td>Rp <?php echo number_format($request['price'], 0, ',', '.'); ?></td>
                            <td><?php echo htmlspecialchars($request['payment_method']); ?></td>
                            <td><?php echo htmlspecialchars(ucfirst($request['status'])); ?></td>
                            <td><?php echo date('d-m-Y H:i', strtotime($request['transaction_date'])); ?></td>
                            <td>
                                <?php if ($request['status'] == 'pending'): ?>
                                    <form action="/growtopia_gems_project/public/process/admin_process.php" method="POST" style="display: inline-block;">
                                        <input type="hidden" name="action" value="approve_buy">
                                        <input type="hidden" name="buy_id" value="<?php echo $request['buy_id']; ?>">
                                        <input type="hidden" name="user_id" value="<?php echo $request['user_id']; ?>">
                                        <input type="hidden" name="gems_amount" value="<?php echo $request['gems_amount']; ?>">
                                        <button type="submit" class="btn btn-primary" style="padding: 5px 10px; font-size: 0.9em;">Approve</button>
                                    </form>
                                    <form action="/growtopia_gems_project/public/process/admin_process.php" method="POST" style="display: inline-block; margin-left: 5px;">
                                        <input type="hidden" name="action" value="cancel_buy">
                                        <input type="hidden" name="buy_id" value="<?php echo $request['buy_id']; ?>">
                                        <button type="submit" class="btn btn-danger" style="padding: 5px 10px; font-size: 0.9em;">Batal</button>
                                    </form>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Tidak ada permintaan pembelian Gems yang tertunda.</p>
        <?php endif; ?>
    </div>
</div>

<?php include('../../includes/footer.php'); ?>
