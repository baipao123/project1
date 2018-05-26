<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "job".
 *
 * @property string $id
 * @property string $jobNo
 * @property string $uid
 * @property string $city_id
 * @property string $area_id
 * @property string $name
 * @property string $prize
 * @property int $prize_type
 * @property string $num
 * @property int $gender
 * @property string $start_at
 * @property string $end_at
 * @property string $work_start
 * @property string $work_end
 * @property string $quiz_position
 * @property string $quiz_longitude
 * @property string $quiz_latitude
 * @property string $work_position
 * @property string $work_longitude
 * @property string $work_latitude
 * @property string $description
 * @property string $require_desc
 * @property string $extra_desc
 * @property string $phone
 * @property string $contact_name
 * @property string $tips
 * @property int $status
 * @property int $follow_num
 * @property string $created_at
 */
class Job extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'job';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['id', 'uid', 'city_id', 'area_id', 'prize', 'num', 'start_at', 'end_at', 'work_start', 'work_end', 'created_at', 'gender', 'prize_type', 'status', 'follow_num'], 'integer'],
            [['jobNo', 'description', 'require_desc', 'extra_desc', 'tips'], 'string'],
            [['name', 'quiz_position', 'quiz_longitude', 'quiz_latitude', 'work_position', 'work_longitude', 'work_latitude', 'phone', 'contact_name'], 'string', 'max' => 255],
            [['jobNo'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id'             => 'ID',
            'jobNO'          => 'JobNo',
            'uid'            => 'Uid',
            'city_id'        => 'City ID',
            'area_id'        => 'Area ID',
            'name'           => 'Name',
            'prize'          => 'Prize',
            'prize_type'     => 'Prize Type',
            'num'            => 'Num',
            'gender'         => 'Gender',
            'start_at'       => 'Start At',
            'end_at'         => 'End At',
            'work_start'     => 'Work Start',
            'work_end'       => 'Work End',
            'quiz_position'  => 'Quiz Position',
            'quiz_longitude' => 'Quiz Longitude',
            'quiz_latitude'  => 'Quiz Latitude',
            'work_position'  => 'Work Position',
            'work_longitude' => 'Work Longitude',
            'work_latitude'  => 'Work Latitude',
            'description'    => 'Description',
            'require_desc'   => 'Require Desc',
            'extra_desc'     => 'Extra Desc',
            'phone'          => 'Phone',
            'contact_name'   => 'Contact Name',
            'tips'           => 'Tips',
            'status'         => 'Status',
            'follow_num'     => 'Follow Num',
            'created_at'     => 'Created At',
        ];
    }
}
