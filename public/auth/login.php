<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Gry Store</title>
    <link rel="stylesheet" href="/growtopia_gems_project/public/css/style.css">
</head>
<body>
    <div class="form-container">
        <h2>Welcome to Gry Store</h2>
        <h3>Please login ...</h3>
        <?php
        session_start(); // Mulai sesi untuk menampilkan pesan
        if (isset($_SESSION['message'])) {
            echo '<div class="message ' . $_SESSION['message_type'] . '">' . $_SESSION['message'] . '</div>';
            unset($_SESSION['message']); // Hapus pesan setelah ditampilkan
            unset($_SESSION['message_type']);
        }
        ?>
        <form action="/growtopia_gems_project/public/process/login_process.php" method="POST">
            <div class="form-group">
                <label for="username">Username atau Email:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary">Login</button>
        </form>
        <p style="margin-top: 20px;">Belum punya akun? <a href="/growtopia_gems_project/public/auth/register.php">Daftar di sini</a></p>
    </div>
</body>
</html>
