<?php
// Mendefinisikan ROOTPATH sebagai path ke direktori root proyek
define('ROOTPATH', $_SERVER['DOCUMENT_ROOT'] . '/indomaret');
include ROOTPATH . '/includes/header.php';
?>

<center style="margin-top: 50px;">
    <h1>Tambah Data Donasi</h1>
    <form action="/indomaret/process/donation_process.php" method="post">
        <input type="hidden" name="action" value="add">
        <div class=" input-group">
            <input type="text" name="nama_donasi" id="username" class="custom-input" placeholder="Masukkan Nama.."
                required>

            <input type="text" name="harga" id="harga" class="  custom-input" placeholder="Masukkan Nominal.." required>
        </div>
        <button type="submit" class="btn btn-primary">Tambah Donasi</button>
    </form>
</center>