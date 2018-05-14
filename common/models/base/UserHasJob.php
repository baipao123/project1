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
            [['jid', 'uid', 'created_at', 'auth_at', 'end_at', 'updated_at'], 'integer'],
            [['content'], 'required'],
            [['content'], 'string'],
            [['formId', 'auth_key'], 'string', 'max' => 255],
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
            'formId' => 'Form ID',
            'jid' => 'Jid',
            'uid' => 'Uid',
            'auth_key' => 'Auth Key',
            'status' => 'Status',
            'content' => 'Content',
            'created_at' => 'Created At',
            'auth_at' => 'Auth At',
            'end_at' => 'End At',
            'updated_at' => 'Updated At',
        ];
    }
}
