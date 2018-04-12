<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "user_has_job".
 *
 * @property string $id
 * @property string $jid
 * @property string $uid
 * @property int $status
 * @property string $content
 * @property string $created_at
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
            [['jid', 'uid', 'created_at'], 'integer'],
            [['content'], 'required'],
            [['content'], 'string'],
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
            'jid' => 'Jid',
            'uid' => 'Uid',
            'status' => 'Status',
            'content' => 'Content',
            'created_at' => 'Created At',
        ];
    }
}
