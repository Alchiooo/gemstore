<?php
// public/dashboard/admin/users.php
$page_title = "Manajemen Pengguna - Growtopia Gems";
require_once('../../../config/db.php');
require_once('../../includes/auth_check.php');

// Pastikan hanya admin yang bisa mengakses halaman ini
if ($current_user_role !== 'admin') {
    header("Location: /growtopia_gems_project/public/dashboard/home.php");
    exit();
}

// Ambil semua pengguna
$users = [];
$stmt = $conn->prepare("SELECT user_id, username, email, role, growid, world_name, balance, created_at FROM users ORDER BY created_at DESC");
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $users[] = $row;
}
$stmt->close();

include('../../includes/header.php');
?>

<div class="dashboard-layout">
    <?php include('../../includes/sidebar.php'); ?>

    <div class="main-content">
        <h2>Manajemen Pengguna</h2>

        <?php
        if (isset($_SESSION['message'])) {
            echo '<div class="message ' . $_SESSION['message_type'] . '">' . $_SESSION['message'] . '</div>';
            unset($_SESSION['message']);
            unset($_SESSION['message_type']);
        }
        ?>

        <?php if (!empty($users)): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>GrowID</th>
                        <th>World Name</th>
                        <th>Balance</th>
                        <th>Bergabung</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($user['user_id']); ?></td>
                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td><?php echo htmlspecialchars(ucfirst($user['role'])); ?></td>
                            <td><?php echo htmlspecialchars($user['growid'] ?: '-'); ?></td>
                            <td><?php echo htmlspecialchars($user['world_name'] ?: '-'); ?></td>
                            <td><?php echo number_format($user['balance'], 0, ',', '.'); ?></td>
                            <td><?php echo date('d-m-Y', strtotime($user['created_at'])); ?></td>
                            <td>
                                <!-- Form untuk mengubah role atau menghapus -->
                                <form action="/growtopia_gems_project/public/process/admin_process.php" method="POST" style="display: inline-block;">
                                    <input type="hidden" name="action" value="update_user_role">
                                    <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">
                                    <select name="new_role" onchange="this.form.submit()" style="padding: 5px; border-radius: 5px;">
                                        <option value="pelanggan" <?php echo ($user['role'] == 'pelanggan') ? 'selected' : ''; ?>>Pelanggan</option>
                                        <option value="admin" <?php echo ($user['role'] == 'admin') ? 'selected' : ''; ?>>Admin</option>
                                    </select>
                                </form>
                                <?php if ($user['user_id'] !== $current_user_id): // Tidak bisa menghapus diri sendiri ?>
                                    <form action="/growtopia_gems_project/public/process/admin_process.php" method="POST" style="display: inline-block; margin-left: 5px;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengguna ini?');">
                                        <input type="hidden" name="action" value="delete_user">
                                        <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">
                                        <button type="submit" class="btn btn-danger" style="padding: 5px 10px; font-size: 0.9em;">Hapus</button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Tidak ada pengguna yang terdaftar.</p>
        <?php endif; ?>
    </div>
</div>

<?php include('../../includes/footer.php'); ?>