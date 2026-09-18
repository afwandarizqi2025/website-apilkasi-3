<?php
require 'db.php';

$sql = "SELECT p.*, GROUP_CONCAT(CONCAT(dp.menu_nama, ' (', dp.jumlah, 'x)') SEPARATOR ', ') AS detail_item 
        FROM pesanan p 
        JOIN detail_pesanan dp ON p.id = dp.pesanan_id 
        GROUP BY p.id 
        ORDER BY p.tanggal DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Riwayat Pesanan Warkop</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, sans-serif; margin: 0; padding: 20px; background: #f4f6f9; }
        .nav { margin-bottom: 20px; }
        .nav a { margin-right: 15px; text-decoration: none; font-weight: bold; color: #3d2314; padding: 8px 12px; background: #fff; border-radius: 5px; border: 1px solid #ccc; }
        .card { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #3d2314; color: white; }
        .badge { padding: 4px 8px; background: #e0e0e0; border-radius: 4px; font-size: 12px; }
    </style>
</head>
<body>

<div class="nav">
    <a href="index.php">Daftar Menu</a>
    <a href="pos.php">Kasir / POS</a>
    <a href="riwayat.php">Riwayat Pesanan</a>
</div>

<h2>Riwayat Transaksi Pesanan</h2>

<div class="card">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Tanggal</th>
                <th>Meja / Lokasi</th>
                <th>Detail Pesanan</th>
                <th>Metode</th>
                <th>Total Bayar</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td>#ORD-<?= $row['id']; ?></td>
                    <td><?= date('d/m/Y H:i', strtotime($row['tanggal'])); ?></td>
                    <td><strong><?= htmlspecialchars($row['nomor_meja']); ?></strong></td>
                    <td><?= htmlspecialchars($row['detail_item']); ?></td>
                    <td><span class="badge"><?= $row['metode_bayar']; ?></span></td>
                    <td><strong>Rp <?= number_format($row['total'], 0, ',', '.'); ?></strong></td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" style="text-align: center;">Belum ada riwayat transaksi.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>
