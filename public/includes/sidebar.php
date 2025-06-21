<nav class="sidebar">
    <h2>Menu</h2>
    <ul>
        <li><a href="/growtopia_gems_project/public/dashboard/home.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'home.php') ? 'active' : ''; ?>">Dashboard</a></li>
        <li><a href="/growtopia_gems_project/public/dashboard/profile.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'profile.php') ? 'active' : ''; ?>">Pengaturan Profil</a></li>
        <li><a href="/growtopia_gems_project/public/dashboard/buy_gems.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'buy_gems.php') ? 'active' : ''; ?>">Beli Gems</a></li>
        <li><a href="/growtopia_gems_project/public/dashboard/sell_gems.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'sell_gems.php') ? 'active' : ''; ?>">Jual Gems</a></li>
        <li><a href="/growtopia_gems_project/public/dashboard/my_transactions.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'my_transactions.php') ? 'active' : ''; ?>">Riwayat Transaksi</a></li>

        <?php if ($current_user_role === 'admin'): ?>
            <li style="margin-top: 30px;"><h3 style="color: #ccc; padding: 0 25px; margin-bottom: 10px;">Admin Panel</h3></li>
            <li><a href="/growtopia_gems_project/public/dashboard/admin/users.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'users.php') ? 'active' : ''; ?>">Manajemen Pengguna</a></li>
            <li><a href="/growtopia_gems_project/public/dashboard/admin/buy_requests.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'buy_requests.php') ? 'active' : ''; ?>">Permintaan Beli</a></li>
            <li><a href="/growtopia_gems_project/public/dashboard/admin/sell_requests.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'sell_requests.php') ? 'active' : ''; ?>">Permintaan Jual</a></li>
            <li><a href="/growtopia_gems_project/public/dashboard/admin/price_settings.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'price_settings.php') ? 'active' : ''; ?>">Pengaturan Harga</a></li>
        <?php endif; ?>
    </ul>
</nav>