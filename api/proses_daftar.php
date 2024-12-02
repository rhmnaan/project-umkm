<?php
// Panggil file koneksi
require_once 'koneksi.php';

// Ambil data dari form
$username = $_POST['username'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Enkripsi password
$nama_lengkap = $_POST['nama_lengkap'];
$alamat = $_POST['alamat'];
$telepon = $_POST['telepon'];
$peran = 'user'; // Tetapkan peran default sebagai 'user'
$dibuat_pada = date('Y-m-d H:i:s');
$diperbarui_pada = date('Y-m-d H:i:s');

// Query untuk menyimpan data ke tabel user
$sql = "INSERT INTO user (username, password, nama_lengkap, alamat, telepon, peran, dibuat_pada, diperbarui_pada) 
        VALUES ('$username', '$password', '$nama_lengkap', '$alamat', '$telepon', '$peran', '$dibuat_pada', '$diperbarui_pada')";

if ($conn->query($sql) === TRUE) {
    // Jika pendaftaran berhasil, arahkan ke login.php dengan notifikasi JavaScript
    echo "<script>
            alert('Pendaftaran berhasil! Silakan login.');
            window.location.href = 'login.php';
          </script>";
} else {
    // Jika terjadi error, tampilkan pesan error
    echo "<script>
            alert('Pendaftaran gagal! Silakan coba lagi.');
            window.history.back();
          </script>";
}

// Tutup koneksi
$conn->close();
?>
