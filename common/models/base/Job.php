<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "job".
 *
 * @property string $id
 * @property string $formId
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
 * @property string $timeTips
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
 * @property int $view_num
 * @property int $follow_num
 * @property int $worktime_0
 * @property int $worktime_1
 * @property int $worktime_2
 * @property int $worktime_3
 * @property string $created_at
 * @property string $updated_at
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
            [['id', 'uid', 'city_id', 'area_id', 'prize', 'num', 'start_at', 'end_at', 'work_start', 'work_end', 'created_at', 'gender', 'prize_type', 'status', 'view_num', 'follow_num', 'worktime_1', 'worktime_2', 'worktime_3', 'updated_at'], 'integer'],
            [['worktime_0'], 'number'],
            [['jobNo', 'description', 'require_desc', 'extra_desc', 'tips'], 'string'],
            [['formId', 'name', 'timeTips', 'quiz_position', 'quiz_longitude', 'quiz_latitude', 'work_position', 'work_longitude', 'work_latitude', 'phone', 'contact_name'], 'string', 'max' => 255],
            [['jobNo'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id'             => 'ID',
            'formId'         => 'Form Id',
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
            'timeTips'       => 'Time Tips',
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
            'view_num'       => 'View Num',
            'follow_num'     => 'Follow Num',
            'worktime_0'     => 'Worktime 0',
            'worktime_1'     => 'Worktime 1',
            'worktime_2'     => 'Worktime 2',
            'worktime_3'     => 'Worktime 3',
            'created_at'     => 'Created At',
            'updated_at'     => 'Updated At'
        ];
    }
}
