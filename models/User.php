<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\web\IdentityInterface;

/**
 * Pengguna sistem.
 *
 * @property int $id
 * @property string $username
 * @property string $password_hash
 * @property string $auth_key
 * @property string $nama_lengkap
 * @property string $role super_admin | admin | kepala_unit
 * @property int|null $unit_id hanya diisi untuk kepala_unit
 * @property int $status 1=Aktif, 0=Nonaktif
 *
 * @property Unit|null $unit
 */
class User extends ActiveRecord implements IdentityInterface
{
    const ROLE_SUPER_ADMIN = 'super_admin';
    const ROLE_ADMIN = 'admin';
    const ROLE_KEPALA_UNIT = 'kepala_unit';

    const STATUS_AKTIF = 1;
    const STATUS_NONAKTIF = 0;

    /** Password baru (tidak disimpan langsung; di-hash ke password_hash). */
    public $password_baru;

    public static function tableName()
    {
        return '{{%user}}';
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
            [['username', 'nama_lengkap', 'role'], 'required'],
            ['username', 'unique'],
            ['username', 'string', 'max' => 50],
            ['nama_lengkap', 'string', 'max' => 100],
            ['role', 'in', 'range' => array_keys(self::daftarRole())],
            ['status', 'in', 'range' => [self::STATUS_AKTIF, self::STATUS_NONAKTIF]],
            ['unit_id', 'required', 'when' => fn($model) => $model->role === self::ROLE_KEPALA_UNIT,
                'whenClient' => "function() { return $('#user-role').val() === 'kepala_unit'; }",
                'message' => 'Unit wajib dipilih untuk kepala unit.'],
            ['unit_id', 'exist', 'targetClass' => Unit::class, 'targetAttribute' => 'id'],
            ['password_baru', 'required', 'on' => 'create'],
            ['password_baru', 'string', 'min' => 6, 'skipOnEmpty' => true],
        ];
    }

    public function attributeLabels()
    {
        return [
            'username' => 'Username',
            'nama_lengkap' => 'Nama Lengkap',
            'role' => 'Peran',
            'unit_id' => 'Unit',
            'status' => 'Status',
            'password_baru' => 'Password',
        ];
    }

    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }
        if ($this->password_baru) {
            $this->password_hash = Yii::$app->security->generatePasswordHash($this->password_baru);
        }
        if ($insert) {
            $this->auth_key = Yii::$app->security->generateRandomString();
        }
        if ($this->role !== self::ROLE_KEPALA_UNIT) {
            $this->unit_id = null;
        }
        return true;
    }

    // ── IdentityInterface ─────────────────────────────────────────────

    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_AKTIF]);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return null; // tidak dipakai (tanpa API)
    }

    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'status' => self::STATUS_AKTIF]);
    }

    public function getId()
    {
        return $this->id;
    }

    public function getAuthKey()
    {
        return $this->auth_key;
    }

    public function validateAuthKey($authKey)
    {
        return $this->auth_key === $authKey;
    }

    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    // ── Relasi & helper ───────────────────────────────────────────────

    public function getUnit()
    {
        return $this->hasOne(Unit::class, ['id' => 'unit_id']);
    }

    public function isSuperAdmin()
    {
        return $this->role === self::ROLE_SUPER_ADMIN;
    }

    public function isAdmin()
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isKepalaUnit()
    {
        return $this->role === self::ROLE_KEPALA_UNIT;
    }

    public static function daftarRole()
    {
        return [
            self::ROLE_SUPER_ADMIN => 'Super Admin',
            self::ROLE_ADMIN => 'Admin (Komite Mutu)',
            self::ROLE_KEPALA_UNIT => 'Kepala Unit',
        ];
    }

    public function getRoleLabel()
    {
        return self::daftarRole()[$this->role] ?? $this->role;
    }

    /** True jika role pengguna yang sedang login termasuk salah satu dari $roles. */
    public static function roleIs(...$roles)
    {
        $identity = Yii::$app->user->identity;
        return $identity !== null && in_array($identity->role, $roles, true);
    }
}
