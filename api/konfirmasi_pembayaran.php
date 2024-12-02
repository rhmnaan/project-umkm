<?php
include 'koneksi.php';
include 'layout/header.php';

// Pastikan pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    die("Harap login terlebih dahulu.");
}

$user_id = $_SESSION['user_id'];

// Periksa apakah ada parameter order_id yang diterima
if (isset($_GET['order_id'])) {
    $order_id = $_GET['order_id'];

    // Cek apakah pesanan yang dimaksud milik pengguna dan belum memiliki status 'Pembayaran Dikonfirmasi' atau 'Pesanan Diterima'
    $query_check = "SELECT * FROM transaksi WHERE order_id = ? AND user_id = ? AND status NOT IN ('Pembayaran Dikonfirmasi', 'Pesanan Diterima')";
    $stmt_check = mysqli_prepare($conn, $query_check);
    mysqli_stmt_bind_param($stmt_check, "ii", $order_id, $user_id);
    mysqli_stmt_execute($stmt_check);
    $result_check = mysqli_stmt_get_result($stmt_check);

    if (mysqli_num_rows($result_check) > 0) {
        // Jika status masih "Menunggu Konfirmasi Pembayaran", lakukan konfirmasi pembayaran
        $query_update = "UPDATE transaksi SET status = 'Pembayaran Dikonfirmasi' WHERE order_id = ?";
        $stmt_update = mysqli_prepare($conn, $query_update);
        mysqli_stmt_bind_param($stmt_update, "i", $order_id);

        if (mysqli_stmt_execute($stmt_update)) {
            echo "<script>
                    alert('Pembayaran berhasil dikonfirmasi. Anda dapat menerima pesanan.');
                    window.location.href = 'pesanan_saya.php'; // Pengalihan ke halaman pesanan saya
                  </script>";
        } else {
            echo "<script>
                    alert('Gagal mengkonfirmasi pembayaran. Silakan coba lagi.');
                  </script>";
        }
    } elseif (mysqli_num_rows($result_check) == 0) {
        // Jika status sudah "Pembayaran Dikonfirmasi", maka ganti menjadi "Pesanan Diterima"
        $query_update = "UPDATE transaksi SET status = 'Pesanan Diterima' WHERE order_id = ?";
        $stmt_update = mysqli_prepare($conn, $query_update);
        mysqli_stmt_bind_param($stmt_update, "i", $order_id);

        if (mysqli_stmt_execute($stmt_update)) {
            echo "<script>
                    alert('Pesanan telah diterima.');
                    window.location.href = 'pesanan_saya.php'; // Pengalihan ke halaman pesanan saya
                  </script>";
        } else {
            echo "<script>
                    alert('Gagal memperbarui status pesanan. Silakan coba lagi.');
                  </script>";
        }
    } else {
        echo "<script>
                alert('Pesanan tidak ditemukan atau sudah diproses.');
                window.location.href = 'pesanan_saya.php';
              </script>";
    }
} else {
    echo "<script>
            alert('Pesanan tidak valid.');
            window.location.href = 'pesanan_saya.php';
          </script>";
}

include 'layout/footer.php';
?>
