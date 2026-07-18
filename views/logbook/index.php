<?php

/**
 * Logbook kepala unit: grid isian bulan berjalan.
 *
 * @var yii\web\View $this
 * @var app\models\Unit $unit
 * @var app\models\IndikatorUnit[] $penugasan
 * @var array $isian
 * @var string $bulan
 */

use app\models\Logbook;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Logbook — Unit ' . $unit->nama;
$namaBulan = Yii::$app->formatter->asDate($bulan . '-01', 'MMMM yyyy');
$bulanIni = $bulan === date('Y-m');
?>

<div class="tanpa-cetak" style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px">
    <div style="font-size:12.5px;color:#64748b">
        Periode: <b style="color:#293681"><?= Html::encode($namaBulan) ?></b>
        &nbsp;·&nbsp; Unit: <b style="color:#4274D9"><?= Html::encode($unit->nama) ?></b>
        &nbsp;·&nbsp; Kolom yang bisa diisi hanya <b><?= Logbook::BATAS_HARI ?> hari</b> ke belakang.
    </div>
    <div style="display:flex;gap:8px;align-items:center">
        <form method="get" class="form-kotak" style="margin:0">
            <input type="month" name="bulan" value="<?= Html::encode($bulan) ?>"
                   onchange="this.form.submit()" style="width:auto;padding:6px 9px;font-size:12px">
        </form>
        <button type="button" class="btn btn-garis" onclick="window.print()">🖨 Cetak</button>
        <?php if ($bulanIni): ?>
            <button type="submit" form="form-logbook" class="btn btn-biru">💾 Simpan Isian</button>
        <?php endif ?>
    </div>
</div>

<?php if ($bulanIni): ?>
    <?= Html::beginForm(['logbook/simpan', 'bulan' => $bulan], 'post', ['id' => 'form-logbook']) ?>
<?php endif ?>

<?= $this->render('_grid', [
    'penugasan' => $penugasan,
    'isian' => $isian,
    'bulan' => $bulan,
    'editable' => $bulanIni,
]) ?>

<?php if ($bulanIni): ?>
    <?= Html::endForm() ?>
<?php endif ?>
