<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "company_record".
 *
 * @property int $id
 * @property string $formId
 * @property int $type
 * @property int $cid
 * @property int $aid
 * @property int $uid
 * @property string $name
 * @property string $icon
 * @property string $cover
 * @property string $position
 * @property string $latitude
 * @property string $longitude
 * @property string $description
 * @property string $reason
 * @property int $status
 * @property int $created_at
 * @property int $updated_at
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
            [['cid', 'aid', 'uid', 'created_at', 'updated_at', 'type', 'status'], 'integer'],
            [['description'], 'string'],
            [['formId', 'name', 'icon', 'cover', 'position', 'latitude', 'longitude', 'reason'], 'string', 'max' => 255],
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
            'type' => 'Type',
            'cid' => 'Cid',
            'aid' => 'Aid',
            'uid' => 'Uid',
            'name' => 'Name',
            'icon' => 'Icon',
            'cover' => 'Cover',
            'position' => 'Position',
            'latitude' => 'Latitude',
            'longitude' => 'Longitude',
            'description' => 'Description',
            'reason' => 'Reason',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
