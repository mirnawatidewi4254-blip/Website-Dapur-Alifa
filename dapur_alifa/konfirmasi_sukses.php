<?php
session_start();
include('koneksi.php');

// 1. Tangkap parameter id_pesanan yang dilempar dari proses checkout
if (!isset($_GET['id_pesanan'])) {
    header("Location: index.php");
    exit();
}

$id_pesanan = mysqli_real_escape_string($conn, $_GET['id_pesanan']);

// 2. Query mengambil data pesanan utama berdasarkan ID Pesanan tersebut
$query_pesanan = mysqli_query($conn, "SELECT * FROM pesanan WHERE id_pesanan = '$id_pesanan'");
$data_pesanan  = mysqli_fetch_assoc($query_pesanan);

// Jika data tidak ditemukan di database
if (!$data_pesanan) {
    echo "Pesanan tidak ditemukan.";
    exit();
}

// Format gabungan tanggal dan jam pengantaran untuk nota sukses
$rencana_antar = date('d-m-Y', strtotime($data_pesanan['tanggal_pengantaran'])) . ' (Jam ' . $data_pesanan['jam_pengantaran'] . ')';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pesanan Berhasil Dikirim - Dapur Alifa</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', -apple-system, sans-serif; }
        body { background-color: #f9f9f9; color: #333; }
        
        /* Navbar Utama */
        nav { background: white; padding: 15px 8%; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .logo { font-size: 18px; font-weight: bold; color: #FF661D; text-decoration: none; display: flex; align-items: center; gap: 10px; }
        .nav-links { list-style: none; display: flex; gap: 20px; align-items: center; }
        .nav-links a { text-decoration: none; color: #555; }

        .container { max-width: 650px; margin: 40px auto; background: white; padding: 35px; border-radius: 8px; box-shadow: 0 0 20px rgba(0,0,0,0.05); }
        .text-center { text-align: center; }
        
        /* Bulatan Centang Hijau */
        .container-centang {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 10px;
            margin-bottom: 20px;
        }
        .lingkaran-centang-sukses {
            width: 80px;
            height: 80px;
            background-color: #25D366; 
            border-radius: 50%;
            position: relative;
            box-shadow: 0 4px 15px rgba(37, 211, 102, 0.2);
        }
        .lingkaran-centang-sukses::after {
            content: '';
            position: absolute;
            left: 32px;
            top: 18px;
            width: 16px;
            height: 32px;
            border: solid white;
            border-width: 0 5px 5px 0;
            transform: rotate(45deg);
        }

        h2 { font-size: 26px; font-weight: bold; color: #333; margin-bottom: 12px; }
        .desc-text { color: #666; font-size: 14px; line-height: 1.6; margin-bottom: 25px; }

        /* Tabel Nota */
        .tabel-nota { width: 100%; margin: 25px 0; border-collapse: collapse; }
        .tabel-nota td { padding: 14px 10px; border-bottom: 1px solid #eee; font-size: 15px; }
        .tabel-nota td.bold { font-weight: bold; color: #222; }
        .text-right { text-align: right; }
        .harga-total { color: #FF661D !important; font-size: 18px; }

        /* Panduan Box */
        .guide-box { background-color: #fff8f5; border: 1px solid #ffd5c2; padding: 18px; border-radius: 8px; font-size: 13.5px; line-height: 1.6; margin-bottom: 25px; }
        .guide-box b { color: #FF661D; }
        .guide-box ul { margin: 8px 0 0 0; padding-left: 20px; color: #555; }

        /* Tombol Aksi */
        .btn-wa { background-color: #25D366; color: white; padding: 14px; display: block; text-align: center; text-decoration: none; border-radius: 6px; font-weight: bold; font-size: 15px; transition: 0.2s; box-shadow: 0 4px 10px rgba(37, 211, 102, 0.15); }
        .btn-wa:hover { background-color: #1ebd59; }

        .link-riwayat { display: inline-block; margin-top: 25px; color: #FF661D; text-decoration: none; font-weight: bold; font-size: 15px; transition: 0.2s; }
        .link-riwayat:hover { color: #e55510; }
        
        footer { text-align: center; color: #aaa; margin-top: 50px; padding-bottom: 30px; font-size: 13px; }
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
        <a href="riwayat.php">Riwayat Pesanan</a>
    </div>
</nav>

<div class="container">
    <div class="text-center">
        <div class="container-centang">
            <div class="lingkaran-centang-sukses"></div>
        </div>
        <h2>Pesanan Berhasil Dikirim!</h2>
        <p class="desc-text">Terima kasih. Bukti pembayaran Anda telah berhasil diunggah dan saat ini sedang diverifikasi oleh Admin Dapur Alifa.</p>
    </div>

    <table class="tabel-nota">
        <tr>
            <td>ID Pesanan:</td>
            <td class="bold text-right">#<?php echo $data_pesanan['id_pesanan']; ?></td>
        </tr>
        <tr>
            <td>Total Bayar:</td>
            <td class="bold text-right harga-total">Rp <?php echo number_format($data_pesanan['total_bayar'], 0, ',', '.'); ?></td>
        </tr>
        <tr>
            <td>Rencana Pengantaran:</td>
            <td class="bold text-right"><?php echo $rencana_antar; ?></td>
        </tr>
    </table>

    <div class="guide-box">
        <b>💡 Bagaimana Cara Memantau Pesanan Anda?</b>
        <ul>
            <li>Status pesanan dapat dipantau berkala pada halaman <b>Riwayat Pesanan</b>.</li>
            <li>Ketika status berubah menjadi <span style="color: #FF661D; font-weight:bold;">"Diproses/Dikirim"</span>, tombol koordinasi dengan kurir/admin akan aktif otomatis di halaman riwayat.</li>
        </ul>
    </div>

    <?php 
        $no_admin = "6289520336530"; 
        $pesan_wa = "Halo Admin Dapur Alifa, saya ingin mengonfirmasi pembayaran untuk pesanan baru saya dengan ID Pesanan: #" . $data_pesanan['id_pesanan'] . ". Mohon segera dicek ya, terima kasih!";
        $link_wa = "https://api.whatsapp.com/send?phone=" . $no_admin . "&text=" . urlencode($pesan_wa);
    ?>
    <a href="<?php echo $link_wa; ?>" class="btn-wa" target="_blank">💬 Hubungi Admin via WhatsApp</a>
    
    <div class="text-center">
        <a href="riwayat.php" class="link-riwayat">Lihat Riwayat Pesanan Saya</a>
    </div>
</div>

<footer>
    © 2026 Dapur Alifa
</footer>

<?php 
// Membersihkan session notif checkout di latar belakang agar memori server tetap bersih
if (isset($_SESSION['notif_sukses_checkout'])) {
    unset($_SESSION['notif_sukses_checkout']);
}
?>

</body>
</html>