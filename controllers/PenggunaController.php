<?php

namespace app\controllers;

use app\models\User;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

/**
 * Kelola Pengguna — super admin & admin (komite mutu).
 * Admin hanya bisa melihat & mengelola akun kepala unit
 * (tidak bisa melihat akun super admin dan admin lain).
 */
class PenggunaController extends Controller
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
                'actions' => ['status' => ['post']],
            ],
        ];
    }

    public function actionIndex()
    {
        $query = User::find()->with('unit')->orderBy(['role' => SORT_ASC, 'nama_lengkap' => SORT_ASC]);
        if (!Yii::$app->user->identity->isSuperAdmin()) {
            $query->andWhere(['role' => User::ROLE_KEPALA_UNIT]);
        }

        return $this->render('index', ['daftar' => $query->all()]);
    }

    public function actionTambah()
    {
        $model = new User(['scenario' => 'create', 'status' => User::STATUS_AKTIF]);

        if ($model->load(Yii::$app->request->post())) {
            $this->pastikanRoleDiizinkan($model->role);
            if ($model->save()) {
                Yii::$app->session->setFlash('sukses', 'Pengguna berhasil ditambahkan.');
                return $this->redirect(['index']);
            }
        }

        return $this->render('form', ['model' => $model]);
    }

    public function actionUbah($id)
    {
        $model = $this->temukan($id);

        if ($model->load(Yii::$app->request->post())) {
            $this->pastikanRoleDiizinkan($model->role);
            if ($model->save()) {
                Yii::$app->session->setFlash('sukses', 'Data pengguna berhasil diperbarui.');
                return $this->redirect(['index']);
            }
        }

        return $this->render('form', ['model' => $model]);
    }

    /** Aktif/nonaktifkan pengguna (khusus super admin). */
    public function actionStatus($id)
    {
        if (!Yii::$app->user->identity->isSuperAdmin()) {
            throw new ForbiddenHttpException('Hanya super admin yang dapat mengubah status pengguna.');
        }
        $model = $this->temukan($id);
        if ($model->id === Yii::$app->user->id) {
            Yii::$app->session->setFlash('gagal', 'Tidak bisa menonaktifkan akun sendiri.');
            return $this->redirect(['index']);
        }

        $model->status = $model->status === User::STATUS_AKTIF ? User::STATUS_NONAKTIF : User::STATUS_AKTIF;
        $model->save(false);
        Yii::$app->session->setFlash('sukses', 'Status pengguna berhasil diubah.');
        return $this->redirect(['index']);
    }

    /** Batasi role yang boleh dikelola sesuai peran yang sedang login. */
    private function pastikanRoleDiizinkan($role)
    {
        if (!Yii::$app->user->identity->isSuperAdmin() && $role !== User::ROLE_KEPALA_UNIT) {
            throw new ForbiddenHttpException('Admin hanya dapat mengelola akun kepala unit.');
        }
    }

    private function temukan($id)
    {
        $model = User::findOne($id);
        if ($model === null) {
            throw new NotFoundHttpException('Pengguna tidak ditemukan.');
        }
        // Admin tidak boleh menyentuh akun super admin / admin lain
        if (!Yii::$app->user->identity->isSuperAdmin() && $model->role !== User::ROLE_KEPALA_UNIT) {
            throw new ForbiddenHttpException('Anda tidak berhak mengelola akun ini.');
        }
        return $model;
    }
}
