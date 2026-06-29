<?php
session_start();
include('koneksi.php');

if (isset($_POST['ganti'])) {
    $id = $_SESSION['reset_id'];
    $pass_baru = password_hash($_POST['password_baru'], PASSWORD_DEFAULT); // Menggunakan hash agar aman

    $update = mysqli_query($conn, "UPDATE users SET password = '$pass_baru' WHERE id = '$id'");

    if ($update) {
        session_destroy(); // Hapus session reset
        echo "<script>alert('Password berhasil diubah! Silakan login kembali.'); window.location='login.php';</script>";
    } else {
        echo "<script>alert('Gagal mengubah password!'); window.location='reset_password.php';</script>";
    }
}
?>