<?php
include 'koneksi.php';
include 'layout/header.php';

// Pastikan pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    die("Harap login terlebih dahulu.");
}

$user_id = $_SESSION['user_id'];

// Validasi order_id dari URL
if (!isset($_GET['order_id']) || empty($_GET['order_id'])) {
    echo "<script>
            alert('Order ID tidak ditemukan.');
            window.location.href = 'pesanan_saya.php';
          </script>";
    exit;
}

$order_id = htmlspecialchars($_GET['order_id']);

// Jika form di-submit, proses unggah bukti pembayaran
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['bukti_pembayaran'])) {
    $file_name = $_FILES['bukti_pembayaran']['name'];
    $file_tmp = $_FILES['bukti_pembayaran']['tmp_name'];
    $file_size = $_FILES['bukti_pembayaran']['size'];
    $file_error = $_FILES['bukti_pembayaran']['error'];

    $upload_dir = 'uploads/bukti_pembayaran/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true); // Buat folder jika belum ada
    }

    $allowed_extensions = ['jpg', 'jpeg', 'png'];
    $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);

    if (!in_array(strtolower($file_ext), $allowed_extensions)) {
        echo "<script>
                alert('Format file tidak valid. Hanya file JPG, JPEG, atau PNG yang diizinkan.');
              </script>";
        exit;
    }

    if ($file_error === 0) {
        if ($file_size < 2000000) { // Batas ukuran file 2MB
            $file_path = $upload_dir . uniqid('bukti_', true) . '.' . $file_ext;

            if (move_uploaded_file($file_tmp, $file_path)) {
                // Update status setelah bukti pembayaran diunggah
                $query_update = "UPDATE transaksi SET bukti_pembayaran = ?, status = 'Pembayaran Dikonfirmasi' WHERE order_id = ? AND user_id = ?";
                $stmt_update = mysqli_prepare($conn, $query_update);
                mysqli_stmt_bind_param($stmt_update, "sii", $file_path, $order_id, $user_id);


                if (mysqli_stmt_execute($stmt_update)) {
                    echo "<script>
                            alert('Bukti pembayaran berhasil diunggah.');
                            window.location.href = 'pesanan_saya.php';
                          </script>";
                } else {
                    echo "<script>
                            alert('Gagal menyimpan bukti pembayaran. Silakan coba lagi.');
                          </script>";
                }
            } else {
                echo "<script>
                        alert('Gagal mengunggah file. Pastikan file tidak rusak.');
                      </script>";
            }
        } else {
            echo "<script>
                    alert('File terlalu besar. Maksimal ukuran file adalah 2MB.');
                  </script>";
        }
    } else {
        echo "<script>
                alert('Terjadi kesalahan saat mengunggah file.');
              </script>";
    }
}
?>

<div class="container my-5">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h2 class="text-center mb-0">Unggah Bukti Pembayaran</h2>
        </div>
        <div class="card-body">
            <form action="form_bukti_pembayaran.php?order_id=<?php echo $order_id; ?>" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="order_id">Order ID</label>
                    <p class="form-control-static"><?php echo $order_id; ?></p>
                    <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
                </div>
                <div class="form-group">
                    <label for="bukti_pembayaran">Pilih Bukti Pembayaran</label>
                    <input type="file" class="form-control" name="bukti_pembayaran" required>
                </div>
                <button type="submit" class="btn btn-primary">Unggah Bukti Pembayaran</button>
            </form>
        </div>
    </div>
</div>

<?php
include 'layout/footer.php';
?>
