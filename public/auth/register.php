<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi - Growtopia Gems</title>
    <link rel="stylesheet" href="/growtopia_gems_project/public/css/style.css">
</head>
<body>
    <div class="form-container">
        <h2>Registrasi Akun Baru</h2>
        <?php
        session_start(); // Mulai sesi untuk menampilkan pesan
        if (isset($_SESSION['message'])) {
            echo '<div class="message ' . $_SESSION['message_type'] . '">' . $_SESSION['message'] . '</div>';
            unset($_SESSION['message']);
            unset($_SESSION['message_type']);
        }
        ?>
        <form action="/growtopia_gems_project/public/process/register_process.php" method="POST">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="confirm_password">Konfirmasi Password:</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            <div class="form-group">
                <label for="growid">GrowID (Opsional):</label>
                <input type="text" id="growid" name="growid">
            </div>
            <div class="form-group">
                <label for="world_name">Nama World (Opsional):</label>
                <input type="text" id="world_name" name="world_name">
            </div>
            <button type="submit" class="btn btn-primary">Daftar</button>
        </form>
        <p style="margin-top: 20px;">Sudah punya akun? <a href="/growtopia_gems_project/public/auth/login.php">Login di sini</a></p>
    </div>
</body>
</html>