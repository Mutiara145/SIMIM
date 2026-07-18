<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * Penugasan satu indikator ke satu unit.
 * Logbook menempel ke tabel ini (bukan langsung ke indikator),
 * sehingga isian tiap unit terpisah.
 *
 * @property int $id
 * @property int $indikator_id
 * @property int $unit_id
 *
 * @property Indikator $indikator
 * @property Unit $unit
 */
class IndikatorUnit extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%indikator_unit}}';
    }

    public function rules()
    {
        return [
            [['indikator_id', 'unit_id'], 'required'],
            [['indikator_id', 'unit_id'], 'integer'],
        ];
    }

    public function getIndikator()
    {
        return $this->hasOne(Indikator::class, ['id' => 'indikator_id']);
    }

    public function getUnit()
    {
        return $this->hasOne(Unit::class, ['id' => 'unit_id']);
    }

    public function getLogbooks()
    {
        return $this->hasMany(Logbook::class, ['indikator_unit_id' => 'id']);
    }

    /**
     * Capaian bulanan: SUM(N)/SUM(D) x 100 untuk bulan tertentu.
     * @param string $bulan format 'Y-m', mis. '2026-07'
     * @return array{n:int, d:int, persen:float|null}
     */
    public function hitungCapaian($bulan)
    {
        $row = Logbook::find()
            ->where(['indikator_unit_id' => $this->id])
            ->andWhere(['between', 'tanggal', $bulan . '-01', $bulan . '-31'])
            ->select(['n' => 'COALESCE(SUM(numerator),0)', 'd' => 'COALESCE(SUM(denominator),0)'])
            ->asArray()->one();

        $n = (int) $row['n'];
        $d = (int) $row['d'];
        return [
            'n' => $n,
            'd' => $d,
            'persen' => $d > 0 ? round($n / $d * 100, 1) : null,
        ];
    }

    /**
     * Apakah capaian memenuhi target indikator?
     * @param float|null $persen hasil hitungCapaian()['persen']
     */
    public function isTercapai($persen)
    {
        if ($persen === null) {
            return false; // belum ada data
        }
        return $this->indikator->arah_target === '<='
            ? $persen <= (float) $this->indikator->target
            : $persen >= (float) $this->indikator->target;
    }

    /**
     * Status pengisian logbook bulan berjalan untuk dashboard kepala unit:
     * - 'hijau'  : semua tanggal s/d hari ini sudah diisi
     * - 'kuning' : ada tanggal yang belum diisi tetapi masih dalam batas 7 hari
     * - 'merah'  : ada tanggal belum diisi yang sudah melewati batas 7 hari
     */
    public function statusPengisian()
    {
        $hariIni = date('Y-m-d');
        $awalBulan = date('Y-m-01');

        $terisi = Logbook::find()
            ->where(['indikator_unit_id' => $this->id])
            ->andWhere(['between', 'tanggal', $awalBulan, $hariIni])
            ->select('tanggal')->column();
        $terisi = array_flip($terisi);

        $tertua = null; // tanggal belum terisi paling lama
        for ($d = strtotime($awalBulan); $d <= strtotime($hariIni); $d += 86400) {
            $tgl = date('Y-m-d', $d);
            if (!isset($terisi[$tgl])) {
                $tertua = $tgl;
                break;
            }
        }

        if ($tertua === null) {
            return 'hijau';
        }
        $selisihHari = (int) ((strtotime($hariIni) - strtotime($tertua)) / 86400);
        return $selisihHari > Logbook::BATAS_HARI ? 'merah' : 'kuning';
    }
}
