<?php
session_start();
include('../koneksi.php');

// Validasi session login admin terbaru
if (!isset($_SESSION['login_admin_status']) || $_SESSION['role'] !== 'admin') {
    header("Location: login_admin.php");
    exit;
}

// Pengaman jika ID menu tidak ada di URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<script>alert('ID Menu tidak ditemukan!'); window.location='data_menu.php';</script>";
    exit;
}

// Ambil ID dari URL
$id = $_GET['id'];
$query = mysqli_query($conn, "SELECT * FROM menu WHERE id_menu = '$id'");
$data = mysqli_fetch_assoc($query);

// Jika menu tidak ditemukan di database
if (!$data) {
    echo "<script>alert('Data menu tidak ditemukan!'); window.location='data_menu.php';</script>";
    exit;
}

if (isset($_POST['update'])) {
    $nama          = $_POST['nama_menu'];
    $harga         = $_POST['harga'];
    $status_order  = $_POST['status_order']; // PERBAIKAN: Menangkap status order baru
    $deskripsi     = $_POST['deskripsi'];
    $kategori      = $_POST['kategori'];
    
    $nama_file = $_FILES['gambar']['name'];
    $sumber    = $_FILES['gambar']['tmp_name'];

    // Cek apakah admin mengunggah gambar baru
    if (!empty($nama_file)) {
        move_uploaded_file($sumber, '../images/' . $nama_file);
        // PERBAIKAN: Mengubah kolom 'stok' menjadi 'status_order'
        $update = mysqli_query($conn, "UPDATE menu SET 
            nama_menu='$nama', harga='$harga', status_order='$status_order', kategori='$kategori', 
            deskripsi='$deskripsi', gambar='$nama_file' WHERE id_menu='$id'");
    } else {
        // PERBAIKAN: Update tanpa mengganti gambar (kolom 'stok' diganti 'status_order')
        $update = mysqli_query($conn, "UPDATE menu SET 
            nama_menu='$nama', harga='$harga', status_order='$status_order', kategori='$kategori', 
            deskripsi='$deskripsi' WHERE id_menu='$id'");
    }

    if ($update) {
        echo "<script>alert('Perubahan data menu berhasil disimpan!'); window.location='data_menu.php';</script>";
        exit;
    } else {
        echo "<script>alert('Gagal menyimpan perubahan!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Menu - Dapur Alifa</title>
    <style>
        body { 
            font-family: 'Segoe UI', sans-serif; background: rgba(0,0,0,0.05); 
            display: flex; justify-content: center; align-items: center; 
            height: 100vh; margin: 0; 
        }
        .modal-card { 
            background: white; width: 450px; padding: 30px; border-radius: 15px; 
            box-shadow: 0 10px 25px rgba(0,0,0,0.1); position: relative;
        }
        .header-modal { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .header-modal h2 { font-size: 20px; color: #333; margin: 0; }
        .close-btn { text-decoration: none; color: #999; font-size: 24px; }
        
        label { display: block; font-size: 14px; font-weight: bold; color: #333; margin-bottom: 8px; }
        input, select, textarea { 
            width: 100%; padding: 12px; margin-bottom: 15px; border: 1px solid #ddd; 
            border-radius: 8px; font-size: 14px; box-sizing: border-box;
        }

        .image-preview-section { display: flex; align-items: center; gap: 15px; margin-bottom: 20px; }
        .current-img { width: 80px; height: 60px; object-fit: cover; border-radius: 8px; border: 1px solid #ddd; }
        .btn-ganti { 
            background: #f0f0f0; color: #333; padding: 8px 15px; border-radius: 8px; 
            font-size: 13px; cursor: pointer; border: 1px solid #ddd; font-weight: bold;
        }

        .footer-buttons { display: flex; justify-content: flex-end; gap: 10px; }
        .btn { padding: 12px 25px; border-radius: 8px; font-weight: bold; cursor: pointer; border: none; font-size: 14px; }
        .btn-batal { background: #fff; color: #555; border: 1px solid #ddd; text-decoration: none; display: flex; align-items: center; }
        .btn-simpan { background: #FF661D; color: white; }
    </style>
</head>
<body>

<div class="modal-card">
    <div class="header-modal">
        <h2>Edit Menu</h2>
        <a href="data_menu.php" class="close-btn">&times;</a>
    </div>

    <form action="" method="POST" enctype="multipart/form-data">
        <label>Nama Menu</label>
        <input type="text" name="nama_menu" value="<?php echo isset($data['nama_menu']) ? htmlspecialchars($data['nama_menu']) : ''; ?>" required>

        <label>Harga</label>
        <input type="number" name="harga" value="<?php echo isset($data['harga']) ? $data['harga'] : 0; ?>" required>

        <!-- PERBAIKAN: Input angka stok diganti dengan Dropdown Status Order -->
        <label>Status Order</label>
        <select name="status_order" required>
            <option value="Open Order" <?php if(isset($data['status_order']) && $data['status_order'] == 'Open Order') echo 'selected'; ?>>Open Order (Bisa Dipesan)</option>
            <option value="Close Order" <?php if(isset($data['status_order']) && $data['status_order'] == 'Close Order') echo 'selected'; ?>>Close Order (Tutup Sementara)</option>
        </select>

        <label>Kategori</label>
        <select name="kategori">
            <option value="Makanan" <?php if(isset($data['kategori']) && $data['kategori'] == 'Makanan') echo 'selected'; ?>>Makanan</option>
            <option value="Minuman" <?php if(isset($data['kategori']) && $data['kategori'] == 'Minuman') echo 'selected'; ?>>Minuman</option>
            <option value="Snack" <?php if(isset($data['kategori']) && $data['kategori'] == 'Snack') echo 'selected'; ?>>Snack</option>
        </select>

        <label>Deskripsi</label>
        <textarea name="deskripsi" rows="3"><?php echo isset($data['deskripsi']) ? htmlspecialchars($data['deskripsi']) : ''; ?></textarea>

        <label>Gambar</label>
        <div class="image-preview-section">
            <?php if(!empty($data['gambar'])): ?>
                <img src="../images/<?php echo $data['gambar']; ?>" class="current-img">
            <?php endif; ?>
            <label for="upload-file" class="btn-ganti">Ganti Gambar</label>
            <input type="file" id="upload-file" name="gambar" style="display:none">
        </div>

        <div class="footer-buttons">
            <a href="data_menu.php" class="btn btn-batal">Batal</a>
            <button type="submit" name="update" class="btn btn-simpan">Simpan Perubahan</button>
        </div>
    </form>
</div>

</body>
</html>