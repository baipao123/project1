<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "sms_record".
 *
 * @property int $id
 * @property string $phone
 * @property string $code
 * @property int $status
 * @property string $res
 * @property string $bizId
 * @property int $created_at
 */
class SmsRecord extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sms_record';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['res'], 'string'],
            [['created_at'], 'integer'],
            [['phone', 'code', 'bizId'], 'string', 'max' => 255],
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
            'phone' => 'Phone',
            'code' => 'Code',
            'status' => 'Status',
            'res' => 'Res',
            'bizId' => 'Biz ID',
            'created_at' => 'Created At',
        ];
    }
}
