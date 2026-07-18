<?php

use yii\db\Migration;

/**
 * Tabel `unit` — daftar unit kerja di rumah sakit.
 */
class m260718_000001_create_table_unit extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%unit}}', [
            'id' => $this->primaryKey(),
            'nama' => $this->string(100)->notNull()->unique(),
            'created_at' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);
    }

    public function safeDown()
    {
        $this->dropTable('{{%unit}}');
    }
}
