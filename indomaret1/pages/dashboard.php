<?php
define('ROOTPATH', $_SERVER['DOCUMENT_ROOT'] . '/indomaret1');
include ROOTPATH . '/config/config.php';

function dashboardMoney($n) {
    return 'Rp ' . number_format((float)$n, 0, ',', '.');
}

// ===== QUERY DATA =====
$totalTransaksi = (int)(mysqli_fetch_assoc(mysqli_query($connection, "SELECT COUNT(*) AS total FROM transaksi"))['total'] ?? 0);
$omzet         = (float)(mysqli_fetch_assoc(mysqli_query($connection, "SELECT COALESCE(SUM(total),0) AS total FROM transaksi"))['total'] ?? 0);
$totalProduk   = (int)(mysqli_fetch_assoc(mysqli_query($connection, "SELECT COUNT(*) AS total FROM produk"))['total'] ?? 0);
$totalKasir    = (int)(mysqli_fetch_assoc(mysqli_query($connection, "SELECT COUNT(*) AS total FROM kasir"))['total'] ?? 0);
$totalDonasi   = (int)(mysqli_fetch_assoc(mysqli_query($connection, "SELECT COUNT(*) AS total FROM donasi"))['total'] ?? 0);

// Omzet 7 hari terakhir
$dailyRows = [];
$qDaily = mysqli_query($connection, "
    SELECT DATE(transaksi_date) AS tgl, COALESCE(SUM(total),0) AS total
    FROM transaksi
    WHERE transaksi_date >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
    GROUP BY DATE(transaksi_date)
");
while ($r = mysqli_fetch_assoc($qDaily)) {
    $dailyRows[$r['tgl']] = (float)$r['total'];
}

$dailyChart = [];
$maxDaily = 0;
for ($i = 6; $i >= 0; $i--) {
    $date  = date('Y-m-d', strtotime("-$i days"));
    $value = $dailyRows[$date] ?? 0;
    $dailyChart[] = ['label' => date('d/m', strtotime($date)), 'value' => $value];
    if ($value > $maxDaily) $maxDaily = $value;
}

// Transaksi terbaru
$recentTransactions = mysqli_query($connection, "
    SELECT kode_transaksi, transaksi_date, total
    FROM transaksi
    ORDER BY transaksi_date DESC, kode_transaksi DESC
    LIMIT 6
");

// Produk terlaris
$topProducts = mysqli_query($connection, "
    SELECT p.nama_produk, SUM(d.kuantitas) AS terjual
    FROM detail_transaksi d
    INNER JOIN produk p ON p.id_produk = d.id_produk
    GROUP BY d.id_produk, p.nama_produk
    ORDER BY terjual DESC
    LIMIT 5
");

include ROOTPATH . '/includes/header.php';
?>

<style>
/* ===== Dashboard Modern ===== */
.dashboard-wrap {
    max-width: 1180px;
    margin: 32px auto 60px;
    padding: 0 22px;
    color: #172033;
}

.dashboard-heading {
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    gap: 16px;
    margin-bottom: 28px;
    flex-wrap: wrap;
}

.dashboard-heading h1 {
    font-size: 28px;
    margin: 0 0 6px;
    color: #153b68;
    letter-spacing: -0.3px;
}

.dashboard-heading p {
    margin: 0;
    color: #64748b;
    font-size: 14px;
}

.dashboard-date {
    font-size: 13px;
    color: #475569;
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 10px 16px;
    box-shadow: 0 2px 8px rgba(15, 40, 70, 0.04);
}

/* ===== Stat Cards ===== */
.dashboard-grid {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 18px;
    margin-bottom: 22px;
}

.dash-card {
    background: #fff;
    border: 1px solid #e7edf4;
    border-radius: 16px;
    padding: 20px 22px;
    box-shadow: 0 6px 20px rgba(15, 40, 70, 0.05);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    position: relative;
    overflow: hidden;
}

.dash-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 28px rgba(15, 40, 70, 0.09);
}

.dash-card::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 4px;
    background: linear-gradient(90deg, #d61627, #00569c);
    opacity: 0;
    transition: opacity 0.2s ease;
}

.dash-card:hover::before {
    opacity: 1;
}

.dash-card-top {
    display: flex;
    justify-content: space-between;
    align-items: center;
    color: #64748b;
    font-size: 13px;
    font-weight: 600;
}

.dash-icon {
    width: 42px;
    height: 42px;
    border-radius: 12px;
    display: grid;
    place-items: center;
    font-size: 20px;
}

.dash-icon.blue  { background: #e8f1ff; }
.dash-icon.green { background: #e8f8ef; }
.dash-icon.orange{ background: #fff4e8; }
.dash-icon.red   { background: #fdeeee; }

.dash-number {
    font-size: 26px;
    font-weight: 750;
    color: #142d4e;
    margin-top: 14px;
    line-height: 1.2;
}

.dash-note {
    font-size: 12px;
    color: #94a3b8;
    margin-top: 6px;
}

/* ===== Panels ===== */
.dashboard-columns {
    display: grid;
    grid-template-columns: 1.5fr 1fr;
    gap: 18px;
    margin-bottom: 18px;
}

.dash-panel {
    background: #fff;
    border: 1px solid #e7edf4;
    border-radius: 16px;
    padding: 22px;
    box-shadow: 0 6px 20px rgba(15, 40, 70, 0.04);
    min-width: 0;
}

.dash-panel h2 {
    font-size: 17px;
    margin: 0 0 4px;
    color: #17385f;
}

.panel-subtitle {
    font-size: 12px;
    color: #94a3b8;
    margin-bottom: 20px;
}

/* ===== Chart ===== */
.chart {
    height: 210px;
    display: flex;
    align-items: flex-end;
    gap: 10px;
    padding: 8px 2px 0;
    border-bottom: 1px solid #e8edf3;
}

.chart-col {
    height: 100%;
    flex: 1;
    display: flex;
    flex-direction: column;
    justify-content: flex-end;
    align-items: center;
    gap: 7px;
    min-width: 0;
}

.chart-value {
    font-size: 10px;
    color: #64748b;
    white-space: nowrap;
}

.chart-bar-area {
    height: 150px;
    width: 100%;
    display: flex;
    align-items: flex-end;
    justify-content: center;
}

.chart-bar {
    width: min(36px, 78%);
    min-height: 4px;
    border-radius: 8px 8px 3px 3px;
    background: linear-gradient(180deg, #3b9ae8, #07549a);
    transition: height 0.4s ease;
}

.chart-bar:hover {
    background: linear-gradient(180deg, #d61627, #9b0f1c);
}

.chart-label {
    font-size: 11px;
    color: #64748b;
}

/* ===== Table ===== */
.dash-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 13px;
}

.dash-table th {
    text-align: left;
    color: #64748b;
    font-weight: 600;
    background: #f7f9fc;
    padding: 11px 12px;
}

.dash-table td {
    padding: 13px 12px;
    border-bottom: 1px solid #edf1f5;
    color: #334155;
}

.dash-table tr:last-child td {
    border-bottom: 0;
}

.dash-table tr:hover td {
    background: #f8fafc;
}

.dash-table .money {
    text-align: right;
    white-space: nowrap;
    font-weight: 650;
}

.dash-table a {
    color: #07549a;
    text-decoration: none;
    font-weight: 600;
}

.dash-table a:hover {
    color: #d61627;
}

/* ===== Actions ===== */
.dash-actions {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    margin-top: 18px;
}

.dash-link {
    display: inline-block;
    text-decoration: none;
    background: #07549a;
    color: #fff;
    padding: 10px 16px;
    border-radius: 9px;
    font-size: 13px;
    font-weight: 600;
    transition: background 0.2s ease, transform 0.15s ease;
}

.dash-link:hover {
    background: #053d72;
    transform: translateY(-1px);
}

.dash-link.red {
    background: #d61627;
}

.dash-link.red:hover {
    background: #b81220;
}

.dash-empty {
    color: #94a3b8;
    text-align: center;
    padding: 24px 8px;
    font-size: 13px;
}

/* Donasi card khusus */
.donasi-box {
    background: linear-gradient(135deg, #fff5f5, #f0f7ff);
    border-radius: 14px;
    padding: 22px;
    margin-top: 8px;
    text-align: center;
}

.donasi-box .dash-number {
    font-size: 36px;
    color: #d61627;
}

/* Responsive */
@media (max-width: 900px) {
    .dashboard-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    .dashboard-columns { grid-template-columns: 1fr; }
}

@media (max-width: 520px) {
    .dashboard-wrap { padding: 0 14px; margin-top: 20px; }
    .dashboard-grid { gap: 12px; }
    .dash-card { padding: 16px; }
    .dash-number { font-size: 22px; }
    .dash-panel { padding: 16px; overflow-x: auto; }
    .dash-table { min-width: 480px; }
}
</style>

<main class="dashboard-wrap">

    <!-- Header -->
    <div class="dashboard-heading">
        <div>
            <h1>Dashboard Indomaret</h1>
            <p>Ringkasan real-time produk, kasir, donasi & transaksi</p>
        </div>
        <div class="dashboard-date">📅 <?= htmlspecialchars(date('d F Y')); ?></div>
    </div>

    <!-- Stat Cards -->
    <section class="dashboard-grid">
        <article class="dash-card">
            <div class="dash-card-top">
                <span>Total Transaksi</span>
                <span class="dash-icon blue">🧾</span>
            </div>
            <div class="dash-number"><?= number_format($totalTransaksi, 0, ',', '.'); ?></div>
            <div class="dash-note">Semua transaksi tercatat</div>
        </article>

        <article class="dash-card">
            <div class="dash-card-top">
                <span>Total Omzet</span>
                <span class="dash-icon green">💰</span>
            </div>
            <div class="dash-number" style="font-size: clamp(18px, 2.1vw, 26px)">
                <?= dashboardMoney($omzet); ?>
            </div>
            <div class="dash-note">Akumulasi nilai transaksi</div>
        </article>

        <article class="dash-card">
            <div class="dash-card-top">
                <span>Data Produk</span>
                <span class="dash-icon orange">📦</span>
            </div>
            <div class="dash-number"><?= number_format($totalProduk, 0, ',', '.'); ?></div>
            <div class="dash-note">Produk terdaftar</div>
        </article>

        <article class="dash-card">
            <div class="dash-card-top">
                <span>Data Kasir</span>
                <span class="dash-icon red">👤</span>
            </div>
            <div class="dash-number"><?= number_format($totalKasir, 0, ',', '.'); ?></div>
            <div class="dash-note">Kasir aktif</div>
        </article>
    </section>

    <!-- Chart + Donasi -->
    <section class="dashboard-columns">
        <div class="dash-panel">
            <h2>Grafik Omzet 7 Hari Terakhir</h2>
            <div class="panel-subtitle">Total nilai transaksi per hari</div>
            <div class="chart">
                <?php foreach ($dailyChart as $point):
                    $height = $maxDaily > 0 ? max(4, ($point['value'] / $maxDaily) * 100) : 4;
                ?>
                <div class="chart-col">
                    <span class="chart-value">
                        <?= $point['value'] > 0
                            ? htmlspecialchars(number_format($point['value'] / 1000, 0, ',', '.') . 'k')
                            : '–'; ?>
                    </span>
                    <div class="chart-bar-area">
                        <div class="chart-bar"
                             style="height: <?= $height; ?>%"
                             title="<?= htmlspecialchars(dashboardMoney($point['value'])); ?>">
                        </div>
                    </div>
                    <span class="chart-label"><?= htmlspecialchars($point['label']); ?></span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="dash-panel">
            <h2>Ringkasan Donasi</h2>
            <div class="panel-subtitle">Jumlah pilihan donasi tersedia</div>
            <div class="donasi-box">
                <div class="dash-number"><?= number_format($totalDonasi, 0, ',', '.'); ?></div>
                <div class="dash-note">Program donasi aktif</div>
            </div>
            <div class="dash-actions">
                <a class="dash-link" href="/indomaret1/pages/donation/list.php">Lihat Data Donasi</a>
            </div>
        </div>
    </section>

    <!-- Transaksi + Produk Terlaris -->
    <section class="dashboard-columns">
        <div class="dash-panel">
            <h2>Transaksi Terbaru</h2>
            <div class="panel-subtitle">6 transaksi terakhir yang tercatat</div>
            <table class="dash-table">
                <thead>
                    <tr>
                        <th>Kode Transaksi</th>
                        <th>Tanggal</th>
                        <th class="money">Total</th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($recentTransactions && mysqli_num_rows($recentTransactions) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($recentTransactions)): ?>
                    <tr>
                        <td>
                            <a href="/indomaret1/pages/transactions/transaction_detail.php?kode_transaksi=<?= urlencode($row['kode_transaksi']); ?>">
                                <?= htmlspecialchars($row['kode_transaksi']); ?>
                            </a>
                        </td>
                        <td><?= htmlspecialchars(date('d/m/Y', strtotime($row['transaksi_date']))); ?></td>
                        <td class="money"><?= dashboardMoney($row['total']); ?></td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="3" class="dash-empty">Belum ada transaksi.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
            <div class="dash-actions">
                <a class="dash-link" href="/indomaret1/pages/transactions/list.php">Lihat Semua</a>
                <a class="dash-link red" href="/indomaret1/pages/transactions/add.php">+ Tambah Transaksi</a>
            </div>
        </div>

        <div class="dash-panel">
            <h2>Produk Terlaris</h2>
            <div class="panel-subtitle">Berdasarkan kuantitas terjual</div>
            <table class="dash-table">
                <thead>
                    <tr>
                        <th>Nama Produk</th>
                        <th class="money">Terjual</th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($topProducts && mysqli_num_rows($topProducts) > 0): ?>
                    <?php while ($product = mysqli_fetch_assoc($topProducts)): ?>
                    <tr>
                        <td><?= htmlspecialchars($product['nama_produk']); ?></td>
                        <td class="money"><?= number_format((int)$product['terjual'], 0, ',', '.'); ?></td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="2" class="dash-empty">Belum ada data penjualan.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
            <div class="dash-actions">
                <a class="dash-link" href="/indomaret1/pages/product/list.php">Kelola Produk</a>
                <a class="dash-link" href="/indomaret1/pages/cashiers/list.php">Data Kasir</a>
            </div>
        </div>
    </section>

</main>

<?php include ROOTPATH . '/includes/footer.php'; ?>