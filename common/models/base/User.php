<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "user".
 *
 * @property int $id
 * @property int $type
 * @property string $openId
 * @property string $unionId
 * @property string $session_key
 * @property string $phone
 * @property string $nickname
 * @property int $gender
 * @property string $avatar
 * @property string $cityName
 * @property string $province
 * @property string $country
 * @property int $status
 * @property int $cash
 * @property string $auth_key
 * @property int $city_id
 * @property int $area_id
 * @property string $realname
 * @property int $created_at
 * @property int $real_at
 */
class User extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'user';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['cash', 'created_at', 'real_at', 'type', 'gender', 'status', 'city_id', 'area_id'], 'integer'],
            [['openId', 'unionId', 'session_key', 'phone', 'nickname', 'avatar', 'cityName', 'province', 'country', 'auth_key', 'realname'], 'string', 'max' => 255],
            [['openId'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id'          => 'ID',
            'type'        => 'Type',
            'openId'      => 'Open ID',
            'unionId'     => 'Union ID',
            'session_key' => 'Session Key',
            'phone'       => 'Phone',
            'nickname'    => 'Nickname',
            'gender'      => 'Gender',
            'avatar'      => 'Avatar',
            'cityName'        => 'City Name',
            'province'    => 'Province',
            'country'     => 'Country',
            'status'      => 'Status',
            'cash'        => 'Cash',
            'auth_key'    => 'Auth Key',
            'city_id'     => 'City Id',
            'area_id'     => 'Area Id',
            'realname'    => 'Realname',
            'created_at'  => 'Created At',
            'real_at'     => 'Real At',
        ];
    }
}
