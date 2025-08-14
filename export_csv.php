<?php
require 'inc/koneksi.php'; require 'inc/auth.php';
header('Content-Type: text/csv; charset=utf-8'); header('Content-Disposition: attachment; filename=inventaris.csv');
$out = fopen('php://output','w'); fputcsv($out,['No','Barang','Merek','Ruangan','Sub Ruangan','Jumlah','Nilai']);
$q=trim($_GET['q'] ?? ''); $ru=trim($_GET['ruangan'] ?? ''); $sr=trim($_GET['sub_ruangan'] ?? '');
$where=[]; if($q!==''){ $qs=mysqli_real_escape_string($koneksi,$q); $where[]="(barang LIKE '%$qs%' OR merek LIKE '%$qs%')"; }
if($ru!==''){ $rs=mysqli_real_escape_string($koneksi,$ru); $where[]="ruangan='$rs'"; }
if($sr!==''){ $ss=mysqli_real_escape_string($koneksi,$sr); $where[]="sub_ruangan='$ss'"; }
$where_sql = $where ? 'WHERE '.implode(' AND ',$where) : '';
$res = mysqli_query($koneksi,'SELECT * FROM inventaris '.$where_sql.' ORDER BY id DESC');
$no=1; while($r = mysqli_fetch_assoc($res)) fputcsv($out,[$no++,$r['barang'],$r['merek'],$r['ruangan'],$r['sub_ruangan'],$r['jumlah'],$r['nilai']]);
fclose($out); exit;
?>