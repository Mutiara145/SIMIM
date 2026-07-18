<?php

/**
 * Daftar indikator per jenis (INM / IMP-RS / IMU).
 *
 * @var yii\web\View $this
 * @var string $jenis
 * @var app\models\Indikator[] $daftar
 */

use app\models\Indikator;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Kelola Profil Indikator';
?>

<div class="dua-panel">
    <div class="sub-sidebar">
        <div class="kepala">Kategori Indikator</div>
        <?php foreach (Indikator::daftarJenis() as $kode => $namaJenis): ?>
            <a href="<?= Url::to(['index', 'jenis' => $kode]) ?>" class="<?= $jenis === $kode ? 'aktif' : '' ?>">
                <div style="font-weight:700;font-size:13px"><?= $kode ?></div>
                <div style="font-size:10px;color:#94a3b8"><?= Html::encode($namaJenis) ?></div>
            </a>
        <?php endforeach ?>
    </div>

    <div class="panel-isi">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px">
            <div>
                <h2 style="margin:0;font-size:16px"><?= Html::encode(Indikator::daftarJenis()[$jenis]) ?></h2>
                <div style="font-size:11.5px;color:#94a3b8"><?= count($daftar) ?> indikator terdaftar</div>
            </div>
            <a class="btn btn-biru" href="<?= Url::to(['tambah', 'jenis' => $jenis]) ?>">+ Tambah Indikator</a>
        </div>

        <div class="tabel-bungkus" style="border-radius:10px">
            <table class="tabel">
                <thead>
                <tr>
                    <th class="tengah" style="width:40px">No</th>
                    <th>Nama Indikator</th>
                    <th class="tengah">Target</th>
                    <th>Unit Pelaksana</th>
                    <th class="tengah">Aksi</th>
                </tr>
                </thead>
                <tbody>
                <?php $no = 0; foreach ($daftar as $ind): $no++; ?>
                    <tr>
                        <td class="tengah" style="color:#94a3b8"><?= $no ?></td>
                        <td style="font-weight:500;max-width:340px"><?= Html::encode($ind->nama) ?></td>
                        <td class="tengah angka"><?= $ind->arah_target ?>&thinsp;<?= (float) $ind->target ?>%</td>
                        <td style="max-width:280px">
                            <?php foreach ($ind->units as $u): ?>
                                <span class="chip"><?= Html::encode($u->nama) ?></span>
                            <?php endforeach ?>
                        </td>
                        <td class="tengah" style="white-space:nowrap">
                            <a class="btn btn-garis btn-kecil" href="<?= Url::to(['ubah', 'id' => $ind->id]) ?>">Ubah</a>
                            <?= Html::beginForm(['hapus', 'id' => $ind->id], 'post', [
                                'style' => 'display:inline',
                                'onsubmit' => "return confirm('Hapus indikator ini? Seluruh isian logbook-nya juga akan terhapus.')",
                            ]) ?>
                            <?= Html::submitButton('Hapus', ['class' => 'btn btn-merah-garis btn-kecil']) ?>
                            <?= Html::endForm() ?>
                        </td>
                    </tr>
                <?php endforeach ?>
                <?php if ($no === 0): ?>
                    <tr><td colspan="5" class="tengah" style="padding:24px;color:#94a3b8">Belum ada indikator.</td></tr>
                <?php endif ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
