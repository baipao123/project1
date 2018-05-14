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
use yii\helpers\ArrayHelper;

/**
 * @property Company $company
 * @property District $city
 * @property District $area
 * @property User $owner
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
    const EXPIRE = 4;

    const TYPE_HOUR = 1;
    const TYPE_DAY = 2;

    const WORK_POSITION_TYPE_QUIZ = 0;
    const WORK_POSITION_TYPE_OTHER = 1;

    const CONCAT_TYPE_COMPANY = 0;
    const CONCAT_TYPE_OTHER = 1;

    public function getCity() {
        return $this->hasOne(District::className(), ["id" => "city_id"]);
    }

    public function getArea() {
        return $this->hasOne(District::className(), ["id" => "area_id"]);
    }

    public function getOwner() {
        return $this->hasOne(User::className(), ["id" => "uid"]);
    }

    /**
     * @param self[] $jobs
     * @param int $uid
     * @return array
     */
    public static function formatJobs($jobs, $uid = 0) {
        $jids = ArrayHelper::getColumn($jobs, "id");
        $uJobs = empty($uid) ? [] : UserHasJob::find()->where(["uid" => $uid, "jid" => $jids])->all();
        /* @var $uJobs UserHasJob[] */
        $uJobs = ArrayHelper::index($uJobs, "id");
        $likedJids = empty($uid) ? [] : JobFollow::find()->where(["uid" => $uid, "jid" => $jids])->select("jid")->column();

        $data = [];
        foreach ($jobs as $job) {
            if (!$job->company)
                continue;
            $uJob = isset($uJobs[ $job->id ]) ? $uJobs[ $job->id ] : null;
            $data[] = [
                "id"           => $job->id,
                "name"         => $job->name,
                "company"      => $job->company->info(),
                "prize"        => $job->prizeStr(),
                "position"     => empty($job->work_position) ? $job->quiz_position : $job->work_position,
                "date"         => $job->workDate(),
                "time"         => $job->workTime(0),
                "cityStr"      => $job->cityStr(),
                "userApplyNum" => $job->getApplyNum(),
                "userPassNum"  => $job->getPassNum(),
                "num"          => $job->num,
                "status"       => $job->status,
                "pushAt"       => date("Y-m-d", $job->created_at),
                "user"         => [
                    "isOwner" => $uid == $job->uid,
                    "isLike"  => in_array($job->id, $likedJids),
                    "status"  => $uJob ? $uJob->status : 0,
                    "uJid"    => $uJob ? $uJob->id : 0
                ],
            ];
        }
        return $data;
    }

    public function prizeStr() {
        if (empty($this->prize))
            return "面议";
        $str = $this->prize / 100 . "/";
        $str .= $this->prize_type == self::TYPE_HOUR ? "小时" : "天";
        return $str;
    }

    public function workDate() {
        $start = date("Y-m-d", strtotime($this->start_at));
        $end = date("Y-m-d", strtotime($this->end_at));
        return $start == $end ? $start : $start . " 至 " . $end;
    }

    public function workTime($type = 0) {
        $start = $type == 2 ? "" : sprintf("%02d", floor($this->work_start / 100)) . ":" . sprintf("%02d", $this->work_start % 100);
        if ($type == 1)
            return $start;
        $end = sprintf("%02d", floor($this->work_end / 100)) . ":" . sprintf("%02d", $this->work_end % 100);
        if ($type == 2)
            return $end;
        return $start . " -- " . $end;
    }


    protected function getJobIdFromRedis() {
        if (!empty($this->jobId))
            return $this->jobId;
        $date = date("Ymd");
        $index = Yii::$app->redis->incr("JOB-ID-NUM-DATE:{$date}");
        return $date . sprintf("%04d", $index);
    }

    public static function saveJob($data, $saveTmp = false, &$jid = 0) {
        if (!empty($jid)) {
            $job = self::findOne($jid);
            if (!$job || $job->uid != Yii::$app->user->id)
                return "未找到岗位";
        } else {
            $job = new self;
            $job->jobId = $job->getJobIdFromRedis();
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
        if (!$saveTmp && $job->isNewRecord && $data['end_date'] < date("Y-m-d"))
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
        if ($data['work_position_type'] == 'false') {
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
        if ($data['contact_type'] == 'false') {
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
        if ($job->save()) {
            $jid = $job->attributes['id'];
            return true;
        }
        Yii::warning($job->errors, "保存Job失败");
        return false;
    }

    public function cityStr() {
        $str = "";
        if ($this->city)
            $str = $this->city->name;
        if ($this->area)
            $str .= " " . $this->area->name;
        return trim($str);
    }

    public function info($uid = 0) {
        $uJob = empty($uid) ? null : UserHasJob::find()->where(["uid" => $uid, "jid" => $this->id])->one();
        /* @var $uJob UserHasJob */
        return [
            "id"                => $this->id,
            "jobId"             => $this->jobId,
            "uid"               => $this->uid,
            "city_id"           => $this->city_id,
            "area_id"           => $this->area_id,
            "cityStr"           => $this->CityStr(),
            "name"              => $this->name,
            "gender"            => $this->gender,
            "num"               => $this->num,
            "prize_type"        => $this->prize_type,
            "prize"             => $this->prize / 100,
            "start_date"        => date("Y-m-d", strtotime($this->start_at)),
            "end_date"          => date("Y-m-d", strtotime($this->end_at)),
            "start_time"        => $this->workTime(1),
            "end_time"          => $this->workTime(2),
            "quiz"              => [
                "longitude" => $this->quiz_longitude,
                "latitude"  => $this->quiz_latitude,
                "position"  => $this->quiz_position,
            ],
            "work"              => [
                "longitude" => $this->work_longitude,
                "latitude"  => $this->work_latitude,
                "position"  => $this->work_position,
                "useQuiz"   => empty($this->work_position)
            ],
            "description"       => $this->description,
            "require_desc"      => $this->require_desc,
            "extra_desc"        => $this->extra_desc,
            "useCompanyContact" => empty($this->contact_name),
            "contact_name"      => empty($this->contact_name) ? $this->owner->realname : $this->contact_name,
            "phone"             => empty($this->phone) ? $this->owner->phone : $this->phone,
            "tips"              => $this->tips,
            "status"            => $this->status,
            "isLike"            => empty($uid) ? false : JobFollow::find()->where(["uid" => $uid, "jid" => $this->id])->exists(),
            "userStatus"        => $uJob ? $uJob->status : 0,
            "userApplyNum"      => $this->getApplyNum(),
            "userPassNum"       => $this->getPassNum(),
            "pushAt"            => date("Y-m-d", $this->created_at),
        ];
    }

    public function getApplyNum() {
        $jid = $this->id;
        return Yii::$app->db->cache(function () use ($jid) {
            return UserHasJob::find()->where(["jid" => $jid])->count();
        }, 10);
    }

    public function getPassNum() {
        $jid = $this->id;
        return Yii::$app->db->cache(function () use ($jid) {
            return UserHasJob::find()->where(["jid" => $jid, "status" => [UserHasJob::ON, UserHasJob::END]])->count();
        }, 10);
    }

    public static function getList($uid = 0, $text = "", $cid = 0, $aid = 0, $page = 1, $limit = 10) {
        $jobs = Yii::$app->db->cache(function () use ($text, $cid, $aid, $page, $limit) {
            $query = self::find()
                ->where(["status" => self::ON])
                ->offset(($page - 1) * $limit)->limit($limit)
                ->orderBy("created_at desc");
            if (!empty($text))
                $query->andWhere(["LIKE", "name", $text]);
            if ($cid > 0)
                $query->andWhere(["city_id" => $cid]);
            if ($aid > 0)
                $query->andWhere(["area_id" => $aid]);
            return $query->all();
        }, 30);
        /* @var $jobs self[] */
        return self::formatJobs($jobs, $uid);
    }

    public function sampleInfo() {
        return [
            "id"     => $this->id,
            "name"   => $this->name,
            "status" => $this->status,
            "tStart" => $this->workTime(1),
            "tEnd"   => $this->workTime(2),
            "date"   => $this->workDate(),
            "icon"   => $this->company->icon,
            "prize"  => $this->prizeStr()
        ];
    }
}