<?php
session_start();
include('../koneksi.php');

if (!isset($_SESSION['login_admin'])) {
    header("Location: login_admin.php");
    exit;
}

// Ambil ID dari URL
$id = $_GET['id'];

// Ambil data menu untuk menampilkan namanya di kartu konfirmasi
$query = mysqli_query($conn, "SELECT * FROM menu WHERE id_menu = '$id'");
$data = mysqli_fetch_assoc($query);

// Jika tombol "Ya, Hapus" ditekan
if (isset($_POST['konfirmasi_hapus'])) {
    $nama_gambar = $data['gambar'];
    
    // Hapus file gambar di folder images
    if (file_exists("../images/$nama_gambar")) {
        unlink("../images/$nama_gambar");
    }

    // Hapus data dari database
    $hapus = mysqli_query($conn, "DELETE FROM menu WHERE id_menu = '$id'");

    if ($hapus) {
        echo "<script>alert('Menu berhasil dihapus!'); window.location='data_menu.php';</script>";
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Konfirmasi Hapus - Dapur Alifa</title>
    <style>
        body { 
            font-family: 'Segoe UI', sans-serif; background: rgba(0,0,0,0.05); 
            display: flex; justify-content: center; align-items: center; 
            height: 100vh; margin: 0; 
        }
        .modal-card { 
            background: white; width: 400px; padding: 40px 30px; border-radius: 15px; 
            box-shadow: 0 10px 25px rgba(0,0,0,0.1); text-align: center; position: relative;
        }
        .close-btn { position: absolute; top: 20px; right: 20px; text-decoration: none; color: #999; font-size: 20px; }
        
        /* Icon Tong Sampah (Sesuai Gambar) */
        .icon-circle {
            width: 70px; height: 70px; background: #FFEBEE; color: #F44336;
            border-radius: 50%; display: flex; align-items: center; justify-content: center;
            margin: 0 auto 20px; font-size: 30px;
        }

        h2 { font-size: 22px; color: #333; margin-bottom: 15px; }
        p { font-size: 14px; color: #666; line-height: 1.5; margin-bottom: 30px; }
        .menu-name { font-weight: bold; color: #333; }

        .footer-buttons { display: flex; justify-content: center; gap: 15px; }
        .btn { padding: 12px 30px; border-radius: 8px; font-weight: bold; cursor: pointer; border: none; font-size: 14px; min-width: 120px; }
        .btn-batal { background: #fff; color: #555; border: 1px solid #ddd; text-decoration: none; display: inline-block; }
        .btn-ya { background: #F44336; color: white; } /* Merah sesuai gambar */
        
        .btn-ya:hover { background: #d32f2f; }
    </style>
</head>
<body>

<div class="modal-card">
    <a href="data_menu.php" class="close-btn">&times;</a>
    
    <div class="icon-circle">🗑</div>

    <h2>Hapus Menu</h2>
    
    <p>
        Apakah Anda yakin ingin menghapus menu <br>
        <span class="menu-name">"<?php echo $data['nama_menu']; ?>"</span>? <br><br>
        <small>Menu yang sudah dihapus tidak dapat dikembalikan.</small>
    </p>

    <form action="" method="POST">
        <div class="footer-buttons">
            <a href="data_menu.php" class="btn btn-batal">Batal</a>
            <button type="submit" name="konfirmasi_hapus" class="btn btn-ya">Ya, Hapus</button>
        </div>
    </form>
</div>

</body>
</html>