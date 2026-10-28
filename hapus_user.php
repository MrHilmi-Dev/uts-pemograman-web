<?php
require_once __DIR__ . "/Koneksi.php";

// Mengambil NIM dari form modal (melalui input type="hidden")
$nim = $_POST['nim'] ?? '';

// Memeriksa apakah NIM kosong
if ($nim === '') {
    die("Data tidak valid. NIM tidak ditemukan.");
}

// Menjalankan perintah DELETE ke database
$sql = "DELETE FROM nilaimhs WHERE nim = ?";
$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {
    die("Query gagal disiapkan: " . mysqli_error($conn));
}

// Bind param: 's' berarti String (sesuaikan jika di database menggunakan tipe INT)
mysqli_stmt_bind_param($stmt, "s", $nim);

if (mysqli_stmt_execute($stmt)) {
    mysqli_stmt_close($stmt);
    // Jika berhasil, kembalikan ke halaman tabel
    header("Location: users.php?status=hapus_sukses");
    exit;
} else {
    echo "Gagal menghapus data: " . mysqli_stmt_error($stmt);
}

mysqli_stmt_close($stmt);
?>