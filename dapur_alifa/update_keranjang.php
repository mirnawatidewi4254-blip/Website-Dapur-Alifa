<?php
session_start();
include('koneksi.php');

// Cek apakah request berasal dari form tombol update
if (isset($_POST['btn_update'])) {
    if (!isset($_SESSION['login'])) {
        header("Location: login.php");
        exit;
    }

    $id_user = $_SESSION['id_user'];
    $id_menu = mysqli_real_escape_string($conn, $_POST['id_menu']);
    $jumlah_baru = intval($_POST['jumlah_baru']);

    // 1. Validasi Maksimal 200 Pax
    if ($jumlah_baru > 200) {
        echo "<script>alert('Mohon maaf, batas maksimal pesanan adalah 200 pax per menu dalam satu hari.'); window.location='keranjang.php';</script>";
        exit;
    }

    // 2. Validasi Minimal 1
    if ($jumlah_baru < 1) {
        $jumlah_baru = 1;
    }

    // 3. Update Database
    $update = mysqli_query($conn, "UPDATE keranjang SET jumlah = '$jumlah_baru' WHERE id_user = '$id_user' AND id_menu = '$id_menu'");

    if ($update) {
        // Redirect kembali ke keranjang setelah sukses
        header("Location: keranjang.php");
        exit;
    } else {
        echo "<script>alert('Gagal memperbarui database.'); window.location='keranjang.php';</script>";
        exit;
    }
} else {
    // Jika user mengakses file ini secara langsung tanpa klik tombol, lempar ke keranjang
    header("Location: keranjang.php");
    exit;
}
?>