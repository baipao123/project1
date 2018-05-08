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
 */
class UserHasJob extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'user_has_job';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['jid', 'uid', 'created_at', 'status'], 'integer'],
            [['content'], 'string'],
            [['auth_key', 'formId'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id'         => 'ID',
            'formId'     => 'FormId',
            'jid'        => 'Jid',
            'uid'        => 'Uid',
            'auth_key'   => 'Auth Key',
            'status'     => 'Status',
            'content'    => 'Content',
            'created_at' => 'Created At',
        ];
    }
}
