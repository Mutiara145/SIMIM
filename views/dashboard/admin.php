<?php

/** @var yii\web\View $this */
/** @var string $bulan */
/** @var array $statUnit */
/** @var int $totalIndikator */
/** @var int $penuh */
/** @var int $sebagian */
/** @var int $nol */

use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\View;

$this->title = 'Dashboard';
$this->registerJsFile('https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js', ['position' => View::POS_HEAD]);

$namaBulan = Yii::$app->formatter->asDate($bulan . '-01', 'MMMM yyyy');

// Warna batang: hijau = semua tercapai, kuning = sebagian, merah = belum sama sekali
$label = $nilai = $warna = $keterangan = [];
foreach ($statUnit as $s) {
    $label[] = $s['nama'];
    $nilai[] = $s['persen'];
    $warna[] = $s['total'] > 0 && $s['tercapai'] === $s['total'] ? '#22c55e'
        : ($s['tercapai'] > 0 ? '#f59e0b' : '#ef4444');
    $keterangan[] = $s['tercapai'] . ' dari ' . $s['total'] . ' indikator tercapai';
}
?>

<div class="grid-kartu kolom-4">
    <div class="kartu kartu-angka">
        <div class="label">Total Indikator Aktif</div>
        <div class="angka" style="color:#293681"><?= $totalIndikator ?></div>
        <div class="sub">3 jenis: INM, IMP-RS, IMU</div>
    </div>
    <div class="kartu kartu-angka">
        <div class="label">Unit Memenuhi Semua Target</div>
        <div class="angka" style="color:#22c55e"><?= $penuh ?></div>
        <div class="sub">dari <?= count($statUnit) ?> unit</div>
    </div>
    <div class="kartu kartu-angka">
        <div class="label">Unit Memenuhi Sebagian</div>
        <div class="angka" style="color:#f59e0b"><?= $sebagian ?></div>
        <div class="sub">perlu perhatian</div>
    </div>
    <div class="kartu kartu-angka">
        <div class="label">Unit Belum Sama Sekali</div>
        <div class="angka" style="color:#ef4444"><?= $nol ?></div>
        <div class="sub">belum ada indikator tercapai</div>
    </div>
</div>

<div style="display:grid;grid-template-columns:3fr 2fr;gap:14px">
    <div class="kartu">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px">
            <div>
                <div class="judul-kartu" style="margin:0">Pencapaian Indikator per Unit — <?= Html::encode($namaBulan) ?></div>
                <div style="font-size:11px;color:#94a3b8">Diurutkan dari terendah · Hijau = semua target terpenuhi</div>
            </div>
            <form method="get" class="form-kotak" style="margin:0">
                <input type="month" name="bulan" value="<?= Html::encode($bulan) ?>"
                       onchange="this.form.submit()" style="width:auto;padding:5px 9px;font-size:12px">
            </form>
        </div>
        <div style="height:<?= max(300, count($statUnit) * 26) ?>px">
            <canvas id="grafik-unit"></canvas>
        </div>
    </div>

    <div class="kartu" style="max-height:75vh;overflow:auto">
        <div class="judul-kartu">Detail Target &amp; Capaian</div>
        <?php foreach ($statUnit as $s): ?>
            <?php $warnaBaris = $s['total'] > 0 && $s['tercapai'] === $s['total'] ? '#22c55e'
                : ($s['tercapai'] > 0 ? '#f59e0b' : '#ef4444'); ?>
            <div style="margin-bottom:12px">
                <div style="display:flex;justify-content:space-between;margin-bottom:4px">
                    <span style="font-size:12px;font-weight:600"><?= Html::encode($s['nama']) ?></span>
                    <span style="font-size:11px">
                        <span class="angka" style="color:#94a3b8;font-family:'JetBrains Mono',monospace"><?= $s['tercapai'] ?>/<?= $s['total'] ?> tercapai</span>
                        <b style="color:<?= $warnaBaris ?>;margin-left:6px"><?= $s['persen'] ?>%</b>
                    </span>
                </div>
                <div style="height:8px;background:#e2e8f0;border-radius:4px;overflow:hidden">
                    <div style="height:100%;width:<?= $s['persen'] ?>%;background:<?= $warnaBaris ?>;border-radius:4px"></div>
                </div>
            </div>
        <?php endforeach ?>
    </div>
</div>

<?php
$js = 'new Chart(document.getElementById("grafik-unit"), {
    type: "bar",
    data: {
        labels: ' . Json::encode($label) . ',
        datasets: [{
            data: ' . Json::encode($nilai) . ',
            backgroundColor: ' . Json::encode($warna) . ',
            borderRadius: 4,
            maxBarThickness: 18
        }]
    },
    options: {
        indexAxis: "y",
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: function (ctx) {
                        var info = ' . Json::encode($keterangan) . ';
                        return info[ctx.dataIndex] + " (" + ctx.parsed.x + "%)";
                    }
                }
            }
        },
        scales: {
            x: { min: 0, max: 100, ticks: { callback: v => v + "%" } },
            y: { ticks: { font: { size: 11 } } }
        }
    }
});';
$this->registerJs($js);
?>
