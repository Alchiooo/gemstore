<?php
// config/db.php
// Konfigurasi koneksi ke database MySQL
$servername = "localhost"; // Nama server database Anda (biasanya localhost)
$username = "root";      // Username database Anda
$password = "";          // Password database Anda
$dbname = "growtopia_gems_db"; // Nama database yang telah Anda buat

// Membuat koneksi ke database
$conn = new mysqli($servername, $username, $password, $dbname);

// Memeriksa koneksi
if ($conn->connect_error) {
    // Jika koneksi gagal, hentikan skrip dan tampilkan pesan error
    die("Koneksi database gagal: " . $conn->connect_error);
}

// Mengatur karakter set untuk koneksi
$conn->set_charset("utf8mb4");

// Fungsi untuk mendapatkan harga gems saat ini
function get_price_settings($conn) {
    $sql = "SELECT buy_price_per_k, sell_price_per_k FROM price_settings LIMIT 1";
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        return $result->fetch_assoc();
    }
    return ['buy_price_per_k' => 0, 'sell_price_per_k' => 0]; // Nilai default jika tidak ditemukan
}
?>