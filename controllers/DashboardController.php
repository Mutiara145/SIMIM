<?php

namespace app\controllers;

use app\models\Indikator;
use app\models\IndikatorUnit;
use app\models\Logbook;
use app\models\Unit;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;

class DashboardController extends Controller
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
        ];
    }

    public function actionIndex()
    {
        $identity = Yii::$app->user->identity;

        if ($identity->isKepalaUnit()) {
            return $this->dashboardKepalaUnit();
        }
        return $this->dashboardAdmin();
    }

    /**
     * Dashboard super admin & admin: rekap capaian seluruh unit
     * pada bulan yang dipilih (default bulan berjalan).
     */
    private function dashboardAdmin()
    {
        $bulan = $this->ambilBulan();

        // Total N & D per penugasan pada bulan tsb (satu query)
        $jumlah = Logbook::find()
            ->select([
                'indikator_unit_id',
                'n' => 'SUM(numerator)',
                'd' => 'SUM(denominator)',
            ])
            ->andWhere(['between', 'tanggal', $bulan . '-01', $bulan . '-31'])
            ->groupBy('indikator_unit_id')
            ->asArray()->indexBy('indikator_unit_id')->all();

        // Rekap per unit: berapa indikator tercapai dari total
        $units = Unit::find()->orderBy('nama')->all();
        $penugasan = IndikatorUnit::find()->with('indikator')->all();
        $perUnit = [];
        foreach ($penugasan as $iu) {
            $perUnit[$iu->unit_id][] = $iu;
        }

        $statUnit = [];
        foreach ($units as $unit) {
            $daftar = $perUnit[$unit->id] ?? [];
            $total = count($daftar);
            $tercapai = 0;
            foreach ($daftar as $iu) {
                $baris = $jumlah[$iu->id] ?? null;
                $persen = ($baris && $baris['d'] > 0)
                    ? round($baris['n'] / $baris['d'] * 100, 1)
                    : null;
                if ($iu->isTercapai($persen)) {
                    $tercapai++;
                }
            }
            $statUnit[] = [
                'nama' => $unit->nama,
                'total' => $total,
                'tercapai' => $tercapai,
                'persen' => $total > 0 ? round($tercapai / $total * 100) : 0,
            ];
        }

        // Urutkan dari terendah agar unit bermasalah terlihat dulu
        usort($statUnit, fn($a, $b) => $a['persen'] <=> $b['persen']);

        $penuh = count(array_filter($statUnit, fn($s) => $s['total'] > 0 && $s['tercapai'] === $s['total']));
        $sebagian = count(array_filter($statUnit, fn($s) => $s['tercapai'] > 0 && $s['tercapai'] < $s['total']));
        $nol = count(array_filter($statUnit, fn($s) => $s['tercapai'] === 0));

        return $this->render('admin', [
            'bulan' => $bulan,
            'statUnit' => $statUnit,
            'totalIndikator' => (int) Indikator::find()->count(),
            'penuh' => $penuh,
            'sebagian' => $sebagian,
            'nol' => $nol,
        ]);
    }

    /**
     * Dashboard kepala unit: status pengisian logbook tiap indikator
     * unitnya pada bulan berjalan (hijau/kuning/merah).
     */
    private function dashboardKepalaUnit()
    {
        $identity = Yii::$app->user->identity;
        $bulan = date('Y-m');

        $penugasan = IndikatorUnit::find()
            ->where(['unit_id' => $identity->unit_id])
            ->with('indikator')
            ->all();

        $baris = [];
        $hitung = ['hijau' => 0, 'kuning' => 0, 'merah' => 0];
        foreach ($penugasan as $iu) {
            $status = $iu->statusPengisian();
            $capaian = $iu->hitungCapaian($bulan);
            $hitung[$status]++;
            $baris[] = [
                'iu' => $iu,
                'status' => $status,
                'capaian' => $capaian,
                'tercapai' => $iu->isTercapai($capaian['persen']),
            ];
        }

        return $this->render('kepala-unit', [
            'bulan' => $bulan,
            'baris' => $baris,
            'hitung' => $hitung,
        ]);
    }

    /** Ambil parameter ?bulan=YYYY-MM (default bulan berjalan). */
    private function ambilBulan()
    {
        $bulan = Yii::$app->request->get('bulan', date('Y-m'));
        return preg_match('/^\d{4}-\d{2}$/', $bulan) ? $bulan : date('Y-m');
    }
}
