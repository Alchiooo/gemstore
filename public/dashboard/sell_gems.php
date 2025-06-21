<?php
// public/dashboard/sell_gems.php
$page_title = "Jual Gems - Growtopia Gems";
require_once('../../config/db.php');
require_once('../includes/auth_check.php');

$price_settings = get_price_settings($conn);
$sell_price_per_k = $price_settings['sell_price_per_k'];

// Ambil GrowID dan World Name pengguna untuk ditampilkan
$user_growid = '';
$user_world_name = '';
$stmt_user_info = $conn->prepare("SELECT growid, world_name FROM users WHERE user_id = ?");
$stmt_user_info->bind_param("i", $current_user_id);
$stmt_user_info->execute();
$result_user_info = $stmt_user_info->get_result();
if ($result_user_info->num_rows > 0) {
    $user_info = $result_user_info->fetch_assoc();
    $user_growid = $user_info['growid'];
    $user_world_name = $user_info['world_name'];
}
$stmt_user_info->close();

include('../includes/header.php');
?>

<div class="dashboard-layout">
    <?php include('../includes/sidebar.php'); ?>

    <div class="main-content">
        <h2>Jual Gems Growtopia</h2>

        <?php
        if (isset($_SESSION['message'])) {
            echo '<div class="message ' . $_SESSION['message_type'] . '">' . $_SESSION['message'] . '</div>';
            unset($_SESSION['message']);
            unset($_SESSION['message_type']);
        }
        ?>

        <div class="form-container" style="max-width: 600px; margin: 20px auto;">
            <p>Harga jual Gems saat ini: Rp <?php echo number_format($sell_price_per_k, 0, ',', '.'); ?> per 10000 Gems.</p>
            <p>Pastikan GrowID (<strong><?php echo htmlspecialchars($user_growid ?: 'Belum diatur'); ?></strong>) dan Nama World (<strong><?php echo htmlspecialchars($user_world_name ?: 'Belum diatur'); ?></strong>) Anda di profil sudah benar, karena Gems akan diterima dari sana.</p>
            <form action="/growtopia_gems_project/public/process/sell_process.php" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="gems_amount">Jumlah Gems (dalam kelipatan 10000):</label>
                    <input type="number" id="gems_amount" name="gems_amount" min="1000" step="1000" required oninput="calculatePrice()">
                </div>
                <div class="form-group">
                    <label for="total_price">Anda akan menerima:</label>
                    <input type="text" id="total_price" name="total_price" readonly value="Rp 0">
                </div>
                <div class="form-group">
                    <label for="proof_image">Upload Bukti Transfer Gems di Growtopia (Screenshot):</label>
                    <input type="file" id="proof_image" name="proof_image" accept="image/*" required>
                    <small>Maks. ukuran file 2MB. Format: JPG, PNG.</small>
                </div>
                <button type="submit" class="btn btn-primary">Jual Sekarang</button>
            </form>
        </div>
    </div>
</div>

<script>
    function calculatePrice() {
        const gemsInput = document.getElementById('gems_amount');
        const totalPriceInput = document.getElementById('total_price');
        const gemsAmount = parseInt(gemsInput.value);
        const pricePerK = <?php echo json_encode($sell_price_per_k); ?>;

        if (gemsAmount && gemsAmount % 10000 === 0 && gemsAmount > 0) {
            const totalPrice = (gemsAmount / 10000) * pricePerK;
            totalPriceInput.value = 'Rp ' + totalPrice.toLocaleString('id-ID');
        } else {
            totalPriceInput.value = 'Rp 0';
        }
    }

    calculatePrice(); // Panggil saat halaman dimuat
</script>

<?php include('../includes/footer.php'); ?>