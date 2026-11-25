<?php
// Mendefinisikan ROOTPATH sebagai path ke direktori root proyek
define('ROOTPATH', $_SERVER['DOCUMENT_ROOT'] . '/indomaret');
include ROOTPATH . '/includes/header.php';
include ROOTPATH . '/config/config.php';

$id = $_GET['id_donasi'];
$query = "SELECT * FROM donasi WHERE id_donasi = '$id'";
$result = mysqli_query($connection, $query);
$row = mysqli_fetch_assoc($result);

?>
<center style="margin-top: 50px;">
    <h1>Edit Data Donasi</h1>
    <form action="/indomaret/process/donation_process.php" method="post">
        <input type="hidden" name="action" value="edit">

        <input type="hidden" name="id_donasi" value="<?php echo $row['id_donasi']; ?>">
        <div class="input-group">

            <input type="text" name="nama_donasi" value="<?php echo $row['nama_donasi']; ?>" id="username"
                class="custom-input" placeholder="Masukkan Nama..." required>

            <input type="number" name="harga" value="<?php echo $row['harga']; ?>" id="harga" class="custom-input"
                placeholder="Masukkan Nominal..." required>
        </div>
        <button type="submit" class="btn btn-primary">Edit Donasi</button>
    </form>
</center>