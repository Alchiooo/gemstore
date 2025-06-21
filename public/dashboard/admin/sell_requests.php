<?php
// public/dashboard/admin/sell_requests.php
$page_title = "Permintaan Jual - Growtopia Gems";
require_once('../../../config/db.php');
require_once('../../includes/auth_check.php');

if ($current_user_role !== 'admin') {
    header("Location: /growtopia_gems_project/public/dashboard/home.php");
    exit();
}

$sell_requests = [];
$stmt = $conn->prepare("
    SELECT st.sell_id, u.username, st.gems_amount, st.price, st.status, st.transaction_date, st.proof_image, u.growid, u.world_name, u.user_id
    FROM sell_transactions st
    JOIN users u ON st.user_id = u.user_id
    ORDER BY st.transaction_date DESC
");
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $sell_requests[] = $row;
}
$stmt->close();

include('../../includes/header.php');
?>

<div class="dashboard-layout">
    <?php include('../../includes/sidebar.php'); ?>

    <div class="main-content">
        <h2>Permintaan Penjualan Gems</h2>

        <?php
        if (isset($_SESSION['message'])) {
            echo '<div class="message ' . $_SESSION['message_type'] . '">' . $_SESSION['message'] . '</div>';
            unset($_SESSION['message']);
            unset($_SESSION['message_type']);
        }
        ?>

        <?php if (!empty($sell_requests)): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID Jual</th>
                        <th>Pengguna</th>
                        <th>GrowID/World</th>
                        <th>Jumlah Gems</th>
                        <th>Total Harga</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                        <th>Bukti</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($sell_requests as $request): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($request['sell_id']); ?></td>
                            <td><?php echo htmlspecialchars($request['username']); ?></td>
                            <td><?php echo htmlspecialchars($request['growid'] ?: '-') . ' / ' . htmlspecialchars($request['world_name'] ?: '-'); ?></td>
                            <td><?php echo number_format($request['gems_amount'], 0, ',', '.'); ?></td>
                            <td>Rp <?php echo number_format($request['price'], 0, ',', '.'); ?></td>
                            <td><?php echo htmlspecialchars(ucfirst($request['status'])); ?></td>
                            <td><?php echo date('d-m-Y H:i', strtotime($request['transaction_date'])); ?></td>
                            <td>
                                <?php if (!empty($request['proof_image'])): ?>
                                    <a href="/growtopia_gems_project/public/assets/images/<?php echo htmlspecialchars($request['proof_image']); ?>" target="_blank" class="btn btn-secondary" style="padding: 5px 10px; font-size: 0.9em;">Lihat</a>
                                <?php else: ?>
                                    Tidak ada
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($request['status'] == 'pending'): ?>
                                    <form action="/growtopia_gems_project/public/process/admin_process.php" method="POST" style="display: inline-block;">
                                        <input type="hidden" name="action" value="approve_sell">
                                        <input type="hidden" name="sell_id" value="<?php echo $request['sell_id']; ?>">
                                        <input type="hidden" name="user_id" value="<?php echo $request['user_id']; ?>">
                                        <input type="hidden" name="gems_amount" value="<?php echo $request['gems_amount']; ?>">
                                        <button type="submit" class="btn btn-primary" style="padding: 5px 10px; font-size: 0.9em;">Approve</button>
                                    </form>
                                    <form action="/growtopia_gems_project/public/process/admin_process.php" method="POST" style="display: inline-block; margin-left: 5px;">
                                        <input type="hidden" name="action" value="cancel_sell">
                                        <input type="hidden" name="sell_id" value="<?php echo $request['sell_id']; ?>">
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
            <p>Tidak ada permintaan penjualan Gems yang tertunda.</p>
        <?php endif; ?>
    </div>
</div>

<?php include('../../includes/footer.php'); ?>