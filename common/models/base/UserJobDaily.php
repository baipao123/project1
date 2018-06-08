<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "user_job_daily".
 *
 * @property int $id
 * @property int $uid
 * @property int $jid
 * @property int $cid
 * @property int $uJid
 * @property int $type
 * @property int $num
 * @property string $msg
 * @property int $date
 * @property int $status
 * @property string $formId
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
            [['uid', 'jid', 'cid', 'uJid', 'type', 'date', 'status', 'created_at', 'updated_at'], 'integer'],
            [['msg'], 'string', 'max' => 2000],
            [['formId'], 'string', 'max' => 255],
            [['num'], 'number'],
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
            'cid' => 'Cid',
            'uJid' => 'U Jid',
            'type' => 'Type',
            'num' => 'Num',
            'msg' => 'Msg',
            'date' => 'Date',
            'status' => 'Status',
            'formId' => 'Form Id',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
