<?php
define('ROOTPATH', $_SERVER['DOCUMENT_ROOT'] . '/indomaret1');
include ROOTPATH . '/config/config.php';

$action = isset($_POST['action']) ? $_POST['action'] : (isset($_GET['action']) ? $_GET['action'] : '');


if ($action == 'add') {

    $kode_transaksi = $_POST['kode_transaksi'] ?? '';
    $transaksi_date = $_POST['transaksi_date'] ?? '';
    $no_order       = $_POST['no_order'] ?? '';

    $id_donasi = $_POST['id_donasi'] ?? '';
    $id_kasir  = (int)($_POST['id_kasir'] ?? 0);

    $QRIS  = (float)($_POST['QRIS'] ?? 0);
    $tunai = (float)($_POST['tunai'] ?? 0);

    $id_produk = $_POST['id_produk'] ?? [];
    $kuantitas = $_POST['kuantitas'] ?? [];

    // Pastikan data produk berbentuk array
    if (!is_array($id_produk)) {
        $id_produk = [$id_produk];
    }

    if (!is_array($kuantitas)) {
        $kuantitas = [$kuantitas];
    }

    // Format tanggal
    $transaksi_date = str_replace('T', ' ', $transaksi_date);

    if (substr_count($transaksi_date, ':') === 1) {
        $transaksi_date .= ':00';
    }

    // Validasi data
    if (
        empty($kode_transaksi) ||
        empty($transaksi_date) ||
        empty($no_order) ||
        empty($id_produk) ||
        $id_kasir <= 0
    ) {
        echo "<script>alert('Data transaksi belum lengkap!'); window.history.back();</script>";
        exit;
    }

    mysqli_begin_transaction($connection);

    try {

        // 1. Hitung total berdasarkan database
        $total = 0;
        $detail_produk = [];

        foreach ($id_produk as $i => $id) {

            $id = (int)$id;
            $qty = max(1, (int)($kuantitas[$i] ?? 1));

            if ($id <= 0) {
                throw new Exception("Produk tidak valid!");
            }

            // Ambil harga dan diskon dari tabel produk
            $query_produk = mysqli_query(
                $connection,
                "SELECT harga, diskon FROM produk WHERE id_produk = $id"
            );

            if (!$query_produk || mysqli_num_rows($query_produk) == 0) {
                throw new Exception("Produk dengan ID $id tidak ditemukan!");
            }

            $produk = mysqli_fetch_assoc($query_produk);

            $harga = (float)$produk['harga'];
            $diskon = (float)$produk['diskon'];

            $harga_jual = ($harga - $diskon) * $qty;

            if ($harga_jual < 0) {
                throw new Exception("Harga produk tidak valid!");
            }

            $total += $harga_jual;

            $detail_produk[] = [
                'id_produk' => $id,
                'harga' => $harga,
                'diskon' => $diskon,
                'kuantitas' => $qty,
                'harga_jual' => $harga_jual
            ];
        }

        // 2. Hitung kembalian
        $pembayaran = $QRIS + $tunai;

        if ($pembayaran < $total) {
            throw new Exception("Pembayaran kurang dari total belanja!");
        }

        $kembalian = $pembayaran - $total;

        // 3. Simpan transaksi utama
        $stmt = mysqli_prepare($connection, "
            INSERT INTO transaksi
            (kode_transaksi, transaksi_date, no_order, total, QRIS, tunai, kembalian, id_donasi)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");

        mysqli_stmt_bind_param(
            $stmt,
            "sssdddds",
            $kode_transaksi,
            $transaksi_date,
            $no_order,
            $total,
            $QRIS,
            $tunai,
            $kembalian,
            $id_donasi
        );

        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Gagal menyimpan transaksi!");
        }

        // 4. Simpan semua detail produk
        $stmt_detail = mysqli_prepare($connection, "
            INSERT INTO detail_transaksi
            (kode_transaksi, id_produk, id_kasir, harga, kuantitas, diskon, harga_jual)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");

        foreach ($detail_produk as $detail) {

            $id = $detail['id_produk'];
            $harga = $detail['harga'];
            $qty = $detail['kuantitas'];
            $diskon = $detail['diskon'];
            $harga_jual = $detail['harga_jual'];

            mysqli_stmt_bind_param(
                $stmt_detail,
                "siididd",
                $kode_transaksi,
                $id,
                $id_kasir,
                $harga,
                $qty,
                $diskon,
                $harga_jual
            );

            if (!mysqli_stmt_execute($stmt_detail)) {
                throw new Exception("Gagal menyimpan detail produk!");
            }
        }

     mysqli_commit($connection);

echo "<script>
    alert('Transaksi berhasil ditambahkan!');
    window.location.href = '/indomaret1/pages/transactions/list.php';
</script>";
exit;

    } catch (Exception $e) {

        mysqli_rollback($connection);

        echo "<script>alert('" .
            addslashes($e->getMessage()) .
            "'); window.history.back();</script>";

        exit;
    }
}


mysqli_close($connection);
?>