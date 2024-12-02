<?php
include('../koneksi.php');

if (isset($_GET['id'])) {
    $id_produk = $_GET['id'];

    // Hapus gambar jika ada
    $query = "SELECT gambar FROM produk WHERE id = '$id_produk'";
    $result = mysqli_query($conn, $query);
    $produk = mysqli_fetch_assoc($result);
    if ($produk && file_exists($produk['gambar'])) {
        unlink($produk['gambar']); // Hapus file gambar
    }

    // Hapus produk dari database
    $query_hapus = "DELETE FROM produk WHERE id = '$id_produk'";
    if (mysqli_query($conn, $query_hapus)) {
        echo "<script>
                alert('Produk berhasil dihapus');
                window.location.href = 'manajemen_toko.php';
              </script>";
    } else {
        echo "<script>
                alert('Gagal menghapus produk');
              </script>";
    }
}
?>
