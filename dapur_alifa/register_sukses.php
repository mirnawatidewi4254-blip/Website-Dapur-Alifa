<?php
session_start();
include('koneksi.php'); 
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Berhasil - Dapur Alifa</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background-color: #f4f4f4; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        
        .card { background: white; padding: 40px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05); width: 420px; text-align: center; border: 1px solid #eee; }
        
        /* TEMA LOGO ORANYE DAPUR ALIFA */
        .logo-section { display: flex; align-items: center; justify-content: center; gap: 12px; margin-bottom: 25px; }
        .logo-section img { width: 45px; height: auto; }
        .logo-section span { font-size: 20px; font-weight: bold; color: #FF661D; }

        .sub-title { color: #2c3e50; font-size: 16px; font-weight: bold; margin-bottom: 5px; }
        .desc { color: #7f8c8d; font-size: 13px; margin-bottom: 25px; }

        /* TEMA ICON CENTANG HIJAU SUKSES */
        .check-icon { 
            background-color: #5CB85C; color: white; width: 70px; height: 70px; 
            border-radius: 50%; display: flex; justify-content: center; align-items: center; 
            font-size: 35px; margin: 0 auto 20px auto; 
            box-shadow: 0 4px 10px rgba(46, 204, 113, 0.3);
        }

        h2 { color: #2c3e50; margin-bottom: 10px; font-size: 24px; font-weight: bold; }
        .info-text { color: #7f8c8d; font-size: 14px; margin-bottom: 30px; line-height: 1.5; }
        
        /* TEMA TOMBOL ORANYE UTAMA */
        .btn-login { 
            display: block; width: 100%; padding: 13px; background-color: #FF661D; color: white; 
            text-decoration: none; border-radius: 6px; font-size: 16px; font-weight: bold; 
            box-sizing: border-box; transition: background 0.3s;
        }
        .btn-login:hover { background-color: #e55a1a; }
    </style>
</head>
<body>

<div class="card">
    <div class="logo-section">
        <img src="images/logo.jpg" alt="Logo"> <span>Dapur Alifa</span>
    </div>

    <div class="sub-title">Daftar Account Customer</div>
    <div class="desc">Silahkan lengkapi data berikut untuk membuat akun baru.</div>

    <div class="check-icon">&#10003;</div>

    <h2>Pendaftaran Berhasil!</h2>
    <p class="info-text">Akun Anda telah berhasil dibuat.<br>Silahkan login untuk melanjutkan belanja.</p>

    <a href="login.php" class="btn-login">Login Sekarang</a>
</div>

</body>
</html>