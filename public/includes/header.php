<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? 'Growtopia Gems'; ?></title>
    <link rel="stylesheet" href="/growtopia_gems_project/public/css/style.css">
</head>
<body>
    <header class="main-header">
        <h1>Gry Store</h1>
        <div class="user-info">
            <span>Selamat datang, <?php echo htmlspecialchars($current_username); ?>! (<?php echo htmlspecialchars($current_user_role); ?>)</span>
            <a href="/growtopia_gems_project/public/process/logout_process.php">Logout</a>
        </div>
    </header>