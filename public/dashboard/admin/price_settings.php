<?php
// public/dashboard/admin/price_settings.php
$page_title = "Pengaturan Harga - Growtopia Gems";
require_once('../../../config/db.php');
require_once('../../includes/auth_check.php');

if ($current_user_role !== 'admin') {
    header("Location: /growtopia_gems_project/public/dashboard/home.php");
    exit();
}

// Ambil pengaturan harga saat ini
$price_settings = get_price_settings($conn);
$current_buy_price = $price_settings['buy_price_per_k'];
$current_sell_price = $price_settings['sell_price_per_k'];

include('../../includes/header.php');
?>

<div class="dashboard-layout">
    <?php include('../../includes/sidebar.php'); ?>

    <div class="main-content">
        <h2>Pengaturan Harga Gems</h2>

        <?php
        if (isset($_SESSION['message'])) {
            echo '<div class="message ' . $_SESSION['message_type'] . '">' . $_SESSION['message'] . '</div>';
            unset($_SESSION['message']);
            unset($_SESSION['message_type']);
        }
        ?>

        <div class="form-container" style="max-width: 500px; margin: 20px auto;">
            <form action="/growtopia_gems_project/public/process/admin_process.php" method="POST">
                <input type="hidden" name="action" value="update_price_settings">
                <div class="form-group">
                    <label for="buy_price_per_k">Harga Beli Gems (dari sistem ke pelanggan, per 1000 Gems):</label>
                    <input type="number" id="buy_price_per_k" name="buy_price_per_k" value="<?php echo htmlspecialchars($current_buy_price); ?>" step="100" required>
                </div>
                <div class="form-group">
                    <label for="sell_price_per_k">Harga Jual Gems (dari pelanggan ke sistem, per 1000 Gems):</label>
                    <input type="number" id="sell_price_per_k" name="sell_price_per_k" value="<?php echo htmlspecialchars($current_sell_price); ?>" step="100" required>
                </div>
                <button type="submit" class="btn btn-primary">Update Harga</button>
            </form>
        </div>
    </div>
</div>

<?php include('../../includes/footer.php'); ?>
