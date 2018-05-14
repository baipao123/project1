<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "user_clock".
 *
 * @property int $id
 * @property int $uJid
 * @property int $type
 * @property int $uid
 * @property int $jid
 * @property string $position
 * @property string $latitude
 * @property string $longitude
 * @property string $accuracy
 * @property string $msg
 * @property string $formId
 * @property int $created_at
 */
class UserClock extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_clock';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uJid','type', 'uid', 'jid', 'created_at'], 'integer'],
            [['position', 'latitude', 'longitude', 'accuracy', 'msg', 'formId'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'uJid' => 'U Jid',
            'type' => 'Type',
            'uid' => 'Uid',
            'jid' => 'Jid',
            'position' => 'Position',
            'latitude' => 'Latitude',
            'longitude' => 'Longitude',
            'accuracy' => 'Accuracy',
            'msg' => 'Msg',
            'formId' => 'Form ID',
            'created_at' => 'Created At',
        ];
    }
}
