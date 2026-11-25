<?php
// Mendefinisikan ROOTPATH sebagai path ke direktori root proyek
define('ROOTPATH', $_SERVER['DOCUMENT_ROOT'] . '/indomaret');
include ROOTPATH . '/includes/header.php';
?>

<center style="margin-top: 50px;">
    <h1>Tambah Data Produk</h1>
    <form action="/indomaret/process/product_process.php" method="post">
        <input type="hidden" name="action" value="add">
        <div class=" input-group">
            <input type="text" name="nama_produk" id="username" class="custom-input" placeholder="Masukkan Nama Produk"
                required>

            <input type="number" name="harga" id="harga" class="  custom-input" placeholder="Harga Produk" required>

            <input type="number" name="diskon" id="diskon" class="custom-input" placeholder="Diskon Produk" required>
        </div>
        <button type="submit" class="btn btn-primary">Tambah Produk</button>
    </form>
</center>