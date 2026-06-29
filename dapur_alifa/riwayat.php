<?php 
session_start();
include('koneksi.php');

// Pastikan user sudah login
if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit;
}

$id_user = $_SESSION['id_user'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Riwayat Pesanan - Dapur Alifa</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', sans-serif; }
        body { background-color: #f9f9f9; color: #333; }
        
        nav { background: white; padding: 15px 8%; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .logo { font-size: 18px; font-weight: bold; color: #FF661D; text-decoration: none; display: flex; align-items: center; gap: 10px; }
        .nav-links { list-style: none; display: flex; gap: 20px; align-items: center; }
        .nav-links a { text-decoration: none; color: #555; }
        .active { color: #FF661D !important; border-bottom: 2px solid #FF661D; padding-bottom: 5px; }

        .container { max-width: 950px; margin: 50px auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 0 20px rgba(0,0,0,0.05); }
        h2 { text-align: center; margin-bottom: 20px; font-size: 28px; }

        /* Panduan Status Box */
        .status-guide { background: #fff8f5; border: 1px solid #ffd5c2; padding: 15px 20px; border-radius: 8px; margin-bottom: 25px; font-size: 13px; color: #555; line-height: 1.6; }
        .status-guide strong { color: #FF661D; }
        .guide-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-top: 10px; }

        table { width: 100%; border-collapse: collapse; }
        th { padding: 15px; text-align: left; border-bottom: 2px solid #eee; color: #666; background: #fafafa; }
        td { padding: 20px 15px; border-bottom: 1px solid #eee; vertical-align: middle; }

        /* Status Badge */
        .status { padding: 6px 16px; border-radius: 20px; font-size: 13px; font-weight: bold; display: inline-block; text-align: center; white-space: nowrap; }
        .status-verifikasi { background: #FDE8E8; color: #E53E3E; }
        .status-proses { background: #FEF3C7; color: #D97706; }
        .status-dikirim { background: #E3F2FD; color: #2196F3; }
        .status-selesai { background: #E8F5E9; color: #4CAF50; }

        /* Style Tombol Aksi */
        .btn-detail { background-color: #FF661D; color: white; padding: 8px 18px; text-decoration: none; border-radius: 6px; font-size: 14px; font-weight: bold; transition: 0.2s; display: inline-block; }
        .btn-detail:hover { background-color: #e55510; }

        .btn-wa { background-color: #25D366; color: white; padding: 8px 15px; text-decoration: none; border-radius: 6px; font-size: 14px; font-weight: bold; transition: 0.2s; display: inline-block; margin-left: 5px; }
        .btn-wa:hover { background-color: #1ebd59; }

        .total-row { font-weight: bold; font-size: 18px; text-align: right; padding: 20px; color: #444; }
        .btn-center { text-align: center; margin-top: 40px; }
        .btn-back { background-color: #e55510; color: white; padding: 12px 30px; text-decoration: none; border-radius: 6px; font-weight: bold; transition: 0.3s; }
        footer { text-align: center; color: #aaa; margin-top: 50px; padding-bottom: 30px; }
    </style>
</head>
<body>

<nav>
    <a href="index.php" class="logo">
        <img src="images/logo.jpg" alt="" style="width:60px;"> Dapur Alifa
    </a>
    <div class="nav-links">
        <a href="index.php">Beranda</a>
        <a href="keranjang.php">Keranjang</a>
        <a href="riwayat.php" class="active">Riwayat Pesanan</a>
        <a href="logout.php" style="background:#FF661D; color:white; padding:5px 15px; border-radius:5px; text-decoration:none;">Logout</a>
    </div>
</nav>

<div class="container">
    <h2>Riwayat Pesanan</h2>
    
    <div class="status-guide">
        <strong>💡 Informasi Status Pesanan:</strong>
        <div class="guide-grid">
            <div><span class="status status-verifikasi" style="padding: 2px 8px; font-size: 11px;">Menunggu Verifikasi</span> : Admin sedang mengecek bukti pembayaran.</div>
            <div><span class="status status-proses" style="padding: 2px 8px; font-size: 11px;">Diproses</span> : Pembayaran valid, pesanan sedang dimasak.</div>
            <div>
                <span class="status status-dikirim" style="padding: 2px 8px; font-size: 11px;">Dikirim</span> : Kurir sedang mengantar pesanan. 
                <br><span style="color: #25D366; font-weight: bold; font-size: 11px;">(Kamu bisa menghubungi Admin via tombol WhatsApp untuk info kurir)</span>
            </div>
            <div><span class="status status-selesai" style="padding: 2px 8px; font-size: 11px;">Selesai</span> : Pesanan sudah tiba dan transaksi selesai.</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Total Harga</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;
            $total_seluruhnya = 0;
            
            // Pengambilan data berdasarkan ID User yang aktif
            $query = mysqli_query($conn, "SELECT * FROM pesanan WHERE id_user = '$id_user' ORDER BY tanggal_pesanan DESC");
            
            if (mysqli_num_rows($query) > 0) {
                while($row = mysqli_fetch_assoc($query)) {
                    $total_seluruhnya += $row['total_bayar'];
                    $status_raw = $row['status_pesanan'];
                    
                    // SINKRONISASI: Menyesuaikan string database dengan kelas CSS
                    if ($status_raw == 'Menunggu' || $status_raw == 'Menunggu Verifikasi') {
                        $status_class = 'status-verifikasi';
                        $status_tampil = 'Menunggu Verifikasi';
                    } elseif ($status_raw == 'Diproses') {
                        $status_class = 'status-proses';
                        $status_tampil = 'Diproses';
                    } elseif ($status_raw == 'Dikirim') {
                        $status_class = 'status-dikirim';
                        $status_tampil = 'Dikirim';
                    } else {
                        $status_class = 'status-selesai';
                        $status_tampil = 'Selesai';
                    }
            ?>
            <tr>
                <td><?php echo $no++; ?></td>
                <td><?php echo date('d M Y', strtotime($row['tanggal_pesanan'])); ?></td>
                <td><strong>Rp <?php echo number_format($row['total_bayar'], 0, ',', '.'); ?></strong></td>
                <td>
                    <span class="status <?php echo $status_class; ?>">
                        <?php echo htmlspecialchars($status_tampil); ?>
                    </span>
                </td>
                <td>
                    <a href="detail_pesanan.php?id=<?php echo $row['id_pesanan']; ?>" class="btn-detail">Detail</a>

                    <?php if ($status_raw == 'Dikirim') : ?>
                        <?php 
                            $no_admin = "6289520336530"; 
                            $pesan_wa = "Halo Admin Dapur Alifa, saya ingin bertanya mengenai pesanan saya dengan nomor ID: #" . $row['id_pesanan'] . " yang statusnya saat ini sedang *DIKIRIM*. Bisa tolong infokan nomor kurirnya?";
                            $link_wa = "https://api.whatsapp.com/send?phone=" . $no_admin . "&text=" . urlencode($pesan_wa);
                        ?>
                        <a href="<?php echo $link_wa; ?>" target="_blank" class="btn-wa">Hubungi Admin</a>
                    <?php endif; ?>
                </td>
            </tr>
            <?php 
                } 
            } else {
                echo "<tr><td colspan='5' style='text-align:center; padding: 30px;'>Belum ada riwayat pesanan. Yuk pesan sekarang!</td></tr>";
            }
            ?>
        </tbody>
    </table>

    <div class="total-row">
        <span style="margin-right: 30px;">Total Riwayat Belanja</span>
        <span>Rp <?php echo number_format($total_seluruhnya, 0, ',', '.'); ?></span>
    </div>

    <div class="btn-center">
        <a href="index.php" class="btn-back">Kembali ke Menu Utama</a>
    </div>
</div>

<footer>
    © 2026 Dapur Alifa
</footer>

</body>
</html>