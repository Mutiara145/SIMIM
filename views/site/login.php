<?php

/** @var yii\web\View $this */
/** @var app\models\LoginForm $model */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Masuk';
?>
<div class="login-kartu form-kotak">
    <div class="logo">✦</div>
    <h1>SIMIM</h1>
    <div class="sub">Sistem Informasi Profil Indikator Mutu<br>RS Jiwa Tampan</div>

    <?php $form = ActiveForm::begin([
        'id' => 'login-form',
        'fieldConfig' => ['template' => "{label}\n{input}\n{error}"],
    ]); ?>

    <?= $form->field($model, 'username')->textInput(['autofocus' => true, 'placeholder' => 'Username']) ?>

    <?= $form->field($model, 'password')->passwordInput(['placeholder' => 'Password']) ?>

    <?= $form->field($model, 'rememberMe')->checkbox(['label' => 'Ingat saya']) ?>

    <?= Html::submitButton('Masuk', ['class' => 'btn btn-biru', 'style' => 'width:100%;padding:11px']) ?>

    <?php ActiveForm::end(); ?>
</div>
