<?php
require 'inc/koneksi.php';
require 'inc/auth.php';

$q = trim($_GET['q'] ?? '');
$where = '';
if ($q !== '') {
  $qs = mysqli_real_escape_string($koneksi, $q);
  $where = "WHERE (barang LIKE '%$qs%' OR merek LIKE '%$qs%' OR ruangan LIKE '%$qs%')";
}
$res = mysqli_query($koneksi, "SELECT * FROM inventaris $where ORDER BY id DESC");
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>LAPORAN KESELURUHAN</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="container" style="max-width:1200px;margin:40px auto 0 auto;">
  <div class="card" style="background:#fff;border-radius:12px;padding:48px 32px 48px 32px;min-height:700px;box-shadow:0 2px 12px rgba(0,0,0,0.07);display:flex;flex-direction:column;justify-content:flex-start;">
    <div style="margin-bottom:18px;display:flex;justify-content:flex-start;">
      <a href="index.php" class="btn" style="padding:7px 18px;font-size:15px;background:#e3e7ed;color:#222;border-radius:6px;text-decoration:none;box-shadow:0 1px 4px rgba(0,0,0,0.04);font-weight:500;">&larr; HOME</a>
    </div>
    <h2 style="text-align:center;font-weight:600;margin-bottom:18px;color:#222;">LAPORAN KESELURUHAN</h2>
    <form method="get" style="margin-bottom:24px;display:flex;gap:12px;align-items:center;justify-content:center;">
      <input type="text" name="q" placeholder="Cari barang, merek, ruangan..." value="<?php echo htmlspecialchars($_GET['q'] ?? ''); ?>" style="padding:7px 14px;font-size:16px;border-radius:6px;border:1px solid #ccc;min-width:220px;">
      <button class="btn" type="submit" style="padding:7px 18px;font-size:15px;">Cari</button>
    </form>
    <div style="overflow-x:auto;">
    <table style="margin:auto;width:100%;max-width:760px;border-collapse:collapse;background:#fff;">
      <thead>
        <tr style="background:#f4f6f8;">
          <th style="padding:7px 6px;font-size:14px;">No</th>
          <th style="padding:7px 6px;font-size:14px;">No Barang</th>
          <th style="padding:7px 6px;font-size:14px;">Barang</th>
          <th style="padding:7px 6px;font-size:14px;">Merek</th>
          <th style="padding:7px 6px;font-size:14px;">Ukuran</th>
          <th style="padding:7px 6px;font-size:14px;">Bahan</th>
          <th style="padding:7px 6px;font-size:14px;">Tahun</th>
          <th style="padding:7px 6px;font-size:14px;">Pabrik</th>
          <th style="padding:7px 6px;font-size:14px;">Rangka</th>
          <th style="padding:7px 6px;font-size:14px;">Mesin</th>
          <th style="padding:7px 6px;font-size:14px;">Polisi</th>
          <th style="padding:7px 6px;font-size:14px;">Nomor</th>
          <th style="padding:7px 6px;font-size:14px;">Harga</th>
          <th style="padding:7px 6px;font-size:14px;">Jumlah</th>
          <th style="padding:7px 6px;font-size:14px;">Nilai</th>
          <th style="padding:7px 6px;font-size:14px;">Kondisi</th>
          <th style="padding:7px 6px;font-size:14px;">Ruangan</th>
          <th style="padding:7px 6px;font-size:14px;">Sub Ruangan</th>
          <th class="col-img" style="padding:7px 6px;font-size:14px;">Gambar</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $no = 1;
        while($row = mysqli_fetch_assoc($res)) {
          echo '<tr style="background:' . ($no%2==0 ? '#f9fafb' : '#fff') . ';">';
          echo '<td style="padding:6px 5px;text-align:center;">' . $no++ . '</td>';
          echo '<td style="padding:6px 5px;">' . htmlspecialchars($row['no_barang']) . '</td>';
          echo '<td style="padding:6px 5px;"><a href="detail_besar.php?id=' . $row['id'] . '" style="color:#1a4fa3;text-decoration:underline;font-weight:500;" target="_blank">' . htmlspecialchars($row['barang']) . '</a></td>';
          echo '<td style="padding:6px 5px;">' . htmlspecialchars($row['merek']) . '</td>';
          echo '<td style="padding:6px 5px;">' . htmlspecialchars($row['ukuran']) . '</td>';
          echo '<td style="padding:6px 5px;">' . htmlspecialchars($row['bahan']) . '</td>';
          echo '<td style="padding:6px 5px;">' . htmlspecialchars($row['tahun']) . '</td>';
          echo '<td style="padding:6px 5px;">' . htmlspecialchars($row['pabrik']) . '</td>';
          echo '<td style="padding:6px 5px;">' . htmlspecialchars($row['rangka']) . '</td>';
          echo '<td style="padding:6px 5px;">' . htmlspecialchars($row['mesin']) . '</td>';
          echo '<td style="padding:6px 5px;">' . htmlspecialchars($row['polisi']) . '</td>';
          echo '<td style="padding:6px 5px;">' . htmlspecialchars($row['nomor']) . '</td>';
          echo '<td style="padding:6px 5px;">Rp ' . number_format((float)$row['harga'], 2, ',', '.') . '</td>';
          echo '<td style="padding:6px 5px;text-align:center;">' . (int)$row['jumlah'] . '</td>';
          echo '<td style="padding:6px 5px;">Rp ' . number_format((float)$row['nilai'], 2, ',', '.') . '</td>';
          echo '<td style="padding:6px 5px;">' . htmlspecialchars($row['kondisi']) . '</td>';
          echo '<td style="padding:6px 5px;">' . htmlspecialchars($row['ruangan']) . '</td>';
          echo '<td style="padding:6px 5px;">' . htmlspecialchars($row['sub_ruangan']) . '</td>';
          if (!empty($row['gambar'])) {
            echo '<td class="col-img"><img src="uploads/' . htmlspecialchars($row['gambar']) . '" alt="gambar"></td>';
          } else {
            echo '<td class="col-img"><span style="color:#888;font-size:13px;">(Tidak ada gambar)</span></td>';
          }
          echo '</tr>';
        }
        ?>
      </tbody>
    </table>
    </div>
  </div>
</div>
</body>
</html>
