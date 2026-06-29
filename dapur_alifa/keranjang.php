<?php
session_start();
include('koneksi.php');

// Proteksi Pelanggan: pastikan sudah login
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

$id_user = $_SESSION['id_user'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Keranjang Belanja - Dapur Alifa</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', sans-serif; }
        body { background-color: #f9f9f9; color: #333; }
        
        /* Navbar */
        nav { background: white; padding: 15px 8%; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .logo { font-size: 18px; font-weight: bold; color: #FF661D; text-decoration: none; display: flex; align-items: center; gap: 10px; }
        .nav-links { list-style: none; display: flex; gap: 20px; }
        .nav-links a { text-decoration: none; color: #555; }

        .container { max-width: 1000px; margin: 40px auto; padding: 0 20px; }
        h1 { margin-bottom: 25px; font-size: 24px; color: #2c3e50; }

        /* ELEMEN TEKS INFO & PEMBATAS */
        .info-maksimal-pax {
            font-size: 12px;
            color: #e67e22;
            background-color: #fff9f5;
            border-left: 3px solid #FF661D;
            padding: 6px 10px;
            border-radius: 4px;
            margin: 8px 0;
            display: block;
            font-weight: 500;
        }
        .pembatas-produk-pax {
            border: 0;
            height: 1px;
            background-image: linear-gradient(to right, rgba(255, 102, 29, 0.3), rgba(0, 0, 0, 0));
            margin: 8px 0;
        }

        /* Tabel Keranjang */
        .cart-box { background: white; border-radius: 8px; border: 1px solid #eee; overflow: hidden; box-shadow: 0 2px 5px rgba(0,0,0,0.02); }
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; padding: 15px; background: #fafafa; border-bottom: 2px solid #eee; color: #666; font-size: 14px; }
        td { padding: 20px 15px; border-bottom: 1px solid #eee; }
        
        .menu-info { display: flex; align-items: center; gap: 15px; }
        .menu-info img { width: 70px; height: 55px; border-radius: 6px; object-fit: cover; }
        .menu-name { font-weight: bold; color: #333; }

        /* Form Input Jumlah Tanpa Tombol */
        .quantity-control { display: flex; align-items: center; gap: 5px; }
        .qty-input { width: 80px; height: 35px; text-align: center; font-size: 14px; border: 1px solid #ddd; border-radius: 4px; font-weight: bold; }
        .info-petunjuk { font-size: 10px; color: #999; margin-top: 4px; display: block; }

        /* Footer */
        .cart-footer { padding: 20px; background: #fafafa; display: flex; justify-content: space-between; align-items: center; border-top: 1px solid #eee; }
        .total-amount { font-size: 22px; font-weight: bold; color: #FF661D; }
        .btn-checkout { background: #5CB85C; color: white; text-decoration: none; padding: 12px 30px; border-radius: 6px; font-weight: bold; }
        .btn-hapus { color: #d9534f; text-decoration: none; font-size: 14px; }
    </style>
</head>
<body>

<nav>
    <a href="index.php" class="logo"><img src="images/logo.jpg" alt="" style="width:40px;"> Dapur Alifa</a>
    <div class="nav-links">
        <a href="index.php">Beranda</a>
        <a href="keranjang.php" style="color: #FF661D; font-weight: bold;">Keranjang</a>
        <a href="riwayat.php">Riwayat Pesanan</a>
        <a href="logout.php" style="background:#FF661D; color:white; padding:5px 15px; border-radius:5px;">Logout</a>
    </div>
</nav>

<div class="container">
    <h1>Keranjang Belanja</h1>
    <div class="cart-box">
        <table>
            <thead>
                <tr>
                    <th>Produk</th>
                    <th>Harga</th>
                    <th>Jumlah</th>
                    <th>Subtotal</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $total_keranjang = 0;
                $db_conn = isset($koneksi) ? $koneksi : $conn;
                $query = mysqli_query($db_conn, "SELECT k.id_keranjang, k.id_menu, k.jumlah, m.nama_menu, m.harga, m.gambar 
                                              FROM keranjang k JOIN menu m ON k.id_menu = m.id_menu 
                                              WHERE k.id_user = '$id_user'");
                
                if(mysqli_num_rows($query) > 0) {
                    while($row = mysqli_fetch_assoc($query)) {
                        $subtotal = $row['harga'] * $row['jumlah'];
                        $total_keranjang += $subtotal;
                ?>
                <tr>
                    <td>
                        <div class="menu-info">
                            <img src="images/<?php echo $row['gambar']; ?>" alt="">
                            <span class="menu-name"><?php echo $row['nama_menu']; ?></span>
                        </div>
                    </td>
                    <td>Rp <?php echo number_format($row['harga'], 0, ',', '.'); ?></td>
                    <td>
                        <div class="pembatas-produk-pax"></div>
                        <div class="info-maksimal-pax">⚠️ Maksimal 200 pax / hari</div>
                        <div class="pembatas-produk-pax"></div>
                        
                        <form action="update_keranjang.php" method="POST" class="quantity-control">
                            <input type="hidden" name="id_menu" value="<?php echo $row['id_menu']; ?>">
                            <input type="number" name="jumlah_baru" class="qty-input" value="<?php echo $row['jumlah']; ?>" min="1" max="200" required>
                            <input type="hidden" name="btn_update" value="1">
                        </form>
                        <span class="info-petunjuk">*Ketik angka lalu tekan Enter</span>
                    </td>
                    <td><strong>Rp <?php echo number_format($subtotal, 0, ',', '.'); ?></strong></td>
                    <td>
                        <a href="hapus_keranjang.php?id=<?php echo $row['id_keranjang']; ?>" class="btn-hapus" onclick="return confirm('Hapus produk ini?')">Hapus</a>
                    </td>
                </tr>
                <?php } } else { ?>
                <tr><td colspan='5' style='text-align:center; padding: 40px;'>Keranjang Anda kosong.</td></tr>
                <?php } ?>
            </tbody>
        </table>
        
        <?php if($total_keranjang > 0) { ?>
        <div class="cart-footer">
            <div><span class="total-label">Total Belanja:</span> <span class="total-amount">Rp <?php echo number_format($total_keranjang, 0, ',', '.'); ?></span></div>
            <a href="checkout.php" class="btn-checkout">Lanjut Ke Checkout</a>
        </div>
        <?php } ?>
    </div>
</div>
</body>
</html>