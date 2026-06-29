<?php 
include('koneksi.php'); 
session_start(); 

$error_msg = "";

if (isset($_POST['register'])) {
    $nama_lengkap = mysqli_real_escape_string($conn, $_POST['nama_lengkap']);
    $user         = mysqli_real_escape_string($conn, $_POST['username']);
    $email        = mysqli_real_escape_string($conn, $_POST['email']);
    $pass         = $_POST['password'];
    $role         = 'customer'; // Set otomatis sebagai customer

    // Enkripsi password dengan password_hash
    $password_aman = password_hash($pass, PASSWORD_DEFAULT);

    // Cek dulu apakah username atau email sudah ada yang pakai
    $cek_username = mysqli_query($conn, "SELECT * FROM users WHERE username = '$user' OR email = '$email'");
    
    if (mysqli_num_rows($cek_username) > 0) {
        $error_msg = "Username atau Email sudah terdaftar! Gunakan yang lain.";
    } else {
        // Masukkan data baru ke tabel users
        $insert = mysqli_query($conn, "INSERT INTO users (nama_lengkap, username, email, password, role) 
                                       VALUES ('$nama_lengkap', '$user', '$email', '$password_aman', '$role')");
        
        if ($insert) {
            // SUDAH DIGANTI: Langsung dialihkan ke register_sukses.php
            header("Location: register_sukses.php");
            exit();
        } else {
            $error_msg = "Gagal mendaftar, terjadi kesalahan sistem.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun - Dapur Alifa</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background-color: #f4f4f4; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        
        .card { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); width: 380px; text-align: center; border: 1px solid #eee; }
        
        .logo { 
            color: #FF661D; 
            font-size: 18px; 
            font-weight: bold; 
            margin-bottom: 25px; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            text-decoration: none;
        }
        .logo img {
            width: 50px; 
            height: auto;
            margin-right: 12px;
        }

        input { width: 100%; padding: 12px; margin: 10px 0; border: 1px solid #ddd; border-radius: 5px; box-sizing: border-box; background: #fafafa; }
        button { width: 100%; padding: 12px; background-color: #FF661D; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; margin-top: 10px; font-weight: bold; }
        button:hover { background-color: #e55a1a; }
        
        .footer-text { margin-top: 20px; border-top: 1px solid #eee; padding-top: 15px; font-size: 14px; }
        .footer-text a { color: #FF661D; text-decoration: none; font-weight: bold; }

        /* Error Alert */
        .alert-error { background-color: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; font-size: 13px; margin-bottom: 15px; border: 1px solid #f5c6cb; text-align: center; }
    </style>
</head>
<body>

<div class="card">
    <div class="logo">
        <img src="images/logo.jpg" alt=""> 
        Dapur Alifa
    </div>

    <h3 style="color:#444;">Daftar Akun Customer</h3>
    <p style="color: #666; font-size: 14px; margin-bottom: 20px;">Silakan lengkapi data di bawah ini untuk mendaftar.</p>

    <?php if (!empty($error_msg)) : ?>
        <div class="alert-error"><?php echo $error_msg; ?></div>
    <?php endif; ?>

    <form action="" method="POST">
        <input type="text" name="nama_lengkap" placeholder="Nama Lengkap" required>
        <input type="text" name="username" placeholder="Username" required>
        <input type="email" name="email" placeholder="Alamat Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit" name="register">Daftar Sekarang</button>
    </form>

    <div class="footer-text">
        Sudah punya akun? <a href="login.php">Login di sini</a>
    </div>
</div>

</body>
</html>