<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "school".
 *
 * @property int $id
 * @property int $city_id
 * @property string $name
 * @property int $status
 * @property int $created_at
 */
class School extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'school';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['city_id', 'created_at'], 'integer'],
            [['name'], 'string', 'max' => 255],
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
            'city_id' => 'City ID',
            'name' => 'Name',
            'status' => 'Status',
            'created_at' => 'Created At',
        ];
    }
}
