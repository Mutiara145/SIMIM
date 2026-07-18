<?php

namespace app\controllers;

use app\models\IndikatorUnit;
use app\models\Logbook;
use app\models\Unit;
use app\models\User;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;

class LogbookController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    ['allow' => true, 'roles' => ['@']],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => ['simpan' => ['post']],
            ],
        ];
    }

    /**
     * Kepala unit  : grid pengisian logbook unitnya sendiri.
     * Admin/super  : pilih unit di sub-sidebar, lihat logbook (baca saja).
     */
    public function actionIndex($bulan = null, $unit_id = null)
    {
        $identity = Yii::$app->user->identity;
        $bulan = $this->validasiBulan($bulan);

        if ($identity->isKepalaUnit()) {
            $unit = $identity->unit;
            $view = 'index';
        } else {
            $units = Unit::find()->orderBy('nama')->all();
            $unit = $unit_id !== null
                ? Unit::findOne($unit_id)
                : ($units[0] ?? null);
            if ($unit === null) {
                throw new ForbiddenHttpException('Unit tidak ditemukan.');
            }
            $view = 'admin';
        }

        [$penugasan, $isian] = $this->dataGrid($unit->id, $bulan);

        return $this->render($view, [
            'unit' => $unit,
            'units' => $units ?? [],
            'bulan' => $bulan,
            'penugasan' => $penugasan,
            'isian' => $isian,
        ]);
    }

    /**
     * Simpan isian grid (khusus kepala unit).
     * Format POST: isian[<indikator_unit_id>][<tanggal_ke>][n|d]
     */
    public function actionSimpan($bulan)
    {
        $identity = Yii::$app->user->identity;
        if (!$identity->isKepalaUnit()) {
            throw new ForbiddenHttpException('Hanya kepala unit yang dapat mengisi logbook.');
        }

        $bulan = $this->validasiBulan($bulan);
        $data = Yii::$app->request->post('isian', []);

        // Hanya penugasan milik unit sendiri yang boleh diisi
        $milikSendiri = IndikatorUnit::find()
            ->where(['unit_id' => $identity->unit_id])
            ->select('id')->column();
        $milikSendiri = array_flip(array_map('intval', $milikSendiri));

        $tersimpan = 0;
        $ditolak = 0;
        foreach ($data as $iuId => $hariIsi) {
            if (!isset($milikSendiri[(int) $iuId])) {
                continue;
            }
            foreach ($hariIsi as $hari => $nilai) {
                $n = trim($nilai['n'] ?? '');
                $d = trim($nilai['d'] ?? '');
                if ($n === '' && $d === '') {
                    continue; // sel kosong: lewati
                }
                $tanggal = sprintf('%s-%02d', $bulan, (int) $hari);

                $log = Logbook::findOne(['indikator_unit_id' => $iuId, 'tanggal' => $tanggal])
                    ?? new Logbook(['indikator_unit_id' => $iuId, 'tanggal' => $tanggal]);
                $log->numerator = (int) $n;
                $log->denominator = (int) $d;
                $log->diisi_oleh = $identity->id;

                if ($log->save()) {
                    $tersimpan++;
                } else {
                    $ditolak++; // gagal validasi (mis. lewat batas 7 hari)
                }
            }
        }

        if ($tersimpan > 0) {
            Yii::$app->session->setFlash('sukses', "Berhasil menyimpan $tersimpan isian logbook.");
        }
        if ($ditolak > 0) {
            Yii::$app->session->setFlash('gagal', "$ditolak isian ditolak (melewati batas pengisian " . Logbook::BATAS_HARI . ' hari).');
        }

        return $this->redirect(['index', 'bulan' => $bulan]);
    }

    /**
     * Data grid: daftar penugasan unit + isian logbook bulan tsb.
     * @return array [IndikatorUnit[], array isian[iu_id][tanggal_ke] => Logbook]
     */
    private function dataGrid($unitId, $bulan)
    {
        $penugasan = IndikatorUnit::find()
            ->where(['unit_id' => $unitId])
            ->with('indikator')
            ->joinWith('indikator i')
            ->orderBy(['i.jenis' => SORT_ASC, 'i.nama' => SORT_ASC])
            ->all();

        $logs = Logbook::find()
            ->where(['indikator_unit_id' => array_map(fn($iu) => $iu->id, $penugasan)])
            ->andWhere(['between', 'tanggal', $bulan . '-01', $bulan . '-31'])
            ->all();

        $isian = [];
        foreach ($logs as $log) {
            $isian[$log->indikator_unit_id][(int) date('j', strtotime($log->tanggal))] = $log;
        }

        return [$penugasan, $isian];
    }

    private function validasiBulan($bulan)
    {
        return ($bulan && preg_match('/^\d{4}-\d{2}$/', $bulan)) ? $bulan : date('Y-m');
    }
}
