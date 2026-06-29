<?php
session_start();
include('koneksi.php');

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: riwayat.php");
    exit;
}

$id_pesanan = mysqli_real_escape_string($conn, $_GET['id']);
$query_pesanan = mysqli_query($conn, "SELECT * FROM pesanan WHERE id_pesanan = '$id_pesanan'");
$data_pesanan = mysqli_fetch_assoc($query_pesanan);

if (!$data_pesanan) {
    header("Location: riwayat.php");
    exit;
}

$status_asli = strtolower($data_pesanan['status_pesanan'] ?? 'diproses');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Detail Pesanan - Dapur Alifa</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', sans-serif; }
        body { background-color: #f9f9f9; color: #333; }
        
        nav { background: white; padding: 15px 8%; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .logo { font-size: 18px; font-weight: bold; color: #FF661D; text-decoration: none; display: flex; align-items: center; gap: 10px; }
        .nav-links { list-style: none; display: flex; gap: 20px; align-items: center; }
        .nav-links a { text-decoration: none; color: #555; font-size: 15px; }

        .container { max-width: 800px; margin: 40px auto; padding: 0 20px; }
        h1 { margin-bottom: 25px; font-size: 24px; color: #2c3e50; text-align: center; }

        .detail-box { background: white; border-radius: 8px; border: 1px solid #eee; padding: 30px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        
        .info-header { border-bottom: 2px dashed #eee; padding-bottom: 15px; margin-bottom: 20px; display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; line-height: 1.6; }
        .status-badge { display: inline-block; padding: 6px 16px; border-radius: 20px; font-weight: bold; font-size: 14px; text-align: center; }
        
        /* Warna Lencana Status Sesuai Referensi Gambar */
        .status-diproses { background-color: #d0ecff; color: #007bff; } 
        .status-selesai { background-color: #e2f6e9; color: #28a745; } 

        .alamat-box { background: #fff5f0; padding: 15px; border-radius: 8px; border-left: 4px solid #FF661D; margin-bottom: 25px; }
        .alamat-title { color: #FF661D; font-weight: bold; font-size: 15px; margin-bottom: 6px; }
        .alamat-text { color: #555; font-size: 14px; line-height: 1.5; }
        .waktu-text { font-size: 13px; color: #666; margin-top: 8px; border-top: 1px dashed #ffdcd0; padding-top: 6px; }

        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th { text-align: left; padding: 10px 0; border-bottom: 1px solid #ddd; color: #777; font-size: 14px; }
        td { padding: 15px 0; border-bottom: 1px solid #eee; vertical-align: middle; }
        
        .menu-item { display: flex; align-items: center; gap: 12px; }
        .menu-item img { width: 60px; height: 45px; border-radius: 4px; object-fit: cover; border: 1px solid #eee; }

        .flex-bottom { display: flex; justify-content: space-between; align-items: flex-start; margin-top: 25px; gap: 30px; }
        .catatan-box { flex: 1; background: #fafafa; border: 1px solid #eee; padding: 12px; border-radius: 6px; font-size: 14px; color: #666; min-height: 90px; }
        .total-box { text-align: right; min-width: 220px; }
        .grand-total { font-size: 24px; font-weight: bold; color: #FF661D; margin-bottom: 15px; }

        .bukti-img { max-width: 160px; margin-top: 8px; border: 1px solid #ddd; border-radius: 6px; display: inline-block; cursor: pointer; transition: 0.2s; }
        .bukti-img:hover { opacity: 0.85; transform: scale(1.02); }

        .center-btn { text-align: center; margin-top: 30px; }
        .btn-kembali { background: #FF661D; color: white; text-decoration: none; padding: 11px 30px; border-radius: 6px; font-weight: bold; display: inline-block; }

        .modal { display: none; position: fixed; z-index: 9999; padding-top: 60px; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.8); backdrop-filter: blur(3px); }
        .modal-content { margin: auto; display: block; max-width: 450px; width: 85%; border-radius: 8px; animation: zoomIn 0.3s ease; }
        .close-btn { position: absolute; top: 20px; right: 35px; color: #fff; font-size: 40px; font-weight: bold; cursor: pointer; }
    </style>
</head>
<body>

<nav>
    <a href="index.php" class="logo">Dapur Alifa</a>
    <div class="nav-links">
        <a href="index.php">Beranda</a>
        <a href="keranjang.php">Keranjang</a>
        <a href="riwayat.php" style="color: #FF661D; font-weight: bold;">Riwayat Pesanan</a>
    </div>
</nav>

<div class="container">
    <h1>Detail Pesanan</h1>

    <div class="detail-box">
        <div class="info-header">
            <div><strong>No Pesanan:</strong><br>#<?php echo $data_pesanan['id_pesanan']; ?></div>
            <div><strong>Tanggal Transaksi:</strong><br><?php echo date('d M Y', strtotime($data_pesanan['tanggal_pesanan'])); ?></div>
            <div>
                <strong>Status Katering:</strong><br>
                <?php 
                if ($status_asli == 'selesai') {
                    echo '<span class="status-badge status-selesai">Selesai</span>';
                } else {
                    echo '<span class="status-badge status-diproses">Diproses</span>';
                }
                ?>
            </div>
        </div>

        <div class="alamat-box">
            <div class="alamat-title">📍 Alamat Pengiriman Katering Lengkap</div>
            <div class="alamat-text">
                <?php echo !empty($data_pesanan['alamat_pengiriman']) ? htmlspecialchars($data_pesanan['alamat_pengiriman']) : "Alamat tidak diisi secara lengkap."; ?>
            </div>
            <div class="waktu-text">
                Rencana Antar: <strong><?php echo isset($data_pesanan['tanggal_pengantaran']) ? date('d M Y', strtotime($data_pesanan['tanggal_pengantaran'])) : '-'; ?></strong> pada Jam <strong><?php echo $data_pesanan['jam_pengantaran'] ?? '-'; ?> WIB</strong>
            </div>
        </div>

        <p style="font-weight:bold; margin-bottom:15px;">Daftar Menu Hidangan</p>
        <table>
            <thead>
                <tr>
                    <th>Produk</th>
                    <th style="text-align: right;">Harga</th>
                    <th style="text-align: center;">Jumlah</th>
                    <th style="text-align: right;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // --- DIKOREKSI: Menambahkan m.harga ke dalam SELECT SQL ---
                $query_detail = mysqli_query($conn, "SELECT pd.*, m.nama_menu, m.harga, m.gambar 
                                                     FROM pesanan_detail pd 
                                                     JOIN menu m ON pd.id_menu = m.id_menu 
                                                     WHERE pd.id_pesanan = '$id_pesanan'");
                
                while($item = mysqli_fetch_assoc($query_detail)) {
                    // --- DIKOREKSI: Menghitung subtotal menggunakan harga asli dari tabel menu ($item['harga']) ---
                    $subtotal = $item['harga'] * $item['jumlah'];
                ?>
                <tr>
                    <td>
                        <div class="menu-item">
                            <img src="images/<?php echo !empty($item['gambar']) ? $item['gambar'] : 'default.jpg'; ?>" alt="Menu">
                            <span><?php echo htmlspecialchars($item['nama_menu']); ?></span>
                        </div>
                    </td>
                    <td style="text-align: right;">Rp <?php echo number_format($item['harga'], 0, ',', '.'); ?></td>
                    <td style="text-align: center;"><?php echo $item['jumlah']; ?>x</td>
                    <td style="text-align: right;"><strong>Rp <?php echo number_format($subtotal, 0, ',', '.'); ?></strong></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>

        <div class="flex-bottom">
            <div style="flex: 1;">
                <strong>Catatan Khusus Pesanan:</strong>
                <div class="catatan-box">
                    <?php echo !empty($data_pesanan['catatan']) ? nl2br(htmlspecialchars($data_pesanan['catatan'])) : 'Tidak ada catatan tambahan.'; ?>
                </div>
            </div>
            
            <div class="total-box">
                <span style="color:#777; font-size:14px;">Total Pembayaran Anda:</span>
                <div class="grand-total">Rp <?php echo number_format($data_pesanan['total_bayar'], 0, ',', '.'); ?></div>
                <div>Metode: <strong><?php echo htmlspecialchars($data_pesanan['metode_pembayaran'] ?? 'Transfer Bank'); ?></strong></div>
                
                <?php 
                if(!empty($data_pesanan['bukti_pembayaran'])) { 
                    $foto_bukti = $data_pesanan['bukti_pembayaran'];
                    $path_gambar = "images/bukti_bayar/" . $foto_bukti;
                ?>
                    <div style="margin-top: 15px; font-weight: bold; font-size: 14px;">Bukti Pembayaran Sah:</div>
                    <img src="<?php echo $path_gambar; ?>" class="bukti-img" alt="Bukti Transfer" onclick="openModal(this.src)">
                <?php } ?>
            </div>
        </div>
    </div>

    <div class="center-btn">
        <a href="riwayat.php" class="btn-kembali">← Kembali ke Riwayat Pesanan</a>
    </div>
</div>

<div id="myModal" class="modal" onclick="closeModal()">
    <span class="close-btn" onclick="closeModal()">&times;</span>
    <img class="modal-content" id="imgTarget" onclick="event.stopPropagation();">
</div>

<script>
function openModal(src) {
    document.getElementById("myModal").style.display = "block";
    document.getElementById("imgTarget").src = src;
}
function closeModal() {
    document.getElementById("myModal").style.display = "none";
}
document.addEventListener('keydown', function(event) {
    if (event.key === "Escape") closeModal();
});
</script>

</body>
</html>