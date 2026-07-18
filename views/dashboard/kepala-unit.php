<?php

/** @var yii\web\View $this */
/** @var string $bulan */
/** @var array $baris */
/** @var array $hitung */

use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\View;

$identity = Yii::$app->user->identity;
$this->title = 'Dashboard — Unit ' . $identity->unit->nama;
$this->registerJsFile('https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js', ['position' => View::POS_HEAD]);

$namaBulan = Yii::$app->formatter->asDate($bulan . '-01', 'MMMM yyyy');
$warnaStatus = ['hijau' => '#22c55e', 'kuning' => '#f59e0b', 'merah' => '#ef4444'];
$labelStatus = [
    'hijau' => 'Sudah diisi',
    'kuning' => 'Mendekati batas pengisian',
    'merah' => 'Melewati batas pengisian',
];

$label = $nilai = $warna = [];
foreach ($baris as $b) {
    $nama = $b['iu']->indikator->nama;
    $label[] = mb_strlen($nama) > 45 ? mb_substr($nama, 0, 45) . '…' : $nama;
    $nilai[] = $b['capaian']['persen'] ?? 0;
    $warna[] = $warnaStatus[$b['status']];
}
?>

<div class="grid-kartu kolom-3">
    <div class="kartu kartu-angka" style="background:#dcfce7">
        <div class="label">Indikator Selesai Diisi</div>
        <div class="angka" style="color:#15803d"><?= $hitung['hijau'] ?></div>
        <div class="sub">semua tanggal s/d hari ini terisi</div>
    </div>
    <a class="kartu kartu-angka" style="background:#fef9c3;display:block" href="<?= Url::to(['/logbook/index']) ?>">
        <div class="label">Mendekati Batas Pengisian</div>
        <div class="angka" style="color:#854d0e"><?= $hitung['kuning'] ?></div>
        <div class="sub">klik untuk mengisi logbook →</div>
    </a>
    <div class="kartu kartu-angka" style="background:#fee2e2">
        <div class="label">Melewati Batas Pengisian</div>
        <div class="angka" style="color:#991b1b"><?= $hitung['merah'] ?></div>
        <div class="sub">lewat batas <?= \app\models\Logbook::BATAS_HARI ?> hari</div>
    </div>
</div>

<div class="kartu" style="margin-bottom:14px">
    <div class="judul-kartu">Capaian Indikator — <?= Html::encode($namaBulan) ?></div>
    <div style="font-size:11px;color:#94a3b8;margin-top:-8px;margin-bottom:10px">
        Warna batang = status pengisian logbook (hijau: terisi, kuning: mendekati batas, merah: melewati batas)
    </div>
    <div style="height:<?= max(220, count($baris) * 30) ?>px">
        <canvas id="grafik-indikator"></canvas>
    </div>
</div>

<div class="kartu">
    <div class="judul-kartu">Status Pengisian — Unit <?= Html::encode($identity->unit->nama) ?></div>
    <table class="tabel" style="border:none">
        <thead>
        <tr>
            <th>Indikator</th>
            <th class="tengah">Jenis</th>
            <th class="tengah">Capaian</th>
            <th class="tengah">Target</th>
            <th class="tengah">Status Capaian</th>
            <th class="tengah">Status Pengisian</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($baris as $b): ?>
            <?php $ind = $b['iu']->indikator; ?>
            <tr>
                <td style="font-size:12px"><?= Html::encode($ind->nama) ?></td>
                <td class="tengah"><span class="badge badge-jenis <?= $ind->jenis ?>"><?= $ind->jenis ?></span></td>
                <td class="tengah angka">
                    <?= $b['capaian']['persen'] !== null ? $b['capaian']['persen'] . '%' : '—' ?>
                </td>
                <td class="tengah angka"><?= $ind->arah_target ?> <?= (float) $ind->target ?>%</td>
                <td class="tengah">
                    <?php if ($b['capaian']['persen'] === null): ?>
                        <span class="badge" style="background:#f1f5f9;color:#94a3b8;border:1px solid #e2e8f0">Belum ada data</span>
                    <?php elseif ($b['tercapai']): ?>
                        <span class="badge badge-hijau">Tercapai</span>
                    <?php else: ?>
                        <span class="badge badge-merah">Tidak Tercapai</span>
                    <?php endif ?>
                </td>
                <td class="tengah">
                    <span class="badge badge-<?= $b['status'] ?>"><?= $labelStatus[$b['status']] ?></span>
                </td>
            </tr>
        <?php endforeach ?>
        </tbody>
    </table>
</div>

<?php
$js = 'new Chart(document.getElementById("grafik-indikator"), {
    type: "bar",
    data: {
        labels: ' . Json::encode($label) . ',
        datasets: [{
            data: ' . Json::encode($nilai) . ',
            backgroundColor: ' . Json::encode($warna) . ',
            borderRadius: 4,
            maxBarThickness: 16
        }]
    },
    options: {
        indexAxis: "y",
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: { callbacks: { label: ctx => "Capaian: " + ctx.parsed.x + "%" } }
        },
        scales: {
            x: { min: 0, max: 100, ticks: { callback: v => v + "%" } },
            y: { ticks: { font: { size: 10 } } }
        }
    }
});';
$this->registerJs($js);
?>
