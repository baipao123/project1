<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "company_record".
 *
 * @property string $id
 * @property string $formId
 * @property string $uid
 * @property string $name
 * @property string $icon
 * @property string $cover
 * @property string $position
 * @property string $latitude
 * @property string $longitude
 * @property string $accuracy
 * @property string $description
 * @property string $reason
 * @property int $status
 * @property string $created_at
 * @property string $updated_at
 */
class CompanyRecord extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'company_record';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uid', 'created_at', 'updated_at'], 'integer'],
            [['description'], 'string'],
            [['formId', 'name', 'icon', 'cover', 'position', 'latitude', 'longitude', 'accuracy', 'reason'], 'string', 'max' => 255],
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
            'uid' => 'Uid',
            'name' => 'Name',
            'icon' => 'Icon',
            'cover' => 'Cover',
            'position' => 'Position',
            'latitude' => 'Latitude',
            'longitude' => 'Longitude',
            'accuracy' => 'Accuracy',
            'description' => 'Description',
            'reason' => 'Reason',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
