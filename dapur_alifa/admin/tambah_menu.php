<?php
session_start();
include('../koneksi.php');

// Validasi session login admin terbaru
if (!isset($_SESSION['login_admin_status']) || $_SESSION['role'] !== 'admin') {
    header("Location: login_admin.php");
    exit;
}

if (isset($_POST['simpan'])) {
    $nama          = $_POST['nama_menu'];
    $harga         = $_POST['harga'];
    $status_order  = $_POST['status_order']; // PERBAIKAN: Menangkap status order (Open/Close)
    $kategori      = $_POST['kategori'];
    $deskripsi     = $_POST['deskripsi'];
    
    // Proses Upload Gambar
    $nama_file = $_FILES['gambar']['name'];
    $sumber    = $_FILES['gambar']['tmp_name'];
    
    if (!empty($nama_file)) {
        move_uploaded_file($sumber, '../images/' . $nama_file);
    } else {
        $nama_file = ""; 
    }

    // PERBAIKAN: Mengubah kolom 'stok' menjadi 'status_order' pada query INSERT
    // *Catatan: Pastikan nama kolom di database Anda sudah disesuaikan atau tetap menggunakan kolom lama tetapi diisi string 'Open Order'/'Close Order'*
    $query = "INSERT INTO menu (nama_menu, harga, status_order, kategori, deskripsi, gambar) 
              VALUES ('$nama', '$harga', '$status_order', '$kategori', '$deskripsi', '$nama_file')";
    
    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Menu katering berhasil disimpan!'); window.location='data_menu.php';</script>";
        exit;
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Menu - Dapur Alifa</title>
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
            border-radius: 8px; background: #fff; font-size: 14px; box-sizing: border-box;
        }
        .file-input-container { display: flex; border: 1px solid #ddd; border-radius: 8px; overflow: hidden; margin-bottom: 25px; }
        .file-input-container input[type="file"] { border: none; margin-bottom: 0; flex: 1; }
        .browse-label { background: #eee; padding: 12px 20px; font-size: 14px; color: #555; border-left: 1px solid #ddd; }
        .footer-buttons { display: flex; justify-content: flex-end; gap: 10px; margin-top: 10px; }
        .btn { padding: 12px 30px; border-radius: 8px; font-weight: bold; cursor: pointer; border: none; font-size: 14px; }
        .btn-batal { background: #fff; color: #555; border: 1px solid #ddd; text-decoration: none; display: flex; align-items: center; }
        .btn-simpan { background: #FF661D; color: white; } 
        .btn-simpan:hover { background: #e55a1a; }
    </style>
</head>
<body>

<div class="modal-card">
    <div class="header-modal">
        <h2>Tambah Menu</h2>
        <a href="data_menu.php" class="close-btn">&times;</a>
    </div>

    <form action="" method="POST" enctype="multipart/form-data">
        <label>Nama Menu</label>
        <input type="text" name="nama_menu" placeholder="Contoh: Nasi Tumpeng Mini" required>

        <label>Harga</label>
        <input type="number" name="harga" placeholder="Contoh: 25000" required>

        <!-- PERBAIKAN: Input Stok diganti dengan Pilihan Status Order (Open/Close) -->
        <label>Status Order</label>
        <select name="status_order" required>
            <option value="Open Order">Open Order (Bisa Dipesan)</option>
            <option value="Close Order">Close Order (Tutup Sementara)</option>
        </select>

        <label>Kategori</label>
        <select name="kategori" required>
            <option value="Makanan">Makanan</option>
            <option value="Minuman">Minuman</option>
            <option value="Snack">Snack</option>
        </select>

        <label>Deskripsi</label>
        <textarea name="deskripsi" rows="3" placeholder="Deskripsi menu (opsional)"></textarea>

        <label>Gambar</label>
        <div class="file-input-container">
            <input type="file" name="gambar" required>
            <div class="browse-label">Browse</div>
        </div>

        <div class="footer-buttons">
            <a href="data_menu.php" class="btn btn-batal">Batal</a>
            <button type="submit" name="simpan" class="btn btn-simpan">Simpan Menu</button>
        </div>
    </form>
</div>

</body>
</html>