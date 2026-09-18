<?php
require 'db.php';

// Fitur Tambah Menu
if (isset($_POST['tambah_menu'])) {
    $nama = $_POST['nama'];
    $kategori = $_POST['kategori'];
    $harga = (int)$_POST['harga'];

    if (!empty($nama) && $harga > 0) {
        $stmt = $conn->prepare("INSERT INTO menu (nama, kategori, harga) VALUES (?, ?, ?)");
        $stmt->bind_param("ssi", $nama, $kategori, $harga);
        $stmt->execute();
    }
    header("Location: index.php");
    exit;
}

// Fitur Hapus Menu
if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    $conn->query("DELETE FROM menu WHERE id = $id");
    header("Location: index.php");
    exit;
}

$resultMenu = $conn->query("SELECT * FROM menu ORDER BY kategori, nama ASC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Sistem Warkop - Kelola Menu</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, sans-serif; margin: 0; padding: 20px; background: #f4f6f9; }
        .nav { margin-bottom: 20px; }
        .nav a { margin-right: 15px; text-decoration: none; font-weight: bold; color: #3d2314; padding: 8px 12px; background: #fff; border-radius: 5px; border: 1px solid #ccc; }
        .container { display: flex; gap: 20px; }
        .card { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .form-card { flex: 1; }
        .list-card { flex: 2; }
        input, select, button { width: 100%; padding: 10px; margin-top: 5px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        button { background: #27ae60; color: white; border: none; font-weight: bold; cursor: pointer; }
        button:hover { background: #219150; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #3d2314; color: white; }
        .btn-danger { color: red; text-decoration: none; font-weight: bold; }
    </style>
</head>
<body>

<div class="nav">
    <a href="index.php">Daftar Menu</a>
    <a href="pos.php">Kasir / POS</a>
    <a href="riwayat.php">Riwayat Pesanan</a>
</div>

<h2>Kelola Menu Warkop</h2>

<div class="container">
    <!-- Form Tambah Menu -->
    <div class="card form-card">
        <h3>Tambah Menu Baru</h3>
        <form method="POST">
            <label>Nama Menu</label>
            <input type="text" name="nama" required placeholder="Contoh: Kopi Joss">
            
            <label>Kategori</label>
            <select name="kategori">
                <option value="Minuman">Minuman</option>
                <option value="Makanan">Makanan</option>
                <option value="Camilan">Camilan</option>
            </select>
            
            <label>Harga (Rp)</label>
            <input type="number" name="harga" required placeholder="5000">
            
            <button type="submit" name="tambah_menu">Simpan Menu</button>
        </form>
    </div>

    <!-- Tabel Daftar Menu -->
    <div class="card list-card">
        <h3>Daftar Menu Saat Ini</h3>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Menu</th>
                    <th>Kategori</th>
                    <th>Harga</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; while($row = $resultMenu->fetch_assoc()): ?>
                <tr>
                    <td><?= $no++; ?></td>
                    <td><?= htmlspecialchars($row['nama']); ?></td>
                    <td><?= $row['kategori']; ?></td>
                    <td>Rp <?= number_format($row['harga'], 0, ',', '.'); ?></td>
                    <td><a href="index.php?hapus=<?= $row['id']; ?>" class="btn-danger" onclick="return confirm('Hapus menu ini?')">Hapus</a></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
