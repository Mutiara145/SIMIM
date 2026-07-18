<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * Master profil indikator mutu.
 *
 * @property int $id
 * @property string $nama
 * @property string $jenis INM | IMP-RS | IMU
 * @property string $target persen (mis. 100.00)
 * @property string $arah_target '>=' atau '<='
 * @property string|null $keterangan
 *
 * @property Unit[] $units
 */
class Indikator extends ActiveRecord
{
    const JENIS_INM = 'INM';
    const JENIS_IMPRS = 'IMP-RS';
    const JENIS_IMU = 'IMU';

    /** Untuk checkbox unit pada form (array id unit). */
    public $unit_ids = [];

    public static function tableName()
    {
        return '{{%indikator}}';
    }

    public function rules()
    {
        return [
            [['nama', 'jenis', 'target'], 'required'],
            ['nama', 'string', 'max' => 255],
            ['jenis', 'in', 'range' => array_keys(self::daftarJenis())],
            ['target', 'number', 'min' => 0, 'max' => 100],
            ['arah_target', 'in', 'range' => ['>=', '<=']],
            ['keterangan', 'string'],
            ['nama', 'unique', 'targetAttribute' => ['nama', 'jenis'],
                'message' => 'Indikator dengan nama dan jenis yang sama sudah ada.'],
            ['unit_ids', 'each', 'rule' => ['integer']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'nama' => 'Nama Indikator',
            'jenis' => 'Jenis',
            'target' => 'Target (%)',
            'arah_target' => 'Arah Target',
            'keterangan' => 'Keterangan',
            'unit_ids' => 'Unit Pelaksana',
        ];
    }

    public static function daftarJenis()
    {
        return [
            self::JENIS_INM => 'Indikator Nasional Mutu (INM)',
            self::JENIS_IMPRS => 'Indikator Mutu Prioritas RS (IMP-RS)',
            self::JENIS_IMU => 'Indikator Mutu Unit (IMU)',
        ];
    }

    public function getPenugasan()
    {
        return $this->hasMany(IndikatorUnit::class, ['indikator_id' => 'id']);
    }

    public function getUnits()
    {
        return $this->hasMany(Unit::class, ['id' => 'unit_id'])->via('penugasan');
    }

    /**
     * Sinkronkan penugasan unit dengan $this->unit_ids.
     * Hanya menambah yang baru dan menghapus yang dihilangkan,
     * supaya data logbook penugasan lama yang masih dipakai tidak ikut terhapus.
     */
    public function simpanPenugasan()
    {
        $baru = array_map('intval', (array) $this->unit_ids);
        $lama = IndikatorUnit::find()
            ->where(['indikator_id' => $this->id])
            ->select('unit_id')->column();
        $lama = array_map('intval', $lama);

        foreach (array_diff($baru, $lama) as $unitId) {
            $iu = new IndikatorUnit(['indikator_id' => $this->id, 'unit_id' => $unitId]);
            $iu->save(false);
        }
        if ($hapus = array_diff($lama, $baru)) {
            IndikatorUnit::deleteAll(['indikator_id' => $this->id, 'unit_id' => $hapus]);
        }
    }
}
