<?php
include '../layout/header.php'; 

// Pastikan penjual sudah login
if (!isset($_SESSION['user_id'])) {
    die("Harap login terlebih dahulu.");
}

$user_id = $_SESSION['user_id']; 

// Validasi apakah user adalah penjual
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

$seller_id = $seller_info['id'];

// Ambil data pesanan berdasarkan seller_id
$query_pesanan = "
    SELECT 
        transaksi.order_id AS order_id, 
        transaksi.total_biaya,
        transaksi.status AS status_pesanan, 
        transaksi_detail.jumlah AS jumlah_produk,
        transaksi_detail.subtotal AS total_produk,
        produk.nama_produk,
        produk.harga AS harga_produk,
        user.nama_lengkap AS nama_pembeli,
        transaksi.alamat_pengiriman AS alamat_pengiriman,
        transaksi.bukti_pembayaran AS bukti_pembayaran
    FROM transaksi 
    JOIN transaksi_detail ON transaksi.order_id = transaksi_detail.order_id
    JOIN produk ON transaksi_detail.product_id = produk.id
    JOIN user ON transaksi.user_id = user.user_id
    WHERE transaksi.seller_id = ?
";

$stmt_pesanan = mysqli_prepare($conn, $query_pesanan);
mysqli_stmt_bind_param($stmt_pesanan, "i", $seller_id);
mysqli_stmt_execute($stmt_pesanan);
$result_pesanan = mysqli_stmt_get_result($stmt_pesanan);

?>

<div class="container my-5">
    <div class="card shadow">
        <div class="card-header bg-success text-white">
            <h2 class="text-center mb-0">Pesanan Masuk</h2>
        </div>
        <div class="card-body">
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>Nama Pembeli</th>
                        <th>Nama Produk</th>
                        <th>Jumlah Produk</th>
                        <th>Harga Produk</th>
                        <th>Total Produk</th>
                        <th>Alamat Pengiriman</th>
                        <th>Status Pesanan</th>
                        <th>Bukti Pembayaran</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (mysqli_num_rows($result_pesanan) > 0) {
                        while ($row = mysqli_fetch_assoc($result_pesanan)) {
                            // Menentukan status dan tombol aksi
                            $status = "";
                            $aksiButton = "";

                            switch ($row['status_pesanan']) {
                                case 'Menunggu Pembayaran':
                                    $status = "<span class='badge badge-secondary'>Menunggu Pembayaran</span>";
                                    $aksiButton = "<span class='text-muted'>Tidak Ada Aksi</span>";
                                    break;
                                case 'Pembayaran Dikonfirmasi':
                                    $status = "<span class='badge badge-warning'>Pembayaran Dikonfirmasi</span>";
                                    $aksiButton = "<a href='ubah_status.php?order_id=" . $row['order_id'] . "&status=Sedang Dikemas' class='btn btn-warning btn-sm'>Sedang Dikemas</a>";
                                    break;
                                case 'Sedang Dikemas':
                                    $status = "<span class='badge badge-info'>Sedang Dikemas</span>";
                                    $aksiButton = "<a href='ubah_status.php?order_id=" . $row['order_id'] . "&status=Pesanan Siap' class='btn btn-info btn-sm'>Pesanan Siap</a>";
                                    break;
                                case 'Pesanan Siap':
                                    $status = "<span class='badge badge-primary'>Pesanan Siap</span>";
                                    $aksiButton = "<a href='ubah_status.php?order_id=" . $row['order_id'] . "&status=Pesanan Diantar' class='btn btn-primary btn-sm'>Kirim Pesanan</a>";
                                    break;
                                case 'Pesanan Diantar':
                                    $status = "<span class='badge badge-dark'>Pesanan Diantar</span>";
                                    $aksiButton = "<span class='text-muted'>Menunggu Pembeli</span>";
                                    break;
                                case 'Pesanan Diterima':
                                    $status = "<span class='badge badge-success'>Pesanan Selesai</span>";
                                    $aksiButton = "<span class='text-muted'>Pesanan diterima dan Selesai</span>";
                                    break;
                                default:
                                    $status = "<span class='badge badge-secondary'>Status tidak diketahui</span>";
                                    $aksiButton = "<span class='text-muted'>Aksi Tidak Tersedia</span>";
                                    break;
                            }

                            // Preview bukti pembayaran
                            $buktiPembayaran = htmlspecialchars($row['bukti_pembayaran']);
                            $previewButton = $buktiPembayaran
                                ? "<button class='btn btn-info btn-sm' onclick='showPreview(\"../$buktiPembayaran\")'>Preview</button>"
                                : "<span class='text-muted'>Tidak Ada</span>";

                            echo "<tr>
                                <td>" . htmlspecialchars($row['nama_pembeli']) . "</td>
                                <td>" . htmlspecialchars($row['nama_produk']) . "</td>
                                <td>" . htmlspecialchars($row['jumlah_produk']) . "</td>
                                <td>Rp " . number_format($row['harga_produk'], 0, ',', '.') . "</td>
                                <td>Rp " . number_format($row['total_produk'], 0, ',', '.') . "</td>
                                <td>" . htmlspecialchars($row['alamat_pengiriman']) . "</td>
                                <td>" . $status . "</td>
                                <td>" . $previewButton . "</td>
                                <td>" . $aksiButton . "</td>
                            </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='9' class='text-center text-danger'>Tidak ada pesanan masuk saat ini.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal untuk Preview -->
<div id="previewModal" class="modal fade" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Preview Bukti Pembayaran</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body text-center">
        <img id="previewImage" src="" alt="Bukti Pembayaran" class="img-fluid">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>

<script>
function showPreview(imageUrl) {
    // Set the image source in the modal
    document.getElementById('previewImage').src = imageUrl;
    // Show the modal
    $('#previewModal').modal('show');
}
</script>

<?php include '../layout/footer.php'; ?>
