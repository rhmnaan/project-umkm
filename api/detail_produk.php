<?php
include 'layout/header.php';

// Pastikan ID produk ada di URL
if (isset($_GET['id'])) {
    $id_produk = $_GET['id'];

    // Koneksi ke database untuk mengambil data produk dan seller
    $query = "SELECT produk.*, seller.nama_toko, seller.alamat, seller.nomor_telepon 
              FROM produk
              JOIN seller ON produk.seller_id = seller.id
              WHERE produk.id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $id_produk);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    // Jika produk ditemukan
    if ($row = mysqli_fetch_assoc($result)):
?>
<div class="container my-5">
    <h2 class="text-center text-primary mb-4"><?= htmlspecialchars($row['nama_produk']) ?></h2>
    <div class="row">
        <!-- Gambar Produk -->
        <div class="col-md-6 mb-4">
            <div class="product-image text-center">
                <img src="<?= file_exists('uploads/' . $row['gambar']) ? 'uploads/' . htmlspecialchars($row['gambar']) : 'https://via.placeholder.com/500x400' ?>" 
                     class="img-fluid rounded shadow-sm" alt="<?= htmlspecialchars($row['nama_produk']) ?>" style="max-height: 400px; object-fit: cover;">
            </div>
        </div>

        <!-- Kolom Informasi Produk dan Seller -->
        <div class="col-md-6 mb-4">
            <div class="row">
                <!-- Card Detail Produk -->
                <div class="col-12 mb-4">
                    <div class="card shadow-sm p-4 bg-white rounded">
                        <h4 class="card-title text-primary">Detail Produk</h4>
                        <p><strong>Deskripsi:</strong> <?= nl2br(htmlspecialchars($row['deskripsi'])) ?></p>
                        <p><strong>Harga:</strong> Rp <?= number_format($row['harga'], 0, ',', '.') ?></p>
                        <p><strong>Stok:</strong> <?= $row['stok'] ?> item</p>
                    </div>
                </div>

                <!-- Card Informasi Seller -->
                <div class="col-12">
                    <div class="card shadow-sm p-4 bg-white rounded">
                        <h4 class="card-title text-primary">Informasi Seller</h4>
                        <p><strong>Toko:</strong> <?= htmlspecialchars($row['nama_toko']) ?></p>
                        <p><strong>Alamat:</strong> <?= htmlspecialchars($row['alamat']) ?></p>
                        <p><strong>Telepon:</strong> <?= htmlspecialchars($row['nomor_telepon']) ?></p>
                    </div>
                    <!-- Tombol Beli Sekarang -->
                    <a href="form_pemesanan.php?id_produk=<?= $id_produk ?>" class="btn btn-success btn-lg btn-block mt-2">Beli Sekarang</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
    else:
        echo "<p class='text-danger text-center'>Produk tidak ditemukan.</p>";
    endif;
} else {
    echo "<p class='text-danger text-center'>ID produk tidak ditemukan.</p>";
}

include 'layout/footer.php';
?>
