<?php
include '../koneksi.php';
$order_id = $_GET['order_id'];

// Update status pengemasan menjadi "Sedang Dikirim"
$query = "UPDATE transaksi SET status = 'Sedang Dikirim' WHERE order_id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, 'i', $order_id);
mysqli_stmt_execute($stmt);

if (mysqli_stmt_affected_rows($stmt) > 0) {
    echo "<script>
        alert('Pesanan sedang dikemas!');
        window.location.href = 'transaksi.php';
    </script>";
} else {
    echo "<script>
        alert('Gagal mengonfirmasi pengemasan.');
        window.history.back();
    </script>";
}
?>
