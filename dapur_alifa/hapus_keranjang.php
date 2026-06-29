<?php
// 1. Mulai session dan hubungkan ke database dapur_alifa
session_start();
include 'koneksi.php'; 

// 2. Pastikan user sudah login sebagai customer
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

$id_user = $_SESSION['id_user'];

// 3. Tangkap id_keranjang dari URL
if (isset($_GET['id'])) {
    $id_keranjang = $_GET['id'];

    // 4. Jalankan query menggunakan variabel $conn (bukan $koneksi)
    $query = "DELETE FROM keranjang WHERE id_keranjang = '$id_keranjang' AND id_user = '$id_user'";
    $bisa_dihapus = mysqli_query($conn, $query);

    if ($bisa_dihapus) {
        // Jika sukses, kembalikan ke halaman keranjang
        header("Location: keranjang.php");
        exit;
    } else {
        echo "Gagal menghapus item: " . mysqli_error($conn);
    }
} else {
    // Jika diakses tanpa ID langsung kembalikan ke keranjang
    header("Location: keranjang.php");
    exit;
}
?>