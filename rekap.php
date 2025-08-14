<?php
require 'inc/koneksi.php'; require 'inc/auth.php';
$q = trim($_GET['q'] ?? ''); $ru = trim($_GET['ruangan'] ?? '');
$where = [];
if ($q !== ''){ $qs = mysqli_real_escape_string($koneksi,$q); $where[] = "(barang LIKE '%$qs%' OR merek LIKE '%$qs%')"; }
if ($ru !== ''){ $rs = mysqli_real_escape_string($koneksi,$ru); $where[] = "ruangan='$rs'"; }
$where_sql = $where ? 'WHERE '.implode(' AND ', $where) : '';
$res = mysqli_query($koneksi, "SELECT * FROM inventaris $where_sql ORDER BY ruangan, sub_ruangan, barang");
?>
<!DOCTYPE html><html><head><meta charset='utf-8'><title>Rekap</title><link rel='stylesheet' href='css/style.css'><script src='js/combo.js'></script></head><body>
<div class='container'><div class='card'>
<div class='header'><div class='brand'><img src='assets/logo.png' alt='logo'><div><strong>Rekap Inventaris</strong></div></div><div><a class='btn' href='index.php'>Kembali</a></div></div>
<form method='get' class='form no-print'>
<div class='field'><label>Cari</label><input name='q' value='<?php echo htmlspecialchars($q); ?>'></div>
<div class='field'><label>Ruangan</label><select id='ruangan' name='ruangan'><option value=''>-- Semua --</option><option<?php if($ru=='REKTORAT') echo ' selected'; ?>>REKTORAT</option><option<?php if($ru=='FKIP') echo ' selected'; ?>>FKIP</option><option<?php if($ru=='FE') echo ' selected'; ?>>FE</option><option<?php if($ru=='FST') echo ' selected'; ?>>FST</option></select></div>
<div class='field' style='align-self:end;display:flex;justify-content:center;gap:8px;'>
  <button class='btn' type='submit' style='padding:4px 10px;font-size:13px;min-width:60px;'>Filter</button>
  <a class='btn' href='rekap.php' style='padding:4px 10px;font-size:13px;min-width:60px;display:inline-block;text-align:center;'>Reset</a>
</div>
</form>
<div style='margin-top:10px'>Penanggung Jawab (untuk kop/ttd saat cetak): <input id='pj' placeholder='Nama Penanggung Jawab'></div>
<div class='table-wrap' style='margin-top:8px'><table><thead><tr>
<th>No</th><th>Barang</th><th>Merek</th><th>Ukuran</th><th>Bahan</th><th>Tahun</th><th>Pabrik</th><th>Rangka</th><th>Mesin</th><th>Polisi</th><th>Nomor</th><th>Harga</th><th>Jumlah</th><th>Nilai</th><th>Kondisi</th><th>Ruangan</th><th>Sub Ruangan</th><th class='col-img'>Gambar</th>
</tr></thead><tbody>
<?php $no=1; if ($res) while($row=mysqli_fetch_assoc($res)){
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
<div style='margin-top:8px'><button class='btn' onclick='doPrint()'>Cetak / Export PDF</button> <a class='btn' href='export_xlsx.php?<?php echo http_build_query($_GET); ?>'>Export XLSX</a> <a class='btn' href='export_word.php?<?php echo http_build_query($_GET); ?>'>Export Word</a></div>
<script>
function doPrint(){ const pj = document.getElementById('pj').value || ''; const params = new URLSearchParams(location.search); if (pj) params.set('pj', pj); window.open('rekap_print.php?'+params.toString(), '_blank'); }
</script>
</div></div></body></html></script></script>