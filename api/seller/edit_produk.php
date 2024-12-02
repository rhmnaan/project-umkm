<?php
include 'layout/header.php'; 

// Periksa apakah user sudah login dan memiliki akses sebagai seller
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include('koneksi.php');

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

// Ambil ID produk yang akan diedit
if (isset($_GET['id'])) {
    $produk_id = $_GET['id'];

    // Ambil data produk yang akan diedit
    $query_produk = "SELECT * FROM produk WHERE id = '$produk_id' AND seller_id = '{$seller_info['id']}'";
    $result_produk = mysqli_query($conn, $query_produk);
    $produk = mysqli_fetch_assoc($result_produk);

    if (!$produk) {
        echo "<script>
                alert('Produk tidak ditemukan.');
                window.location.href = 'manajemen_toko.php';
              </script>";
        exit;
    }
} else {
    echo "<script>
            alert('ID produk tidak ditemukan.');
            window.location.href = 'manajemen_toko.php';
          </script>";
    exit;
}

// Proses update produk
if (isset($_POST['submit'])) {
    $nama_produk = mysqli_real_escape_string($conn, $_POST['nama_produk']);
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $harga = mysqli_real_escape_string($conn, $_POST['harga']);
    $stok = mysqli_real_escape_string($conn, $_POST['stok']);
    $gambar = $_FILES['gambar']['name'];
    $gambar_tmp = $_FILES['gambar']['tmp_name'];

    // Jika gambar baru diupload
    if ($gambar) {
        $gambar_path = 'uploads/' . $gambar;
        move_uploaded_file($gambar_tmp, $gambar_path);
    } else {
        // Jika tidak ada gambar baru, gunakan gambar lama
        $gambar_path = $produk['gambar'];
    }

    // Update data produk
    $query_update = "UPDATE produk SET 
                     nama_produk = '$nama_produk', 
                     deskripsi = '$deskripsi', 
                     harga = '$harga', 
                     stok = '$stok', 
                     gambar = '$gambar_path' 
                     WHERE id = '$produk_id' AND seller_id = '{$seller_info['id']}'";

    if (mysqli_query($conn, $query_update)) {
        echo "<script>
                alert('Produk berhasil diperbarui.');
                window.location.href = 'manajemen_toko.php';
              </script>";
    } else {
        echo "<script>
                alert('Terjadi kesalahan saat memperbarui produk.');
              </script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Produk</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2>Edit Produk</h2>
    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="nama_produk">Nama Produk</label>
            <input type="text" class="form-control" id="nama_produk" name="nama_produk" value="<?php echo $produk['nama_produk']; ?>" required>
        </div>
        <div class="form-group">
            <label for="deskripsi">Deskripsi</label>
            <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3" required><?php echo $produk['deskripsi']; ?></textarea>
        </div>
        <div class="form-group">
            <label for="harga">Harga (Rp)</label>
            <input type="number" class="form-control" id="harga" name="harga" value="<?php echo $produk['harga']; ?>" required>
        </div>
        <div class="form-group">
            <label for="stok">Stok</label>
            <input type="number" class="form-control" id="stok" name="stok" value="<?php echo $produk['stok']; ?>" required>
        </div>
        <div class="form-group">
            <label for="gambar">Gambar</label>
            <input type="file" class="form-control-file" id="gambar" name="gambar">
            <small class="form-text text-muted">Jika tidak ingin mengganti gambar, biarkan kosong.</small>
            <br>
            <img src="<?php echo $produk['gambar']; ?>" alt="Gambar Produk" class="img-fluid" style="max-width: 200px;">
        </div>
        <button type="submit" name="submit" class="btn btn-primary">Perbarui Produk</button>
        <a href="manajemen_toko.php" class="btn btn-secondary">Batal</a>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
