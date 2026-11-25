<?php
// Mendefinisikan ROOTPATH sebagai path ke direktori root proyek
define('ROOTPATH', $_SERVER['DOCUMENT_ROOT'] . '/indomaret1');
include ROOTPATH . '/includes/header.php';
?>

<h2 class="page-title">DATA TRANSAKSI</h2>

<div class="table-container">
    <table class="styled-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Kode Transaksi</th>
                <th>Tanggal</th>
                <th>No Order</th>
                <th>Total</th>
                <th>QRIS</th>
                <th>Tunai</th>
                <th>Kembalian</th>
                <th>ID Donasi</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            include '../../config/config.php';
            $query = "SELECT * FROM transaksi ORDER BY transaksi_date DESC";
            $result = mysqli_query($connection, $query);
            $no = 1;
            while ($row = mysqli_fetch_assoc($result)) {
            ?>
            <tr>
                <td><?php echo $no++; ?></td>
                <td><?php echo htmlspecialchars($row['kode_transaksi']); ?></td>
                <td><?php echo htmlspecialchars($row['transaksi_date']); ?></td>
                <td><?php echo htmlspecialchars($row['no_order']); ?></td>
                <td><?php echo number_format($row['total'], 0, ',', '.'); ?></td>
                <td><?php echo number_format($row['QRIS'], 0, ',', '.'); ?></td>
                <td><?php echo number_format($row['tunai'], 0, ',', '.'); ?></td>
                <td><?php echo number_format($row['kembalian'], 0, ',', '.'); ?></td>
                <td><?php echo htmlspecialchars($row['id_donasi']); ?></td>
                <td>
                    <a href='transaction_detail.php?kode_transaksi=<?php echo urlencode($row['kode_transaksi']); ?>'
                        class='btn btn-detail'>Detail</a>
                </td>
            </tr>
            <?php
            }
            ?>
        </tbody>
    </table>
</div>

<div class="add-bottom">
    <a href="/indomaret1/pages/transactions/add.php" class="btn btn-primary">
        + Tambah Transaksi
    </a>
</div>

<?php
include ROOTPATH . '/includes/footer.php';
?>
