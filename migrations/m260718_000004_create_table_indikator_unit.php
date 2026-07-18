<?php

use yii\db\Migration;

/**
 * Tabel `indikator_unit` — penugasan indikator ke unit (many-to-many).
 * Satu indikator bisa dipantau oleh banyak unit, satu unit bisa
 * mendapat banyak indikator.
 */
class m260718_000004_create_table_indikator_unit extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%indikator_unit}}', [
            'id' => $this->primaryKey(),
            'indikator_id' => $this->integer()->notNull(),
            'unit_id' => $this->integer()->notNull(),
        ]);

        $this->createIndex(
            'idx-indikator_unit-unik',
            '{{%indikator_unit}}',
            ['indikator_id', 'unit_id'],
            true
        );

        $this->addForeignKey(
            'fk-indikator_unit-indikator_id',
            '{{%indikator_unit}}', 'indikator_id',
            '{{%indikator}}', 'id',
            'CASCADE', 'CASCADE'
        );

        $this->addForeignKey(
            'fk-indikator_unit-unit_id',
            '{{%indikator_unit}}', 'unit_id',
            '{{%unit}}', 'id',
            'CASCADE', 'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-indikator_unit-unit_id', '{{%indikator_unit}}');
        $this->dropForeignKey('fk-indikator_unit-indikator_id', '{{%indikator_unit}}');
        $this->dropTable('{{%indikator_unit}}');
    }
}
