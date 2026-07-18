<?php

namespace app\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * Isian logbook harian: numerator & denominator per tanggal
 * untuk satu penugasan indikator-unit.
 *
 * @property int $id
 * @property int $indikator_unit_id
 * @property string $tanggal format Y-m-d
 * @property int $numerator
 * @property int $denominator
 * @property int|null $diisi_oleh
 */
class Logbook extends ActiveRecord
{
    /** Batas pengisian: tanggal hanya bisa diisi sampai N hari ke belakang. */
    const BATAS_HARI = 7;

    public static function tableName()
    {
        return '{{%logbook}}';
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    public function rules()
    {
        return [
            [['indikator_unit_id', 'tanggal'], 'required'],
            [['numerator', 'denominator'], 'integer', 'min' => 0],
            [['numerator', 'denominator'], 'default', 'value' => 0],
            ['tanggal', 'date', 'format' => 'php:Y-m-d'],
            ['tanggal', 'validateBatasHari'],
        ];
    }

    /** Validasi aturan 7 hari: tanggal di luar jendela pengisian ditolak. */
    public function validateBatasHari($attribute)
    {
        if (!self::bolehDiisi($this->tanggal)) {
            $this->addError($attribute, 'Tanggal ' . $this->tanggal . ' sudah melewati batas pengisian ' . self::BATAS_HARI . ' hari.');
        }
    }

    /** Tanggal boleh diisi jika: tidak di masa depan dan belum lewat batas 7 hari. */
    public static function bolehDiisi($tanggal)
    {
        $hariIni = strtotime(date('Y-m-d'));
        $tgl = strtotime($tanggal);
        $selisihHari = ($hariIni - $tgl) / 86400;
        return $selisihHari >= 0 && $selisihHari <= self::BATAS_HARI;
    }

    /** Persentase harian: N/D x 100 (null jika D = 0). */
    public function getPersen()
    {
        return $this->denominator > 0
            ? round($this->numerator / $this->denominator * 100, 1)
            : null;
    }

    public function getIndikatorUnit()
    {
        return $this->hasOne(IndikatorUnit::class, ['id' => 'indikator_unit_id']);
    }
}
