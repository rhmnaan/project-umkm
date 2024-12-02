<?php
session_start();
include 'koneksi.php'; // Include koneksi ke database

// Menangani proses login ketika form disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
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
            
            // Menampilkan notifikasi login berhasil menggunakan SweetAlert
            echo "<script>
                    document.addEventListener('DOMContentLoaded', function() {
                        Swal.fire({
                            icon: 'success',
                            title: 'Login Berhasil!',
                            text: 'Selamat datang, " . $user['nama_lengkap'] . "!',
                            showConfirmButton: false,
                            timer: 1500
                        }).then(function() {
                            window.location.href = 'index.php'; // Arahkan ke halaman utama setelah login berhasil
                        });
                    });
                  </script>";
        } else {
            // Password tidak cocok
            echo "<script>
                    document.addEventListener('DOMContentLoaded', function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Login Gagal!',
                            text: 'Username atau password salah.',
                            showConfirmButton: true
                        }).then(function() {
                            window.location.href = 'login.php'; // Tetap di halaman login jika login gagal
                        });
                    });
                  </script>";
        }
    } else {
        // Username tidak ditemukan
        echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Login Gagal!',
                        text: 'Username tidak ditemukan.',
                        showConfirmButton: true
                    }).then(function() {
                        window.location.href = 'login.php'; // Tetap di halaman login jika username tidak ditemukan
                    });
                });
              </script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - UMKM Universitas Trunojoyo Madura</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="layout/style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            background-color: #f8f9fa;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .card {
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
    <div class="card" style="width: 24rem;">
        <div class="card-body">
            <h3 class="card-title text-center">Login</h3>
            <form action="" method="POST">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" class="form-control" id="username" name="username" placeholder="Masukkan username" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Masukkan password" required>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Login</button>
            </form>
            <p class="text-center mt-3">
                Belum punya akun? <a href="daftar.php">Daftar</a>
            </p>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.4.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
