<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "district".
 *
 * @property int $id
 * @property int $pid
 * @property int $cid
 * @property string $name
 * @property int $status
 * @property int $created_at
 * @property int $updated_at
 */
class District extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'district';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['pid', 'cid', 'status', 'created_at', 'updated_at'], 'integer'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id'         => 'ID',
            'pid'        => 'Pid',
            'cid'        => 'Cid',
            'name'       => 'Name',
            'status'     => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
