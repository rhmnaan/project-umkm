<?php
include '../layout/header.php'; 

// Pastikan pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    die("Harap login terlebih dahulu.");
}

$user_id = $_SESSION['user_id']; 

// Periksa apakah pengguna adalah penjual
$query_seller = "SELECT * FROM user WHERE user_id = '$user_id'";
$result_seller = mysqli_query($conn, $query_seller);
$seller_info = mysqli_fetch_assoc($result_seller);

if (!$seller_info) {
    // Jika bukan penjual, lakukan tindakan sesuai
    echo "<script>
            alert('Anda belum terdaftar sebagai penjual. Silakan daftar terlebih dahulu.');
            window.location.href = 'UMKM/daftar_penjual.php';
          </script>";
    exit;
}

$seller_id = $seller_info['id'];

// Cek apakah ada parameter order_id dan status yang diterima
if (isset($_GET['order_id']) && isset($_GET['status'])) {
    $order_id = $_GET['order_id'];
    $status = $_GET['status'];

    // Jika penjual yang mengubah status pesanan
    if ($seller_id && ($status != 'Pesanan Diterima')) { 
        // Update status pesanan untuk penjual
        $query_update = "UPDATE transaksi SET status = ? WHERE order_id = ? AND seller_id = ?";
        $stmt = mysqli_prepare($conn, $query_update);
        mysqli_stmt_bind_param($stmt, "sii", $status, $order_id, $seller_id);

        if (mysqli_stmt_execute($stmt)) {
            echo "<script>
                    alert('Status pesanan berhasil diubah menjadi $status.');
                    window.location.href = 'transaksi.php'; // Kembali ke halaman pesanan masuk
                  </script>";
        } else {
            echo "<script>
                    alert('Gagal mengubah status pesanan.');
                    window.history.back();
                  </script>";
        }
        mysqli_stmt_close($stmt);
    }

    // Jika status yang diubah adalah 'Pesanan Diterima', hanya pembeli yang bisa melakukannya
    if ($status == 'Pesanan Diterima') {
        // Update status pesanan untuk pembeli (hanya jika pembeli yang mengaksesnya)
        $query_update_buyer = "UPDATE transaksi SET status = 'Pesanan Telah Selesai' WHERE order_id = ?";
        $stmt_buyer = mysqli_prepare($conn, $query_update_buyer);
        mysqli_stmt_bind_param($stmt_buyer, "i", $order_id);
        
        if (mysqli_stmt_execute($stmt_buyer)) {
            echo "<script>
                    alert('Pesanan telah diterima. Status pesanan berubah menjadi Pesanan Telah Selesai.');
                    window.location.href = '../pesanan_saya.php'; // Halaman pesanan pembeli
                  </script>";
        } else {
            echo "<script>
                    alert('Gagal mengubah status pesanan untuk pembeli.');
                    window.history.back();
                  </script>";
        }
        mysqli_stmt_close($stmt_buyer);
    }
} else {
    echo "<script>
            alert('Data tidak lengkap.');
            window.history.back();
          </script>";
}

mysqli_close($conn);
?>
