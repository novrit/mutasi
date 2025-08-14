<?php
require 'inc/koneksi.php';
require 'inc/auth.php';

$q = trim($_GET['q'] ?? '');
$ru = trim($_GET['ruangan'] ?? '');
$sr = trim($_GET['sub_ruangan'] ?? '');
$page = max(1, intval($_GET['page'] ?? 1));
$per = 10;
$off = ($page - 1) * $per;
$where = [];
if ($q !== '') {
  $qs = mysqli_real_escape_string($koneksi, $q);
  $where[] = "(barang LIKE '%$qs%' OR merek LIKE '%$qs%')";
}
if ($ru !== '') {
  $rs = mysqli_real_escape_string($koneksi, $ru);
  $where[] = "ruangan='$rs'";
}
if ($sr !== '') {
  $ss = mysqli_real_escape_string($koneksi, $sr);
  $where[] = "sub_ruangan='$ss'";
}
$where_sql = $where ? 'WHERE ' . implode(' AND ', $where) : '';
$count_r = mysqli_query($koneksi, "SELECT COUNT(*) AS c FROM inventaris $where_sql");
if (!$count_r) die('Query count error: ' . mysqli_error($koneksi));
$total = (int)mysqli_fetch_assoc($count_r)['c'];
$pages = max(1, ceil($total / $per));
$res = mysqli_query($koneksi, "SELECT * FROM inventaris $where_sql ORDER BY id DESC LIMIT $per OFFSET $off");
$ruangan_list = ['REKTORAT', 'FKIP', 'FE', 'FST'];
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Inventaris</title>
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="css/notif.css">
  <script src="js/combo.js"></script>
  <style>
    /* Animasi teks berjalan dari kanan ke kiri dan di tengah */
    .marquee-wrap {
      overflow: hidden;
      position: relative;
      width: 100%;
      height: 40px;
      margin-bottom: 24px;
      background: #f0f0f0;
      border-radius: 8px;
      display: flex;
      align-items: center; /* vertical center */
    }

    .marquee {
      display: inline-block;
      white-space: nowrap;
      position: absolute;
      animation: marquee 10s linear infinite;
      font-size: 1.5em;
      font-weight: bold;
      color: #1a73e8;
    }

    @keyframes marquee {
      0% { transform: translateX(100%); }   /* mulai dari kanan */
      100% { transform: translateX(-100%); } /* bergerak ke kiri */
    }
  </style>
</head>
<body>
  <div class="notif-success">Anda berhasil masuk</div>
  <div class="container" style="max-width:1200px;margin:40px auto 0 auto;">
    <div class="card" style="background:#fff;border-radius:12px;padding:48px 32px 48px 32px;min-height:700px;box-shadow:0 2px 12px rgba(0,0,0,0.07);display:flex;flex-direction:column;justify-content:flex-start;">
      <div style="position:relative;margin-bottom:24px;">
        <h1 style="margin:0;font-size:2em;text-align:center;margin-bottom:16px;">
          INVENTARIS<br>UNIVERSITAS NIAS
        </h1>
        <!-- Teks berjalan -->
        <div class="marquee-wrap">
          <div class="marquee">UNIVERSITAS NIAS</div>
        </div>
        <img src="assets/Logo.png" alt="logo" style="width:80px;position:absolute;top:0;right:0;">
      </div>

      <!-- Form pencarian -->
      <form class="form no-print" method="get" style="margin-bottom:24px;">
        <div class="field">
          <label>Cari</label>
          <input name="q" value="<?php echo htmlspecialchars($q); ?>">
        </div>
        <div class="field">
          <label>Ruangan</label>
          <select id="ruangan" name="ruangan">
            <option value="">-- Semua --</option>
            <?php foreach ($ruangan_list as $r) echo "<option" . ($r == $ru ? ' selected' : '') . " value='$r'>$r</option>"; ?>
          </select>
        </div>
        <div class="field">
          <label>Sub Ruangan</label>
          <select id="sub_ruangan" name="sub_ruangan">
            <option value="">-- Semua --</option>
          </select>
        </div>
        <div class="field" style="align-self:end;display:flex;justify-content:center;gap:8px;">
          <button class="btn" type="submit" style="padding:4px 10px;font-size:13px;min-width:70px;text-align:center;">Terapkan</button>
          <a class="btn" href="index.php" style="padding:4px 10px;font-size:13px;min-width:70px;text-align:center;">Reset</a>
        </div>
      </form>

      <!-- Tabel inventaris -->
      <div class="table-wrap">
        <table>
          <thead>
            <tr>
              <th>No</th>
              <th>Barang</th>
              <th>Merek</th>
              <th>Ruangan</th>
              <th>Sub Ruangan</th>
              <th>Jumlah</th>
              <th>Nilai</th>
              <th class="col-img">Gambar</th>
              <th class="no-print">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $no = $off + 1;
            if ($res) {
              while ($row = mysqli_fetch_assoc($res)) {
                echo '<tr>';
                echo '<td>' . $no++ . '</td>';
                echo '<td>' . htmlspecialchars($row['barang']) . '</td>';
                echo '<td>' . htmlspecialchars($row['merek']) . '</td>';
                echo '<td>' . htmlspecialchars($row['ruangan']) . '</td>';
                echo '<td>' . htmlspecialchars($row['sub_ruangan']) . '</td>';
                echo '<td>' . (int)$row['jumlah'] . '</td>';
                echo '<td>Rp ' . number_format((float)$row['nilai'], 2, ',', '.') . '</td>';
                echo '<td class="col-img">';
                if (!empty($row['gambar'])) {
                  echo '<img src="uploads/' . htmlspecialchars($row['gambar']) . '" alt="gambar" style="height:32px;">';
                } else {
                  echo '<span style="color:#888;font-size:13px;">(Tidak ada gambar)</span>';
                }
                echo '</td>';
                echo '<td class="no-print"><a class="btn" href="edit.php?id=' . $row['id'] . '">Edit</a> <a class="btn warn" href="hapus.php?id=' . $row['id'] . '" onclick="return confirm(\'Hapus?\')">Hapus</a></td>';
                echo '</tr>';
              }
            } else {
              echo '<tr><td colspan="9">Tidak ada data.</td></tr>';
            }
            ?>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div class='pagination no-print' style="margin-top:32px;">
        <?php for($p=1;$p<=$pages;$p++){ $qs = $_GET; $qs['page']=$p; $url='?'.http_build_query($qs); echo '<a class="btn'.($p==$page?'':'').'" href="'.$url.'">'.$p.'</a> '; } ?>
      </div>

      <!-- Tombol aksi bawah tabel -->
      <div style="display:flex;justify-content:flex-start;gap:12px;margin-top:24px;">
        <a class="btn" href="tambah.php">+ Tambah</a>
        <a class="btn" href="rekap.php">Rekap</a>
        <a class="btn" href="detail_barang.php">Detail Barang</a>
        <a class="btn" href="export_xlsx.php?<?php echo http_build_query($_GET); ?>">Export XLSX</a>
        <a class="btn" href="export_word.php?<?php echo http_build_query($_GET); ?>">Export Word</a>
        <a class="btn warn" href="logout.php">Keluar</a>
      </div>
    </div>
  </div>
  <?php include 'inc/footer.php'; ?>
</body>
</html>
