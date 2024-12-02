<?php
include 'layout/header.php';
include 'koneksi.php'; // Pastikan koneksi ke database sudah ada

// Periksa apakah ada produk_id di URL
if (isset($_GET['produk_id'])) {
    $produk_id = $_GET['produk_id'];

    // Koneksi ke database dan ambil data produk
    $query = "SELECT * FROM produk WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $produk_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    // Jika produk ditemukan
    if ($row = mysqli_fetch_assoc($result)):

        // Periksa apakah form pemesanan disubmit
// Periksa apakah form pemesanan disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_pembeli = mysqli_real_escape_string($conn, $_POST['nama_pembeli']);
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
    $nomor_telepon = mysqli_real_escape_string($conn, $_POST['nomor_telepon']);
    $jumlah = mysqli_real_escape_string($conn, $_POST['jumlah']);
    $metode_pengambilan = mysqli_real_escape_string($conn, $_POST['metode_pengambilan']);
    $metode_pembayaran = mysqli_real_escape_string($conn, $_POST['metode_pembayaran']);

    // Hitung total harga
    $harga = $row['harga']; // Harga produk
    $total_harga = $harga * $jumlah; // Total harga = harga per unit * jumlah

    // Insert data ke tabel pesanan
    $query_insert = "INSERT INTO pesanan2 (produk_id, nama_pembeli, alamat, nomor_telepon, jumlah, metode_pengambilan, metode_pembayaran, total_harga) 
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt_insert = mysqli_prepare($conn, $query_insert);
    mysqli_stmt_bind_param($stmt_insert, "isssssss", $produk_id, $nama_pembeli, $alamat, $nomor_telepon, $jumlah, $metode_pengambilan, $metode_pembayaran, $total_harga);

    if (mysqli_stmt_execute($stmt_insert)) {
        echo "<div class='alert alert-success'>Pesanan berhasil dibuat!</div>";
    } else {
        echo "<div class='alert alert-danger'>Terjadi kesalahan: " . mysqli_error($conn) . "</div>";
    }
}

?>

<!-- Form Pemesanan -->
<div class="container my-5">
    <h2 class="text-center mb-4">Form Pemesanan</h2>
    <form method="POST">
        <div class="mb-3">
            <label for="nama_pembeli" class="form-label">Nama Pembeli</label>
            <input type="text" class="form-control" id="nama_pembeli" name="nama_pembeli" required>
        </div>
        <div class="mb-3">
            <label for="alamat" class="form-label">Alamat</label>
            <input type="text" class="form-control" id="alamat" name="alamat" required>
        </div>
        <div class="mb-3">
            <label for="nomor_telepon" class="form-label">Nomor Telepon</label>
            <input type="text" class="form-control" id="nomor_telepon" name="nomor_telepon" required>
        </div>
        <div class="mb-3">
            <label for="jumlah" class="form-label">Jumlah</label>
            <input type="number" class="form-control" id="jumlah" name="jumlah" required>
        </div>
        <div class="mb-3">
            <label for="metode_pengambilan" class="form-label">Metode Pengambilan</label>
            <select class="form-select" id="metode_pengambilan" name="metode_pengambilan" required>
                <option value="Ambil Sendiri">Ambil Ke Toko</option>
                <option value="COD">COD</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="metode_pembayaran" class="form-label">Metode Pembayaran</label>
            <select class="form-select" id="metode_pembayaran" name="metode_pembayaran" required>
                <option value="E-Wallet">E-Wallet</option>
                <option value="Bank Transfer">Bank Transfer</option>
                <option value="QRIS">QRIS</option>
                <option value="CASH">CASH</option>
            </select>
        </div>

        <h4>Detail Produk</h4>
        <p><strong>Nama Produk:</strong> <?= htmlspecialchars($row['nama_produk']) ?></p>
        <p><strong>Harga:</strong> Rp <?= number_format($row['harga'], 0, ',', '.') ?></p>
        <p><strong>Stok:</strong> <?= $row['stok'] ?> item</p>

        <button type="submit" class="btn btn-primary">Kirim Pesanan</button>
    </form>
</div>

<?php
    else:
        echo "<div class='alert alert-danger'>Produk tidak ditemukan.</div>";
    endif;
} else {
    echo "<div class='alert alert-danger'>ID produk tidak ditemukan.</div>";
}

include 'layout/footer.php';
?>
