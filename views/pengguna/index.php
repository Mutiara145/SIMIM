<?php

/**
 * Daftar pengguna.
 *
 * @var yii\web\View $this
 * @var app\models\User[] $daftar
 */

use app\models\User;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Kelola Pengguna';
$identity = Yii::$app->user->identity;
?>

<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px">
    <div>
        <h2 style="margin:0;font-size:16px">Manajemen Pengguna</h2>
        <div style="font-size:11.5px;color:#94a3b8">
            <?= $identity->isSuperAdmin() ? 'Semua pengguna terdaftar' : 'Akun kepala unit' ?>
        </div>
    </div>
    <a class="btn btn-biru" href="<?= Url::to(['tambah']) ?>">+ Tambah Pengguna</a>
</div>

<div class="tabel-bungkus">
    <table class="tabel">
        <thead>
        <tr>
            <th class="tengah" style="width:40px">No</th>
            <th>Nama Lengkap</th>
            <th>Username</th>
            <th>Unit</th>
            <th class="tengah">Peran</th>
            <th class="tengah">Status</th>
            <th class="tengah">Aksi</th>
        </tr>
        </thead>
        <tbody>
        <?php $no = 0; foreach ($daftar as $u): $no++; ?>
            <tr>
                <td class="tengah" style="color:#94a3b8"><?= $no ?></td>
                <td style="font-weight:500"><?= Html::encode($u->nama_lengkap) ?></td>
                <td class="angka"><?= Html::encode($u->username) ?></td>
                <td><?= Html::encode($u->unit->nama ?? '—') ?></td>
                <td class="tengah"><span class="badge-role <?= $u->role ?>" style="font-size:9.5px;padding:3px 9px;border-radius:999px"><?= Html::encode($u->roleLabel) ?></span></td>
                <td class="tengah">
                    <span class="badge <?= $u->status === User::STATUS_AKTIF ? 'badge-hijau' : 'badge-merah' ?>">
                        <?= $u->status === User::STATUS_AKTIF ? 'Aktif' : 'Nonaktif' ?>
                    </span>
                </td>
                <td class="tengah" style="white-space:nowrap">
                    <a class="btn btn-garis btn-kecil" href="<?= Url::to(['ubah', 'id' => $u->id]) ?>">Ubah</a>
                    <?php if ($identity->isSuperAdmin() && $u->id !== $identity->id): ?>
                        <?= Html::beginForm(['status', 'id' => $u->id], 'post', ['style' => 'display:inline']) ?>
                        <?= Html::submitButton(
                            $u->status === User::STATUS_AKTIF ? 'Nonaktifkan' : 'Aktifkan',
                            ['class' => 'btn btn-merah-garis btn-kecil']
                        ) ?>
                        <?= Html::endForm() ?>
                    <?php endif ?>
                </td>
            </tr>
        <?php endforeach ?>
        <?php if ($no === 0): ?>
            <tr><td colspan="7" class="tengah" style="padding:24px;color:#94a3b8">Belum ada pengguna.</td></tr>
        <?php endif ?>
        </tbody>
    </table>
</div>
