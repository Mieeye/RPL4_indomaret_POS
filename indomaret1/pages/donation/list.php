<?php
// Mendefinisikan ROOTPATH sebagai path ke direktori root proyek
define('ROOTPATH', $_SERVER['DOCUMENT_ROOT'] . '/indomaret1');
include ROOTPATH . '/includes/header.php';
?>

<h2 class="page-title">DATA DONASI</h2>

<div class="table-container">
    <table class="styled-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Donasi</th>
                <th>Nominal</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            include '../../config/config.php';
            $query = "SELECT * FROM donasi";
            $result = mysqli_query($connection, $query);
            $no = 1;
            while ($row = mysqli_fetch_assoc($result)) {
            ?>
            <tr>
                <td><?php echo $no++; ?></td>
                <td><?php echo htmlspecialchars($row['nama_donasi']); ?></td>
                <td><?php echo number_format($row['harga'], 0, ',', '.'); ?></td>
                <td>
                    <div class="action-buttons">
                        <a href='edit.php?id_donasi=<?php echo $row['id_donasi']; ?>' class='btn btn-warning'>Edit</a>
                        <a href='/indomaret/process/donation_process.php?action=delete&id_donasi=<?php echo $row['id_donasi']; ?>'
                            class='btn btn-danger'
                            onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">Hapus</a>
                    </div>
                </td>
            </tr>
            <?php
            }
            ?>
        </tbody>
    </table>
</div>

<div class="add-bottom">
    <a href="/indomaret/pages/donation/add.php" class="btn btn-primary">
        + Tambah Donasi
    </a>
</div>

<?php
include ROOTPATH . '/includes/footer.php';
?>
