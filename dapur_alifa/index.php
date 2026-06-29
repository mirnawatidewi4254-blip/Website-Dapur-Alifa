<?php 
session_start();
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}
include('koneksi.php');
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beranda - Dapur Alifa</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Tahoma, sans-serif; }
        body { background-color: #f9f9f9; color: #333; line-height: 1.6; }

        .garis-pembatas-kecil {
            width: 120px; 
            height: 4px;
            background: linear-gradient(90deg, transparent, #FF661D, transparent);
            margin: 15px auto;
            border-radius: 2px;
        }

        /* NAVBAR */
        nav { 
            background: white; 
            padding: 15px 8%; 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            box-shadow: 0 2px 10px rgba(0,0,0,0.05); 
            position: sticky; 
            top: 0; 
            z-index: 1000; 
        }
        .logo { display: flex; align-items: center; gap: 12px; text-decoration: none; font-size: 18px; font-weight: bold; color: #FF661D; }
        .logo-img { width: 60px; height: 60px; object-fit: contain; }
        .nav-links { list-style: none; display: flex; gap: 25px; align-items: center; }
        .nav-links a { text-decoration: none; color: #555; font-weight: 500; transition: 0.3s; }
        .nav-links a:hover, .nav-links a.active { color: #FF661D; border-bottom: 2px solid #FF661D; padding-bottom: 5px; }
        .btn-logout { background-color: #FF661D; color: white !important; padding: 8px 18px; border-radius: 5px; border: none; font-weight: bold; }

        /* HERO SECTION */
        .hero { 
            text-align: center; 
            padding: 110px 20px; 
            background: linear-gradient(to bottom, rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.65)), url('images/lokasi_dapur.jpeg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed; 
            color: white; 
            margin-bottom: 40px; 
        }
        .hero h1 { font-size: 36px; margin-bottom: 10px; text-shadow: 2px 2px 4px rgba(0,0,0,0.5); }
        .hero p { font-size: 20px; font-weight: 500; }
        .jam-operasional { font-size: 14px; color: #fff; background: rgba(255, 102, 29, 0.85); display: inline-block; padding: 6px 18px; border-radius: 20px; font-weight: bold; margin-top: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.15); }

        .container { max-width: 1200px; margin: 0 auto; padding: 20px; }
        .menu-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 30px; }
        
        /* KARTU MENU */
        .card { background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 5px 15px rgba(0,0,0,0.05); transition: 0.4s; border: 1px solid #eee; display: flex; flex-direction: column; position: relative; }
        .card:hover { transform: translateY(-10px); box-shadow: 0 10px 25px rgba(0,0,0,0.1); }
        .card img { width: 100%; height: 200px; object-fit: cover; }
        
        /* LABEL STATUS OPEN / CLOSE ORDER */
        .status-label { position: absolute; top: 10px; right: 10px; padding: 6px 14px; border-radius: 20px; font-size: 12px; font-weight: bold; color: white; z-index: 10; box-shadow: 0 2px 6px rgba(0,0,0,0.15); letter-spacing: 0.3px; }
        .open-order { background-color: #28a745; }
        .close-order { background-color: #dc3545; }

        .card-content { padding: 20px; flex-grow: 1; text-align: center; }
        .card-content h3 { font-size: 22px; margin-bottom: 10px; color: #333; }
        .card-content .price { font-size: 18px; color: #FF661D; font-weight: bold; margin-bottom: 15px; }
        
        .btn-pesan { display: inline-block; width: 100%; padding: 12px; background-color: #FF661D; color: white; text-decoration: none; border-radius: 6px; font-weight: bold; transition: 0.3s; }
        .btn-pesan:hover { background-color: #e55a1a; }
        .btn-habis { background-color: #ccc; cursor: not-allowed; }

        
        .sop-overlay {
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(3px);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            visibility: hidden;
            z-index: 9999;
            transition: opacity 0.3s ease, visibility 0.3s ease;
        }

        /* Aktifkan Pop-up Menggunakan Selektor :target CSS */
        .sop-overlay:target {
            opacity: 1;
            visibility: visible;
        }

        .sop-popup {
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            width: 85%;
            max-width: 480px;
            box-shadow: 0 5px 25px rgba(0,0,0,0.2);
            text-align: left;
        }

        .sop-popup h3 {
            color: #FF661D;
            font-size: 20px;
            margin-bottom: 15px;
            text-align: center;
            border-bottom: 2px solid #fff5f0;
            padding-bottom: 10px;
        }

        .sop-list {
            list-style: none;
            padding: 0;
            margin: 20px 0;
        }

        .sop-list li {
            padding: 12px 0;
            border-bottom: 1px solid #eee;
            font-size: 14px;
            color: #444;
            display: flex;
            align-items: flex-start;
            gap: 12px;
            line-height: 1.5;
        }

        .sop-list li strong {
            color: #FF661D;
        }

        .sop-box-btn {
            display: flex;
            justify-content: space-between;
            gap: 15px;
            margin-top: 25px;
        }

        .btn-sop-batal {
            background-color: #6c757d;
            color: white;
            text-decoration: none;
            padding: 12px 22px;
            border-radius: 6px;
            font-weight: bold;
            transition: 0.2s;
            text-align: center;
        }

        .btn-sop-setuju {
            background-color: #28a745;
            color: white;
            text-decoration: none;
            padding: 12px 22px;
            border-radius: 6px;
            font-weight: bold;
            flex: 1;
            transition: 0.2s;
            text-align: center;
        }

        .btn-sop-setuju:hover { background-color: #218838; }
        .btn-sop-batal:hover { background-color: #5a6268; }

        /* FOOTER */
        footer { text-align: center; padding: 45px 20px; color: #7f8c8d; font-size: 14px; margin-top: 60px; background: white; border-top: 1px solid #eee; }
        .footer-heading { font-size: 16px; font-weight: 600; color: #2c3e50; margin-bottom: 8px; }
        .footer-sub { font-size: 13px; color: #95a5a6; margin-bottom: 20px; }
        .social-container { display: flex; justify-content: center; gap: 20px; margin-bottom: 25px; }
        .social-btn { width: 46px; height: 46px; border-radius: 50%; background-color: #f8f9fa; display: inline-flex; align-items: center; justify-content: center; font-size: 22px; text-decoration: none; transition: all 0.3s ease; box-shadow: 0 3px 6px rgba(0,0,0,0.03); }
        .wa-color { color: #25D366; }
        .wa-color:hover { background-color: #25D366; color: white; transform: translateY(-5px); box-shadow: 0 5px 15px rgba(37, 211, 102, 0.4); }
        .ig-color { color: #E1306C; }
        .ig-color:hover { background-color: #E1306C; color: white; transform: translateY(-5px); box-shadow: 0 5px 15px rgba(225, 48, 108, 0.4); }
        .copyright { font-size: 12px; color: #bdc3c7; border-top: 1px dashed #eee; padding-top: 15px; display: inline-block; width: 80%; max-width: 400px; }
    </style>
</head>
<body>

<nav>
    <a href="index.php" class="logo">
        <img src="images/logo.jpg" alt="" class="logo-img"> 
        Dapur Alifa
    </a>
    <ul class="nav-links">
        <li><a href="index.php" class="active">Beranda</a></li>
        <li><a href="keranjang.php">Keranjang</a></li>
        <li><a href="riwayat.php">Riwayat</a></li>
        <li><a href="profil.php">Profil</a></li>
        <li><a href="logout.php" class="btn-logout" onclick="return confirm('Apakah Anda yakin ingin keluar?')">Logout</a></li>
    </ul>
</nav>

<div class="hero">
    <h1>Selamat Datang di Dapur Alifa</h1>
    <div class="garis-pembatas-kecil"></div>
    <p>Silakan Pilih Menu Favorit Anda <strong><?php echo $_SESSION['nama_user']; ?></strong>!</p>
    <div class="jam-operasional">🕒 Jam Operasional: 08:00 - 17:00 WIB</div>
</div>

<div class="container">
    <div class="menu-grid">
        <?php
        $query = mysqli_query($conn, "SELECT * FROM menu");
        if (mysqli_num_rows($query) > 0) {
            while ($row = mysqli_fetch_assoc($query)) {
                
                $status = isset($row['status_order']) ? $row['status_order'] : '0';
                $isOpen = ($status === 'Open Order' || $status === '1' || $status === 1);
        ?>
            <div class="card">
                <?php if ($isOpen) : ?>
                    <div class="status-label open-order">Open Order</div>
                <?php else : ?>
                    <div class="status-label close-order">Close Order</div>
                <?php endif; ?>

                <img src="images/<?php echo $row['gambar']; ?>" alt="<?php echo $row['nama_menu']; ?>">
                
                <div class="card-content">
                    <h3><?php echo $row['nama_menu']; ?></h3>
                    <p class="price">Rp <?php echo number_format($row['harga'], 0, ',', '.'); ?></p>
                    
                    <?php if ($isOpen) : ?>
                        <a href="#buka-sop-<?php echo $row['id_menu']; ?>" class="btn-pesan"><i class="fa-solid fa-cart-plus"></i> Pesan Sekarang</a>
                    <?php else : ?>
                        <a href="#" class="btn-pesan btn-habis" onclick="alert('Mohon maaf, pemesanan untuk menu katering ini sudah Close Order.'); return false;">Close Order</a>
                    <?php endif; ?>
                </div>
            </div>

            <?php if ($isOpen) : ?>
            <div id="buka-sop-<?php echo $row['id_menu']; ?>" class="sop-overlay">
                <div class="sop-popup">
                    <h3>📋 SOP Standar Dapur Alifa</h3>
                    
                    <ul class="sop-list">
                        <li><strong>1.</strong> Pemesanan maksimal H-1 sebelum waktu pengiriman katering.</li>
                        <li><strong>2.</strong> Ongkos kirim pengantaran menyesuaikan jarak lokasi alamat pengiriman.</li>
                        <li><strong>3.</strong> Pemesanan di atas 100 pax berhak mendapatkan gratis ongkos kirim (Free Ongkir).</li>
                    </ul>

                    <div class="sop-box-btn">
                        <a href="#" class="btn-sop-batal">Batal</a>
                        <a href="tambah_keranjang.php?id=<?php echo $row['id_menu']; ?>" class="btn-sop-setuju">Setuju & Lanjutkan</a>
                    </div>
                </div>
            </div>
            <?php endif; ?>

        <?php 
            }
        } else {
            echo "<p style='text-align:center; grid-column: 1/-1;'>Maaf, menu belum tersedia.</p>";
        }
        ?>
    </div>
</div>

<footer>
    <div class="footer-heading">Yuk, Terhubung dengan Dapur Alifa!</div>
    <div class="footer-sub">Punya pertanyaan seputar katering atau pesanan? Hubungi kami langsung di media sosial.</div>
    
    <div class="social-container">
        <a href="https://wa.me/6289520336530?text=Halo%20Dapur%20Alifa,%20saya%20ingin%20bertanya%20mengenai%20menu%20dan%20pemesanan%20kateringnya%20ya." target="_blank" class="social-btn wa-color"><i class="fab fa-whatsapp"></i></a>
        <a href="https://instagram.com/dapuralifa_pontianak" target="_blank" class="social-btn ig-color"><i class="fab fa-instagram"></i></a>
    </div>
    <div class="copyright">&copy; 2026 Dapur Alifa - Semua Hak Dilindungi.</div>
</footer>

</body>
</html>