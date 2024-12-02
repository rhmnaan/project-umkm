<?php
include 'layout/header.php'; 

// Koneksi ke database
include('koneksi.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // Mengarahkan ke login jika belum login
    exit;
}

$user_id = $_SESSION['user_id'];
$query = "SELECT username,password, nama_lengkap, alamat, telepon, dibuat_pada, diperbarui_pada, foto_profil FROM user WHERE user_id = '$user_id'";
$result = mysqli_query($conn, $query);

if (!$result || mysqli_num_rows($result) === 0) {
    die("Data pengguna tidak ditemukan.");
}

$user = mysqli_fetch_assoc($result);

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Pengguna - UMKM Trunojoyo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css">
    <style>
        .profile-card {
            margin-top: 30px;
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        .profile-header {
            text-align: center;
            margin-bottom: 20px;
        }
        .profile-header img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #ddd;
        }
        #cropper-container {
            max-width: 100%;
            max-height: 400px;
        }
    </style>
</head>
<body>
<div class="container profile-card"> 
        <!-- Informasi Profil -->
        <div class="profile-header">
            <img src="uploads/<?php echo !empty($user['foto_profil']) ? $user['foto_profil'] : 'default.png'; ?>" alt="Foto Profil">
        </div>
            <!-- Informasi Profil -->
            <table class="table table-bordered profile-info">
                <tr>
                    <th>Username</th>
                    <td><?php echo $user['username']; ?></td>
                </tr>
                <tr>
                    <th>Password</th>
                    <td><?php echo str_repeat('*', strlen($user['password'])); ?></td>
                </tr>
                <tr>
                    <th>Nama Lengkap</th>
                    <td><?php echo $user['nama_lengkap']; ?></td>
                </tr>
                <tr>
                    <th>Alamat</th>
                    <td><?php echo $user['alamat']; ?></td>
                </tr>
                <tr>
                    <th>Telepon</th>
                    <td><?php echo $user['telepon']; ?></td>
                </tr>
                <tr>
                    <th>Ditambahkan Pada</th>
                    <td><?php echo $user['dibuat_pada']; ?></td>
                </tr>
                <tr>
                    <th>Diperbarui Pada</th>
                    <td><?php echo $user['diperbarui_pada']; ?></td>
                </tr>
            </table>
        <button type="button" class="btn btn-primary"><a href="edit_profile.php" class="text-light">Edit Profil</a></button>
        <button type="button" class="btn btn-primary"><a href="daftar_penjual.php" class="text-light">Daftar Seller</a></button>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
</body>
</html>
