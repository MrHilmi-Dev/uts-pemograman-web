<?php
require_once __DIR__ . "/Koneksi.php";

// 1. Mengambil data dari form edit
$nim = $_POST['nim'] ?? '';
$nama_lengkap = trim($_POST['nama_lengkap'] ?? '');
$nilai_akhir_input = $_POST['nilai_akhir'] ?? '';

// 2. Memeriksa validitas input
if (
    $nim === '' ||
    $nama_lengkap === '' ||
    $nilai_akhir_input === '' ||
    !is_numeric($nilai_akhir_input) ||
    (float)$nilai_akhir_input < 0 ||
    (float)$nilai_akhir_input > 100
) {
    die("Data tidak valid. Periksa kembali input Anda.");
}

$nilai_akhir = (float)$nilai_akhir_input;
$nilai_huruf = '';
$predikat = '';

// 3. Menghitung ulang Nilai Huruf dan Predikat agar selalu update jika nilainya diubah
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

// 4. Proses UPDATE ke Database berdasarkan NIM
$sql = "UPDATE nilaimhs SET 
        nama_lengkap = ?, 
        nilai_akhir = ?, 
        nilai_huruf = ?, 
        predikat = ? 
        WHERE nim = ?";

$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {
    die("Query gagal disiapkan: " . mysqli_error($conn));
}

// Bind param: 'sdsss' berarti String, Double, String, String, String
mysqli_stmt_bind_param(
    $stmt,
    "sdsss",
    $nama_lengkap,
    $nilai_akhir,
    $nilai_huruf,
    $predikat,
    $nim
);

if (mysqli_stmt_execute($stmt)) {
    mysqli_stmt_close($stmt);
    // Kembalikan ke halaman utama jika berhasil (sesuaikan users.php dengan nama file Anda)
    header("Location: users.php?status=edit_sukses");
    exit;
} else {
    echo "Gagal mengupdate data: " . mysqli_stmt_error($stmt);
}

mysqli_stmt_close($stmt);
?>