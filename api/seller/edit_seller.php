<?php
// Memulai sesi
session_start();

// Koneksi ke database
include('../koneksi.php');

// Mengecek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Mengecek apakah seller sudah terdaftar
$query_check_seller = "SELECT * FROM seller WHERE user_id = '$user_id'";
$result_check = mysqli_query($conn, $query_check_seller);

if (mysqli_num_rows($result_check) == 0) {
    echo "<script>
            alert('Anda belum terdaftar sebagai penjual!');
            window.location.href = 'profile.php';
          </script>";
    exit;
}

// Mendapatkan data seller
$seller_data = mysqli_fetch_assoc($result_check);

// Jika form dikirimkan
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_toko = mysqli_real_escape_string($conn, $_POST['nama_toko']);
    $nomor_telepon = mysqli_real_escape_string($conn, $_POST['nomor_telepon']);
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
    $deskripsi_toko = mysqli_real_escape_string($conn, $_POST['deskripsi_toko']);


    // Update data seller
    $query_update_seller = "UPDATE seller SET nama_toko = '$nama_toko', nomor_telepon = '$nomor_telepon', alamat = '$alamat', deskripsi_toko = '$deskripsi_toko', foto_profil = '$foto_profil' WHERE user_id = '$user_id'";

    if (mysqli_query($conn, $query_update_seller)) {
        echo "<script>
                alert('Data penjual berhasil diperbarui!');
                window.location.href = 'manajemen_toko.php';
              </script>";
    } else {
        echo "<script>alert('Gagal memperbarui data penjual: " . mysqli_error($conn) . "');</script>";
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
    <title>Edit Data Seller</title>
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
        <div class="card-header">Formulir Edit Data Penjual</div>
            <div class="card-body">
                    <form action="edit_seller.php" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="nama_toko">Nama Toko</label>
                        <input type="text" name="nama_toko" id="nama_toko" class="form-control" placeholder="Masukkan nama toko Anda" value="<?php echo $seller_data['nama_toko']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="nomor_telepon">Nomor Telepon</label>
                        <input type="text" name="nomor_telepon" id="nomor_telepon" class="form-control" placeholder="Masukkan nomor telepon" value="<?php echo $seller_data['nomor_telepon']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="alamat">Alamat</label>
                        <textarea name="alamat" id="alamat" class="form-control" rows="3" placeholder="Masukkan alamat lengkap Anda" required><?php echo $seller_data['alamat']; ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="deskripsi_toko">Deskripsi Toko</label>
                        <textarea name="deskripsi_toko" id="deskripsi_toko" class="form-control" rows="4" placeholder="Ceritakan tentang toko Anda" required><?php echo $seller_data['deskripsi_toko']; ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Perbarui Data Penjual</button>
                    <a href="manajemen_toko.php" class="btn btn-secondary btn-block">Kembali</a>
                </form>
        </div>
    </div>
    <footer>&copy; 2024 UMKM Trunojoyo. All Rights Reserved.</footer>
</div>
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
