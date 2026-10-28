<?php
require_once __DIR__ . "/Koneksi.php";

// Mengambil data dari form
$nim = $_POST['nim'] ?? '';
$nama_lengkap = trim($_POST['nama_lengkap'] ?? '');
$nilai_akhir_input = $_POST['nilai_akhir'] ?? '';

// Memeriksa input
if (
    $nim === '' ||
    $nama_lengkap === '' ||
    $nilai_akhir_input === '' ||
    !is_numeric($nilai_akhir_input) ||
    (float)$nilai_akhir_input < 0 ||
    (float)$nilai_akhir_input > 100
) {
    die("Data tidak valid. Periksa kembali input kamu.");
}

$nilai_akhir = (float)$nilai_akhir_input;
$nilai_huruf = '';
$predikat = '';

// Menentukan Nilai Huruf dan Predikat secara otomatis
if ($nilai_akhir >= 85) {
    $nilai_huruf = 'A';
    $predikat = 'Sangat Memuaskan';
} elseif ($nilai_akhir >= 70) {
    $nilai_huruf = 'B';
    $predikat = 'Memuaskan';
} elseif ($nilai_akhir >= 55) {
    $nilai_huruf = 'C';
    $predikat = 'Cukup Memuaskan';
} elseif ($nilai_akhir >= 40) {
    $nilai_huruf = 'D';
    $predikat = 'Kurang Memuaskan';
} else {
    $nilai_huruf = 'E';
    $predikat = 'Tidak Lulus';
}

// Proses Upload Foto (jika ada file yang diunggah)
if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
    // Diubah ke strtolower agar format selalu huruf kecil (.jpg / .png)
    $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
    $nama_foto_baru = $nim . "." . $ext; 
    
    $target_dir = __DIR__ . "/assets/img/";

    // Buat folder jika belum ada
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    // Pindahkan file ke folder assets/img/
    move_uploaded_file($_FILES['foto']['tmp_name'], $target_dir . $nama_foto_baru);
}

// Menyimpan data ke database
$sql = "INSERT INTO nilaimhs 
        (nim, nama_lengkap, nilai_akhir, nilai_huruf, predikat) 
        VALUES (?, ?, ?, ?, ?)";

$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {
    die("Query gagal disiapkan: " . mysqli_error($conn));
}

// Bind param: 'ssdss'
mysqli_stmt_bind_param(
    $stmt,
    "ssdss",
    $nim,
    $nama_lengkap,
    $nilai_akhir,
    $nilai_huruf,
    $predikat
);

if (mysqli_stmt_execute($stmt)) {
    mysqli_stmt_close($stmt);
    header("Location: users.php?status=sukses");
    exit;
} else {
    echo "Gagal menyimpan data: " . mysqli_stmt_error($stmt);
}

mysqli_stmt_close($stmt);
?>