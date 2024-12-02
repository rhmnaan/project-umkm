<?php
// Memulai sesi
session_start();

// Koneksi ke database
include('koneksi.php');

// Mengecek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Mengecek apakah user sudah menjadi penjual
$query_check_seller = "SELECT * FROM seller WHERE user_id = '$user_id'";
$result_check = mysqli_query($conn, $query_check_seller);

if (mysqli_num_rows($result_check) > 0) {
    echo "<script>
            alert('Anda sudah terdaftar sebagai penjual!');
            window.location.href = 'profile.php';
          </script>";
    exit;
}

// Jika form dikirimkan
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_toko = mysqli_real_escape_string($conn, $_POST['nama_toko']);
    $nomor_telepon = mysqli_real_escape_string($conn, $_POST['nomor_telepon']);
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
    $deskripsi_toko = mysqli_real_escape_string($conn, $_POST['deskripsi_toko']);
    
    // Menambahkan data ke tabel seller tanpa foto profil
    $query_insert_seller = "INSERT INTO seller (user_id, nama_toko, nomor_telepon, alamat, deskripsi_toko, tanggal_daftar)
                            VALUES ('$user_id', '$nama_toko', '$nomor_telepon', '$alamat', '$deskripsi_toko', NOW())";

    if (mysqli_query($conn, $query_insert_seller)) {
        echo "<script>
                alert('Pendaftaran sebagai penjual berhasil!');
                window.location.href = 'profile.php';
              </script>";
    } else {
        echo "<script>alert('Gagal mendaftar sebagai penjual: " . mysqli_error($conn) . "');</script>";
    }
}

// Menutup koneksi
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Menjadi Penjual</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f0f8ff;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .container {
            max-width: 600px;
            margin: 50px auto;
        }

        .card {
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border: none;
        }

        .card-header {
            background-color: #007bff;
            color: white;
            text-align: center;
            font-size: 1.5rem;
            font-weight: bold;
            border-radius: 10px 10px 0 0;
        }

        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        }

        .btn-primary {
            background-color: #007bff;
            border: none;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        .btn-secondary {
            background-color: #6c757d;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
        }

        footer {
            margin-top: 30px;
            text-align: center;
            color: #6c757d;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="card">
        <div class="card-header">Formulir Pendaftaran Penjual</div>
            <div class="card-body">
                    <form action="daftar_penjual.php" method="POST">
                    <div class="form-group">
                        <label for="nama_toko">Nama Toko</label>
                        <input type="text" name="nama_toko" id="nama_toko" class="form-control" placeholder="Masukkan nama toko Anda" required>
                    </div>
                    <div class="form-group">
                        <label for="nomor_telepon">Nomor Telepon</label>
                        <input type="text" name="nomor_telepon" id="nomor_telepon" class="form-control" placeholder="Masukkan nomor telepon" required>
                    </div>
                    <div class="form-group">
                        <label for="alamat">Alamat</label>
                        <textarea name="alamat" id="alamat" class="form-control" rows="3" placeholder="Masukkan alamat lengkap Anda" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="deskripsi_toko">Deskripsi Toko</label>
                        <textarea name="deskripsi_toko" id="deskripsi_toko" class="form-control" rows="4" placeholder="Ceritakan tentang toko Anda" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Daftar Menjadi Penjual</button>
                    <a href="profile.php" class="btn btn-secondary btn-block">Kembali</a>
                </form>
        </div>
    </div>
    <footer>&copy; 2024 UMKM Trunojoyo. All Rights Reserved.</footer>
</div>
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
