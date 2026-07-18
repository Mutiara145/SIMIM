<?php

/**
 * Form tambah/ubah indikator + pilih unit pelaksana.
 *
 * @var yii\web\View $this
 * @var app\models\Indikator $model
 */

use app\models\Indikator;
use app\models\Unit;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$this->title = $model->isNewRecord ? 'Tambah Indikator' : 'Ubah Indikator';
$units = ArrayHelper::map(Unit::find()->orderBy('nama')->all(), 'id', 'nama');
?>

<div class="kartu form-kotak" style="max-width:720px">
    <h2 style="margin:0 0 16px;font-size:16px"><?= Html::encode($this->title) ?></h2>

    <?php $form = ActiveForm::begin(['fieldConfig' => ['template' => "{label}\n{input}\n{error}"]]); ?>

    <?= $form->field($model, 'nama')->textInput(['maxlength' => true]) ?>

    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:14px">
        <?= $form->field($model, 'jenis')->dropDownList(Indikator::daftarJenis()) ?>
        <?= $form->field($model, 'target')->textInput(['type' => 'number', 'step' => '0.01', 'min' => 0, 'max' => 100]) ?>
        <?= $form->field($model, 'arah_target')->dropDownList([
            '>=' => '>= (makin tinggi makin baik)',
            '<=' => '<= (makin rendah makin baik)',
        ]) ?>
    </div>

    <?= $form->field($model, 'keterangan')->textarea(['rows' => 3, 'placeholder' => 'Opsional: definisi numerator/denominator, sumber data, dsb.']) ?>

    <?= $form->field($model, 'unit_ids')->checkboxList($units, [
        'class' => 'daftar-centang',
        'itemOptions' => ['labelOptions' => ['style' => '']],
    ]) ?>

    <div style="display:flex;gap:10px;margin-top:6px">
        <?= Html::submitButton('Simpan', ['class' => 'btn btn-biru']) ?>
        <a class="btn btn-garis" href="<?= Url::to(['index', 'jenis' => $model->jenis]) ?>">Batal</a>
    </div>

    <?php ActiveForm::end(); ?>
</div>
