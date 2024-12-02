<?php
include 'koneksi.php';
session_start();

// Pastikan pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    die("Harap login terlebih dahulu.");
}

// Periksa apakah ID transaksi tersedia
if (isset($_GET['id_transaksi'])) {
    $id_transaksi = (int)$_GET['id_transaksi'];

    // Ambil data transaksi
    $query_transaksi = "
    SELECT 
        transaksi.order_id AS order_id, 
        transaksi.total_biaya,
        transaksi_detail.jumlah AS jumlah_produk,
        transaksi_detail.subtotal AS total_produk,
        produk.nama_produk,
        produk.harga AS harga_produk,
        seller.nama_toko,
        seller.alamat AS alamat_toko
    FROM transaksi 
    JOIN transaksi_detail ON transaksi.order_id = transaksi_detail.order_id
    JOIN produk ON transaksi_detail.product_id = produk.id
    JOIN seller ON transaksi.seller_id = seller.id
    WHERE transaksi.order_id = ? AND transaksi.user_id = ?
";


    $stmt_transaksi = mysqli_prepare($conn, $query_transaksi);
    mysqli_stmt_bind_param($stmt_transaksi, "ii", $id_transaksi, $_SESSION['user_id']);
    mysqli_stmt_execute($stmt_transaksi);
    $result_transaksi = mysqli_stmt_get_result($stmt_transaksi);

    // Jika data ditemukan
    if ($row = mysqli_fetch_assoc($result_transaksi)) {
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Detail Pemesanan</title>
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/css/bootstrap.min.css">
        </head>
        <body>
        <div class="container my-5">
            <h2 class="text-center text-primary">Detail Pemesanan</h2>

            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Informasi Pemesanan</h4>
                    <p><strong>ID Transaksi:</strong> <?= htmlspecialchars($row['order_id']) ?></p>
                    <p><strong>Total Biaya:</strong> Rp <?= number_format($row['total_biaya'], 0, ',', '.') ?></p>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-body">
                    <h4 class="card-title">Detail Produk</h4>
                    <p><strong>Nama Produk:</strong> <?= htmlspecialchars($row['nama_produk']) ?></p>
                    <p><strong>Jumlah:</strong> <?= htmlspecialchars($row['jumlah_produk']) ?></p>
                    <p><strong>Harga Satuan:</strong> Rp <?= number_format($row['harga_produk'], 0, ',', '.') ?></p>
                    <p><strong>Total Produk:</strong> Rp <?= number_format($row['total_produk'], 0, ',', '.') ?></p>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-body">
                    <h4 class="card-title">Informasi Toko</h4>
                    <p><strong>Nama Toko:</strong> <?= htmlspecialchars($row['nama_toko']) ?></p>
                    <p><strong>Alamat Toko:</strong> <?= htmlspecialchars($row['alamat_toko']) ?></p>
                </div>
            </div>

            <a href="index.php" class="btn btn-primary mt-4">Kembali ke Halaman Utama</a>
        </div>
        </body>
        </html>
        <?php
    } else {
        echo "<p class='text-danger text-center'>Transaksi tidak ditemukan atau Anda tidak memiliki akses ke transaksi ini.</p>";
    }
} else {
    echo "<p class='text-danger text-center'>ID transaksi tidak ditemukan.</p>";
}
?>
