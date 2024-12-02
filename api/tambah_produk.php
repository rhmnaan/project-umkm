<?php
include 'layout/header.php';
include('koneksi.php');

// Periksa apakah user sudah login dan memiliki akses sebagai seller
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Periksa apakah user sudah menjadi seller
$query_seller = "SELECT * FROM seller WHERE user_id = '$user_id'";
$result_seller = mysqli_query($conn, $query_seller);
$seller_info = mysqli_fetch_assoc($result_seller);

if (!$seller_info) {
    echo "<script>
            alert('Anda belum terdaftar sebagai penjual. Silakan daftar terlebih dahulu.');
            window.location.href = 'daftar_penjual.php';
          </script>";
    exit;
}

// Proses tambah produk
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_produk = mysqli_real_escape_string($conn, $_POST['nama_produk']);
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $harga = $_POST['harga'];
    $stok = $_POST['stok'];

    // Proses upload gambar produk
    $gambar = "";
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
        $gambar = 'uploads/' . basename($_FILES['gambar']['name']);
        move_uploaded_file($_FILES['gambar']['tmp_name'], $gambar);
    }

    // Insert produk baru ke database
    $query = "INSERT INTO produk (nama_produk, deskripsi, harga, stok, gambar, seller_id) 
              VALUES ('$nama_produk', '$deskripsi', '$harga', '$stok', '$gambar', '{$seller_info['id']}')";
    
    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Produk berhasil ditambahkan.'); window.location.href = 'manajemen_toko.php';</script>";
    } else {
        echo "<script>alert('Gagal menambahkan produk.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Produk</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2>Tambah Produk Baru</h2>
    <form action="tambah_produk.php" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="nama_produk">Nama Produk</label>
            <input type="text" class="form-control" id="nama_produk" name="nama_produk" required>
        </div>
        <div class="form-group">
            <label for="deskripsi">Deskripsi</label>
            <textarea class="form-control" id="deskripsi" name="deskripsi" required></textarea>
        </div>
        <div class="form-group">
            <label for="harga">Harga (Rp)</label>
            <input type="number" class="form-control" id="harga" name="harga" required>
        </div>
        <div class="form-group">
            <label for="stok">Stok</label>
            <input type="number" class="form-control" id="stok" name="stok" required>
        </div>
        <div class="form-group">
            <label for="gambar">Gambar Produk</label>
            <input type="file" class="form-control" id="gambar" name="gambar" accept="image/*">
        </div>
        <button type="submit" class="btn btn-primary">Tambah Produk</button>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
