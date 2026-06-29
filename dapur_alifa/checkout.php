<?php
session_start();
include('koneksi.php');

// Kunci zona waktu default ke Barat Indonesia (WIB)
date_default_timezone_set('Asia/Jakarta');

if (!isset($_SESSION['login'])) { header("Location: login.php"); exit; }

$id_user = $_SESSION['id_user'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Checkout - Dapur Alifa</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', sans-serif; }
        body { background-color: #f9f9f9; color: #333; }
        
        /* Navbar */
        nav { background: white; padding: 15px 8%; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .logo { font-size: 18px; font-weight: bold; color: #FF661D; text-decoration: none; display: flex; align-items: center; gap: 10px; }
        .nav-links { list-style: none; display: flex; gap: 20px; }
        .nav-links a { text-decoration: none; color: #555; }

        .container { max-width: 1100px; margin: 40px auto; padding: 0 20px; }
        h1 { text-align: center; margin-bottom: 30px; }

        .checkout-grid { display: grid; grid-template-columns: 1.5fr 1fr; gap: 30px; }
        .box { background: white; border-radius: 8px; border: 1px solid #eee; overflow: hidden; margin-bottom: 20px; }
        .box-header { padding: 15px 20px; background: #fafafa; border-bottom: 1px solid #eee; font-weight: bold; font-size: 18px; }
        .box-body { padding: 20px; }

        /* Tabel Rincian */
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th { text-align: left; padding: 10px; border-bottom: 2px solid #eee; color: #666; }
        td { padding: 15px 10px; border-bottom: 1px solid #eee; }
        .menu-item { display: flex; align-items: center; gap: 10px; }
        .menu-item img { width: 60px; height: 45px; border-radius: 4px; object-fit: cover; }

        /* Form Catatan & Alamat */
        .form-group-custom { margin-bottom: 15px; }
        .form-group-custom label { display: block; font-weight: bold; margin-bottom: 8px; font-size: 14px; color: #444; }
        textarea { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px; background: #fff; }
        textarea.catatan-input { resize: none; height: 70px; }
        textarea.alamat-input { resize: vertical; min-height: 90px; }

        /* Sisi Kanan (Total & Pembayaran) */
        .total-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; }
        .total-label { font-size: 18px; color: #666; }
        .total-price { font-size: 24px; font-weight: bold; color: #FF661D; }

        /* Styling List Logo Bank */
        .payment-methods { list-style: none; margin: 20px 0; }
        .payment-item { display: flex; align-items: center; gap: 15px; padding: 15px; background: #f8f9fa; border: 1px solid #edf2f7; border-radius: 6px; margin-bottom: 12px; }
        .bank-logo { width: 75px; height: auto; object-fit: contain; background: white; padding: 4px; border-radius: 4px; border: 1px solid #e2e8f0; }
        .bank-detail { font-size: 14px; color: #333; line-height: 1.4; }
        .bank-info { font-size: 12px; color: #666; margin-top: 2px; }

        /* Form Pengantaran Baru */
        .delivery-section { margin-top: 15px; padding: 15px; background: #fffcf5; border: 1px solid #f3ebd3; border-radius: 6px; text-align: left; }
        .delivery-section label { display: block; font-weight: bold; font-size: 14px; margin-bottom: 5px; color: #555; }
        .delivery-group { margin-bottom: 12px; }
        .delivery-group input, .delivery-group select { width: 100%; padding: 8px 12px; border: 1px solid #ccc; border-radius: 4px; font-size: 14px; background: white; }

        .upload-section { margin-top: 20px; padding-top: 20px; border-top: 1px dotted #ccc; }
        .upload-section label { display: block; font-weight: bold; margin-bottom: 10px; }
        .upload-section input[type="file"] { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; background: #fff; font-size: 14px; }
        
        /* Tombol */
        .btn-group { grid-column: 1 / -1; display: flex; justify-content: center; gap: 20px; margin-top: 10px; }
        .btn { padding: 12px 30px; border-radius: 6px; text-decoration: none; font-weight: bold; cursor: pointer; border: none; font-size: 16px; transition: 0.2s; }
        .btn-orange { background: #FF884D; color: white; }
        .btn-orange:hover { background: #ff732e; }
        .btn-green { background: #5CB85C; color: white; }
        .btn-green:hover { background: #4cae4c; }

        footer { text-align: center; color: #aaa; margin-top: 60px; padding-bottom: 30px; }
    </style>
</head>
<body>

<nav>
    <a href="index.php" class="logo">
        <img src="images/logo.jpg" alt="" style="width:40px;"> Dapur Alifa
    </a>
    <div class="nav-links">
        <a href="index.php">Beranda</a>
        <a href="keranjang.php">Keranjang</a>
        <a href="riwayat.php">Riwayat Pesanan</a>
        <a href="logout.php" style="background:#FF661D; color:white; padding:5px 15px; border-radius:5px;">Logout</a>
    </div>
</nav>

<div class="container">
    <h1>Checkout</h1>

    <form action="proses_checkout.php" method="POST" enctype="multipart/form-data">
        <div class="checkout-grid">
            
            <div class="left-column">
                <div class="box">
                    <div class="box-header">Rincian Pesanan</div>
                    <div class="box-body">
                        <table>
                            <thead>
                                <tr>
                                    <th>Menu</th>
                                    <th>Harga</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $total_akhir = 0;
                                $query = mysqli_query($conn, "SELECT keranjang.*, menu.nama_menu, menu.harga, menu.gambar 
                                                            FROM keranjang JOIN menu ON keranjang.id_menu = menu.id_menu 
                                                            WHERE keranjang.id_user = '$id_user'");
                                while($row = mysqli_fetch_assoc($query)) {
                                    $subtotal = $row['harga'] * $row['jumlah'];
                                    $total_akhir += $subtotal;
                                ?>
                                <tr>
                                    <td>
                                        <div class="menu-item">
                                            <img src="images/<?php echo $row['gambar']; ?>">
                                            <div>
                                                <strong><?php echo htmlspecialchars($row['nama_menu']); ?></strong><br>
                                                <small style="color:#888;">x <?php echo $row['jumlah']; ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>Rp <?php echo number_format($row['harga'], 0, ',', '.'); ?></td>
                                    <td><strong>Rp <?php echo number_format($subtotal, 0, ',', '.'); ?></strong></td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>

                        <div class="form-group-custom" style="margin-bottom: 20px;">
                            <label>Alamat Pengiriman Katering Lengkap</label>
                            <textarea name="alamat_pengiriman" class="alamat-input" placeholder="Tuliskan nama jalan, blok, nomor rumah, RT/RW, kecamatan, dan patokan lokasi pengantaran katering..." required></textarea>
                        </div>

                        <div class="form-group-custom">
                            <label>Catatan Tambahan (Opsional)</label>
                            <textarea name="catatan" class="catatan-input" placeholder="Contoh: request level pedas, dipisah kuahnya, warna tema box, dll..."></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="right-column">
                <div class="box">
                    <div class="box-header">Total Pembayaran</div>
                    <div class="box-body">
                        <div class="total-row">
                            <span class="total-label">Total Harga</span>
                            <span class="total-price">Rp <?php echo number_format($total_akhir, 0, ',', '.'); ?></span>
                        </div>

                        <strong>Metode Pembayaran Transfer</strong>
                        
                        <ul class="payment-methods">
                            <li class="payment-item">
                                <img src="https://upload.wikimedia.org/wikipedia/commons/5/5c/Bank_Central_Asia.svg" alt="BCA Logo" class="bank-logo">
                                <div class="bank-detail">
                                    <strong>Bank BCA</strong><br>
                                    No. Rek: 1234567890
                                    <div class="bank-info">Atas Nama: Dapur Alifa</div>
                                </div>
                            </li>
                        </ul>
                        
                        <p style="font-size: 13px; color: #777; margin-bottom: 15px;">Silakan transfer ke rekening BCA di atas, kemudian upload bukti transfer Anda.</p>

                        <div class="delivery-section">
                            <strong style="color: #8a6d3b; display: block; margin-bottom: 10px; font-size: 15px;">🕒 Rencana Waktu Pengantaran</strong>
                            
                            <div class="delivery-group">
                                <label>Tanggal Pengantaran:</label>
                                <input type="date" name="tanggal_pengantaran" required min="<?php echo date('Y-m-d'); ?>">
                            </div>
                            
                            <div class="delivery-group">
                                <label>Jam Pengantaran:</label>
                                <select name="jam_pengantaran" required>
                                    <option value="">-- Pilih Jam Pengantaran --</option>
                                    <?php
                                    for ($h = 7; $h <= 20; $h++) {
                                        $jam_pas = str_pad($h, 2, "0", STR_PAD_LEFT) . ":00";
                                        $jam_setengah = str_pad($h, 2, "0", STR_PAD_LEFT) . ":30";
                                        
                                        echo "<option value='$jam_pas'>$jam_pas WIB</option>";
                                        echo "<option value='$jam_setengah'>$jam_setengah WIB</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <div class="upload-section">
                            <label>Upload Bukti Pembayaran Resmi</label>
                            <input type="file" name="bukti_bayar" accept="image/*" required>
                        </div>
                    </div>
                </div>
            </div>

            <div class="btn-group">
                <a href="keranjang.php" class="btn btn-orange">Kembali ke Keranjang</a>
                <button type="submit" name="konfirmasi" class="btn btn-green">Konfirmasi Pesanan</button>
            </div>

        </div>
    </form>
</div>

<footer>
    © 2026 Dapur Alifa
</footer>

</body>
</html>