<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "job_follow".
 *
 * @property int $id
 * @property int $jid
 * @property int $uid
 * @property int $created_at
 */
class JobFollow extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'job_follow';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['jid', 'uid', 'created_at'], 'integer'],
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
            'created_at' => 'Created At',
        ];
    }
}
