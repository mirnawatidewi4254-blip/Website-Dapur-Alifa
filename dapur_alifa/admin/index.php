<?php
session_start();
include('../koneksi.php');

// Kunci zona waktu server ke Barat Indonesia (WIB)
date_default_timezone_set('Asia/Jakarta');

// =============================================================
// PERBAIKAN LOGIKA: Proteksi Admin disesuaikan dengan login_admin.php yang baru
// =============================================================
if (!isset($_SESSION['login_admin_status']) || $_SESSION['role'] !== 'admin') {
    header("Location: login_admin.php");
    exit;
}

// Mengambil id_admin yang baru tanpa mengganggu id_user milik customer
$id_admin = $_SESSION['id_admin'];

// 2. Ambil data statistik dari Database
// Total Pesanan
$q_pesanan = mysqli_query($conn, "SELECT COUNT(*) as total FROM pesanan");
$total_pesanan = mysqli_fetch_assoc($q_pesanan)['total'] ?? 0;

// Total Pendapatan (Hanya yang statusnya Selesai)
$q_pendapatan = mysqli_query($conn, "SELECT SUM(total_bayar) as total FROM pesanan WHERE status_pesanan='Selesai'");
$fetch_pendapatan = mysqli_fetch_assoc($q_pendapatan);
$total_pendapatan = $fetch_pendapatan['total'] ?? 0;

// Total Menu yang tersedia
$q_menu = mysqli_query($conn, "SELECT COUNT(*) as total FROM menu");
$total_menu = mysqli_fetch_assoc($q_menu)['total'] ?? 0;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Dapur Alifa</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', sans-serif; }
        body { background-color: #f0f2f5; display: flex; }

        /* Sidebar */
        .sidebar { width: 220px; height: 100vh; background: #343a40; color: white; position: fixed; }
        .sidebar-header { padding: 20px; display: flex; align-items: center; background: white; color: #FF661D; font-weight: bold; font-size: 20px; border-bottom: 1px solid #ddd; }
        .sidebar-header img { width: 60px; margin-right: 10px; }
        .menu-list { list-style: none; padding: 10px 0; }
        .menu-list li a { display: flex; align-items: center; padding: 12px 20px; color: #adb5bd; text-decoration: none; transition: 0.3s; }
        .menu-list li a:hover, .menu-list li a.active { background: #FF661D; color: white; border-radius: 0 25px 25px 0; margin-right: 10px; }
        
        /* Content Area */
        .main-content { margin-left: 220px; width: 100%; padding: 20px; }
        header { display: flex; justify-content: space-between; align-items: center; background: white; padding: 15px 30px; border-radius: 8px; margin-bottom: 25px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        
        /* Stats Cards */
        .stats-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 30px; }
        .card { background: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); display: flex; align-items: center; gap: 20px; border: 1px solid #eee; }
        .icon-box { width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 24px; color: white; }
        .bg-orange { background: #FF9800; } .bg-green { background: #4CAF50; } .bg-blue { background: #2196F3; }
        .card-info h3 { font-size: 14px; color: #777; margin-bottom: 5px; }
        .card-info p { font-size: 22px; font-weight: bold; color: #333; }

        /* Table Area */
        .data-box { background: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th { text-align: left; padding: 15px; background: #f8f9fa; color: #666; font-size: 13px; text-transform: uppercase; }
        td { padding: 15px; border-bottom: 1px solid #f1f1f1; font-size: 14px; vertical-align: top; }
        
        /* Tombol Download Laporan */
        .btn-download {
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            padding: 6px 14px;
            font-size: 13px;
            font-weight: bold;
            border-radius: 4px;
            display: flex;
            align-items: center;
            gap: 5px;
            transition: 0.2s;
        }
        .btn-download:hover {
            background-color: #439a46;
        }
        
        /* Status Labels */
        .status { padding: 5px 12px; border-radius: 20px; font-size: 11px; font-weight: bold; text-transform: capitalize; display: inline-block; }
        .status-menunggu { background: #FFF3E0; color: #FF9800; }
        .status-proses { background: #E3F2FD; color: #2196F3; }
        .status-selesai { background: #E8F5E9; color: #4CAF50; }
        .status-batal { background: #FADBD8; color: #E74C3C; }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="sidebar-header">
        <img src="../images/logo.jpg" alt="Logo"> Dapur Alifa
    </div>
    <ul class="menu-list">
        <li><a href="index.php" class="active">Dashboard</a></li>
        <li><a href="data_menu.php">Data Menu</a></li>
        <li><a href="data_pesanan.php">Data Pesanan</a></li>
        <li><a href="logout_admin.php">Logout</a></li>
    </ul>
</div>

<div class="main-content">
    <header>
        <h2>Dashboard</h2>
        <div class="admin-profile">
            <?php echo date('d F Y'); ?> | Admin <strong><?php echo isset($_SESSION['admin_nama']) ? $_SESSION['admin_nama'] : 'Admin'; ?></strong> ⌵
        </div>
    </header>

    <div class="stats-grid">
        <div class="card">
            <div class="icon-box bg-orange">🛒</div>
            <div class="card-info">
                <h3>Total Pesanan</h3>
                <p><?php echo $total_pesanan; ?> Pesanan</p>
            </div>
        </div>
        <div class="card">
            <div class="icon-box bg-green">Rp</div>
            <div class="card-info">
                <h3>Total Pendapatan</h3>
                <p>Rp <?php echo number_format($total_pendapatan, 0, ',', '.'); ?></p>
            </div>
        </div>
        <div class="card">
            <div class="icon-box bg-blue">☰</div>
            <div class="card-info">
                <h3>Total Menu</h3>
                <p><?php echo $total_menu; ?> Menu</p>
            </div>
        </div>
    </div>

    <div class="data-box">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
            <h4>Pesanan Terbaru</h4>
            <div style="display: flex; align-items: center; gap: 15px;">
                <a href="download_laporan.php" class="btn-download" target="_blank">
                    📊 Download Laporan
                </a>
                <a href="data_pesanan.php" style="color: #FF661D; text-decoration: none; font-size: 14px; font-weight: bold;">Lihat Semua</a>
            </div>
        </div>
        <table>
            <thead>
                <tr>
                    <th>No. Pesanan</th>
                    <th>Customer ID</th>
                    <th>Menu yang Dipesan</th>
                    <th style="width: 220px;">Alamat Pengiriman</th>
                    <th>Rencana Antar</th>
                    <th>Total Bayar</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $query_pesanan_baru = mysqli_query($conn, "SELECT * FROM pesanan ORDER BY id_pesanan DESC LIMIT 10");
                
                if($query_pesanan_baru && mysqli_num_rows($query_pesanan_baru) > 0) {
                    while($row = mysqli_fetch_assoc($query_pesanan_baru)) {
                        
                        $id_pesanan = $row['id_pesanan'];
                        
                        $daftar_item_katering = [];
                        $query_detail = mysqli_query($conn, "SELECT pesanan_detail.*, menu.nama_menu FROM pesanan_detail 
                                                             JOIN menu ON pesanan_detail.id_menu = menu.id_menu 
                                                             WHERE pesanan_detail.id_pesanan = '$id_pesanan'");
                        
                        if ($query_detail) {
                            while($item = mysqli_fetch_assoc($query_detail)) {
                                $daftar_item_katering[] = $item['nama_menu'] . " (x" . $item['jumlah'] . ")";
                            }
                        }
                        $text_menu_dipesan = implode(", ", $daftar_item_katering);

                        $status_pesanan_text = $row['status_pesanan'];
                        if($status_pesanan_text == 'Diproses' || $status_pesanan_text == 'Proses') { 
                            $status_class = 'status-proses'; 
                        } elseif($status_pesanan_text == 'Selesai') { 
                            $status_class = 'status-selesai'; 
                        } elseif($status_pesanan_text == 'Dibatalkan') { 
                            $status_class = 'status-batal'; 
                        } else {
                            $status_class = 'status-menunggu';
                        }
                ?>
                <tr>
                    <td style="font-weight: bold;">#<?php echo $id_pesanan; ?></td>
                    <td>User ID: <?php echo $row['id_user']; ?></td>
                    
                    <td style="max-width: 200px; color: #555; font-size: 13px; line-height: 1.4; word-wrap: break-word;">
                        <?php echo !empty($text_menu_dipesan) ? $text_menu_dipesan : "<span style='color:#ccc;'>Tidak ada rincian</span>"; ?>
                    </td>

                    <td style="color: #555; font-size: 13px; line-height: 1.4; word-wrap: break-word;">
                        <?php echo !empty($row['alamat_pengiriman']) ? htmlspecialchars($row['alamat_pengiriman']) : "<span style='color:#bbb; font-style:italic;'>Tidak ada alamat</span>"; ?>
                    </td>
                    
                    <td>
                        <strong><?php echo date('d/m/Y', strtotime($row['tanggal_pengantaran'])); ?></strong><br>
                        <small style="color: #FF661D; font-weight: bold;"><?php echo $row['jam_pengantaran']; ?> WIB</small>
                    </td>
                    
                    <td style="font-weight: bold;">Rp <?php echo number_format($row['total_bayar'], 0, ',', '.'); ?></td>
                    <td><span class="status <?php echo $status_class; ?>"><?php echo $status_pesanan_text; ?></span></td>
                </tr>
                <?php 
                    }
                } else {
                    echo "<tr><td colspan='7' style='text-align:center; color:#999; padding: 20px;'>Belum ada pesanan terbaru masuk.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>