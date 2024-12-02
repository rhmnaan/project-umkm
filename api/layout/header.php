<?php
session_start(); // Memulai session untuk memeriksa login

include_once __DIR__ . '/../koneksi.php';



// Cek apakah user sudah login
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    // Periksa apakah pengguna terdaftar sebagai seller
    $query = "SELECT * FROM seller WHERE user_id = '$user_id'";
    $result = mysqli_query($conn, $query);

    // Jika ada hasil, berarti pengguna adalah seller
    if (mysqli_num_rows($result) > 0) {
        $_SESSION['is_seller'] = true; // Tandai sebagai seller
    } else {
        $_SESSION['is_seller'] = false; // Bukan seller
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UMKM Universitas Trunojoyo Madura</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="layout/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="/project-umkm/index.php">UMKM Trunojoyo</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <!-- Navigasi utama di sebelah kiri -->
            <ul class="navbar-nav mr-auto">
                <li class="nav-item"><a class="nav-link" href="/project-umkm/api/index.php">Beranda</a></li>
                <li class="nav-item"><a class="nav-link" href="/project-umkm/api/produk.php">Produk</a></li>
                <li class="nav-item"><a class="nav-link" href="/project-umkm/api/tentang.php">Tentang Kami</a></li>
                <li class="nav-item"><a class="nav-link" href="/project-umkm/api/kontak.php">Kontak</a></li>
                <!-- Tambahkan menu Pesanan Saya jika bukan seller -->
                <?php if (isset($_SESSION['is_seller']) && !$_SESSION['is_seller']): ?>
                    <li class="nav-item"><a class="nav-link" href="/project-umkm/api/pesanan_saya.php">Pesanan Saya</a></li>
                <?php endif; ?>
                <li class="nav-item">
                    <?php if (isset($_SESSION['is_seller']) && $_SESSION['is_seller']): ?>
                        <a class="nav-link" href="/project-umkm/api/seller/manajemen_toko.php">Manajemen Toko</a>
                    <?php endif; ?>
                </li>
                <li class="nav-item">
                    <?php if (isset($_SESSION['is_seller']) && $_SESSION['is_seller']): ?>
                        <a class="nav-link" href="/project-umkm/api/seller/transaksi.php">transaksi</a>
                    <?php endif; ?>
                </li>
            </ul>
            <!-- Login, Daftar, dan Dropdown nama pengguna di sebelah kanan -->
            <ul class="navbar-nav ml-auto">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <!-- Jika sudah login, tampilkan nama pengguna dalam dropdown -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <?php echo $_SESSION['nama_lengkap']; ?>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right pr-3" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="/project-umkm/api/profile.php">Profil</a>

                            <a class="dropdown-item" href="/project-umkm/api/logout.php">Logout</a>
                        </div>
                    </li>
                <?php else: ?>
                    <!-- Jika belum login, tampilkan menu login dan daftar -->
                    <li class="nav-item"><a class="nav-link" href="project-umkm/api/login.php">Login</a></li>
                    <li class="nav-item"><a class="nav-link" href="project-umkm/api/daftar.php">Daftar</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>

    <!-- Konten halaman kontak -->
    
</body>
</html>
