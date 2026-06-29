<?php 
include('../koneksi.php'); 
session_start(); 
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login Admin - Dapur Alifa</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background-color: #f4f4f4; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .card { background: white; padding: 40px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05); width: 400px; text-align: center; }
        
        .logo-section { display: flex; align-items: center; justify-content: center; gap: 10px; margin-bottom: 20px; }
        .logo-section img { width: 50px; }
        .logo-section span { font-size: 24px; font-weight: bold; color: #e55a1a; }

        h2 { color: #333; margin-bottom: 10px; }
        p { color: #666; font-size: 14px; margin-bottom: 25px; }

        input { 
            width: 100%; padding: 12px; margin: 10px 0; border: 1px solid #ddd; border-radius: 5px; 
            box-sizing: border-box; background: #fdfdfd;
        }
        
        button { 
            width: 100%; padding: 12px; background-color: #FF661D; color: white; border: none; 
            border-radius: 5px; cursor: pointer; font-size: 16px; font-weight: bold; margin-top: 10px; 
        }
        button:hover { background-color: #e55a1a; }
    </style>
</head>
<body>

<div class="card">
    <div class="logo-section">
        <img src="../images/logo.jpg" alt="Logo">
        <span>Dapur Alifa</span>
    </div>

    <h2>Login Admin</h2>
    <p>Gunakan akun admin Anda untuk masuk.</p>

    <form action="" method="POST">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit" name="login_admin">Login</button>
    </form>

    <?php
    if (isset($_POST['login_admin'])) {
        $user = mysqli_real_escape_string($conn, $_POST['username']);
        $pass = $_POST['password'];

        $result = mysqli_query($conn, "SELECT * FROM users WHERE username = '$user' AND role = 'admin'");
        
        if (mysqli_num_rows($result) === 1) {
            $row = mysqli_fetch_assoc($result);
            
            if (password_verify($pass, $row['password'])) {
                
                // =============================================================
                // PERBAIKAN: Mengubah nama session agar tidak menimpa Customer
                // =============================================================
                $_SESSION['login_admin_status'] = true; // Membedakan status login admin
                $_SESSION['role'] = 'admin';
                $_SESSION['id_admin'] = $row['id_user']; // Menggunakan id_admin, bukan id_user lagi
                $_SESSION['admin_nama'] = $row['nama_lengkap']; 
                
                echo "<script>
                        alert('Selamat bekerja, Admin " . $row['nama_lengkap'] . "!'); 
                        window.location='index.php';
                      </script>";
                exit();
            } else {
                echo "<script>alert('Password salah!');</script>";
            }
        } else {
            echo "<script>alert('Akun Admin tidak ditemukan atau Anda bukan Admin!');</script>";
        }
    }
    ?>
</div>

</body>
</html>