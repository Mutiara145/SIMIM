<?php

use yii\db\Migration;

/**
 * Tabel `user` — pengguna sistem dengan 3 peran:
 * super_admin, admin (komite mutu), kepala_unit.
 * Kolom unit_id hanya diisi untuk kepala_unit.
 */
class m260718_000002_create_table_user extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%user}}', [
            'id' => $this->primaryKey(),
            'username' => $this->string(50)->notNull()->unique(),
            'password_hash' => $this->string(255)->notNull(),
            'auth_key' => $this->string(32)->notNull(),
            'nama_lengkap' => $this->string(100)->notNull(),
            'role' => $this->string(20)->notNull()->comment('super_admin | admin | kepala_unit'),
            'unit_id' => $this->integer()->null(),
            'status' => $this->tinyInteger()->notNull()->defaultValue(1)->comment('1=Aktif, 0=Nonaktif'),
            'created_at' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        $this->addForeignKey(
            'fk-user-unit_id',
            '{{%user}}', 'unit_id',
            '{{%unit}}', 'id',
            'SET NULL', 'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-user-unit_id', '{{%user}}');
        $this->dropTable('{{%user}}');
    }
}
