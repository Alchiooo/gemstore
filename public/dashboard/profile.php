<?php
// public/dashboard/profile.php
$page_title = "Pengaturan Profil - Growtopia Gems";
require_once('../../config/db.php');
require_once('../includes/auth_check.php');

// Ambil data profil pengguna saat ini
$user_data = [];
$stmt = $conn->prepare("SELECT username, email, growid, world_name FROM users WHERE user_id = ?");
$stmt->bind_param("i", $current_user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $user_data = $result->fetch_assoc();
} else {
    // Jika data tidak ditemukan, mungkin ada masalah sesi. Redirect ke login.
    header("Location: /growtopia_gems_project/public/auth/login.php");
    exit();
}
$stmt->close();

include('../includes/header.php'); // Sertakan header
?>

<div class="dashboard-layout">
    <?php include('../includes/sidebar.php'); // Sertakan sidebar ?>

    <div class="main-content">
        <h2>Pengaturan Profil</h2>

        <?php
        // Tampilkan pesan jika ada
        if (isset($_SESSION['message'])) {
            echo '<div class="message ' . $_SESSION['message_type'] . '">' . $_SESSION['message'] . '</div>';
            unset($_SESSION['message']);
            unset($_SESSION['message_type']);
        }
        ?>

        <div class="form-container" style="max-width: 600px; margin: 20px auto;">
            <form action="/growtopia_gems_project/public/process/update_profile_process.php" method="POST">
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user_data['username']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user_data['email']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="growid">GrowID:</label>
                    <input type="text" id="growid" name="growid" value="<?php echo htmlspecialchars($user_data['growid']); ?>">
                </div>
                <div class="form-group">
                    <label for="world_name">Nama World:</label>
                    <input type="text" id="world_name" name="world_name" value="<?php echo htmlspecialchars($user_data['world_name']); ?>">
                </div>

                <hr style="margin: 30px 0;">
                <h3>Ubah Password (Opsional)</h3>
                <p style="font-size: 0.9em; color: #777;">Isi hanya jika Anda ingin mengubah password.</p>
                <div class="form-group">
                    <label for="current_password">Password Saat Ini:</label>
                    <input type="password" id="current_password" name="current_password">
                </div>
                <div class="form-group">
                    <label for="new_password">Password Baru:</label>
                    <input type="password" id="new_password" name="new_password">
                </div>
                <div class="form-group">
                    <label for="confirm_new_password">Konfirmasi Password Baru:</label>
                    <input type="password" id="confirm_new_password" name="confirm_new_password">
                </div>

                <button type="submit" class="btn btn-primary">Update Profil</button>
            </form>
        </div>
    </div>
</div>

<?php include('../includes/footer.php'); ?>
