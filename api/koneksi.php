<?php
// Database connection
$conn = mysqli_connect('localhost', 'root', '', 'umkm');

// Cek koneksi
if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

?>
