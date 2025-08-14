<?php
require 'vendor/autoload.php';
require 'inc/koneksi.php';
require 'inc/auth.php';

use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Shared\Converter;
use PhpOffice\PhpWord\SimpleType\JcTable;

// Cek library PhpWord
if (!class_exists('PhpOffice\\PhpWord\\PhpWord')) {
    http_response_code(500);
    header('Content-Type: text/plain; charset=utf-8');
    echo 'Library PhpWord belum terpasang. Jalankan: composer require phpoffice/phpword';
    exit;
}

// Ambil filter dari GET
$q = trim($_GET['q'] ?? '');
$ru = trim($_GET['ruangan'] ?? '');
$sr = trim($_GET['sub_ruangan'] ?? '');
$pj = trim($_GET['pj'] ?? '');

// Bangun kondisi WHERE
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

// Ambil data
$res = mysqli_query($koneksi, "SELECT * FROM inventaris $where_sql ORDER BY ruangan, sub_ruangan, barang");

// Judul laporan
$fakultas = $ru !== '' ? 'DI FAKULTAS ' . strtoupper($ru) : 'LAPORAN KESELURUHAN';

// Buat dokumen Word
$phpWord = new PhpWord();
$section = $phpWord->addSection([
    'orientation' => 'landscape',
    'paperSize' => 'A4',
    'marginTop' => Converter::inchToTwip(0.5),
    'marginBottom' => Converter::inchToTwip(0.5),
    'marginLeft' => Converter::inchToTwip(0.5),
    'marginRight' => Converter::inchToTwip(0.5),
]);

// Kop dokumen
$headerTable = $section->addTable([
    'borderSize' => 0,
    'alignment' => JcTable::CENTER,
    'cellMargin' => 0
]);
$headerTable->addRow();
$cellLogo = $headerTable->addCell(Converter::cmToTwip(3));
if (file_exists('assets/logo.png')) {
    $cellLogo->addImage('assets/logo.png', ['width' => 70]);
}
$cellTitle = $headerTable->addCell();
$cellTitle->addText('UNIVERSITAS NIAS', ['bold' => true, 'size' => 16], ['alignment' => 'center']);
$cellTitle->addText('INVENTARISASI GEDUNG/BARANG/PERALATAN DAN MESIN', ['size' => 14], ['alignment' => 'center']);
$cellTitle->addText($fakultas, ['size' => 12], ['alignment' => 'center']);
$section->addLine(['weight' => 2, 'color' => '000000']);
$section->addText('Tanggal: ' . date('d/m/Y'), ['size' => 10], ['alignment' => 'right']);

// Gaya tabel
$tableStyleName = 'InventoryTable';
$phpWord->addTableStyle($tableStyleName, [
    'borderSize' => 6,
    'borderColor' => '000000',
    'cellMargin' => 50,
    'alignment' => JcTable::CENTER
], [
    'bgColor' => 'EEF2FF'
]);

$table = $section->addTable($tableStyleName);

// Header tabel
$headers = ['No','Barang','Merek','Ukuran','Bahan','Tahun','Pabrik','Rangka','Mesin','Polisi','Nomor','Harga','Jumlah','Nilai','Kondisi','Ruangan','Sub Ruangan','Gambar'];
$table->addRow(Converter::cmToTwip(1));
foreach ($headers as $h) {
    $table->addCell(null, ['valign' => 'center'])->addText($h, ['bold' => true], ['alignment' => 'center']);
}

// Baris data
$rowHeight = Converter::cmToTwip(3);
$imgMaxPx = 100; // Tinggi gambar dalam pixel (setara ~2.6 cm)
$no = 1;

if ($res && mysqli_num_rows($res) > 0) {
    while ($r = mysqli_fetch_assoc($res)) {
        $table->addRow($rowHeight);
        $table->addCell()->addText((string)$no++, null, ['alignment' => 'center']);
        $table->addCell()->addText($r['barang'] ?? '');
        $table->addCell()->addText($r['merek'] ?? '');
        $table->addCell()->addText($r['ukuran'] ?? '');
        $table->addCell()->addText($r['bahan'] ?? '');
        $table->addCell()->addText($r['tahun'] ?? '');
        $table->addCell()->addText($r['pabrik'] ?? '');
        $table->addCell()->addText($r['rangka'] ?? '');
        $table->addCell()->addText($r['mesin'] ?? '');
        $table->addCell()->addText($r['polisi'] ?? '');
        $table->addCell()->addText($r['nomor'] ?? '');
        $table->addCell()->addText('Rp ' . number_format((float)($r['harga'] ?? 0), 2, ',', '.'));
        $table->addCell()->addText((string)(int)($r['jumlah'] ?? 0), null, ['alignment' => 'center']);
        $table->addCell()->addText('Rp ' . number_format((float)($r['nilai'] ?? 0), 2, ',', '.'));
        $table->addCell()->addText($r['kondisi'] ?? '');
        $table->addCell()->addText($r['ruangan'] ?? '');
        $table->addCell()->addText($r['sub_ruangan'] ?? '');
        $imgCell = $table->addCell(Converter::cmToTwip(3));
        if (!empty($r['gambar']) && file_exists('uploads/' . $r['gambar'])) {
            $imgCell->addImage('uploads/' . $r['gambar'], [
                'height' => $imgMaxPx,
                'wrappingStyle' => 'inline'
            ]);
        } else {
            $imgCell->addText('(Tidak ada gambar)', ['italic' => true, 'size' => 9], ['alignment' => 'center']);
        }
    }
}

// Tanda tangan
$section->addTextBreak(1);
$sign = $section->addTable(['borderSize' => 0, 'alignment' => JcTable::CENTER]);
$sign->addRow();
$left = $sign->addCell(Converter::cmToTwip(8));
$right = $sign->addCell(Converter::cmToTwip(8));
$left->addText('Mengetahui', null, ['alignment' => 'center']);
$left->addTextBreak(3);
$left->addText(str_repeat('_', 30), null, ['alignment' => 'center']);
$right->addText('Penanggung Jawab', null, ['alignment' => 'center']);
$right->addTextBreak(3);
$right->addText($pj !== '' ? $pj : str_repeat('_', 30), null, ['alignment' => 'center']);

// Output ke browser
header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
header('Content-Disposition: attachment; filename="inventaris.docx"');
$writer = IOFactory::createWriter($phpWord, 'Word2007');
$writer->save('php://output');
exit;
