<?php

/** @var yii\web\View $this */
/** @var string $content */

use app\assets\AppAsset;
use yii\helpers\Html;
use yii\helpers\Url;

AppAsset::register($this);
$this->registerCsrfMetaTags();
$this->registerMetaTag(['charset' => Yii::$app->charset], 'charset');
$this->registerMetaTag(['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1']);

$identity = Yii::$app->user->identity;
$controllerId = Yii::$app->controller->id;

// Menu sidebar: [controller, ikon, label, role yang boleh]
$menu = [
    ['dashboard', '◈', 'Dashboard', ['super_admin', 'admin', 'kepala_unit']],
    ['logbook', '⊞', 'Logbook', ['super_admin', 'admin', 'kepala_unit']],
    ['indikator', '◫', 'Kelola Profil Indikator', ['super_admin', 'admin']],
    ['pengguna', '◎', 'Kelola Pengguna', ['super_admin', 'admin']],
];

$inisial = '?';
if ($identity) {
    $kata = preg_split('/\s+/', trim($identity->nama_lengkap));
    $inisial = strtoupper(substr($kata[0], 0, 1) . substr($kata[1] ?? '', 0, 1));
}
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <title><?= Html::encode($this->title) ?> — SIMIM</title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<?php if ($identity === null): ?>
    <div class="halaman-polos"><div class="kartu" style="max-width:520px"><?= $content ?></div></div>
<?php else: ?>
<div class="app">
    <aside class="sidebar">
        <div class="brand">
            <div class="logo">✦</div>
            <div>
                <div class="nama">SIMIM</div>
                <div class="sub">Sistem Indikator Mutu RS</div>
            </div>
        </div>
        <nav>
            <div class="kepala-menu">Menu Utama</div>
            <?php foreach ($menu as [$ctrl, $ikon, $label, $roles]): ?>
                <?php if (in_array($identity->role, $roles, true)): ?>
                    <a href="<?= Url::to(["/$ctrl/index"]) ?>" class="<?= $controllerId === $ctrl ? 'aktif' : '' ?>">
                        <span class="ikon"><?= $ikon ?></span> <?= $label ?>
                    </a>
                <?php endif ?>
            <?php endforeach ?>
        </nav>
        <div class="profil">
            <div class="avatar"><?= Html::encode($inisial) ?></div>
            <div>
                <div class="nama"><?= Html::encode($identity->nama_lengkap) ?></div>
                <div class="jabatan">
                    <?= Html::encode($identity->isKepalaUnit() && $identity->unit
                        ? 'Kepala Unit ' . $identity->unit->nama
                        : $identity->roleLabel) ?>
                </div>
            </div>
        </div>
    </aside>

    <div class="utama">
        <header class="navbar">
            <div class="judul">
                <?= Html::encode($this->title) ?>
                <span style="color:#cbd5e1">|</span>
                <span class="jam" id="jam-navbar"></span>
            </div>
            <div class="kanan">
                <span class="badge-role <?= $identity->role ?>"><?= Html::encode($identity->roleLabel) ?></span>
                <?= Html::beginForm(['/site/logout']) ?>
                <?= Html::submitButton('Keluar', ['class' => 'tombol-keluar']) ?>
                <?= Html::endForm() ?>
            </div>
        </header>

        <main class="konten">
            <?php foreach (['sukses' => 'flash-sukses', 'gagal' => 'flash-gagal'] as $kunci => $kelas): ?>
                <?php if (Yii::$app->session->hasFlash($kunci)): ?>
                    <div class="flash <?= $kelas ?>"><?= Yii::$app->session->getFlash($kunci) ?></div>
                <?php endif ?>
            <?php endforeach ?>
            <?= $content ?>
        </main>

        <footer class="footer">
            <span><b>SIMIM</b> — Sistem Informasi Profil Indikator Mutu · RS Jiwa Tampan</span>
            <span>&copy; <?= date('Y') ?></span>
        </footer>
    </div>
</div>

<script>
(function () {
    var el = document.getElementById('jam-navbar');
    function tik() {
        var kini = new Date();
        el.innerHTML = kini.toLocaleDateString('id-ID', {weekday:'short', day:'2-digit', month:'short', year:'numeric'})
            + ' <b>' + kini.toLocaleTimeString('id-ID') + '</b>';
    }
    tik();
    setInterval(tik, 1000);
})();
</script>
<?php endif ?>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
