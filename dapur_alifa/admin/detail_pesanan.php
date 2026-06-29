<?php
session_start();

// Validasi apakah admin sudah login dengan kunci session yang benar
if (!isset($_SESSION['login_admin_status']) || $_SESSION['role'] !== 'admin') {
    // Jika tidak valid, lempar kembali ke halaman login admin
    header("Location: login_admin.php");
    exit();
}

// Koneksi ke database (Pastikan file koneksi.php kamu sudah benar jalurnya)
include '../koneksi.php'; 

// Pengaman jika ID tidak ada di URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<script>location='data_pesanan.php';</script>";
    exit;
}

$id_pesanan = $_GET['id'];

// 1. Ambil info utama pesanan & customer
$query_p = mysqli_query($conn, "SELECT pesanan.*, users.nama_lengkap 
                                FROM pesanan 
                                JOIN users ON pesanan.id_user = users.id 
                                WHERE id_pesanan = '$id_pesanan'");
$data_p = mysqli_fetch_assoc($query_p);

// 2. Ambil rincian item (JOIN ke tabel menu) untuk tabel HTML di bawah
$query_d = mysqli_query($conn, "SELECT pesanan_detail.*, menu.nama_menu 
                                FROM pesanan_detail 
                                JOIN menu ON pesanan_detail.id_menu = menu.id_menu 
                                WHERE id_pesanan = '$id_pesanan'");

// 3. REVISI DOSEN: Logika Update Status Baru (Bersih dari urusan logika stok/restock)
if (isset($_POST['simpan_status'])) {
    $status_baru = $_POST['status_pesanan'];
    
    // Jalankan update status utama saja di database
    mysqli_query($conn, "UPDATE pesanan SET status_pesanan = '$status_baru' WHERE id_pesanan = '$id_pesanan'");
    
    echo "<script>alert('Status berhasil diperbarui menjadi " . $status_baru . "!'); window.location='detail_pesanan.php?id=$id_pesanan';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pesanan #<?php echo $id_pesanan; ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', sans-serif; }
        body { background-color: #f0f2f5; display: flex; padding: 20px; justify-content: center; }
        .container { width: 95%; max-width: 1100px; }
        .header-area { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .btn-kembali { padding: 8px 15px; background: white; border: 1px solid #ddd; text-decoration: none; color: #333; border-radius: 5px; }

        .detail-grid { display: grid; grid-template-columns: 1.5fr 1fr; gap: 20px; }
        .card { background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); margin-bottom: 20px; }
        .card h3 { font-size: 16px; margin-bottom: 15px; border-bottom: 1px solid #eee; padding-bottom: 10px; }

        .info-row { display: flex; margin-bottom: 10px; font-size: 14px; }
        .info-label { width: 150px; color: #666; }
        .info-value { font-weight: bold; color: #333; }
        
        /* Pewarnaan Status Badge Tersinkronisasi */
        .status-badge { padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: bold; display: inline-block; white-space: nowrap; }
        .status-verifikasi { background: #FDE8E8; color: #E53E3E; }
        .status-proses { background: #FEF3C7; color: #D97706; }
        .status-dikirim { background: #E3F2FD; color: #2196F3; }
        .status-selesai { background: #E8F5E9; color: #4CAF50; }

        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { text-align: left; padding: 12px; background: #f8f9fa; font-size: 13px; }
        td { padding: 12px; border-bottom: 1px solid #eee; font-size: 14px; }
        .total-row { font-weight: bold; font-size: 16px; background: #f9f9f9; }

        .img-bukti { width: 100%; border-radius: 8px; border: 1px solid #ddd; margin-bottom: 15px; max-height: 300px; object-fit: contain; cursor: pointer; transition: 0.3s; }
        .img-bukti:hover { opacity: 0.8; }
        .btn-lihat { display: block; width: 100%; text-align: center; background: #FF661D; color: white; padding: 10px; text-decoration: none; border-radius: 5px; font-weight: bold; cursor: pointer; }
        
        select { width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd; margin-bottom: 15px; background: #fff; font-size: 14px; }
        .btn-simpan { width: 100%; padding: 12px; background: #FF661D; color: white; border: none; border-radius: 5px; font-weight: bold; cursor: pointer; }
        .btn-simpan:hover { background: #e55a1a; }

        /* CSS STYLING MODAL UNTUK GAMBAR FULL */
        .modal-gambar { display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.85); justify-content: center; align-items: center; flex-direction: column; }
        .modal-gambar img { max-width: 85%; max-height: 80%; border-radius: 6px; box-shadow: 0 4px 25px rgba(0,0,0,0.6); }
        .close-btn { position: absolute; top: 20px; right: 35px; color: #fff; font-size: 45px; font-weight: bold; cursor: pointer; transition: 0.3s; user-select: none; }
        .close-btn:hover { color: #FF661D; }
    </style>
</head>
<body>

<div class="container">
    <div class="header-area">
        <h2>Detail Pesanan #<?php echo $id_pesanan; ?></h2>
        <a href="data_pesanan.php" class="btn-kembali">← Kembali</a>
    </div>

    <div class="detail-grid">
        <div class="left-col">
            <div class="card">
                <div class="info-row">
                    <div class="info-label">Customer</div>
                    <div class="info-value">: <?php echo isset($data_p['nama_lengkap']) ? htmlspecialchars($data_p['nama_lengkap']) : '-'; ?></div>
                </div>
                <div class="info-row">
                    <div class="info-label">Tanggal</div>
                    <div class="info-value">: <?php echo isset($data_p['tanggal_pesanan']) ? date('d M Y - H:i', strtotime($data_p['tanggal_pesanan'])) : '-'; ?> WIB</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Status Saat Ini</div>
                    <div class="info-value">: 
                        <?php 
                        // Logika untuk menampilkan badge dengan warna yang tepat
                        $status_sekarang = isset($data_p['status_pesanan']) ? $data_p['status_pesanan'] : 'Menunggu Verifikasi';
                        
                        if ($status_sekarang == 'Menunggu Verifikasi') {
                            $class_badge = 'status-verifikasi';
                        } elseif ($status_sekarang == 'Diproses') {
                            $class_badge = 'status-proses';
                        } elseif ($status_sekarang == 'Dikirim') {
                            $class_badge = 'status-dikirim';
                        } else {
                            $class_badge = 'status-selesai';
                        }
                        ?>
                        <span class="status-badge <?php echo $class_badge; ?>"><?php echo $status_sekarang; ?></span>
                    </div>
                </div>
            </div>

            <div class="card">
                <h3>Rincian Pesanan</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Menu</th>
                            <th>Harga</th>
                            <th>Qty</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if ($query_d && mysqli_num_rows($query_d) > 0) :
                            mysqli_data_seek($query_d, 0); 
                            while($dt = mysqli_fetch_assoc($query_d)) : 
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($dt['nama_menu']); ?></td>
                            <td>Rp <?php echo number_format($dt['harga'], 0, ',', '.'); ?></td>
                            <td><?php echo $dt['jumlah']; ?></td>
                            <td>Rp <?php echo number_format($dt['harga'] * $dt['jumlah'], 0, ',', '.'); ?></td>
                        </tr>
                        <?php 
                            endwhile; 
                        endif;
                        ?>
                        <tr class="total-row">
                            <td colspan="3" style="text-align:right;">Total Pembayaran</td>
                            <td style="color: #FF661D;">Rp <?php echo isset($data_p['total_bayar']) ? number_format($data_p['total_bayar'], 0, ',', '.') : '0'; ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="right-col">
            <div class="card">
                <h3>Bukti Pembayaran</h3>
                <?php if(!empty($data_p['bukti_pembayaran'])): ?>
                    <img src="../images/bukti_bayar/<?php echo $data_p['bukti_pembayaran']; ?>" class="img-bukti" onclick="bukaModal()">
                    <div class="btn-lihat" onclick="bukaModal()">Lihat Gambar Full</div>
                <?php else: ?>
                    <div style="text-align:center; padding: 20px; color: #999; border: 2px dashed #ccc; border-radius: 8px;">Belum upload bukti</div>
                <?php endif; ?>
            </div>

            <div class="card">
                <h3>Ubah Status Pesanan</h3>
                <form action="" method="POST">
                    <select name="status_pesanan">
                        <option value="Menunggu Verifikasi" <?php if(isset($data_p['status_pesanan']) && $data_p['status_pesanan']=='Menunggu Verifikasi') echo 'selected'; ?>>Menunggu Verifikasi</option>
                        <option value="Diproses" <?php if(isset($data_p['status_pesanan']) && $data_p['status_pesanan']=='Diproses') echo 'selected'; ?>>Diproses</option>
                        <option value="Dikirim" <?php if(isset($data_p['status_pesanan']) && $data_p['status_pesanan']=='Dikirim') echo 'selected'; ?>>Dikirim</option>
                        <option value="Selesai" <?php if(isset($data_p['status_pesanan']) && $data_p['status_pesanan']=='Selesai') echo 'selected'; ?>>Selesai</option>
                    </select>
                    <button type="submit" name="simpan_status" class="btn-simpan">Simpan Perubahan</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div id="modalMurni" class="modal-gambar">
    <span class="close-btn" onclick="tutupModal()">&times;</span>
    <img src="../images/bukti_bayar/<?php echo isset($data_p['bukti_pembayaran']) ? $data_p['bukti_pembayaran'] : ''; ?>" alt="Bukti Pembayaran Full">
    <p style="color: #bbb; margin-top: 15px; font-size: 14px;">Tekan tanda (X) atau tombol ESC untuk keluar</p>
</div>

<script>
function bukaModal() {
    document.getElementById("modalMurni").style.display = "flex";
}
function tutupModal() {
    document.getElementById("modalMurni").style.display = "none";
}

window.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
        tutupModal();
    }
});
</script>

</body>
</html>