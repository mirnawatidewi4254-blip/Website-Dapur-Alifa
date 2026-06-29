<?php
session_start();
include('../koneksi.php');

if (!isset($_SESSION['login_admin_status']) || $_SESSION['role'] !== 'admin') {
    header("Location: login_admin.php");
    exit;
}

// Ambil semua data pesanan dengan JOIN ke tabel users untuk nama customer
$query = mysqli_query($conn, "SELECT pesanan.*, users.nama_lengkap 
                              FROM pesanan 
                              JOIN users ON pesanan.id_user = users.id 
                              ORDER BY tanggal_pesanan DESC");
$total_p = mysqli_num_rows($query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Pesanan - Dapur Alifa</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', sans-serif; }
        body { background-color: #f0f2f5; display: flex; }

        /* Sidebar Konsisten */
        .sidebar { width: 220px; height: 100vh; background: #343a40; color: white; position: fixed; }
        .sidebar-header { padding: 20px; display: flex; align-items: center; background: white; color: #FF661D; font-weight: bold; font-size: 20px; border-bottom: 1px solid #ddd; }
        .sidebar-header img { width: 60px; margin-right: 10px; }
        .menu-list { list-style: none; padding: 10px 0; }
        .menu-list li a { display: flex; align-items: center; padding: 12px 20px; color: #adb5bd; text-decoration: none; }
        .menu-list li a:hover, .menu-list li a.active { background: #FF661D; color: white; border-radius: 0 25px 25px 0; margin-right: 10px; }
        
        .main-content { margin-left: 220px; width: 100%; padding: 20px; }
        
        .content-header { margin-bottom: 20px; }
        .content-header h2 { font-size: 24px; color: #333; }
        .content-header p { color: #777; font-size: 14px; }

        .data-box { background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th { text-align: left; padding: 15px; background: #f8f9fa; color: #333; font-size: 14px; }
        td { padding: 15px; border-bottom: 1px solid #f1f1f1; font-size: 14px; vertical-align: top; }

        /* Style Tombol Download Laporan Excel */
        .btn-download {
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            padding: 8px 16px;
            font-size: 13px;
            font-weight: bold;
            border-radius: 6px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: 0.2s;
        }
        .btn-download:hover {
            background-color: #439a46;
        }

        /* Desain Warna Status Badge untuk 4 Kategori */
        .status { padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: bold; display: inline-block; white-space: nowrap; }
        .status-verifikasi { background: #FDE8E8; color: #E53E3E; } /* Merah */
        .status-proses { background: #FEF3C7; color: #D97706; }     /* Kuning */
        .status-dikirim { background: #E3F2FD; color: #2196F3; }    /* Biru */
        .status-selesai { background: #E8F5E9; color: #4CAF50; }    /* Hijau */

        /* Tombol Aksi */
        .btn-group { display: flex; gap: 5px; }
        .btn-aksi { padding: 6px 15px; border-radius: 5px; text-decoration: none; font-size: 12px; font-weight: bold; border: none; cursor: pointer; text-align: center; }
        
        .btn-detail { background: #FF661D; color: white; }
        .btn-detail:hover { background: #e55a1a; }

        .total-info { margin-top: 20px; color: #777; font-size: 14px; font-weight: bold; }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="sidebar-header">
        <img src="../images/logo.jpg" alt="Logo"> Dapur Alifa
    </div>
    <ul class="menu-list">
        <li><a href="index.php">Dashboard</a></li>
        <li><a href="data_menu.php">Data Menu</a></li>
        <li><a href="data_pesanan.php" class="active">Data Pesanan</a></li>
        <li><a href="logout_admin.php">Logout</a></li>
    </ul>
</div>

<div class="main-content">
    <div class="content-header">
        <h2>Data Pesanan</h2>
        <p>Kelola pesanan yang masuk dari customer</p>
    </div>

    <div class="data-box">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 5px;">
            <h3 style="font-size: 16px; color: #444;">Daftar Transaksi Katering</h3>
            <a href="download_laporan.php" class="btn-download" target="_blank">
                📊 Download Laporan (Excel)
            </a>
        </div>

        <table>
            <thead>
                <tr>
                    <th>No. Pesanan</th>
                    <th>Customer</th>
                    <th style="width: 250px;">Alamat Pengiriman</th>
                    <th>Tanggal</th>
                    <th>Total</th>
                    <th>Metode</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = mysqli_fetch_assoc($query)) : 
                    
                    $status_raw = $row['status_pesanan'];
                    
                    // SINKRONISASI STATUS: Menangani jika status di database tertulis 'Menunggu' atau 'Menunggu Verifikasi'
                    if ($status_raw == 'Menunggu' || $status_raw == 'Menunggu Verifikasi') {
                        $class = 'status-verifikasi';
                        $status_tampil = 'Menunggu Verifikasi';
                    } elseif ($status_raw == 'Diproses') {
                        $class = 'status-proses';
                        $status_tampil = 'Diproses';
                    } elseif ($status_raw == 'Dikirim') {
                        $class = 'status-dikirim';
                        $status_tampil = 'Dikirim';
                    } else {
                        $class = 'status-selesai';
                        $status_tampil = 'Selesai';
                    }
                ?>
                <tr>
                    <td><strong>#<?php echo $row['id_pesanan']; ?></strong></td>
                    <td><?php echo htmlspecialchars($row['nama_lengkap']); ?></td>
                    
                    <td style="color: #555; font-size: 13px; line-height: 1.4; word-wrap: break-word;">
                        <?php 
                        if (!empty($row['alamat_pengiriman'])) {
                            echo htmlspecialchars($row['alamat_pengiriman']); 
                        } else {
                            echo "<span style='color:#bbb; font-style:italic;'>Belum mengisi alamat (Data Lama)</span>";
                        }
                        ?>
                    </td>
                    
                    <td><?php echo date('d M Y', strtotime($row['tanggal_pesanan'])); ?></td>
                    <td style="font-weight: bold; color: #333;">Rp <?php echo number_format($row['total_bayar'], 0, ',', '.'); ?></td>
                    <td><?php echo $row['metode_pembayaran'] ?? 'Transfer BCA'; ?></td>
                    <td><span class="status <?php echo $class; ?>"><?php echo $status_tampil; ?></span></td>
                    <td>
                        <div class="btn-group">
                            <a href="detail_pesanan.php?id=<?php echo $row['id_pesanan']; ?>" class="btn-aksi btn-detail">Detail</a>
                        </div>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <div class="total-info">
            Total Rekaman Pesanan: <?php echo $total_p; ?>
        </div>
    </div>
</div>

</body>
</html>