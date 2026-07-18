<?php

namespace app\controllers;

use app\models\Indikator;
use app\models\User;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * Kelola Profil Indikator — hanya super admin & admin (komite mutu).
 */
class IndikatorController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => fn() => User::roleIs(User::ROLE_SUPER_ADMIN, User::ROLE_ADMIN),
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => ['hapus' => ['post']],
            ],
        ];
    }

    public function actionIndex($jenis = Indikator::JENIS_INM)
    {
        if (!isset(Indikator::daftarJenis()[$jenis])) {
            $jenis = Indikator::JENIS_INM;
        }

        $daftar = Indikator::find()
            ->where(['jenis' => $jenis])
            ->with('units')
            ->orderBy('nama')
            ->all();

        return $this->render('index', [
            'jenis' => $jenis,
            'daftar' => $daftar,
        ]);
    }

    public function actionTambah($jenis = Indikator::JENIS_INM)
    {
        $model = new Indikator(['jenis' => $jenis, 'arah_target' => '>=', 'target' => 100]);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $model->simpanPenugasan();
            Yii::$app->session->setFlash('sukses', 'Indikator berhasil ditambahkan.');
            return $this->redirect(['index', 'jenis' => $model->jenis]);
        }

        return $this->render('form', ['model' => $model]);
    }

    public function actionUbah($id)
    {
        $model = $this->temukan($id);
        $model->unit_ids = $model->getUnits()->select('id')->column();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $model->simpanPenugasan();
            Yii::$app->session->setFlash('sukses', 'Indikator berhasil diperbarui.');
            return $this->redirect(['index', 'jenis' => $model->jenis]);
        }

        return $this->render('form', ['model' => $model]);
    }

    public function actionHapus($id)
    {
        $model = $this->temukan($id);
        $jenis = $model->jenis;
        $model->delete(); // penugasan & logbook ikut terhapus (FK cascade)
        Yii::$app->session->setFlash('sukses', 'Indikator berhasil dihapus.');
        return $this->redirect(['index', 'jenis' => $jenis]);
    }

    private function temukan($id)
    {
        $model = Indikator::findOne($id);
        if ($model === null) {
            throw new NotFoundHttpException('Indikator tidak ditemukan.');
        }
        return $model;
    }
}
