<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "user_sign".
 *
 * @property string $id
 * @property int $type
 * @property string $uid
 * @property string $jid
 * @property string $position
 * @property string $latitude
 * @property string $longitude
 * @property string $accuracy
 * @property string $msg
 * @property string $created_at
 */
class UserClock extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_sign';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uid', 'jid', 'created_at'], 'integer'],
            [['type'], 'string', 'max' => 1],
            [['position', 'latitude', 'longitude', 'accuracy', 'msg'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type' => 'Type',
            'uid' => 'Uid',
            'jid' => 'Jid',
            'position' => 'Position',
            'latitude' => 'Latitude',
            'longitude' => 'Longitude',
            'accuracy' => 'Accuracy',
            'msg' => 'Msg',
            'created_at' => 'Created At',
        ];
    }
}
