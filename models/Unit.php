<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * Unit kerja rumah sakit.
 *
 * @property int $id
 * @property string $nama
 *
 * @property IndikatorUnit[] $penugasan
 */
class Unit extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%unit}}';
    }

    public function rules()
    {
        return [
            ['nama', 'required'],
            ['nama', 'string', 'max' => 100],
            ['nama', 'unique'],
        ];
    }

    public function attributeLabels()
    {
        return ['nama' => 'Nama Unit'];
    }

    public function getPenugasan()
    {
        return $this->hasMany(IndikatorUnit::class, ['unit_id' => 'id']);
    }
}
