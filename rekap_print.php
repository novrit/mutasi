<?php
require 'inc/koneksi.php'; require 'inc/auth.php';
$q = trim($_GET['q'] ?? ''); $ru = trim($_GET['ruangan'] ?? ''); $pj = trim($_GET['pj'] ?? '');
$where = [];
if ($q !== ''){ $qs = mysqli_real_escape_string($koneksi,$q); $where[] = "(barang LIKE '%$qs%' OR merek LIKE '%$qs%')"; }
if ($ru !== ''){ $rs = mysqli_real_escape_string($koneksi,$ru); $where[] = "ruangan='$rs'"; }
$where_sql = $where ? 'WHERE '.implode(' AND ', $where) : '';
$res = mysqli_query($koneksi, "SELECT * FROM inventaris $where_sql ORDER BY ruangan, sub_ruangan, barang");
// Tentukan nama fakultas/kop
$fakultas = '';
if ($ru !== '') {
  $fakultas = 'DI FAKULTAS ' . strtoupper($ru);
} else {
  $fakultas = 'LAPORAN KESELURUHAN';
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset='utf-8'>
  <title>Rekap Cetak</title>
  <style>
    @media print {
      @page { size: A4 landscape; margin: 0.5in; }
      body { font-family: "Times New Roman", Times, serif; font-size:15px; }
      .container { max-width:100%; margin:0 auto; }
      .card { background:#fff; border-radius:0; box-shadow:none; padding:0; }
      .print-header { display:flex; align-items:center; justify-content:center; margin-bottom:12px; }
      .print-header img { height:70px; margin-right:24px; }
      .kop-wrap { text-align:center; }
      .kop-title { font-size:18px; font-weight:bold; margin:0; }
      .kop-sub { font-size:15px; margin:2px 0 6px 0; }
      .kop-line { border-top:2px solid #000; margin:6px 0 10px 0; }
      table { width:100%; border-collapse:collapse; font-size:15px; }
      th, td { border:1px solid #222; padding:6px 4px; text-align:center; }
      th { background:#f4f6f8; }
      tr { height: 3cm; page-break-inside: avoid; }
      .col-img { width: 3cm; max-width: 3cm; }
      .col-img img { max-height: 2.6cm; max-width: 2.6cm; }
      .ttd-wrap { display:flex; justify-content:space-between; margin-top:18px; }
      .ttd-col { width:45%; text-align:center; }
      .ttd-space { height:90px; }
      .ttd-name { display:inline-block; border-top:1px solid #000; padding-top:2px; min-width:220px; }
    }
    body { font-family: "Times New Roman", Times, serif; font-size:15px; }
    .container { max-width:100%; margin:0 auto; }
    .card { background:#fff; border-radius:0; box-shadow:none; padding:0; }
    .print-header { display:flex; align-items:center; justify-content:center; margin-bottom:12px; }
    .print-header img { height:70px; margin-right:24px; }
    .kop-wrap { text-align:center; }
    .kop-title { font-size:18px; font-weight:bold; margin:0; }
    .kop-sub { font-size:15px; margin:2px 0 6px 0; }
    .kop-line { border-top:2px solid #000; margin:6px 0 10px 0; }
    table { width:100%; border-collapse:collapse; font-size:15px; }
    th, td { border:1px solid #222; padding:6px 4px; text-align:center; }
    th { background:#f4f6f8; }
    tr { height: 3cm; }
    .col-img { width: 3cm; max-width: 3cm; }
    .col-img img { max-height: 2.6cm; max-width: 2.6cm; }
    .ttd-wrap { display:flex; justify-content:space-between; margin-top:18px; }
    .ttd-col { width:45%; text-align:center; }
    .ttd-space { height:90px; }
    .ttd-name { display:inline-block; border-top:1px solid #000; padding-top:2px; min-width:220px; }
  </style>
</head>
<body onload='window.print()'>
<div class='container'><div class='card'>
  <div class='print-header'>
    <img src='assets/logo.png' alt='logo'>
    <div class='kop-wrap'>
      <div class='kop-title'>UNIVERSITAS NIAS</div>
      <div class='kop-sub'>INVENTARISASI GEDUNG/BARANG/PERALATAN DAN MESIN</div>
      <?php if ($ru !== '') { ?>
        <div class='kop-sub'><?php echo $fakultas; ?></div>
      <?php } else { ?>
        <div class='kop-sub'>LAPORAN KESELURUHAN</div>
      <?php } ?>
      <div class='kop-line'></div>
    </div>
  </div>
  <div style='text-align:right;font-size:15px;margin-bottom:12px;font-family:"Times New Roman", Times, serif;'>Tanggal: <?php echo date('d/m/Y'); ?></div>
  <div class='table-wrap'><table><thead><tr>
    <th>No</th><th>Barang</th><th>Merek</th><th>Ukuran</th><th>Bahan</th><th>Tahun</th><th>Pabrik</th><th>Rangka</th><th>Mesin</th><th>Polisi</th><th>Nomor</th><th>Harga</th><th>Jumlah</th><th>Nilai</th><th>Kondisi</th><th>Ruangan</th><th>Sub Ruangan</th><th class='col-img'>Gambar</th>
  </tr></thead><tbody>
<?php $no=1; if ($res) while($row = mysqli_fetch_assoc($res)){
  echo '<tr>';
  echo '<td>'.$no++.'</td>';
  echo '<td>'.htmlspecialchars($row['barang']).'</td>';
  echo '<td>'.htmlspecialchars($row['merek']).'</td>';
  echo '<td>'.htmlspecialchars($row['ukuran']).'</td>';
  echo '<td>'.htmlspecialchars($row['bahan']).'</td>';
  echo '<td>'.htmlspecialchars($row['tahun']).'</td>';
  echo '<td>'.htmlspecialchars($row['pabrik']).'</td>';
  echo '<td>'.htmlspecialchars($row['rangka']).'</td>';
  echo '<td>'.htmlspecialchars($row['mesin']).'</td>';
  echo '<td>'.htmlspecialchars($row['polisi']).'</td>';
  echo '<td>'.htmlspecialchars($row['nomor']).'</td>';
  echo '<td>Rp '.number_format((float)$row['harga'],2,',','.').'</td>';
  echo '<td>'.(int)$row['jumlah'].'</td>';
  echo '<td>Rp '.number_format((float)$row['nilai'],2,',','.').'</td>';
  echo '<td>'.htmlspecialchars($row['kondisi']).'</td>';
  echo '<td>'.htmlspecialchars($row['ruangan']).'</td>';
  echo '<td>'.htmlspecialchars($row['sub_ruangan']).'</td>';
  if (!empty($row['gambar'])) {
    echo '<td class="col-img"><img src="uploads/'.htmlspecialchars($row['gambar']).'" alt="gambar"></td>';
  } else {
    echo '<td class="col-img"><span style="color:#888;font-size:13px;">(Tidak ada gambar)</span></td>';
  }
  echo '</tr>';
} else echo '<tr><td colspan="18">Tidak ada data</td></tr>'; ?>
</tbody></table></div>
  <div class='ttd-wrap'>
    <div class='ttd-col'>
      <div>Mengetahui</div>
      <div class='ttd-space'></div>
      <div class='ttd-name'>&nbsp;</div>
    </div>
    <div class='ttd-col'>
      <div>Penanggung Jawab</div>
      <div class='ttd-space'></div>
      <div class='ttd-name'><?php echo htmlspecialchars($pj); ?></div>
    </div>
  </div>
</div></div>
</body>
</html>