<?php
include 'koneksi.php';
include 'layout/header.php';

// Pastikan pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    die("Harap login terlebih dahulu.");
}

$user_id = $_SESSION['user_id'];
$order_id = $_GET['order_id'];

// Ambil bukti pembayaran berdasarkan order_id
$query = "SELECT bukti_pembayaran FROM transaksi WHERE order_id = ? AND user_id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "ii", $order_id, $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($row = mysqli_fetch_assoc($result)) {
    $bukti_pembayaran = $row['bukti_pembayaran'];
    if ($bukti_pembayaran) {
        echo "<div class='container my-5'>
                <div class='card shadow'>
                    <div class='card-header bg-primary text-white'>
                        <h2 class='text-center mb-0'>Bukti Pembayaran</h2>
                    </div>
                    <div class='card-body'>
                        <h4>Bukti Pembayaran untuk Order ID: " . htmlspecialchars($order_id) . "</h4>
                        <img src='" . $bukti_pembayaran . "' class='img-fluid' alt='Bukti Pembayaran'>
                    </div>
                </div>
              </div>";
    } else {
        echo "<p class='text-danger text-center'>Bukti pembayaran belum tersedia.</p>";
    }
} else {
    echo "<p class='text-danger text-center'>Pesanan tidak ditemukan.</p>";
}

include 'layout/footer.php';
?>
