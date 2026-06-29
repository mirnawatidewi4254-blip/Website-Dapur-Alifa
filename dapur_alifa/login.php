<?php 
include('koneksi.php'); 
session_start(); 
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login - Dapur Alifa</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background-color: #f4f4f4; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        
        .card { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); width: 380px; text-align: center; border: 1px solid #eee; }
        
        .logo { color: #FF661D; font-size: 18px; font-weight: bold; margin-bottom: 25px; display: flex; align-items: center; justify-content: center; text-decoration: none; }
        .logo img { width: 50px; height: auto; margin-right: 12px; }

        input { width: 100%; padding: 12px; margin: 10px 0; border: 1px solid #ddd; border-radius: 5px; box-sizing: border-box; background: #fafafa; }
        button { width: 100%; padding: 12px; background-color: #FF661D; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; margin-top: 10px; font-weight: bold; }
        button:hover { background-color: #e55a1a; }
        
        .footer-text { margin-top: 20px; border-top: 1px solid #eee; padding-top: 15px; font-size: 14px; }
        .footer-text a { color: #FF661D; text-decoration: none; font-weight: bold; }
        .lupa-pass { font-size: 12px; color: #888; display: block; margin-top: 10px; text-decoration: none; }
        .lupa-pass:hover { color: #FF661D; }
    </style>
</head>
<body>

<div class="card">
    <div class="logo">
        <img src="images/logo.jpg" alt=""> 
        Dapur Alifa
    </div>

    <h3 style="color:#444;">Login Customer</h3>
    <p style="color: #666; font-size: 14px; margin-bottom: 20px;">Silakan masukkan username dan password Anda.</p>

    <form action="" method="POST">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit" name="login">Login</button>
    </form>

    <a href="lupa_password.php" class="lupa-pass">Lupa password?</a>

    <div class="footer-text">
        Belum punya akun? <a href="register.php">Daftar di sini</a>
    </div>

    <?php
    if (isset($_POST['login'])) {
        $user = $_POST['username'];
        $pass = $_POST['password'];

        $result = mysqli_query($conn, "SELECT * FROM users WHERE username = '$user'");
        
        if (mysqli_num_rows($result) === 1) {
            $row = mysqli_fetch_assoc($result);
            if (password_verify($pass, $row['password'])) {
            $_SESSION['login'] = true;
            
            // PENTING: Pastikan ini menyimpan 'id' dari kolom 'id' di database
            $_SESSION['id_user'] = $row['id']; 
            
            $_SESSION['nama_user'] = $row['nama_lengkap'];
            echo "<script>alert('Selamat datang!'); window.location='index.php';</script>";
        }
        } else {
            echo "<script>alert('Username tidak ditemukan!');</script>";
        }
    }
    ?>
</div>

</body>
</html>