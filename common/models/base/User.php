<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "user".
 *
 * @property string $id
 * @property string $invite_uid
 * @property string $openId
 * @property string $unionId
 * @property string $session_key
 * @property string $phone
 * @property string $nickname
 * @property int $gender
 * @property string $avatar
 * @property string $city
 * @property string $province
 * @property string $country
 * @property int $status
 * @property string $cash
 * @property string $auth_key
 * @property string $created_at
 */
class User extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['invite_uid', 'cash', 'created_at'], 'integer'],
            [['openId', 'unionId', 'session_key', 'phone', 'nickname', 'avatar', 'city', 'province', 'country', 'auth_key'], 'string', 'max' => 255],
            [['gender', 'status'], 'string', 'max' => 1],
            [['openId'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'invite_uid' => 'Invite Uid',
            'openId' => 'Open ID',
            'unionId' => 'Union ID',
            'session_key' => 'Session Key',
            'phone' => 'Phone',
            'nickname' => 'Nickname',
            'gender' => 'Gender',
            'avatar' => 'Avatar',
            'city' => 'City',
            'province' => 'Province',
            'country' => 'Country',
            'status' => 'Status',
            'cash' => 'Cash',
            'auth_key' => 'Auth Key',
            'created_at' => 'Created At',
        ];
    }
}
