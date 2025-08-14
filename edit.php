<?php
require 'inc/koneksi.php'; 
require 'inc/auth.php';
$id = intval($_GET['id'] ?? 0);
$r = mysqli_query($koneksi, "SELECT * FROM inventaris WHERE id=$id");
if (!$r || !mysqli_num_rows($r)) die('Data tidak ditemukan');
$data = mysqli_fetch_assoc($r);
$err = [];
if ($_SERVER['REQUEST_METHOD']==='POST'){
  $fields = ['barang','merek','ukuran','bahan','tahun','pabrik','rangka','mesin','polisi','nomor','harga','jumlah','nilai','kondisi','ruangan','sub_ruangan'];
  $upd = [];
  foreach($fields as $f) $upd[$f] = mysqli_real_escape_string($koneksi, trim($_POST[$f] ?? ''));
  if ($upd['barang'] === '') $err[] = 'Nama barang wajib diisi';
  if (!empty($_FILES['gambar']['name'])){
    $ext = strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));
    $new = 'IMG_'.time().'_'.mt_rand(1000,9999).'.'.$ext;
    move_uploaded_file($_FILES['gambar']['tmp_name'],'uploads/'.$new);
    if (!empty($data['gambar']) && file_exists('uploads/'.$data['gambar'])) @unlink('uploads/'.$data['gambar']);
    $img_sql = ", gambar='".mysqli_real_escape_string($koneksi,$new)."'";
  } else $img_sql = '';
  if (!$err){
    $sql = "UPDATE inventaris SET barang='{$upd['barang']}', merek='{$upd['merek']}', ukuran='{$upd['ukuran']}', bahan='{$upd['bahan']}', tahun='{$upd['tahun']}', pabrik='{$upd['pabrik']}', rangka='{$upd['rangka']}', mesin='{$upd['mesin']}', polisi='{$upd['polisi']}', nomor='{$upd['nomor']}', harga=".($upd['harga']==''?'NULL':$upd['harga']).", jumlah=".($upd['jumlah']==''?'NULL':$upd['jumlah']).", nilai=".($upd['nilai']==''?'NULL':$upd['nilai']).", kondisi='{$upd['kondisi']}', ruangan='{$upd['ruangan']}', sub_ruangan='{$upd['sub_ruangan']}' $img_sql WHERE id=$id";
    if (mysqli_query($koneksi,$sql)) { header('Location: index.php'); exit; } else $err[] = 'DB error: '.mysqli_error($koneksi);
  }
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset='utf-8'>
<title>Edit</title>
<link rel='stylesheet' href='css/style.css'>
<script src='js/combo.js'></script>
<style>
/* Tombol kecil, di tengah, teks di tengah */
.tombol-container {
    grid-column: 1 / -1;
    display: flex;
    justify-content: center; /* horizontal center */
    gap: 10px;
    margin-top: 10px;
}
.btn.kecil {
    padding: 4px 12px;
    font-size: 12px;
    border-radius: 4px;
    display: inline-flex;         /* agar teks di tengah */
    align-items: center;          /* vertical center */
    justify-content: center;      /* horizontal center */
    text-decoration: none;        /* untuk <a> */
}
</style>
</head>
<body>
<div class='container'>
  <div class='card'>
    <h2>Edit Data</h2>
    <?php if($err) echo '<div style="background:#fee2e2;padding:8px;border-radius:6px;margin-bottom:8px;color:#7f1d1d">'.implode('<br>',$err).'</div>'; ?>
    <form method='post' enctype='multipart/form-data' class='form'>
    <?php
    $fields = ['barang'=>'Barang','merek'=>'Merek','ukuran'=>'Ukuran','bahan'=>'Bahan','tahun'=>'Tahun','pabrik'=>'Pabrik','rangka'=>'Rangka','mesin'=>'Mesin','polisi'=>'Polisi','nomor'=>'Nomor','harga'=>'Harga','jumlah'=>'Jumlah','nilai'=>'Nilai','kondisi'=>'Kondisi'];
    foreach($fields as $n=>$label){
      $val = htmlspecialchars($data[$n] ?? '');
      $required = $n==='barang' ? " required" : "";
      echo "<div class='field'><label>$label</label><input name='$n' value='$val'$required></div>";
    }
    ?>
    <div class='field'>
      <label>Ruangan</label>
      <select id='ruangan' name='ruangan'>
        <option value=''>-- Pilih --</option>
        <option<?php if($data['ruangan']=='REKTORAT') echo ' selected'; ?>>REKTORAT</option>
        <option<?php if($data['ruangan']=='FKIP') echo ' selected'; ?>>FKIP</option>
        <option<?php if($data['ruangan']=='FE') echo ' selected'; ?>>FE</option>
        <option<?php if($data['ruangan']=='FST') echo ' selected'; ?>>FST</option>
      </select>
    </div>
    <div class='field'>
      <label>Sub Ruangan</label>
      <select id='sub_ruangan' name='sub_ruangan'><option value=''>-- Pilih --</option></select>
    </div>
    <div class='field'>
      <label>Gambar (biarkan kosong)</label>
      <input type='file' name='gambar'>
    </div>
    <div class='field tombol-container'>
      <button class='btn kecil' type='submit'>Simpan</button>
      <a class='btn kecil' href='index.php'>Batal</a>
    </div>
    </form>
  </div>
</div>
</body>
</html>
