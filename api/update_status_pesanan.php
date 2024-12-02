<?php
include 'koneksi.php';

// Pastikan pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    die("Harap login terlebih dahulu.");
}

$user_id = $_SESSION['user_id'];

// Ambil data dari permintaan POST
$order_id = $_POST['order_id'];
$status = $_POST['status'];

// Cek status yang dikirim
if ($status == 'Pesanan Diterima') {
    // Update status menjadi Pesanan Telah Selesai
    $query_update = "UPDATE transaksi SET status = 'Pesanan Telah Selesai' WHERE order_id = ? AND user_id = ?";
    $stmt_update = mysqli_prepare($conn, $query_update);
    mysqli_stmt_bind_param($stmt_update, "ii", $order_id, $user_id);

    if (mysqli_stmt_execute($stmt_update)) {
        echo "Status berhasil diperbarui menjadi Pesanan Telah Selesai.";
    } else {
        echo "Gagal memperbarui status.";
    }
} elseif ($status == 'Konfirmasi Pembayaran') {
    // Update status menjadi Pesanan Diterima
    $query_update = "UPDATE transaksi SET status = 'Pesanan Diterima' WHERE order_id = ? AND user_id = ?";
    $stmt_update = mysqli_prepare($conn, $query_update);
    mysqli_stmt_bind_param($stmt_update, "ii", $order_id, $user_id);

    if (mysqli_stmt_execute($stmt_update)) {
        echo "Status berhasil diperbarui menjadi Pesanan Diterima.";
    } else {
        echo "Gagal memperbarui status.";
    }
}
?>
