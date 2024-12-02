<?php
include '../layout/header.php'; 

// Periksa apakah user sudah login dan memiliki akses sebagai seller
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}


include('../koneksi.php');

$user_id = $_SESSION['user_id'];

// Periksa apakah user sudah menjadi seller
$query_seller = "SELECT * FROM seller WHERE user_id = '$user_id'";
$result_seller = mysqli_query($conn, $query_seller);
$seller_info = mysqli_fetch_assoc($result_seller);

if (!$seller_info) {
    echo "<script>
            alert('Anda belum terdaftar sebagai penjual. Silakan daftar terlebih dahulu.');
            window.location.href = '../daftar_penjual.php';
          </script>";
    exit;
}

// Ambil daftar produk milik seller dengan fitur pencarian
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$query_produk = "SELECT * FROM produk WHERE seller_id = '{$seller_info['id']}' AND nama_produk LIKE '%$search%'";
$result_produk = mysqli_query($conn, $query_produk);


?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Toko</title>
    <link rel="stylesheet" href="../layout/style.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .product-image {
            width: 100%;
            height: auto;
            max-width: 120px;
            max-height: 120px;
            object-fit: cover;
            display: block;
            margin: 0 auto;
            border-radius: 8px;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0">Manajemen Toko</h2>
        <a href="tambah_produk.php" class="btn btn-primary">Tambah Produk</a>
    </div>

    <!-- Informasi Seller -->
    <div class="card mb-4">
    <div class="card-header">Informasi Toko</div>
    <div class="card-body">
        <p><strong>Nama Toko:</strong> <?php echo $seller_info['nama_toko']; ?></p>
        <p><strong>Nomor Telepon:</strong> <?php echo $seller_info['nomor_telepon']; ?></p>
        <p><strong>Alamat:</strong> <?php echo $seller_info['alamat']; ?></p>
        <p><strong>Deskripsi:</strong> <?php echo $seller_info['deskripsi_toko']; ?></p>
        <p><strong>Tanggal Bergabung:</strong> <?php echo $seller_info['tanggal_daftar']; ?></p>
        <a href="edit_seller.php" class="btn btn-warning btn-sm">Edit Toko</a>
    </div>
</div>


    <!-- Form Pencarian Produk -->
    <form method="GET" class="form-inline mb-3">
        <input type="text" name="search" class="form-control" placeholder="Cari produk..." value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>">
        <button type="submit" class="btn btn-info ml-2">Cari</button>
    </form>

    <!-- Daftar Produk -->
    <div class="card">
        <div class="card-header">Daftar Produk Anda</div>
        <div class="card-body">
            <?php if (mysqli_num_rows($result_produk) > 0): ?>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Nama Produk</th>
                            <th>Deskripsi</th>
                            <th>Harga</th>
                            <th>Stok</th>
                            <th>Gambar</th>
                            <th>Opsi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($produk = mysqli_fetch_assoc($result_produk)): ?>
                            <tr>
                                <td><?php echo $produk['nama_produk']; ?></td>
                                <td><?php echo $produk['deskripsi']; ?></td>
                                <td>Rp<?php echo number_format($produk['harga'], 0, ',', '.'); ?></td>
                                <td><?php echo $produk['stok']; ?></td>
                                <td>
                                    <img src="<?php echo $produk['gambar']; ?>" alt="<?php echo $produk['nama_produk']; ?>" class="product-image">
                                </td>
                                <td>
                                    <a href="edit_produk.php?id=<?php echo $produk['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                                    <a href="hapus_produk.php?id=<?php echo $produk['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus produk ini?')">Hapus</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>Anda belum memiliki produk.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
