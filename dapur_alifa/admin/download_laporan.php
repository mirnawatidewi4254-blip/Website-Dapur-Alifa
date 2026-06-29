<?php
session_start();
include('../koneksi.php');

if (!isset($_SESSION['login_admin_status']) || $_SESSION['role'] !== 'admin') {
    exit("Akses ditolak.");
}

// Pengaturan Header agar browser mendownload sebagai file Excel (.xls)
header("Content-Type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=Laporan_Pesanan_Dapur_Alifa.xls");
header("Pragma: no-cache");
header("Expires: 0");
?>

<h3>LAPORAN DATA PESANAN - DAPUR ALIFA</h3>
<p>Tanggal Cetak: <?php echo date('d-m-Y H:i:s'); ?> WIB</p>

<table border="1">
    <thead>
        <tr style="background-color: #FF661D; color: white; font-weight: bold;">
            <th>No. Pesanan</th>
            <th>ID User</th>
            <th>Menu yang Dipesan</th>
            <th>Tanggal Pengantaran</th>
            <th>Jam Pengantaran</th>
            <th>Alamat Pengiriman</th>
            <th>Total Bayar</th>
            <th>Catatan</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $query = mysqli_query($conn, "SELECT * FROM pesanan ORDER BY id_pesanan DESC");
        while($row = mysqli_fetch_assoc($query)) {
            $id_pesanan = $row['id_pesanan'];
            
            // Ambil rincian menu detail
            $daftar_item = [];
            $query_detail = mysqli_query($conn, "SELECT pesanan_detail.*, menu.nama_menu FROM pesanan_detail 
                                                 JOIN menu ON pesanan_detail.id_menu = menu.id_menu 
                                                 WHERE pesanan_detail.id_pesanan = '$id_pesanan'");
            while($item = mysqli_fetch_assoc($query_detail)) {
                $daftar_item[] = $item['nama_menu'] . " (" . $item['jumlah'] . ")";
            }
            $menu_dipesan = implode(", ", $daftar_item);
        ?>
        <tr>
            <td>#<?php echo $id_pesanan; ?></td>
            <td><?php echo $row['id_user']; ?></td>
            <td><?php echo $menu_dipesan; ?></td>
            <td><?php echo date('d-m-Y', strtotime($row['tanggal_pengantaran'])); ?></td>
            <td><?php echo $row['jam_pengantaran']; ?> WIB</td>
            <td><?php echo isset($row['alamat_pengiriman']) ? $row['alamat_pengiriman'] : '-'; ?></td>
            <td>Rp <?php echo number_format($row['total_bayar'], 0, ',', '.'); ?></td>
            <td><?php echo $row['catatan']; ?></td>
            <td><?php echo $row['status_pesanan']; ?></td>
        </tr>
        <?php } ?>
    </tbody>
</table>