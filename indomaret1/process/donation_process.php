<?php

include '../config/config.php';

$nama_donasi = $_POST["nama_donasi"];
$harga    = $_POST["harga"];

if($_POST["action"] == "add"){
    // menambahkan data produk ke table_produk di database indomaret_RPL4
    $hasil = mysqli_query($connection, "INSERT INTO donasi VALUES (NULL, '$nama_donasi', '$harga')");

    if(!$hasil){
        echo "
        <script>
        alert('Gagal menambahkan data: " . mysqli_error($connection) . "');
        </script>";
    } else {
        echo "<script>
        alert('Produk berhasil ditambahkan.');
        window.location.href='../pages/donation/list.php';
        </script>";
    }
}else if($_POST["action"] == "edit"){
    $id_donasi = $_POST["id_donasi"];
    // mengupdate data produk di table_produk di database indomaret_RPL4
    // $test = "UPDATE produk SET nama_produk= '$nama_produk', harga='$harga', diskon='$diskon' WHERE id_produk='$id_produk'";
    // var_dump($test;
    $hasil = mysqli_query($connection, "UPDATE donasi SET nama_donasi= '$nama_donasi', harga='$harga' WHERE id_donasi='$id_donasi'");

    if(!$hasil){
        echo "
        <script>
        alert('Gagal mengupdate data : " . mysqli_error($connection) . "');
        </script>";
    } else {
        echo "<script>
        alert('Data produk berhasil diupdate.');
        window.location.href='../pages/donation/list.php';
        </script>";
    }

}else if($_GET["action"] == "delete"){
    $id_donasi = $_GET["id_donasi"];
    // menghapus data produk di table_produk di database indomaret_RPL4
    $hasil = mysqli_query($connection, "DELETE FROM donasi WHERE id_donasi='$id_donasi'");

    if(!$hasil){
        echo "
        <script>
        alert('Gagal menghapus data donasi: " . mysqli_error($connection) . "');
        </script>";
    } else {
        echo "<script>
        alert('Data berhasil dihapus.');
        window.location.href='../pages/donation/list.php';
        </script>";
    }
}