<?php
session_start();
include('koneksi.php');

if (isset($_POST['update'])) {
    $id = $_SESSION['id_user'];
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);

    $update = mysqli_query($conn, "UPDATE users SET nama_lengkap='$nama', email='$email' WHERE id='$id'");

    if ($update) {
        $_SESSION['nama_user'] = $nama; // Update sesi agar navbar sinkron
        echo "<script>alert('Profil berhasil diperbarui!'); window.location='profil.php';</script>";
    } else {
        echo "<script>alert('Gagal update!'); window.location='profil.php';</script>";
    }
}
?>