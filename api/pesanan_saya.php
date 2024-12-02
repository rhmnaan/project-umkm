<?php
include 'layout/header.php'; 

// Pastikan pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    die("Harap login terlebih dahulu.");
}

$user_id = $_SESSION['user_id']; 

// Ambil data transaksi berdasarkan user_id
$query_transaksi = "
    SELECT 
        transaksi.order_id AS order_id, 
        transaksi.total_biaya,
        transaksi.status AS status_pesanan, 
        transaksi_detail.jumlah AS jumlah_produk,
        transaksi_detail.subtotal AS total_produk,
        produk.nama_produk,
        produk.harga AS harga_produk,
        seller.nama_toko,
        seller.alamat AS alamat_toko,
        transaksi.bukti_pembayaran
    FROM transaksi 
    JOIN transaksi_detail ON transaksi.order_id = transaksi_detail.order_id
    JOIN produk ON transaksi_detail.product_id = produk.id
    JOIN seller ON transaksi.seller_id = seller.id
    WHERE transaksi.user_id = ?
";

$stmt_transaksi = mysqli_prepare($conn, $query_transaksi);
mysqli_stmt_bind_param($stmt_transaksi, "i", $user_id);
mysqli_stmt_execute($stmt_transaksi);
$result_transaksi = mysqli_stmt_get_result($stmt_transaksi);

// Cek apakah ada transaksi
if (mysqli_num_rows($result_transaksi) > 0) {
    echo "<div class='container my-5'>
            <div class='card shadow'>
                <div class='card-header bg-primary text-white'>
                    <h2 class='text-center mb-0'>Pesanan Saya</h2>
                </div>
                <div class='card-body'>
                    <table class='table table-striped table-bordered' style='width:100%;'>
                        <thead>
                            <tr>
                                <th>Nama Produk</th>
                                <th>Jumlah Produk</th>
                                <th>Harga Produk</th>
                                <th>Total Produk</th>
                                <th>Nama Toko</th>
                                <th>Alamat Toko</th>
                                <th>Status Pesanan</th>
                                <th>Bukti Pembayaran</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>";

    while ($row = mysqli_fetch_assoc($result_transaksi)) {
        // Menampilkan status dengan format yang lebih mudah dibaca
        $status = "";
        $action_button = "";

        // Menentukan status dan aksi berdasarkan status pesanan
        switch ($row['status_pesanan']) {
            case 'Menunggu Konfirmasi Pembayaran':
                $status = "<span class='badge badge-warning'>Menunggu Konfirmasi Pembayaran</span>";
                break;
            case 'Sedang Dikemas':
                $status = "<span class='badge badge-warning'>Sedang Dikemas</span>";
                break;
            case 'Pesanan Siap':
                $status = "<span class='badge badge-info'>Pesanan Siap</span>";
                $action_button = "<a href='seller/ubah_status.php?order_id=" . $row['order_id'] . "&status=Pesanan Diterima' class='btn btn-success btn-sm'>Pesanan Diterima</a>";
                break;
            case 'Sedang Dikirim':
                $status = "<span class='badge badge-info'>Sedang Dikirim</span>";
                $action_button = "<a href='seller/ubah_status.php?order_id=" . $row['order_id'] . "&status=Pesanan Diterima' class='btn btn-success btn-sm'>Pesanan Diterima</a>";
                break;
            case 'Pesanan Telah Selesai':
                $status = "<span class='badge badge-success'>Pesanan Telah Selesai</span>";
                $action_button = "<a href='seller/ubah_status.php?order_id=" . $row['order_id'] . "&status=Pesanan Diterima' class='btn btn-success btn-sm'>Pesanan Diterima</a>";
                break;
            default:
                $status = "<span class='badge badge-secondary'>Status Tidak Diketahui</span>";
                break;
        }

        // Tombol untuk bukti pembayaran
        $bukti_pembayaran = "";
        if ($row['bukti_pembayaran']) {
            $bukti_pembayaran = "<button class='btn btn-info btn-sm' onclick='previewBukti(\"../" . $row['bukti_pembayaran'] . "\")'>Lihat Bukti Pembayaran</button>";
        } else {
            $bukti_pembayaran = "<a href='form_bukti_pembayaran.php?order_id=" . $row['order_id'] . "' class='btn btn-primary btn-sm'>Upload Bukti Pembayaran</a>";
        }

        echo "<tr>
            <td>" . htmlspecialchars($row['nama_produk']) . "</td>
            <td>" . htmlspecialchars($row['jumlah_produk']) . "</td>
            <td>Rp " . number_format($row['harga_produk'], 0, ',', '.') . "</td>
            <td>Rp " . number_format($row['total_produk'], 0, ',', '.') . "</td>
            <td>" . htmlspecialchars($row['nama_toko']) . "</td>
            <td>" . htmlspecialchars($row['alamat_toko']) . "</td>
            <td>" . $status . "</td>
            <td>" . $bukti_pembayaran . "</td>
            <td>" . $action_button . "</td>
        </tr>";
    }

    echo "</tbody>
        </table>
    </div>
    </div>
    </div>";
} else {
    echo "<p class='text-danger text-center'>Anda belum memiliki pesanan.</p>";
}

?>

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
    document.getElementById('previewImage').src = imageUrl;
    $('#previewModal').modal('show');
}
</script>
<?php
include 'layout/footer.php';
?>
