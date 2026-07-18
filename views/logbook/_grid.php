<?php

/**
 * Grid logbook ala Excel: tanggal menjadi kolom, tiap indikator dua baris (N & D).
 *
 * @var yii\web\View $this
 * @var app\models\IndikatorUnit[] $penugasan
 * @var array $isian isian[iu_id][tanggal_ke] => Logbook
 * @var string $bulan format Y-m
 * @var bool $editable true = kepala unit (sel dalam jendela 7 hari bisa diisi)
 */

use app\models\Logbook;
use yii\helpers\Html;

$jumlahHari = (int) date('t', strtotime($bulan . '-01'));
?>
<div class="tabel-bungkus">
<table class="tabel tabel-logbook">
    <thead>
    <tr>
        <th class="tengah">No</th>
        <th>Nama Indikator</th>
        <th class="tengah">Jenis</th>
        <th class="tengah">Target</th>
        <th class="tengah">N/D</th>
        <?php for ($h = 1; $h <= $jumlahHari; $h++): ?>
            <th class="tengah"><?= $h ?></th>
        <?php endfor ?>
        <th class="tengah">Total N/D</th>
        <th class="tengah">Capaian</th>
        <th class="tengah">Status</th>
    </tr>
    </thead>
    <tbody>
    <?php $no = 0; foreach ($penugasan as $iu): $no++;
        $ind = $iu->indikator;
        $totalN = $totalD = 0;
        foreach ($isian[$iu->id] ?? [] as $log) {
            $totalN += $log->numerator;
            $totalD += $log->denominator;
        }
        $persen = $totalD > 0 ? round($totalN / $totalD * 100, 1) : null;
        $tercapai = $iu->isTercapai($persen);
    ?>
    <tr>
        <td class="tengah" rowspan="2"><?= $no ?></td>
        <td class="nama-indikator" rowspan="2"><?= Html::encode($ind->nama) ?></td>
        <td class="tengah" rowspan="2"><span class="badge badge-jenis <?= $ind->jenis ?>"><?= $ind->jenis ?></span></td>
        <td class="tengah angka" rowspan="2"><?= $ind->arah_target ?>&thinsp;<?= (float) $ind->target ?>%</td>
        <td class="kolom-nd">N</td>
        <?php for ($h = 1; $h <= $jumlahHari; $h++):
            $tanggal = sprintf('%s-%02d', $bulan, $h);
            $log = $isian[$iu->id][$h] ?? null;
            $bolehIsi = $editable && Logbook::bolehDiisi($tanggal);
        ?>
            <td class="sel-tgl">
                <?php if ($bolehIsi): ?>
                    <input class="sel-input" type="text" inputmode="numeric"
                           name="isian[<?= $iu->id ?>][<?= $h ?>][n]"
                           value="<?= $log !== null ? $log->numerator : '' ?>">
                <?php else: ?>
                    <span class="angka <?= $log === null ? 'sel-kunci' : '' ?>"><?= $log !== null ? $log->numerator : '·' ?></span>
                <?php endif ?>
            </td>
        <?php endfor ?>
        <td class="total"><?= $totalN ?></td>
        <td class="tengah angka" rowspan="2" style="font-weight:700;color:<?= $persen === null ? '#94a3b8' : ($tercapai ? '#15803d' : '#dc2626') ?>">
            <?= $persen !== null ? $persen . '%' : '—' ?>
        </td>
        <td class="tengah" rowspan="2">
            <?php if ($persen === null): ?>
                <span class="badge" style="background:#f1f5f9;color:#94a3b8;border:1px solid #e2e8f0">Belum ada data</span>
            <?php elseif ($tercapai): ?>
                <span class="badge badge-hijau">Tercapai</span>
            <?php else: ?>
                <span class="badge badge-merah">Tidak Tercapai</span>
            <?php endif ?>
        </td>
    </tr>
    <tr>
        <td class="kolom-nd">D</td>
        <?php for ($h = 1; $h <= $jumlahHari; $h++):
            $tanggal = sprintf('%s-%02d', $bulan, $h);
            $log = $isian[$iu->id][$h] ?? null;
            $bolehIsi = $editable && Logbook::bolehDiisi($tanggal);
        ?>
            <td class="sel-tgl">
                <?php if ($bolehIsi): ?>
                    <input class="sel-input" type="text" inputmode="numeric"
                           name="isian[<?= $iu->id ?>][<?= $h ?>][d]"
                           value="<?= $log !== null ? $log->denominator : '' ?>">
                <?php else: ?>
                    <span class="angka <?= $log === null ? 'sel-kunci' : '' ?>"><?= $log !== null ? $log->denominator : '·' ?></span>
                <?php endif ?>
            </td>
        <?php endfor ?>
        <td class="total"><?= $totalD ?></td>
    </tr>
    <?php endforeach ?>
    <?php if ($no === 0): ?>
        <tr><td colspan="<?= 8 + $jumlahHari ?>" class="tengah" style="padding:24px;color:#94a3b8">
            Belum ada indikator yang ditugaskan ke unit ini.
        </td></tr>
    <?php endif ?>
    </tbody>
</table>
</div>
