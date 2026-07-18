<?php

/**
 * Logbook untuk admin/super admin: pilih unit, lihat isian (baca saja).
 *
 * @var yii\web\View $this
 * @var app\models\Unit $unit
 * @var app\models\Unit[] $units
 * @var app\models\IndikatorUnit[] $penugasan
 * @var array $isian
 * @var string $bulan
 */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Logbook';
$namaBulan = Yii::$app->formatter->asDate($bulan . '-01', 'MMMM yyyy');
?>

<div class="dua-panel">
    <div class="sub-sidebar tanpa-cetak">
        <div class="kepala">Pilih Unit</div>
        <?php foreach ($units as $u): ?>
            <a href="<?= Url::to(['index', 'unit_id' => $u->id, 'bulan' => $bulan]) ?>"
               class="<?= $u->id === $unit->id ? 'aktif' : '' ?>"><?= Html::encode($u->nama) ?></a>
        <?php endforeach ?>
    </div>

    <div class="panel-isi">
        <div class="tanpa-cetak" style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px">
            <div>
                <h2 style="margin:0;font-size:16px">Logbook — <?= Html::encode($unit->nama) ?></h2>
                <div style="font-size:11.5px;color:#94a3b8">Pratinjau isian logbook per bulan (baca saja)</div>
            </div>
            <div style="display:flex;gap:8px;align-items:center">
                <form method="get" class="form-kotak" style="margin:0">
                    <input type="hidden" name="unit_id" value="<?= $unit->id ?>">
                    <input type="month" name="bulan" value="<?= Html::encode($bulan) ?>"
                           onchange="this.form.submit()" style="width:auto;padding:6px 9px;font-size:12px">
                </form>
                <button type="button" class="btn btn-garis" onclick="window.print()">📄 Cetak / PDF</button>
            </div>
        </div>

        <div style="font-size:12px;color:#64748b;margin-bottom:10px">
            Laporan — <b style="color:#293681"><?= Html::encode($unit->nama) ?></b> — <?= Html::encode($namaBulan) ?>
        </div>

        <?= $this->render('_grid', [
            'penugasan' => $penugasan,
            'isian' => $isian,
            'bulan' => $bulan,
            'editable' => false,
        ]) ?>
    </div>
</div>
