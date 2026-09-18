<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "warkop_db";

// Koneksi ke server MySQL
$conn = new mysqli($host, $user, $pass);
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Buat database jika belum ada
$conn->query("CREATE DATABASE IF NOT EXISTS $dbname");
$conn->select_db($dbname);

// Buat Tabel Menu
$conn->query("CREATE TABLE IF NOT EXISTS menu (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    kategori ENUM('Minuman', 'Makanan', 'Camilan') NOT NULL,
    harga INT NOT NULL
)");

// Buat Tabel Pesanan
$conn->query("CREATE TABLE IF NOT EXISTS pesanan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nomor_meja VARCHAR(50) NOT NULL,
    total INT NOT NULL,
    metode_bayar VARCHAR(20) NOT NULL,
    tanggal DATETIME DEFAULT CURRENT_TIMESTAMP
)");

// Buat Tabel Detail Pesanan
$conn->query("CREATE TABLE IF NOT EXISTS detail_pesanan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pesanan_id INT NOT NULL,
    menu_nama VARCHAR(100) NOT NULL,
    jumlah INT NOT NULL,
    subtotal INT NOT NULL,
    FOREIGN KEY (pesanan_id) REFERENCES pesanan(id) ON DELETE CASCADE
)");

// Cek dan isi data awal menu jika masih kosong
$cekMenu = $conn->query("SELECT COUNT(*) as total FROM menu")->fetch_assoc();
if ($cekMenu['total'] == 0) {
    $conn->query("INSERT INTO menu (nama, kategori, harga) VALUES
        ('Kopi Hitam Tubruk', 'Minuman', 5000),
        ('Es Kopi Susu', 'Minuman', 8000),
        ('Teh Manis', 'Minuman', 4000),
        ('Indomie Goreng + Telur', 'Makanan', 10000),
        ('Indomie Kuah', 'Makanan', 8000),
        ('Gorengan (3 Pcs)', 'Camilan', 5000),
        ('Roti Bakar Cokelat Keju', 'Camilan', 12000)");
}
?>
