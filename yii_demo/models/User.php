<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use yii\behaviors\TimestampBehavior;

class User extends ActiveRecord implements IdentityInterface {

    public function behaviors() {
        return [
            TimestampBehavior::class,
        ];
    }

// отбрасываем некоторые поля. Лучше всего использовать в случае наследования
    public function fields() {
        $fields = parent::fields();

        // удаляем небезопасные поля
        unset($fields['auth_key'], 
                $fields['password_hash'], 
                $fields['access_token'], 
                $fields['password_reset_token']
                );

        return $fields;
    }
    public function getUserRole() {
        $roles = Yii::$app->authManager->getRolesByUser($this->id);

        $role = '';

        foreach ($roles as $key => $value) {
            $role = $key;
        }

        return $role;
    }

    public static function tableName() {
        return 'user';
    }

    /**
     * Finds an identity by the given ID.
     *
     * @param string|int $id the ID to be looked for
     * @return IdentityInterface|null the identity object that matches the given ID.
     */
    public static function findIdentity($id) {
        return static::findOne($id);
    }

    /**
     * Finds an identity by the given token.
     *
     * @param string $token the token to be looked for
     * @return IdentityInterface|null the identity object that matches the given token.
     */
    public static function findIdentityByAccessToken($token, $type = null) {
        return static::findOne(['access_token' => $token]);
    }

    /**
     * Finds user by name.
     *
     * @param string $userName
     * @return User|null
     */
    public static function findByUsername($userName) {
        return static::findOne(['username' => $userName]);
    }

    /**
     * @return int|string current user ID
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @return string current user auth key
     */
    public function getAuthKey() {
        return $this->auth_key;
    }

    /**
     * @param string $authKey
     * @return bool if auth key is valid for current user
     */
    public function validateAuthKey($authKey) {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password) {
        return Yii::$app->getSecurity()->validatePassword($password, $this->password_hash);
    }

}
