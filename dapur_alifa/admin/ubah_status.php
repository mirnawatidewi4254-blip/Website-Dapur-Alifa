<?php
session_start();
include('../koneksi.php');

if (!isset($_SESSION['login_admin'])) {
    header("Location: login_admin.php");
    exit;
}

// Ambil ID Pesanan dari URL
$id = $_GET['id'];

// Ambil data pesanan untuk ditampilkan di kartu
$query = mysqli_query($conn, "SELECT pesanan.*, users.nama_lengkap 
                              FROM pesanan 
                              JOIN users ON pesanan.id_user = users.id 
                              WHERE id_pesanan = '$id'");
$data = mysqli_fetch_assoc($query);

// Jika tombol "Simpan Perubahan" ditekan
if (isset($_POST['update_status'])) {
    $status_baru = $_POST['status_pesanan'];

    $update = mysqli_query($conn, "UPDATE pesanan SET status_pesanan = '$status_baru' WHERE id_pesanan = '$id'");

    if ($update) {
        echo "<script>alert('Status pesanan berhasil diperbarui!'); window.location='data_pesanan.php';</script>";
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Ubah Status Pesanan - Dapur Alifa</title>
    <style>
        body { 
            font-family: 'Segoe UI', sans-serif; background: rgba(0,0,0,0.05); 
            display: flex; justify-content: center; align-items: center; 
            height: 100vh; margin: 0; 
        }
        .modal-card { 
            background: white; width: 400px; padding: 30px; border-radius: 15px; 
            box-shadow: 0 10px 25px rgba(0,0,0,0.1); position: relative;
        }
        .header-modal { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .header-modal h2 { font-size: 18px; color: #333; margin: 0; }
        .close-btn { text-decoration: none; color: #999; font-size: 24px; }
        
        .info-box { background: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #eee; }
        .info-box p { font-size: 14px; margin: 5px 0; color: #555; }
        .info-box strong { color: #333; }

        label { display: block; font-size: 14px; font-weight: bold; color: #333; margin-bottom: 10px; }
        
        select { 
            width: 100%; padding: 12px; margin-bottom: 25px; border: 1px solid #ddd; 
            border-radius: 8px; font-size: 14px; background: #fff; outline: none;
        }

        .footer-buttons { display: flex; justify-content: flex-end; gap: 10px; }
        .btn { padding: 12px 20px; border-radius: 8px; font-weight: bold; cursor: pointer; border: none; font-size: 14px; }
        .btn-batal { background: #fff; color: #555; border: 1px solid #ddd; text-decoration: none; text-align: center; }
        .btn-simpan { background: #FF661D; color: white; }
        
        .btn-simpan:hover { background: #e55a1a; }
    </style>
</head>
<body>

<div class="modal-card">
    <div class="header-modal">
        <h2>Ubah Status Pesanan</h2>
        <a href="data_pesanan.php" class="close-btn">&times;</a>
    </div>

    <div class="info-box">
        <p>No. Pesanan: <strong>#<?php echo $data['id_pesanan']; ?></strong></p>
        <p>Customer: <strong><?php echo $data['nama_lengkap']; ?></strong></p>
        <p>Total: <strong>Rp <?php echo number_format($data['total_bayar'], 0, ',', '.'); ?></strong></p>
    </div>

    <form action="" method="POST">
        <label>Status Saat Ini</label>
        <select name="status_pesanan">
            <option value="Menunggu" <?php if($data['status_pesanan'] == 'Menunggu') echo 'selected'; ?>>Menunggu</option>
            <option value="Diproses" <?php if($data['status_pesanan'] == 'Diproses') echo 'selected'; ?>>Diproses</option>
            <option value="Selesai" <?php if($data['status_pesanan'] == 'Selesai') echo 'selected'; ?>>Selesai</option>
            <option value="Dibatalkan" <?php if($row['status_pesanan'] == 'Dibatalkan') echo 'selected'; ?>>Dibatalkan</option>
        </select>

        <div class="footer-buttons">
            <a href="data_pesanan.php" class="btn btn-batal">Batal</a>
            <button type="submit" name="update_status" class="btn btn-simpan">Simpan Perubahan</button>
        </div>
    </form>
</div>

</body>
</html>