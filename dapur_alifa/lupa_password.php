<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Lupa Password - Dapur Alifa</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background-color: #f4f4f4; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .card { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); width: 380px; text-align: center; border: 1px solid #eee; }
        .logo { color: #FF661D; font-size: 20px; font-weight: bold; margin-bottom: 20px; }
        input { width: 100%; padding: 12px; margin: 10px 0; border: 1px solid #ddd; border-radius: 5px; box-sizing: border-box; }
        button { width: 100%; padding: 12px; background-color: #FF661D; color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: bold; }
        button:hover { background-color: #e55a1a; }
        .back-link { margin-top: 15px; display: block; font-size: 13px; color: #666; text-decoration: none; }
    </style>
</head>
<body>
    <div class="card">
        <div class="logo">Dapur Alifa</div>
        <h3 style="color:#444;">Lupa Password</h3>
        <p style="font-size: 13px; color: #777; margin-bottom: 20px;">Masukkan username dan email Anda untuk verifikasi.</p>
        
        <form action="proses_verifikasi.php" method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="email" name="email" placeholder="Email Terdaftar" required>
            <button type="submit" name="verifikasi">Verifikasi Data</button>
        </form>
        <a href="login.php" class="back-link">Kembali ke Login</a>
    </div>
</body>
</html>