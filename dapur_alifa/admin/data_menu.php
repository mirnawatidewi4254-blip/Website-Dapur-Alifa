<?php
session_start();
include('../koneksi.php');

// Validasi session login admin terbaru
if (!isset($_SESSION['login_admin_status']) || $_SESSION['role'] !== 'admin') {
    header("Location: login_admin.php");
    exit;
}

// Ambil semua data menu katering dari database
$query = mysqli_query($conn, "SELECT * FROM menu ORDER BY id_menu ASC");
$total_m = mysqli_num_rows($query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Menu - Dapur Alifa</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', sans-serif; }
        body { background-color: #f0f2f5; display: flex; }

        /* Sidebar Konsisten Sesuai Template Asli Kamu */
        .sidebar { width: 220px; height: 100vh; background: #343a40; color: white; position: fixed; }
        .sidebar-header { padding: 20px; display: flex; align-items: center; background: white; color: #FF661D; font-weight: bold; font-size: 20px; border-bottom: 1px solid #ddd; }
        .sidebar-header img { width: 60px; margin-right: 10px; }
        .menu-list { list-style: none; padding: 10px 0; }
        .menu-list li a { display: flex; align-items: center; padding: 12px 20px; color: #adb5bd; text-decoration: none; }
        .menu-list li a:hover, .menu-list li a.active { background: #FF661D; color: white; border-radius: 0 25px 25px 0; margin-right: 10px; }
        
        .main-content { margin-left: 220px; width: 100%; padding: 20px; }
        
        .content-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .content-header h2 { font-size: 24px; color: #333; }
        .content-header p { color: #777; font-size: 14px; }

        /* Tombol Tambah Menu Oranye */
        .btn-tambah { background: #FF661D; color: white; padding: 10px 20px; text-decoration: none; border-radius: 8px; font-weight: bold; font-size: 14px; border: none; cursor: pointer; }
        .btn-tambah:hover { background: #e55a1a; }

        .data-box { background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { text-align: left; padding: 15px; background: #f8f9fa; color: #333; font-size: 14px; }
        td { padding: 15px; border-bottom: 1px solid #f1f1f1; font-size: 14px; vertical-align: middle; }

        .img-menu { width: 80px; height: 50px; object-fit: cover; border-radius: 6px; }

        /* PERBAIKAN: Status Order Pills (Open/Close) */
        .status { padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: bold; display: inline-block; }
        .status-open { background: #E8F5E9; color: #2E7D32; }
        .status-close { background: #FADBD8; color: #E74C3C; }

        /* Tombol Aksi */
        .btn-group { display: flex; gap: 5px; }
        .btn-aksi { padding: 6px 15px; border-radius: 5px; text-decoration: none; font-size: 12px; font-weight: bold; border: none; cursor: pointer; text-align: center; }
        
        .btn-edit { background: #FFA000; color: white; }
        .btn-edit:hover { background: #e69000; }
        .btn-hapus { background: #EF5350; color: white; }
        .btn-hapus:hover { background: #d32f2f; }

        .total-info { margin-top: 20px; color: #777; font-size: 14px; }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="sidebar-header">
        <img src="../images/logo.jpg" alt="Logo"> Dapur Alifa
    </div>
    <ul class="menu-list">
        <li><a href="index.php">Dashboard</a></li>
        <li><a href="data_menu.php" class="active">Data Menu</a></li>
        <li><a href="data_pesanan.php">Data Pesanan</a></li>
        <li><a href="logout_admin.php">Logout</a></li>
    </ul>
</div>

<div class="main-content">
    <div class="content-header">
        <div>
            <h2>Data Menu</h2>
            <p>Kelola data menu makanan dan minuman</p>
        </div>
        <a href="tambah_menu.php" class="btn-tambah">+ Tambah Menu</a>
    </div>

    <div class="data-box">
        <table>
            <thead>
                <tr>
                    <th style="width: 50px; text-align: center;">No.</th>
                    <th>Gambar</th>
                    <th>Nama Menu</th>
                    <th>Harga</th>
                    <th>Kategori</th>
                    <th>Status Order</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $no = 1;
                while($row = mysqli_fetch_assoc($query)) : 
                    // Logika penentuan class badge sesuai isi kolom database
                    $status = isset($row['status_order']) ? $row['status_order'] : 'Open Order';
                    if ($status === 'Close Order') {
                        $class_status = 'status-close';
                    } else {
                        $class_status = 'status-open';
                    }
                ?>
                <tr>
                    <td style="text-align: center;"><?php echo $no++; ?></td>
                    <td>
                        <?php if(!empty($row['gambar'])): ?>
                            <img src="../images/<?php echo $row['gambar']; ?>" class="img-menu">
                        <?php else: ?>
                            <div style="width: 80px; height: 50px; background: #ddd; border-radius:6px; display:flex; align-items:center; justify-content:center; font-size:10px; color:#777;">No Img</div>
                        <?php endif; ?>
                    </td>
                    <td><strong><?php echo htmlspecialchars($row['nama_menu']); ?></strong></td>
                    <td>Rp <?php echo number_format($row['harga'], 0, ',', '.'); ?></td>
                    <td><?php echo $row['kategori']; ?></td>
                    
                    <td><span class="status <?php echo $class_status; ?>"><?php echo $status; ?></span></td>
                    
                    <td>
                        <div class="btn-group">
                            <a href="edit_menu.php?id=<?php echo $row['id_menu']; ?>" class="btn-aksi btn-edit">Edit</a>
                            <a href="hapus_menu.php?id=<?php echo $row['id_menu']; ?>" class="btn-aksi btn-hapus" onclick="return confirm('Apakah Anda yakin ingin menghapus menu ini?')">Hapus</a>
                        </div>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <div class="total-info">
            Total Menu: <?php echo $total_m; ?>
        </div>
    </div>
</div>

</body>
</html>