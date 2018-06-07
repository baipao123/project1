<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "user_has_job".
 *
 * @property int $id
 * @property string $formId
 * @property int $jid
 * @property int $uid
 * @property string $auth_key
 * @property int $status
 * @property string $content
 * @property int $created_at
 * @property int $auth_at
 * @property int $end_at
 * @property int $worktime_0
 * @property int $worktime_1
 * @property int $worktime_2
 * @property int $worktime_3
 * @property int $updated_at
 */
class UserHasJob extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_has_job';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['jid', 'uid', 'status', 'created_at', 'auth_at', 'end_at', 'worktime_1', 'worktime_2', 'worktime_3', 'updated_at'], 'integer'],
            [['content'], 'string'],
            [['worktime_0'], 'number'],
            [['formId', 'auth_key'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'formId' => 'Form ID',
            'jid' => 'Jid',
            'uid' => 'Uid',
            'auth_key' => 'Auth Key',
            'status' => 'Status',
            'content' => 'Content',
            'created_at' => 'Created At',
            'auth_at' => 'Auth At',
            'end_at' => 'End At',
            'worktime_0' => 'Worktime 0',
            'worktime_1' => 'Worktime 1',
            'worktime_2' => 'Worktime 2',
            'worktime_3' => 'Worktime 3',
            'updated_at' => 'Updated At',
        ];
    }
}
