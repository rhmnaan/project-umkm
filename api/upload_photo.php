<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Koneksi ke database
include('koneksi.php');

// Memproses unggahan file
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['foto_profil'])) {
    $user_id = $_SESSION['user_id'];
    $foto = $_FILES['foto_profil'];
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($foto['name']);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Validasi jenis file
    $check = getimagesize($foto["tmp_name"]);
    if ($check !== false) {
        $uploadOk = 1;
    } else {
        echo "File bukan gambar.";
        $uploadOk = 0;
    }

    // Validasi ukuran file (maksimal 2MB)
    if ($foto["size"] > 2000000) {
        echo "Ukuran file terlalu besar.";
        $uploadOk = 0;
    }

    // Validasi ekstensi file
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
        echo "Hanya file JPG, JPEG, dan PNG yang diizinkan.";
        $uploadOk = 0;
    }

    // Upload file jika validasi lolos
    if ($uploadOk == 1) {
        if (move_uploaded_file($foto["tmp_name"], $target_file)) {
            // Simpan nama file ke database
            $query = "UPDATE user SET foto_profil = '" . basename($foto['name']) . "' WHERE user_id = '$user_id'";
            if (mysqli_query($conn, $query)) {
                echo "Foto profil berhasil diperbarui.";
                header('Location: profile.php'); // Redirect ke halaman profil
                exit;
            } else {
                echo "Terjadi kesalahan saat menyimpan ke database.";
            }
        } else {
            echo "Terjadi kesalahan saat mengunggah file.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Foto Profil</title>
</head>
<body>
    <form action="" method="POST" enctype="multipart/form-data">
        <label for="foto_profil">Pilih Foto Profil:</label>
        <input type="file" name="foto_profil" id="foto_profil" accept="image/*">
        <button type="submit">Unggah</button>
    </form>
</body>
</html>
