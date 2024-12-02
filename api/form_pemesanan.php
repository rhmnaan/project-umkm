<?php
include 'layout/header.php';
include 'koneksi.php';

// Pastikan ID produk ada di URL
if (isset($_GET['id_produk'])) {
    $id_produk = (int)$_GET['id_produk']; // Sanitasi input ID produk

    // Ambil data produk berdasarkan ID
    $query = "SELECT produk.*, seller.nama_toko FROM produk 
              JOIN seller ON produk.seller_id = seller.id 
              WHERE produk.id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $id_produk);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    // Jika produk ditemukan
    if ($row = mysqli_fetch_assoc($result)) {
        ?>
        <div class="container my-5">
            <h2 class="text-center text-primary mb-4">Form Pemesanan</h2>
            <form action="proses_pemesanan.php" method="POST">
                <input type="hidden" name="id_produk" value="<?= htmlspecialchars($row['id']) ?>">

                <!-- Informasi Produk -->
                <div class="mb-3">
                    <label for="nama_produk" class="form-label">Nama Produk</label>
                    <input type="text" id="nama_produk" class="form-control" value="<?= htmlspecialchars($row['nama_produk']) ?>" disabled>
                </div>

                <!-- Harga -->
                <div class="mb-3">
                    <label for="harga" class="form-label">Harga</label>
                    <input type="text" id="harga" class="form-control" value="Rp <?= number_format($row['harga'], 0, ',', '.') ?>" disabled>
                </div>

                <!-- Jumlah Pesanan -->
                <div class="mb-3">
                    <label for="jumlah" class="form-label">Jumlah</label>
                    <input type="number" id="jumlah" name="jumlah" class="form-control" min="1" max="<?= htmlspecialchars($row['stok']) ?>" required>
                </div>

                <!-- Alamat Pengiriman -->
                <div class="mb-3" id="alamat_pengiriman_container">
                    <label for="alamat_pengiriman" class="form-label">Alamat Pengiriman</label>
                    <textarea id="alamat_pengiriman" name="alamat_pengiriman" class="form-control" rows="3" required></textarea>
                </div>

                <!-- Metode Pembayaran -->
                <div class="mb-3">
                    <label for="metode_pembayaran" class="form-label">Metode Pembayaran</label>
                    <select id="metode_pembayaran" name="metode_pembayaran" class="form-control" required>
                        <option value="">Pilih Metode Pembayaran</option>
                        <option value="Transfer Bank">Transfer Bank</option>
                        <option value="Kartu Kredit">Kartu Kredit</option>
                        <option value="E-Wallet">E-Wallet</option>
                        <option value="Cash on Delivery (COD)">Cash on Delivery (COD)</option>
                    </select>
                </div>

                <!-- Metode Pengambilan -->
                <div class="mb-3">
                    <label for="metode_pengambilan" class="form-label">Metode Pengambilan</label>
                    <select id="metode_pengambilan" name="metode_pengambilan" class="form-control">
                        <option value="">Pilih Metode Pengambilan</option>
                        <option value="Ambil Ke Toko">Ambil Ke Toko</option>
                        <option value="Diantar">Diantar</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">Pesan Sekarang</button>
            </form>
        </div>

        <script>
            // JavaScript untuk mengatur visibilitas alamat pengiriman
            document.getElementById('metode_pengambilan').addEventListener('change', function() {
                var alamatPengirimanContainer = document.getElementById('alamat_pengiriman_container');
                if (this.value === 'Ambil Ke Toko') {
                    alamatPengirimanContainer.style.display = 'none';
                } else {
                    alamatPengirimanContainer.style.display = 'block';
                }
            });

            // Atur visibilitas awal saat halaman dimuat
            document.addEventListener('DOMContentLoaded', function() {
                var metodePengambilan = document.getElementById('metode_pengambilan').value;
                var alamatPengirimanContainer = document.getElementById('alamat_pengiriman_container');
                if (metodePengambilan === 'Ambil Ke Toko') {
                    alamatPengirimanContainer.style.display = 'none';
                } else {
                    alamatPengirimanContainer.style.display = 'block';
                }
            });
        </script>
        <?php
    } else {
        echo "<p class='text-danger text-center'>Produk tidak ditemukan.</p>";
    }
} else {
    echo "<p class='text-danger text-center'>ID produk tidak ditemukan.</p>";
}

include 'layout/footer.php';
?>
