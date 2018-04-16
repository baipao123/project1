<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "company".
 *
 * @property int $id
 * @property int $uid
 * @property string $name
 * @property string $icon
 * @property string $attaches
 * @property string $position
 * @property string $description
 * @property string $reason
 * @property int $status
 * @property int $created_at
 */
class Company extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'company';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uid', 'created_at'], 'integer'],
            [['attaches', 'description'], 'required'],
            [['attaches', 'description'], 'string'],
            [['name', 'icon', 'position', 'reason'], 'string', 'max' => 255],
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
            'name' => 'Name',
            'icon' => 'Icon',
            'attaches' => 'Attaches',
            'position' => 'Position',
            'description' => 'Description',
            'reason' => 'Reason',
            'status' => 'Status',
            'created_at' => 'Created At',
        ];
    }
}
