<?php
// Pastikan library tersedia (tanpa fallback ke CSV)
if (!file_exists('vendor/autoload.php')) {
    http_response_code(500);
    header('Content-Type: text/plain; charset=utf-8');
    echo 'Library PhpSpreadsheet belum terpasang. Jalankan: composer require phpoffice/phpspreadsheet';
    exit;
}

require 'vendor/autoload.php'; // pastikan PhpSpreadsheet sudah di-install via Composer
require 'inc/koneksi.php';
require 'inc/auth.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

// Jika library belum terpasang, hentikan (tanpa fallback ke CSV)
if (!class_exists('PhpOffice\\PhpSpreadsheet\\Spreadsheet')) {
    http_response_code(500);
    header('Content-Type: text/plain; charset=utf-8');
    echo 'Library PhpSpreadsheet belum terpasang atau autoload gagal.';
    exit;
}

// Ambil filter
$q = trim($_GET['q'] ?? '');
$ru = trim($_GET['ruangan'] ?? '');
$sr = trim($_GET['sub_ruangan'] ?? '');
$where = [];
if($q!=='') $where[] = "(barang LIKE '%".mysqli_real_escape_string($koneksi,$q)."%' OR merek LIKE '%".mysqli_real_escape_string($koneksi,$q)."%')";
if($ru!=='') $where[] = "ruangan='".mysqli_real_escape_string($koneksi,$ru)."'";
if($sr!=='') $where[] = "sub_ruangan='".mysqli_real_escape_string($koneksi,$sr)."'";
$where_sql = $where ? 'WHERE '.implode(' AND ', $where) : '';

$res = mysqli_query($koneksi, "SELECT * FROM inventaris $where_sql ORDER BY ruangan, sub_ruangan, barang");

// Buat Spreadsheet
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Inventaris');

// Header kolom
$headers = ['No','Barang','Merek','Ukuran','Bahan','Tahun','Pabrik','Rangka','Mesin','Polisi','Nomor','Harga','Jumlah','Nilai','Kondisi','Ruangan','Sub Ruangan','Gambar'];
$sheet->fromArray($headers, NULL, 'A1');

// Tulis data
$rowNum = 2;
$no = 1;
if($res){
    while($r = mysqli_fetch_assoc($res)){
        $sheet->setCellValue("A$rowNum", $no++);
        $sheet->setCellValue("B$rowNum", $r['barang']);
        $sheet->setCellValue("C$rowNum", $r['merek']);
        $sheet->setCellValue("D$rowNum", $r['ukuran']);
        $sheet->setCellValue("E$rowNum", $r['bahan']);
        $sheet->setCellValue("F$rowNum", $r['tahun']);
        $sheet->setCellValue("G$rowNum", $r['pabrik']);
        $sheet->setCellValue("H$rowNum", $r['rangka']);
        $sheet->setCellValue("I$rowNum", $r['mesin']);
        $sheet->setCellValue("J$rowNum", $r['polisi']);
        $sheet->setCellValue("K$rowNum", $r['nomor']);
        $sheet->setCellValue("L$rowNum", (float)$r['harga']);
        $sheet->setCellValue("M$rowNum", (int)$r['jumlah']);
        $sheet->setCellValue("N$rowNum", (float)$r['nilai']);
        $sheet->setCellValue("O$rowNum", $r['kondisi']);
        $sheet->setCellValue("P$rowNum", $r['ruangan']);
        $sheet->setCellValue("Q$rowNum", $r['sub_ruangan']);
        
        // Tambahkan gambar jika ada
        if(!empty($r['gambar']) && file_exists('uploads/'.$r['gambar'])){
            $drawing = new Drawing();
            $drawing->setPath('uploads/'.$r['gambar']);
            $drawing->setCoordinates("R$rowNum");
            $drawing->setHeight(50); // tinggi gambar
            $drawing->setOffsetX(5);
            $drawing->setOffsetY(5);
            $drawing->setWorksheet($sheet);
        }
        
        $rowNum++;
    }
}

// Auto width kolom umum, dan set lebar khusus kolom gambar agar pas satu kolom
foreach(range('A','Q') as $col){
    $sheet->getColumnDimension($col)->setAutoSize(true);
}
// Kolom R untuk gambar, tetapkan lebar tetap kurang-lebih 3cm (Excel col width ~ 0.72cm per unit)
$sheet->getColumnDimension('R')->setWidth(12.5);

// Format numerik agar mudah diedit di Excel (tanpa teks 'Rp ')
$lastRow = $rowNum - 1;
if ($lastRow >= 2) {
    $sheet->getStyle("L2:L$lastRow")->getNumberFormat()->setFormatCode('#,##0.00');
    $sheet->getStyle("N2:N$lastRow")->getNumberFormat()->setFormatCode('#,##0.00');
}

// Kirim ke browser
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="inventaris.xlsx"');
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>
