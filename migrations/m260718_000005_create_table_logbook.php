<?php

use yii\db\Migration;

/**
 * Tabel `logbook` — isian harian numerator/denominator per indikator per unit.
 * Satu baris = satu tanggal untuk satu penugasan indikator-unit
 * (di tampilan, tanggal dijadikan kolom seperti Excel).
 *
 * Persentase TIDAK disimpan — dihitung: numerator/denominator x 100.
 * Capaian bulanan = SUM(numerator)/SUM(denominator) x 100.
 * Aturan: input hanya boleh untuk tanggal <= 7 hari ke belakang
 * (divalidasi di model/controller, bukan di database).
 */
class m260718_000005_create_table_logbook extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%logbook}}', [
            'id' => $this->primaryKey(),
            'indikator_unit_id' => $this->integer()->notNull(),
            'tanggal' => $this->date()->notNull(),
            'numerator' => $this->integer()->notNull()->defaultValue(0),
            'denominator' => $this->integer()->notNull()->defaultValue(0),
            'diisi_oleh' => $this->integer()->null(),
            'created_at' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        $this->createIndex(
            'idx-logbook-unik',
            '{{%logbook}}',
            ['indikator_unit_id', 'tanggal'],
            true
        );

        $this->addForeignKey(
            'fk-logbook-indikator_unit_id',
            '{{%logbook}}', 'indikator_unit_id',
            '{{%indikator_unit}}', 'id',
            'CASCADE', 'CASCADE'
        );

        $this->addForeignKey(
            'fk-logbook-diisi_oleh',
            '{{%logbook}}', 'diisi_oleh',
            '{{%user}}', 'id',
            'SET NULL', 'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-logbook-diisi_oleh', '{{%logbook}}');
        $this->dropForeignKey('fk-logbook-indikator_unit_id', '{{%logbook}}');
        $this->dropTable('{{%logbook}}');
    }
}
