<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "company".
 *
 * @property int $uid
 * @property int $type
 * @property string $name
 * @property string $icon
 * @property string $cover
 * @property string $position
 * @property string $latitude
 * @property string $longitude
 * @property string $description
 * @property string $tips
 * @property int $status
 * @property int $created_at
 * @property int $updated_at
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
            [['uid', 'created_at', 'updated_at', 'type', 'status'], 'integer'],
            [['description','tips'], 'string'],
            [['name', 'icon', 'cover', 'position', 'latitude', 'longitude'], 'string', 'max' => 255],
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
            'latitude' => 'Latitude',
            'longitude' => 'Longitude',
            'description' => 'Description',
            'tips' => 'Tips',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
