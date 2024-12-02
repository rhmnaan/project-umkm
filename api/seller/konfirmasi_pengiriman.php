<?php
include '../koneksi.php';
$order_id = $_GET['order_id'];

// Update status pengiriman menjadi "Sudah Selesai"
$query = "UPDATE transaksi SET status = 'Sudah Selesai' WHERE order_id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, 'i', $order_id);
mysqli_stmt_execute($stmt);

if (mysqli_stmt_affected_rows($stmt) > 0) {
    echo "<script>
        alert('Pesanan telah selesai dan dikirim!');
        window.location.href = 'transaksi.php';
    </script>";
} else {
    echo "<script>
        alert('Gagal mengonfirmasi pengiriman.');
        window.history.back();
    </script>";
}
?>
