<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "company".
 *
 * @property string $uid
 * @property int $type
 * @property string $name
 * @property string $icon
 * @property string $cover
 * @property string $position
 * @property string $description
 * @property int $status
 * @property string $created_at
 * @property string $updated_at
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
            [['uid'], 'required'],
            [['uid', 'created_at', 'updated_at'], 'integer'],
            [['description'], 'string'],
            [['type', 'status'], 'string', 'max' => 1],
            [['name', 'icon', 'cover', 'position'], 'string', 'max' => 255],
            [['uid'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'uid' => 'Uid',
            'type' => 'Type',
            'name' => 'Name',
            'icon' => 'Icon',
            'cover' => 'Cover',
            'position' => 'Position',
            'description' => 'Description',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
