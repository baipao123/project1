<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "attach".
 *
 * @property string $id
 * @property string $type
 * @property string $tid
 * @property string $path
 * @property int $status
 * @property string $created_at
 * @property string $updated_at
 */
class Attach extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'attach';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type', 'tid', 'created_at', 'updated_at'], 'integer'],
            [['path'], 'string', 'max' => 255],
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
            'type' => 'Type',
            'tid' => 'Tid',
            'path' => 'Path',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
