<?php
session_start();
// 1. Cek Login
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}
include('koneksi.php');

// 2. Ambil data user berdasarkan ID yang tersimpan di sesi
$id_user = $_SESSION['id_user'];
$query = mysqli_query($conn, "SELECT * FROM users WHERE id = '$id_user'");
$data = mysqli_fetch_assoc($query);

// 3. Pastikan data ditemukan, jika tidak, arahkan kembali atau beri pesan
if (!$data) {
    echo "<script>alert('Data profil tidak ditemukan!'); window.location='index.php';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Profil Saya - Dapur Alifa</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background-color: #f4f4f4; padding: 20px; }
        .card { 
            width: 400px; margin: 50px auto; padding: 30px; 
            background: white; border-radius: 10px; 
            box-shadow: 0 4px 15px rgba(0,0,0,0.05); 
            border: 1px solid #eee; 
        }
        h3 { color: #333; margin-bottom: 20px; text-align: center; }
        label { font-weight: 600; font-size: 14px; color: #555; display: block; margin-bottom: 5px; }
        input { 
            width: 100%; padding: 12px; margin-bottom: 20px; 
            border: 1px solid #ddd; border-radius: 5px; 
            box-sizing: border-box; background: #fafafa;
        }
        input:disabled { background: #eee; cursor: not-allowed; color: #888; }
        button { 
            width: 100%; padding: 12px; background-color: #FF661D; 
            color: white; border: none; border-radius: 5px; 
            cursor: pointer; font-weight: bold; font-size: 16px;
        }
        button:hover { background-color: #e55a1a; }
        .back-link { 
            display: block; text-align: center; margin-top: 15px; 
            color: #FF661D; text-decoration: none; font-size: 14px; font-weight: bold;
        }
    </style>
</head>
<body>

    <div class="card">
        <h3>Profil Saya</h3>
        <form action="proses_update_profil.php" method="POST">
            <label>Nama Lengkap:</label>
            <input type="text" name="nama" value="<?php echo htmlspecialchars($data['nama_lengkap']); ?>" required>
            
            <label>Email:</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($data['email']); ?>" required>
            
            <label>Username:</label>
            <input type="text" value="<?php echo htmlspecialchars($data['username']); ?>" disabled>
            
            <button type="submit" name="update">Simpan Perubahan</button>
        </form>
        <a href="index.php" class="back-link">← Kembali ke Beranda</a>
    </div>

</body>
</html>