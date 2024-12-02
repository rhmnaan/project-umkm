<?php
include 'layout/header.php';

// Koneksi ke database
$query = "SELECT produk.*, seller.nama_toko, seller.alamat, seller.nomor_telepon 
          FROM produk
          JOIN seller ON produk.seller_id = seller.id"; // Memperbaiki JOIN untuk menggunakan kolom yang benar
$result = mysqli_query($conn, $query);
?>

<div class="container my-5">
    <h2 class="text-center mb-4">Semua Produk</h2>
    <div class="row">
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
        <div class="col-md-4">
            <div class="card shadow-sm border-light rounded mb-4">
                <!-- Cek jika gambar ada, jika tidak tampilkan gambar default -->
                <img src="<?= file_exists('uploads/' . $row['gambar']) ? 'uploads/' . htmlspecialchars($row['gambar']) : 'https://via.placeholder.com/300x200' ?>" class="card-img-top img-fluid" alt="<?= htmlspecialchars($row['nama_produk']) ?>">
                <div class="card-body">
                    <h5 class="card-title"><?= htmlspecialchars($row['nama_produk']) ?></h5>
                    <p class="card-text">Harga: <span class="text-success">Rp <?= number_format($row['harga'], 0, ',', '.') ?></span></p>
                    <a href="detail_produk.php?id=<?= $row['id'] ?>" class="btn btn-primary w-100">Lihat Detail</a>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
</div>

<?php include 'layout/footer.php'; ?>

