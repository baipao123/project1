<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "user_job_daily".
 *
 * @property int $id
 * @property int $uid
 * @property int $jid
 * @property int $uJid
 * @property int $type
 * @property int $num
 * @property string $msg
 * @property int $date
 * @property int $status
 * @property int $created_at
 * @property int $updated_at
 */
class UserJobDaily extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_job_daily';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uid', 'jid', 'uJid', 'type', 'num', 'date', 'created_at', 'updated_at'], 'integer'],
            [['msg'], 'required'],
            [['msg'], 'string', 'max' => 2000],
            [['status'], 'string', 'max' => 1],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'uid' => 'Uid',
            'jid' => 'Jid',
            'uJid' => 'U Jid',
            'type' => 'Type',
            'num' => 'Num',
            'msg' => 'Msg',
            'date' => 'Date',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
