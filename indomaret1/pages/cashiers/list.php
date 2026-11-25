<?php
// Mendefinisikan ROOTPATH sebagai path ke direktori root proyek
define('ROOTPATH', $_SERVER['DOCUMENT_ROOT'] . '/indomaret1');
include ROOTPATH . '/includes/header.php';
?>

<h2 class="page-title">DATA KASIR</h2>

<div class="table-container">
    <table class="styled-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Kasir</th>
                <th>No Telepon</th>
                <th>Alamat</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            include '../../config/config.php';
            $query = "SELECT * FROM kasir";
            $result = mysqli_query($connection, $query);
            $no = 1;
            while ($row = mysqli_fetch_assoc($result)) {
            ?>
            <tr>
                <td><?php echo $no++; ?></td>
                <td><?php echo htmlspecialchars($row['nama_kasir']); ?></td>
                <td><?php echo htmlspecialchars($row['no_telp']); ?></td>
                <td><?php echo htmlspecialchars($row['alamat']); ?></td>
                <td>
                    <div class="action-buttons">
                        <a href='edit.php?id_kasir=<?php echo $row['id_kasir']; ?>' class='btn btn-warning'>Edit</a>
                        <a href='/indomaret1/process/cashiers_process.php?action=delete&id_kasir=<?php echo $row['id_kasir']; ?>'
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
    <a href="/indomaret1/pages/cashiers/add.php" class="btn btn-primary">
        + Tambah Kasir
    </a>
</div>

<?php
include ROOTPATH . '/includes/footer.php';
?>
