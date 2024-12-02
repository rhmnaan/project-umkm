<?php
include '../config/db.php'; // Pastikan file koneksi sudah benar

// Pastikan pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Harap login terlebih dahulu.']);
    exit;
}

// Ambil data dari request JSON
$data = json_decode(file_get_contents('php://input'), true);

$order_id = $data['order_id'] ?? null;
$status = $data['status'] ?? null;

// Validasi input
if (!$order_id || !$status) {
    echo json_encode(['success' => false, 'message' => 'Parameter tidak lengkap.']);
    exit;
}

// Validasi status yang diperbolehkan
$status_valid = ['Sedang Dikemas', 'Sedang Dikirim', 'Pesanan Selesai'];
if (!in_array($status, $status_valid)) {
    echo json_encode(['success' => false, 'message' => 'Status tidak valid.']);
    exit;
}

// Perbarui status di database
$query = "UPDATE transaksi SET status = ? WHERE order_id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "si", $status, $order_id);

if (mysqli_stmt_execute($stmt)) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Gagal memperbarui status.']);
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
?>
