<?php
require 'inc/koneksi.php';
require 'inc/auth.php';
$id = intval($_GET['id'] ?? 0);
$res = mysqli_query($koneksi, "SELECT * FROM inventaris WHERE id=$id LIMIT 1");
$row = mysqli_fetch_assoc($res);
if (!$row) { echo '<div style="padding:40px;text-align:center;">Data tidak ditemukan</div>'; exit; }
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Detail Barang Besar</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="container" style="max-width:900px;margin:40px auto 0 auto;">
  <div class="card" style="background:#fff;border-radius:12px;padding:48px 32px;box-shadow:0 2px 12px rgba(0,0,0,0.07);">
    <a href="detail_barang.php" class="btn" style="margin-bottom:24px;display:inline-block;">&larr; Kembali</a>
    <h2 style="text-align:center;font-weight:600;margin-bottom:32px;color:#222;">Detail Barang</h2>
    <div style="display:flex;gap:32px;align-items:flex-start;flex-wrap:wrap;">
      <div style="flex:1;min-width:260px;">
        <table style="width:100%;font-size:17px;">
          <tr><td><b>Barang</b></td><td><?php echo htmlspecialchars($row['barang']); ?></td></tr>
          <tr><td><b>Merek</b></td><td><?php echo htmlspecialchars($row['merek']); ?></td></tr>
          <tr><td><b>Ukuran</b></td><td><?php echo htmlspecialchars($row['ukuran']); ?></td></tr>
          <tr><td><b>Bahan</b></td><td><?php echo htmlspecialchars($row['bahan']); ?></td></tr>
          <tr><td><b>Tahun</b></td><td><?php echo htmlspecialchars($row['tahun']); ?></td></tr>
          <tr><td><b>Pabrik</b></td><td><?php echo htmlspecialchars($row['pabrik']); ?></td></tr>
          <tr><td><b>Rangka</b></td><td><?php echo htmlspecialchars($row['rangka']); ?></td></tr>
          <tr><td><b>Mesin</b></td><td><?php echo htmlspecialchars($row['mesin']); ?></td></tr>
          <tr><td><b>Polisi</b></td><td><?php echo htmlspecialchars($row['polisi']); ?></td></tr>
          <tr><td><b>Nomor</b></td><td><?php echo htmlspecialchars($row['nomor']); ?></td></tr>
          <tr><td><b>Harga</b></td><td>Rp <?php echo number_format((float)$row['harga'],2,',','.'); ?></td></tr>
          <tr><td><b>Jumlah</b></td><td><?php echo (int)$row['jumlah']; ?></td></tr>
          <tr><td><b>Nilai</b></td><td>Rp <?php echo number_format((float)$row['nilai'],2,',','.'); ?></td></tr>
          <tr><td><b>Kondisi</b></td><td><?php echo htmlspecialchars($row['kondisi']); ?></td></tr>
          <tr><td><b>Ruangan</b></td><td><?php echo htmlspecialchars($row['ruangan']); ?></td></tr>
          <tr><td><b>Sub Ruangan</b></td><td><?php echo htmlspecialchars($row['sub_ruangan']); ?></td></tr>
        </table>
      </div>
      <div style="flex:1;min-width:260px;text-align:center;">
        <?php if (!empty($row['gambar'])) {
          echo '<img src="uploads/'.htmlspecialchars($row['gambar']).'" alt="gambar" style="max-width:340px;max-height:340px;border-radius:8px;border:1px solid #eee;background:#fafafa;">';
        } else {
          echo '<span style="color:#888;font-size:16px;">(Tidak ada gambar)</span>';
        } ?>
      </div>
    </div>
  </div>
</div>
</body>
</html>
