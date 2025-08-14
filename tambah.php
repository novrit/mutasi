<?php
require 'inc/koneksi.php';
require 'inc/auth.php';

$err = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fields = [
        'barang', 'merek', 'ukuran', 'bahan', 'tahun', 
        'pabrik', 'rangka', 'mesin', 'polisi', 'nomor', 'harga', 
        'jumlah', 'nilai', 'kondisi', 'ruangan', 'sub_ruangan'
    ];
    
    $data = [];
    foreach ($fields as $f) {
        $data[$f] = mysqli_real_escape_string($koneksi, trim($_POST[$f] ?? ''));
    }

    if ($data['barang'] == '') $err[] = 'Nama barang wajib diisi';

    // Pengecekan gambar wajib
    $imgname = null;
    if (!empty($_FILES['gambar']['name'])) {
        $ext = strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));
        $allow = ['jpg', 'jpeg', 'png', 'webp'];
        if (!in_array($ext, $allow)) {
            $err[] = 'Format gambar harus jpg/jpeg/png/webp';
        } else {
            // Buat nama unik
            $imgname = 'IMG_' . time() . '_' . mt_rand(1000, 9999) . '.' . $ext;

            // Pastikan folder upload ada
            if (!is_dir('uploads')) {
                mkdir('uploads', 0777, true);
            }

            if (!move_uploaded_file($_FILES['gambar']['tmp_name'], 'uploads/' . $imgname)) {
                $err[] = 'Gagal meng-upload gambar';
            }
        }
    } else {
        $err[] = 'Gambar wajib di-upload';
    }

    if (!$err) {
        $sql = "INSERT INTO inventaris (
                    barang, merek, ukuran, bahan, tahun, pabrik, 
                    rangka, mesin, polisi, nomor, harga, jumlah, nilai, 
                    kondisi, ruangan, sub_ruangan, gambar
                ) VALUES (
                    '{$data['barang']}',
                    '{$data['merek']}',
                    '{$data['ukuran']}',
                    '{$data['bahan']}',
                    '{$data['tahun']}',
                    '{$data['pabrik']}',
                    '{$data['rangka']}',
                    '{$data['mesin']}',
                    '{$data['polisi']}',
                    '{$data['nomor']}',
                    " . ($data['harga'] === '' ? 'NULL' : $data['harga']) . ",
                    " . ($data['jumlah'] === '' ? 'NULL' : $data['jumlah']) . ",
                    " . ($data['nilai'] === '' ? 'NULL' : $data['nilai']) . ",
                    '{$data['kondisi']}',
                    '{$data['ruangan']}',
                    '{$data['sub_ruangan']}',
                    '$imgname'
                )";

        if (mysqli_query($koneksi, $sql)) {
            header('Location: index.php');
            exit;
        } else {
            $err[] = 'DB error: ' . mysqli_error($koneksi);
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset='utf-8'>
    <title>Tambah Data Inventaris</title>
    <link rel='stylesheet' href='css/style.css'>
    <script src='js/combo.js'></script>
    <style>
        .btn {
            flex:1;
            padding:12px;
            font-size:16px;
            text-align:center;
            background:#0b5ed7;
            color:#fff;
            border:none;
            border-radius:8px;
            cursor:pointer;
            display:inline-block;
            text-decoration:none;
        }
        .btn-wrapper {
            grid-column:1/-1;
            display:flex;
            gap:10px;
        }
        input, select {
            width:100%;
            padding:8px;
            border-radius:6px;
            border:1px solid #ccc;
        }
        .field {
            margin-bottom:12px;
        }
    </style>
</head>
<body>
<div class='container'>
    <div class='card'>
        <h2>Tambah Data Inventaris</h2>
        <?php if ($err) : ?>
            <div style="background:#fee2e2;padding:8px;border-radius:6px;margin-bottom:8px;color:#7f1d1d">
                <?= implode('<br>', $err) ?>
            </div>
        <?php endif; ?>

        <form method='post' enctype='multipart/form-data' class='form'>
            <?php
            $fields = [
                'barang' => 'Barang',
                'merek' => 'Merek',
                'ukuran' => 'Ukuran',
                'bahan' => 'Bahan',
                'tahun' => 'Tahun',
                'pabrik' => 'Pabrik',
                'rangka' => 'Rangka',
                'mesin' => 'Mesin',
                'polisi' => 'Polisi',
                'nomor' => 'Nomor'
            ];

            foreach ($fields as $n => $label) {
                $required = $n === 'barang' ? " required" : "";
                echo "<div class='field'><label>$label</label><input name='$n'$required></div>";
            }
            ?>

            <div class='field'>
                <label>Harga</label>
                <input type='number' name='harga' id='harga' oninput="hitungNilai()">
            </div>

            <div class='field'>
                <label>Jumlah</label>
                <input type='number' name='jumlah' id='jumlah' oninput="hitungNilai()">
            </div>

            <div class='field'>
                <label>Nilai</label>
                <input type='number' name='nilai' id='nilai' readonly>
            </div>

            <div class='field'>
                <label>Kondisi</label>
                <select name='kondisi'>
                    <option value=''>-- Pilih --</option>
                    <option value='Baik'>Baik</option>
                    <option value='Baru'>Baru</option>
                    <option value='Rusak Ringan'>Rusak Ringan</option>
                    <option value='Rusak Berat'>Rusak Berat</option>
                </select>
            </div>

            <div class='field'>
                <label>Ruangan</label>
                <select id='ruangan' name='ruangan'>
                    <option value=''>-- Pilih --</option>
                    <option>REKTORAT</option>
                    <option>FKIP</option>
                    <option>FE</option>
                    <option>FST</option>
                </select>
            </div>

            <div class='field'>
                <label>Sub Ruangan</label>
                <select id='sub_ruangan' name='sub_ruangan'>
                    <option value=''>-- Pilih --</option>
                </select>
            </div>

            <div class='field'>
                <label>Gambar</label>
                <input type='file' name='gambar' accept='.jpg,.jpeg,.png,.webp' required>
            </div>

            <div class='btn-wrapper'>
                <button type='submit' class='btn'>Simpan</button>
                <a href='index.php' class='btn'>Batal</a>
            </div>

        </form>
    </div>
</div>

<script>
function hitungNilai() {
    let harga = parseFloat(document.getElementById('harga').value) || 0;
    let jumlah = parseFloat(document.getElementById('jumlah').value) || 0;
    document.getElementById('nilai').value = harga * jumlah;
}
</script>
<?php include 'inc/footer.php'; ?>
</body>
</html>
