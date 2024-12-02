<?php
session_start();
include 'koneksi.php'; // Include koneksi ke database

// Ambil data login dari form
$username = $_POST['username'];
$password = $_POST['password'];

// Query untuk memeriksa user
$query = "SELECT * FROM user WHERE username = '$username'";
$result = mysqli_query($conn, $query);

// Jika user ditemukan
if (mysqli_num_rows($result) == 1) {
    $user = mysqli_fetch_assoc($result);
    
    // Verifikasi password dengan password_verify()
    if (password_verify($password, $user['password'])) {
        // Menyimpan data session
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['nama_lengkap'] = $user['nama_lengkap']; // Menyimpan nama lengkap pengguna
        
        // Menampilkan notifikasi login berhasil
        echo "<script>
                alert('Login berhasil! Selamat datang, " . $user['nama_lengkap'] . "');
                window.location.href = 'index.php'; // Arahkan ke halaman utama setelah login berhasil
              </script>";
        exit();
    } else {
        // Password tidak cocok
        echo "<script>
                alert('Login gagal! Username atau password salah.');
                window.location.href = 'login.php'; // Kembali ke halaman login
              </script>";
    }
} else {
    // Username tidak ditemukan
    echo "<script>
            alert('Login gagal! Username tidak ditemukan.');
            window.location.href = 'login.php'; // Kembali ke halaman login
          </script>";
}
?>
