<?php 
session_start();
include('koneksi.php');

if (isset($_POST['konfirmasi'])) {
    $id_user = $_SESSION['id_user'];
    $tanggal = date("Y-m-d H:i:s");
    
    // --- Tangkap data tambahan dari form checkout (Termasuk Alamat) ---
    $alamat_pengiriman = mysqli_real_escape_string($conn, $_POST['alamat_pengiriman']); 
    $catatan = mysqli_real_escape_string($conn, $_POST['catatan']);
    $tanggal_pengantaran = mysqli_real_escape_string($conn, $_POST['tanggal_pengantaran']);
    $jam_pengantaran = mysqli_real_escape_string($conn, $_POST['jam_pengantaran']);
    
    // Ambil data total dari keranjang
    $q_total = mysqli_query($conn, "SELECT SUM(menu.harga * keranjang.jumlah) as total 
                                    FROM keranjang JOIN menu ON keranjang.id_menu = menu.id_menu 
                                    WHERE keranjang.id_user = '$id_user'");
    $res_total = mysqli_fetch_assoc($q_total);
    $total_bayar = $res_total['total'];

    // --- PROSES UPLOAD BUKTI BAYAR ---
    $nama_file = $_FILES['bukti_bayar']['name'];
    $tmp_name = $_FILES['bukti_bayar']['tmp_name'];
    $nama_file_baru = date('YmdHis') . '_' . $nama_file;
    $path_tujuan = "images/bukti_bayar/" . $nama_file_baru;

    if (move_uploaded_file($tmp_name, $path_tujuan)) {
        
        // --- DIKOREKSI: Mengubah status dari 'Menunggu' menjadi 'Menunggu Verifikasi' agar singkron dengan Database ---
        $query_insert = "INSERT INTO pesanan (id_user, total_bayar, alamat_pengiriman, catatan, bukti_pembayaran, status_pesanan, tanggal_pesanan, tanggal_pengantaran, jam_pengantaran) 
                         VALUES ('$id_user', '$total_bayar', '$alamat_pengiriman', '$catatan', '$nama_file_baru', 'Menunggu Verifikasi', '$tanggal', '$tanggal_pengantaran', '$jam_pengantaran')";
        
        $insert = mysqli_query($conn, $query_insert);
        
        if ($insert) {
            // Ambil ID pesanan yang baru saja tersimpan di atas
            $id_pesanan_baru = mysqli_insert_id($conn);
            
            // 2. Ambil semua item produk yang ada di keranjang user saat ini
            $q_keranjang = mysqli_query($conn, "SELECT * FROM keranjang WHERE id_user = '$id_user'");
            
            while ($row_keranjang = mysqli_fetch_assoc($q_keranjang)) {
                $id_menu = $row_keranjang['id_menu'];
                $jumlah = $row_keranjang['jumlah'];
                
                // 3. Pindahkan item ke tabel pesanan_detail
                mysqli_query($conn, "INSERT INTO pesanan_detail (id_pesanan, id_menu, jumlah) 
                                     VALUES ('$id_pesanan_baru', '$id_menu', '$jumlah')");
            }
            
            // 4. Setelah sukses dipindahkan ke tabel pesanan, baru kosongkan keranjang belanja
            mysqli_query($conn, "DELETE FROM keranjang WHERE id_user = '$id_user'");
            
            // --- BERIKAN NOTIFIKASI BERHASIL UNTUK SISI CUSTOMER ---
            $_SESSION['notif_sukses_checkout'] = "Pesanan Anda berhasil dikirim! Silakan tunggu verifikasi admin.";

            // Alihkan ke halaman sukses dengan melempar ID Pesanan lewat URL parameter
            header("Location: konfirmasi_sukses.php?id_pesanan=" . $id_pesanan_baru);
            exit();
        } else {
            echo "<script>alert('Gagal menyimpan pesanan: " . mysqli_error($conn) . "'); window.location='checkout.php';</script>";
        }
    } else {
        echo "<script>alert('Gagal upload bukti pembayaran. Periksa folder images/bukti_bayar/'); window.location='checkout.php';</script>";
    }
}
?>