<?php

/**
 * Form tambah/ubah pengguna.
 *
 * @var yii\web\View $this
 * @var app\models\User $model
 */

use app\models\Unit;
use app\models\User;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$this->title = $model->isNewRecord ? 'Tambah Pengguna' : 'Ubah Pengguna';
$units = ArrayHelper::map(Unit::find()->orderBy('nama')->all(), 'id', 'nama');

// Admin hanya boleh membuat akun kepala unit
$pilihanRole = Yii::$app->user->identity->isSuperAdmin()
    ? User::daftarRole()
    : [User::ROLE_KEPALA_UNIT => User::daftarRole()[User::ROLE_KEPALA_UNIT]];
?>

<div class="kartu form-kotak" style="max-width:560px">
    <h2 style="margin:0 0 16px;font-size:16px"><?= Html::encode($this->title) ?></h2>

    <?php $form = ActiveForm::begin(['fieldConfig' => ['template' => "{label}\n{input}\n{error}"]]); ?>

    <?= $form->field($model, 'nama_lengkap')->textInput(['maxlength' => true]) ?>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px">
        <?= $form->field($model, 'username')->textInput(['maxlength' => true, 'autocomplete' => 'off']) ?>
        <?= $form->field($model, 'password_baru')->passwordInput([
            'autocomplete' => 'new-password',
            'placeholder' => $model->isNewRecord ? 'Minimal 6 karakter' : 'Kosongkan jika tidak diganti',
        ]) ?>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px">
        <?= $form->field($model, 'role')->dropDownList($pilihanRole, ['id' => 'user-role']) ?>
        <?= $form->field($model, 'unit_id')->dropDownList($units, ['prompt' => '— pilih unit —']) ?>
    </div>

    <?= $form->field($model, 'status')->dropDownList([
        User::STATUS_AKTIF => 'Aktif',
        User::STATUS_NONAKTIF => 'Nonaktif',
    ]) ?>

    <div style="display:flex;gap:10px;margin-top:6px">
        <?= Html::submitButton('Simpan', ['class' => 'btn btn-biru']) ?>
        <a class="btn btn-garis" href="<?= Url::to(['index']) ?>">Batal</a>
    </div>

    <?php ActiveForm::end(); ?>
</div>

<?php
// Kolom unit hanya relevan untuk kepala unit
$this->registerJs(<<<JS
function aturUnit() {
    var role = document.getElementById('user-role').value;
    var unit = document.getElementById('user-unit_id');
    unit.disabled = role !== 'kepala_unit';
    if (unit.disabled) unit.value = '';
}
document.getElementById('user-role').addEventListener('change', aturUnit);
aturUnit();
JS);
?>
