<?php
// Mendefinisikan ROOTPATH sebagai path ke direktori root proyek
define('ROOTPATH', $_SERVER['DOCUMENT_ROOT'] . '/indomaret');
include ROOTPATH . '/includes/header.php';
include ROOTPATH . '/config/config.php';

$id = $_GET['id_produk'];
$query = "SELECT * FROM produk WHERE id_produk = '$id'";
$result = mysqli_query($connection, $query);
$row = mysqli_fetch_assoc($result);

?>
<center style="margin-top: 50px;">
    <h1>Edit Data Produk</h1>
    <form action="/indomaret/process/product_process.php" method="post">
        <input type="hidden" name="action" value="edit">

        <input type="hidden" name="id_produk" value="<?php echo $row['id_produk']; ?>">
        <div class="input-group">

            <input type="text" name="nama_produk" value="<?php echo $row['nama_produk']; ?>" id="username"
                class="custom-input" placeholder="Masukkan Nama Produk..." required>

            <input type="number" name="harga" value="<?php echo $row['harga']; ?>" id="harga" class="custom-input"
                placeholder="Harga Produk..." required>

            <input type="text" name="diskon" value="<?php echo $row['diskon']; ?>" id="diskon" class="custom-input"
                placeholder="Diskon Produk..." required>
        </div>
        <button type="submit" class="btn btn-primary">Edit Produk</button>
    </form>
</center>