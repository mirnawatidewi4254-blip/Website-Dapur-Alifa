<?php
session_start();
// Jika user belum melakukan verifikasi, kembalikan ke halaman lupa password
if (!isset($_SESSION['reset_id'])) {
    header("Location: lupa_password.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Ganti Password - Dapur Alifa</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background-color: #f4f4f4; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .card { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); width: 380px; text-align: center; border: 1px solid #eee; }
        input { width: 100%; padding: 12px; margin: 10px 0; border: 1px solid #ddd; border-radius: 5px; box-sizing: border-box; }
        button { width: 100%; padding: 12px; background-color: #FF661D; color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: bold; }
    </style>
</head>
<body>
    <div class="card">
        <h3>Reset Password</h3>
        <p style="font-size: 13px; color: #777;">Masukkan password baru Anda.</p>
        
        <form action="proses_update_password.php" method="POST">
            <input type="password" name="password_baru" placeholder="Masukkan Password Baru" required>
            <button type="submit" name="ganti">Simpan Password</button>
        </form>
    </div>
</body>
</html>