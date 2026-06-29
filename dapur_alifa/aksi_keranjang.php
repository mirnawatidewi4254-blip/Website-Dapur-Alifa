<?php
session_start();
include('koneksi.php');

if (isset($_GET['id']) && isset($_GET['aksi'])) {
    $id_keranjang = $_GET['id'];
    $aksi = $_GET['aksi'];

    if ($aksi == 'tambah') {
        mysqli_query($conn, "UPDATE keranjang SET jumlah = jumlah + 1 WHERE id_keranjang = '$id_keranjang'");
    } 
    elseif ($aksi == 'kurang') {
        // Ambil data jumlah dulu
        $query = mysqli_query($conn, "SELECT jumlah FROM keranjang WHERE id_keranjang = '$id_keranjang'");
        $data = mysqli_fetch_assoc($query);

        if ($data['jumlah'] > 1) {
            mysqli_query($conn, "UPDATE keranjang SET jumlah = jumlah - 1 WHERE id_keranjang = '$id_keranjang'");
        } else {
            // Jika jumlah sudah 1 dan diklik kurang, barang dihapus dari keranjang
            mysqli_query($conn, "DELETE FROM keranjang WHERE id_keranjang = '$id_keranjang'");
        }
    }

    // Setelah proses selesai, lempar kembali ke halaman keranjang
    header("Location: keranjang.php");
    exit;
}
?>