<?php
/**
 * Created by PhpStorm.
 * User: huangchen
 * Date: 2018/4/12
 * Time: 下午8:31
 */

namespace common\models;

use common\tools\StringHelper;
use Yii;

/**
 * @property Company $company
 * */
class Job extends \common\models\base\Job
{
    public function getCompany() {
        return $this->hasOne(Company::className(), ["uid" => "uid"]);
    }

    const TMP = 0;
    const ON = 1;
    const OFF = 2;
    const DEL = 3;

    const TYPE_HOUR = 1;
    const TYPE_DAY = 2;

    const WORK_POSITION_TYPE_QUIZ = 0;
    const WORK_POSITION_TYPE_OTHER = 1;

    const CONCAT_TYPE_COMPANY = 1;
    const CONCAT_TYPE_OTHER = 2;

    public function format() {
        return [];
    }

    protected function getJobId() {
        if (!empty($this->id))
            return $this->id;
        $date = date("Ymd");
        $index = Yii::$app->redis->incr("JOB-ID-NUM-DATE:{$date}");
        return intval($date . sprintf("%4d", $index));
    }

    public static function saveJob($data, $saveTmp = false, $jid = 0) {
        if (!empty($jid)) {
            $job = self::findOne($jid);
            if (!$job || $job->uid != Yii::$app->user->id)
                return "未找到岗位";
        } else {
            $job = new self;
            $job->id  = $job->getJobId();
        }
        $job->uid = Yii::$app->user->id;
        if (!$saveTmp && empty($data['name']))
            return "岗位名称未填写";
        $job->name = $data['name'];
        if (!$saveTmp && empty($data['city_id']) && empty($data['area_id']))
            return "选择招聘城市";
        $job->city_id = $data['city_id'];
        $job->area_id = $data['area_id'];
        if (!$saveTmp && empty($data['prize']))
            return "工资未填写";
        $job->prize = intval($data['prize'] * 100);
        $job->prize_type = $data['prize_type'];
        $job->num = (int)$data['num'];
        $job->gender = $data['gender'];
        if ($data['start_date'] > $data['end_date'])
            return "用工结束日期不能早于开始日期";
        if (!$saveTmp && $data['end_date']< date("Y-m-d"))
            return "结束日期不能早于今天";
        $job->start_at = date("Ymd", strtotime($data['start_date']));
        $job->end_at = date("Ymd", strtotime($data['end_date']));
        $job->work_start = date("Hi", strtotime($data['start_time']));
        $job->work_end = date("Hi", strtotime($data['end_time']));
//        if (!$saveTmp && $data['age_start'] > $data['age_end'])
//            return "年龄区间不正确";
//        $job->age_start = $data['age_start'];
//        $job->age_end = $data['age_end'];
        if (!$saveTmp && empty($data['quiz_position']))
            return "请输入面试地址";
        if (!$saveTmp && empty($data['quiz_latitude']) || empty($data['quiz_longitude']))
            return "请在地图现在面试地址";
        $job->quiz_position = $data['quiz_position'];
        $job->quiz_latitude = $data['quiz_latitude'];
        $job->quiz_longitude = $data['quiz_longitude'];
        if ($data['work_position_type'] == self::WORK_POSITION_TYPE_OTHER) {
            if (!$saveTmp && empty($data['work_position']))
                return "请输入工作地址";
            if (!$saveTmp && empty($data['work_latitude']) || empty($data['work_longitude']))
                return "请在地图现在工作地址";
            $job->work_position = $data['work_position'];
            $job->work_latitude = $data['work_latitude'];
            $job->work_longitude = $data['work_longitude'];
        } else {
            $job->work_position = "";
            $job->work_latitude = "";
            $job->work_longitude = "";
        }
        $job->description = $data['description'];
        $job->require_desc = $data['require_desc'];
        $job->extra_desc = $data['extra_desc'];
        if ($data['contact_type'] == self::CONCAT_TYPE_OTHER) {
            if (!$saveTmp && !StringHelper::isMobile($data['phone']))
                return "联系方式不正确";
            $job->phone = $data['phone'];
            if (!$saveTmp && !StringHelper::isRealName($data['contact_name']))
                return "请输入正确的联系人";
            $job->contact_name = $data['contact_name'];
        } else {
            $job->phone = "";
            $job->contact_name = "";
        }
        $job->tips = $data['tips'];
        if ($job->isNewRecord) {
            $job->created_at = time();
            $job->status = $saveTmp ? self::TMP : self::OFF;
        } else {
            $job->status = $saveTmp ? self::TMP : $job->status;
        }
        if ($job->save())
            return true;
        Yii::warning($job->errors, "保存Job失败");
        return false;
    }

    public function jobInfo() {
        return [];
    }

    public function info() {
        return [
            "company" => $this->company->info(),
            "job"     => $this->jobInfo()
        ];
    }
}