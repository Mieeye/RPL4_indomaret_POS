<?php
// Mendefinisikan ROOTPATH sebagai path ke direktori root proyek
define('ROOTPATH', $_SERVER['DOCUMENT_ROOT'] . '/indomaret');
include ROOTPATH . '/includes/header.php';
?>
<link rel="stylesheet" href="/indomaret/css/style.css">
<center style="margin-top: 50px;">
    <h1>Tambah Data Kasir</h1>
    <form action="" method="post">
        <div class="input-group">
            <input type="text" name="nama_kasir" id="username" class="custom-input" placeholder="Masukkan Nama Kasir"
                required>

            <input type="text" name="no_telp" id="no_telp" class="custom-input" placeholder="Masukkan No.Telp Kasir"
                required>

            <input type="text" name="alamat" id="alamat" class="custom-input" placeholder="Masukkan Alamat Kasir"
                required>
        </div>
        <button type="submit" class="btn btn-primary">Tambah Kasir</button>
    </form>
</center>