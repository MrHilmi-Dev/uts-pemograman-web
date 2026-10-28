
<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "db5d";

// Membuat koneksi
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Memeriksa koneksi
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// echo "Connected successfully";
// echo "<br>";
?>
