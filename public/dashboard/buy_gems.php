<?php
// public/dashboard/buy_gems.php
$page_title = "Beli Gems - Growtopia Gems";
require_once('../../config/db.php');
require_once('../includes/auth_check.php');

$price_settings = get_price_settings($conn);
$buy_price_per_k = $price_settings['buy_price_per_k'];

include('../includes/header.php');
?>

<div class="dashboard-layout">
    <?php include('../includes/sidebar.php'); ?>

    <div class="main-content">
        <h2>Beli Gems Growtopia</h2>

        <?php
        if (isset($_SESSION['message'])) {
            echo '<div class="message ' . $_SESSION['message_type'] . '">' . $_SESSION['message'] . '</div>';
            unset($_SESSION['message']);
            unset($_SESSION['message_type']);
        }
        ?>

        <div class="form-container" style="max-width: 600px; margin: 20px auto;">
            <p>Harga beli Gems saat ini: Rp <?php echo number_format($buy_price_per_k, 0, ',', '.'); ?> per 10000 Gems.</p>
            <p>Gems akan dikirimkan ke GrowID dan World Name yang terdaftar di profil Anda.</p>
            <form action="/growtopia_gems_project/public/process/buy_process.php" method="POST">
                <div class="form-group">
                    <label for="gems_amount">Jumlah Gems (dalam kelipatan 10000):</label>
                    <input type="number" id="gems_amount" name="gems_amount" min="1000" step="1000" required oninput="calculatePrice()">
                </div>
                <div class="form-group">
                    <label for="total_price">Total Harga:</label>
                    <input type="text" id="total_price" name="total_price" readonly value="Rp 0">
                </div>
                <div class="form-group">
                    <label for="payment_method">Metode Pembayaran:</label>
                    <select id="payment_method" name="payment_method" required>
                        <option value="">Pilih Metode Pembayaran</option>
                        <option value="Bank Transfer">Bank Transfer (BCA/Mandiri)</option>
                        <option value="E-Wallet">E-Wallet (OVO/Dana)</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Beli Sekarang</button>
            </form>
            <div id="payment-info" style="margin-top: 30px; padding: 20px; background-color: #e9f5e9; border-radius: 8px; border: 1px solid #d4edda; display: none;">
                <h4>Instruksi Pembayaran</h4>
                <p>Silakan transfer ke:</p>
                <p><strong>Bank BCA:</strong> 1234567890 a.n. Nama Anda</p>
                <p><strong>Bank Mandiri:</strong> 0987654321 a.n. Nama Anda</p>
                <p><strong>OVO/Dana:</strong> 0812-3456-7890</p>
                <p>Setelah transfer, konfirmasi pembayaran Anda ke admin melalui chat atau email.</p>
                <p>Pesanan Anda akan diproses setelah pembayaran terverifikasi.</p>
            </div>
        </div>
    </div>
</div>

<script>
    function calculatePrice() {
        const gemsInput = document.getElementById('gems_amount');
        const totalPriceInput = document.getElementById('total_price');
        const gemsAmount = parseInt(gemsInput.value);
        const pricePerK = <?php echo json_encode($buy_price_per_k); ?>; // Mengambil harga dari PHP

        if (gemsAmount && gemsAmount % 10000 === 0 && gemsAmount > 0) {
            const totalPrice = (gemsAmount / 10000) * pricePerK;
            totalPriceInput.value = 'Rp ' + totalPrice.toLocaleString('id-ID');
        } else {
            totalPriceInput.value = 'Rp 0';
        }
    }

    document.querySelector('form').addEventListener('submit', function(event) {
        // Tampilkan info pembayaran setelah submit
        document.getElementById('payment-info').style.display = 'block';
        // Opsional: mencegah form submit asli jika Anda ingin validasi lebih lanjut
        // event.preventDefault();
    });

    // Panggil sekali saat halaman dimuat untuk menghitung harga awal jika ada nilai default
    calculatePrice();
</script>

<?php include('../includes/footer.php'); ?>
