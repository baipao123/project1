<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "job".
 *
 * @property int $id
 * @property int $uid
 * @property int $cid
 * @property string $name
 * @property string $wages
 * @property int $prize
 * @property int $join_start
 * @property int $join_end
 * @property int $join_day
 * @property string $wages_desc
 * @property string $use_desc
 * @property string $work_desc
 * @property string $warm_desc
 * @property int $created_at
 */
class Job extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'job';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uid', 'cid', 'prize', 'join_start', 'join_end', 'join_day', 'created_at'], 'integer'],
            [['wages_desc', 'use_desc', 'work_desc', 'warm_desc'], 'required'],
            [['wages_desc', 'use_desc', 'work_desc', 'warm_desc'], 'string'],
            [['name', 'wages'], 'string', 'max' => 255],
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
            'cid' => 'Cid',
            'name' => 'Name',
            'wages' => 'Wages',
            'prize' => 'Prize',
            'join_start' => 'Join Start',
            'join_end' => 'Join End',
            'join_day' => 'Join Day',
            'wages_desc' => 'Wages Desc',
            'use_desc' => 'Use Desc',
            'work_desc' => 'Work Desc',
            'warm_desc' => 'Warm Desc',
            'created_at' => 'Created At',
        ];
    }
}
