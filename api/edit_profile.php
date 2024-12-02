<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

include('koneksi.php');

$user_id = $_SESSION['user_id'];
$query = "SELECT username, password, nama_lengkap, alamat, telepon, foto_profil FROM user WHERE user_id = '$user_id'";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

// Jika form disubmit, lakukan update data
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $nama_lengkap = mysqli_real_escape_string($conn, $_POST['nama_lengkap']);
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
    $telepon = mysqli_real_escape_string($conn, $_POST['telepon']);

    // Proses upload foto
    if (!empty($_POST['cropped_image'])) {
        $croppedImage = $_POST['cropped_image'];
        $decodedImage = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $croppedImage));

        $target_dir = "uploads/";
        $file_name = uniqid() . "_profile.png";
        $target_file = $target_dir . $file_name;

        if (file_put_contents($target_file, $decodedImage)) {
            if (!empty($user['foto_profil']) && $user['foto_profil'] != 'default.png') {
                unlink($target_dir . $user['foto_profil']);
            }
            $user['foto_profil'] = $file_name;
        }
    }

    $update_query = "UPDATE user SET username = '$username', password = '$password', nama_lengkap = '$nama_lengkap', alamat = '$alamat', telepon = '$telepon', foto_profil = '{$user['foto_profil']}' WHERE user_id = '$user_id'";
    if (mysqli_query($conn, $update_query)) {
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Profil Anda berhasil diperbarui.',
                    showConfirmButton: false,
                    timer: 2000
                });
            });
        </script>";
    } else {
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: 'Terjadi kesalahan saat memperbarui profil.',
                    showConfirmButton: true
                });
            });
        </script>";
    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profil Pengguna</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            background-color: #f0f8ff;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .container {
            max-width: 700px;
            margin-top: 50px;
        }

        .card {
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border: none;
        }

        .card-header {
            background-color: #007bff;
            color: white;
            text-align: center;
            font-size: 1.5rem;
            font-weight: bold;
            border-radius: 10px 10px 0 0;
        }

        .profile-photo {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            display: block;
            margin: 0 auto;
        }

        .btn-primary {
            background-color: #007bff;
            border: none;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        .btn-secondary {
            background-color: #6c757d;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
        }

        .form-group textarea {
            resize: none;
        }

        .modal-content {
            border-radius: 10px;
        }

        footer {
            margin-top: 10px;
            margin-bottom: 10px;
            text-align: center;
            color: #6c757d;
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="card">
            <div class="card-header">Edit Profil Pengguna</div>
            <div class="card-body">
                <form method="POST" id="editProfileForm">
                    <div class="form-group text-center">
                        <img src="uploads/<?php echo !empty($user['foto_profil']) ? $user['foto_profil'] : 'default.png'; ?>" alt="Foto Profil" class="profile-photo" id="currentPhoto">
                        <div class="mt-2">
                            <button type="button" class="btn btn-primary" id="changePhotoButton">Ganti Foto</button>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" class="form-control" id="username" name="username" value="<?php echo $user['username']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" class="form-control" id="password" name="password" value="<?php echo $user['password']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="nama_lengkap">Nama Lengkap</label>
                        <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" value="<?php echo $user['nama_lengkap']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="alamat">Alamat</label>
                        <textarea class="form-control" id="alamat" name="alamat" rows="3" required><?php echo $user['alamat']; ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="telepon">Telepon</label>
                        <input type="text" class="form-control" id="telepon" name="telepon" value="<?php echo $user['telepon']; ?>" required>
                    </div>
                    <div class="form-group text-right">
                        <button type="submit" class="btn btn-primary">Update Profil</button>
                        <a href="profile.php" class="btn btn-secondary">Batal</a>
                    </div>
                    <input type="hidden" name="cropped_image" id="croppedImageInput">
                </form>
            </div>
        </div>
    </div>

    <!-- Modal untuk Crop Foto -->
    <div class="modal fade" id="cropModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Atur Foto Profil</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="cropper-container">
                        <img id="cropper-image" src="" alt="Image for cropping">
                    </div>
                </div>
                <div class="form-group text-right">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="saveCroppedImage">Simpan</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
    <script>
        let cropper;

        document.getElementById('changePhotoButton').addEventListener('click', () => {
            const input = document.createElement('input');
            input.type = 'file';
            input.accept = 'image/*';
            input.onchange = function (event) {
                const file = event.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        document.getElementById('cropper-image').src = e.target.result;
                        $('#cropModal').modal('show');

                        if (cropper) cropper.destroy();

                        cropper = new Cropper(document.getElementById('cropper-image'), {
                            aspectRatio: 1,
                            viewMode: 1,
                        });
                    };
                    reader.readAsDataURL(file);
                }
            };
            input.click();
        });

        document.getElementById('saveCroppedImage').addEventListener('click', () => {
            const canvas = cropper.getCroppedCanvas({
                width: 300,
                height: 300
            });
            document.getElementById('croppedImageInput').value = canvas.toDataURL('image/png');
            document.getElementById('editProfileForm').submit();
        });
    </script>

     <!-- Footer -->
     <footer>&copy; 2024 UMKM Trunojoyo. All Rights Reserved.</footer>

</body>
</html>
