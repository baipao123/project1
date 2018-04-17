<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "company_record".
 *
 * @property int $id
 * @property int $cid
 * @property string $formId
 * @property int $uid
 * @property string $name
 * @property string $icon
 * @property string $attaches
 * @property string $position
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
            [['cid', 'uid', 'created_at', 'updated_at'], 'integer'],
            [['attaches', 'description'], 'required'],
            [['attaches', 'description'], 'string'],
            [['formId', 'name', 'icon', 'position', 'reason'], 'string', 'max' => 255],
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
            'cid' => 'Cid',
            'formId' => 'Form ID',
            'uid' => 'Uid',
            'name' => 'Name',
            'icon' => 'Icon',
            'attaches' => 'Attaches',
            'position' => 'Position',
            'description' => 'Description',
            'reason' => 'Reason',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
