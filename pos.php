<?php
require 'db.php';

// Proses Transaksi Pesanan
if (isset($_POST['proses_pesanan'])) {
    $nomor_meja = $_POST['nomor_meja'];
    $metode_bayar = $_POST['metode_bayar'];
    $items = $_POST['items'] ?? [];
    $total = 0;

    if (!empty($items) && !empty($nomor_meja)) {
        // Hitung Total
        foreach ($items as $menu_id => $qty) {
            if ($qty > 0) {
                $res = $conn->query("SELECT harga FROM menu WHERE id = $menu_id");
                $m = $res->fetch_assoc();
                $total += $m['harga'] * $qty;
            }
        }

        if ($total > 0) {
            // Simpan Master Pesanan
            $stmt = $conn->prepare("INSERT INTO pesanan (nomor_meja, total, metode_bayar) VALUES (?, ?, ?)");
            $stmt->bind_param("sis", $nomor_meja, $total, $metode_bayar);
            $stmt->execute();
            $pesanan_id = $conn->insert_id;

            // Simpan Detail Pesanan
            foreach ($items as $menu_id => $qty) {
                if ($qty > 0) {
                    $res = $conn->query("SELECT nama, harga FROM menu WHERE id = $menu_id");
                    $m = $res->fetch_assoc();
                    $subtotal = $m['harga'] * $qty;

                    $stmtDetail = $conn->prepare("INSERT INTO detail_pesanan (pesanan_id, menu_nama, jumlah, subtotal) VALUES (?, ?, ?, ?)");
                    $stmtDetail->bind_param("isii", $pesanan_id, $m['nama'], $qty, $subtotal);
                    $stmtDetail->execute();
                }
            }

            echo "<script>alert('Transaksi Berhasil Ditambahkan!'); window.location='riwayat.php';</script>";
            exit;
        }
    }
}

$menuResult = $conn->query("SELECT * FROM menu ORDER BY kategori, nama ASC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kasir Warkop</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, sans-serif; margin: 0; padding: 20px; background: #f4f6f9; }
        .nav { margin-bottom: 20px; }
        .nav a { margin-right: 15px; text-decoration: none; font-weight: bold; color: #3d2314; padding: 8px 12px; background: #fff; border-radius: 5px; border: 1px solid #ccc; }
        .container { display: flex; gap: 20px; }
        .card { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .menu-list { flex: 2; }
        .order-summary { flex: 1; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 8px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #3d2314; color: white; }
        input[type="number"] { width: 60px; padding: 5px; text-align: center; }
        input[type="text"], select { width: 100%; padding: 8px; margin-bottom: 10px; box-sizing: border-box; }
        .btn-pay { background: #27ae60; color: white; padding: 12px; width: 100%; border: none; font-size: 16px; font-weight: bold; border-radius: 5px; cursor: pointer; }
    </style>
</head>
<body>

<div class="nav">
    <a href="index.php">Daftar Menu</a>
    <a href="pos.php">Kasir / POS</a>
    <a href="riwayat.php">Riwayat Pesanan</a>
</div>

<h2>Kasir / Point of Sale Warkop</h2>

<form method="POST">
    <div class="container">
        <!-- Pilih Menu -->
        <div class="card menu-list">
            <h3>Pilih Menu</h3>
            <table>
                <thead>
                    <tr>
                        <th>Menu</th>
                        <th>Kategori</th>
                        <th>Harga</th>
                        <th>Jumlah</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $menuResult->fetch_assoc()): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($row['nama']); ?></strong></td>
                        <td><?= $row['kategori']; ?></td>
                        <td>Rp <?= number_format($row['harga'], 0, ',', '.'); ?></td>
                        <td>
                            <input type="number" name="items[<?= $row['id']; ?>]" value="0" min="0">
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <!-- Form Informasi Pemesanan -->
        <div class="card order-summary">
            <h3>Detail Transaksi</h3>
            
            <label>Lokasi / Nomor Meja:</label>
            <input type="text" name="nomor_meja" required placeholder="Contoh: Meja 03 / Bungkus">

            <label>Metode Pembayaran:</label>
            <select name="metode_bayar">
                <option value="Tunai">Tunai (Cash)</option>
                <option value="QRIS">QRIS</option>
                <option value="Transfer">Transfer Bank</option>
            </select>

            <button type="submit" name="proses_pesanan" class="btn-pay">Simpan & Proses Transaksi</button>
        </div>
    </div>
</form>

</body>
</html>
