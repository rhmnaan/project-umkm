<?php
include '../layout/header.php';

// Periksa apakah user sudah login dan memiliki akses sebagai seller
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Periksa apakah user sudah menjadi seller
$query_seller = "SELECT id FROM seller WHERE user_id = '$user_id'";
$result_seller = mysqli_query($conn, $query_seller);
$seller_info = mysqli_fetch_assoc($result_seller);

if (!$seller_info) {
    echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: 'Anda belum terdaftar sebagai penjual',
                    text: 'Silakan daftar terlebih dahulu.',
                    icon: 'warning',
                    confirmButtonText: 'Ok'
                }).then(function() {
                    window.location.href = '../daftar_penjual.php';
                });
            });
          </script>";
    exit;
}

$seller_id = $seller_info['id'];

// Proses tambah produk
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_produk = mysqli_real_escape_string($conn, $_POST['nama_produk']);
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $harga = (int) $_POST['harga'];
    $stok = (int) $_POST['stok'];
    $gambar = "";

    // Proses gambar hasil crop
    if (!empty($_POST['croppedImage'])) {
        $croppedImage = $_POST['croppedImage'];

        // Decode data base64
        $image_parts = explode(";base64,", $croppedImage);
        if (count($image_parts) === 2) {
            $image_base64 = base64_decode($image_parts[1]);

            // Tentukan path file untuk menyimpan gambar
            $fileName = '../uploads/' . uniqid() . '.png';
            if (file_put_contents($fileName, $image_base64)) {
                $gambar = $fileName;
            }
        }
    }

    // Insert produk baru ke database
    $query = "INSERT INTO produk (nama_produk, deskripsi, harga, stok, gambar, seller_id) 
              VALUES ('$nama_produk', '$deskripsi', '$harga', '$stok', '$gambar', '$seller_id')";

    if (mysqli_query($conn, $query)) {
        echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        title: 'Sukses!',
                        text: 'Produk berhasil ditambahkan.',
                        icon: 'success',
                        confirmButtonText: 'Ok'
                    }).then(function() {
                        window.location.href = 'manajemen_toko.php';
                    });
                });
              </script>";
    } else {
        echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        title: 'Gagal!',
                        text: 'Gagal menambahkan produk. Coba lagi.',
                        icon: 'error',
                        confirmButtonText: 'Ok'
                    });
                });
              </script>";
    }
}
?>


<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Produk</title>
    <link rel="stylesheet" href="../layout/style.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <style>
        #preview {
            max-width: 300px; /* Batasi lebar preview */
            max-height: 300px; /* Batasi tinggi preview */
            margin-top: 15px;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <h2>Tambah Produk Baru</h2>
    <form action="tambah_produk.php" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="nama_produk">Nama Produk</label>
            <input type="text" class="form-control" id="nama_produk" name="nama_produk" required>
        </div>
        <div class="form-group">
            <label for="deskripsi">Deskripsi</label>
            <textarea class="form-control" id="deskripsi" name="deskripsi" required></textarea>
        </div>
        <div class="form-group">
            <label for="harga">Harga (Rp)</label>
            <input type="number" class="form-control" id="harga" name="harga" required>
        </div>
        <div class="form-group">
            <label for="stok">Stok</label>
            <input type="number" class="form-control" id="stok" name="stok" required>
        </div>
        <div class="form-group">
            <label for="gambar">Gambar Produk</label>
            <input type="file" class="form-control" id="gambar" name="gambar" accept="image/*" onchange="showPreview(event)">
            <div class="mt-3">
                <img id="preview" style="display: none;" alt="Preview Gambar">
            </div>
        </div>
        <input type="hidden" id="croppedImage" name="croppedImage">

        <!-- Tombol Crop dan Tambah Produk -->
        <div class="form-group d-flex">
            <button type="button" id="cropButton" class="btn btn-secondary" style="display: none;">Crop Gambar</button>
            <button type="submit" class="btn btn-primary ml-1">Tambah Produk</button>
            <a href="manajemen_toko.php" class="btn btn-danger ml-1">Kembali</a>
        </div>
    </form>
</div>

<script>
    let cropper;
let isCropping = false; // Status apakah sedang dalam mode crop
let originalImageData; // Menyimpan data asli gambar sebelum di-crop
let cropData = null; // Menyimpan data posisi crop terakhir

// Fungsi untuk menampilkan preview gambar
function showPreview(event) {
    const file = event.target.files[0];
    const preview = document.getElementById('preview');
    const cropButton = document.getElementById('cropButton');

    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
            cropButton.style.display = 'inline-block';

            // Simpan data asli gambar
            originalImageData = e.target.result;

            // Hapus instance Cropper sebelumnya jika ada
            if (cropper) {
                cropper.destroy();
            }

            // Inisialisasi Cropper.js
            cropper = new Cropper(preview, {
                aspectRatio: 1, // Rasio aspek (1:1)
                viewMode: 1,
                ready() {
                    // Jika ada data posisi crop, set posisi crop sebelumnya
                    if (cropData) {
                        cropper.setData(cropData);
                    }
                },
            });

            isCropping = true; // Set status menjadi mode crop
        };
        reader.readAsDataURL(file);
    }
}

// Fungsi untuk memproses crop gambar
document.getElementById('cropButton').addEventListener('click', function() {
    const preview = document.getElementById('preview');
    const cropButton = document.getElementById('cropButton');

    if (isCropping) {
        // Jika sedang dalam mode crop, simpan hasil crop
        const croppedCanvas = cropper.getCroppedCanvas();
        const croppedImage = croppedCanvas.toDataURL('image/png');

        // Tampilkan hasil crop sebagai preview
        preview.src = croppedImage;

        // Simpan hasil crop ke input hidden untuk dikirim ke server
        document.getElementById('croppedImage').value = croppedImage;

        // Simpan data posisi crop terakhir
        cropData = cropper.getData();

        // Matikan Cropper
        cropper.destroy();
        cropper = null;

        isCropping = false; // Set status menjadi tidak dalam mode crop
        cropButton.textContent = 'Edit Crop'; // Ubah teks tombol
    } else {
        // Jika tidak dalam mode crop, aktifkan ulang Cropper
        preview.src = originalImageData; // Reset ke gambar asli
        cropper = new Cropper(preview, {
            aspectRatio: 1,
            viewMode: 1,
            ready() {
                // Jika ada data posisi crop, set posisi crop sebelumnya
                if (cropData) {
                    cropper.setData(cropData);
                }
            },
        });

        isCropping = true; // Set status menjadi mode crop
        cropButton.textContent = 'Crop Gambar'; // Ubah teks tombol
    }
});

// Fungsi untuk menangani pengiriman form
document.querySelector('form').addEventListener('submit', function(event) {
    const croppedImage = document.getElementById('croppedImage').value;
    
    // Cek jika croppedImage kosong (artinya belum ada gambar yang di-crop)
    if (!croppedImage) {
        event.preventDefault(); // Hentikan pengiriman form
        Swal.fire({
            title: 'Peringatan!',
            text: 'Anda harus menyelesaikan proses crop gambar sebelum mengirimkan produk.',
            icon: 'warning',
            confirmButtonText: 'Ok'
        });
    }
});

</script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
</body>
</html>
