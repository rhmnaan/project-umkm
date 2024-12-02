<?php
include 'koneksi.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_produk = (int)$_POST['id_produk'];
    $jumlah = (int)$_POST['jumlah'];
    $alamat_pengiriman = trim($_POST['alamat_pengiriman']);
    $metode_pembayaran = trim($_POST['metode_pembayaran']);
    $metode_pengambilan = trim($_POST['metode_pengambilan']);

    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
    } else {
        die("User belum login. Harap login terlebih dahulu.");
    }

    // Cek apakah produk dengan ID tersebut ada di tabel produk
    $query_check_produk = "SELECT id, harga, stok, seller_id FROM produk WHERE id = ?";
    $stmt_check_produk = mysqli_prepare($conn, $query_check_produk);
    mysqli_stmt_bind_param($stmt_check_produk, "i", $id_produk);
    mysqli_stmt_execute($stmt_check_produk);
    $result_check = mysqli_stmt_get_result($stmt_check_produk);

    if ($row = mysqli_fetch_assoc($result_check)) {
        if ($jumlah > $row['stok']) {
            die("Jumlah pesanan melebihi stok yang tersedia.");
        }

        $harga = $row['harga'];
        $seller_id = $row['seller_id'];
        $total_biaya = $jumlah * $harga;

        // Insert data transaksi
        $query_transaksi = "INSERT INTO transaksi (user_id, seller_id, id_produk, total_biaya, alamat_pengiriman, metode_pengambilan, status, tanggal_pesanan, nomor_resi) 
                            VALUES (?, ?, ?, ?, ?, ?, 'Pending', NOW(), ?)";
        $stmt_transaksi = mysqli_prepare($conn, $query_transaksi);
        $nomor_resi = uniqid('resi-', true); // Buat nomor resi unik
        mysqli_stmt_bind_param($stmt_transaksi, "iiissss", $user_id, $seller_id, $id_produk, $total_biaya, $alamat_pengiriman, $metode_pengambilan, $nomor_resi);

        if (mysqli_stmt_execute($stmt_transaksi)) {
            $order_id = mysqli_insert_id($conn); // Ambil ID transaksi yang baru dibuat

            // Simpan detail transaksi di tabel transaksi_detail
            $query_detail = "INSERT INTO transaksi_detail (order_id, product_id, jumlah, harga_satuan, subtotal) 
                             VALUES (?, ?, ?, ?, ?)";
            $stmt_detail = mysqli_prepare($conn, $query_detail);
            $subtotal = $jumlah * $harga;
            mysqli_stmt_bind_param($stmt_detail, "iiiss", $order_id, $id_produk, $jumlah, $harga, $subtotal);

            if (mysqli_stmt_execute($stmt_detail)) {
                // Simpan data ke tabel pembayaran
                $query_pembayaran = "INSERT INTO pembayaran (order_id, jumlah, metode_pembayaran, status, transaction_id, dibuat_pada) 
                                     VALUES (?, ?, ?, 'Pending', ?, NOW())";
                $stmt_pembayaran = mysqli_prepare($conn, $query_pembayaran);
                mysqli_stmt_bind_param($stmt_pembayaran, "iiss", $order_id, $total_biaya, $metode_pembayaran, $nomor_resi);

                if (mysqli_stmt_execute($stmt_pembayaran)) {
                    // Tampilkan alert dan redirect ke transaksi_detail.php
                    echo "
                        <script>
                            alert('Pesanan berhasil dibuat!');
                            window.location.href = 'transaksi_detail.php?id_transaksi=$order_id';
                        </script>
                    ";
                    exit;
                } else {
                    echo "Gagal menyimpan data pembayaran: " . mysqli_error($conn);
                }
            } else {
                echo "Gagal menyimpan detail pesanan: " . mysqli_error($conn);
            }
        } else {
            echo "Gagal menyimpan transaksi: " . mysqli_error($conn);
        }
    } else {
        echo "Produk tidak ditemukan.";
    }
}
?>
