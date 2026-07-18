<?php

use yii\db\Migration;

/**
 * Tabel `indikator` — master profil indikator mutu.
 * jenis: INM (Indikator Nasional Mutu), IMP-RS (Indikator Mutu Prioritas RS),
 *        IMU (Indikator Mutu Unit).
 * target: dalam persen (mis. 100.00, 85.00).
 * arah_target: '>=' berarti capaian makin tinggi makin baik,
 *              '<=' untuk indikator yang makin rendah makin baik
 *              (mis. angka kejadian, target 0%).
 */
class m260718_000003_create_table_indikator extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%indikator}}', [
            'id' => $this->primaryKey(),
            'nama' => $this->string(255)->notNull(),
            'jenis' => $this->string(10)->notNull()->comment('INM | IMP-RS | IMU'),
            'target' => $this->decimal(5, 2)->notNull()->comment('target dalam persen'),
            'arah_target' => $this->string(2)->notNull()->defaultValue('>=')->comment('>= atau <='),
            'keterangan' => $this->text()->null(),
            'created_at' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);
    }

    public function safeDown()
    {
        $this->dropTable('{{%indikator}}');
    }
}
