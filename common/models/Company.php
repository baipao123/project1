<?php
/**
 * Created by PhpStorm.
 * User: huangchen
 * Date: 2018/4/16
 * Time: 下午10:19
 */

namespace common\models;

use common\tools\Img;
use common\tools\Sms;
use common\tools\StringHelper;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * @property CompanyRecord $record
 * @property CompanyRecord $refuseRecord
 * */
class Company extends \common\models\base\Company
{
    const STATUS_VERIFY = 0;
    const STATUS_PASS = 1;
    const STATUS_FORBID = 2;
    const STATUS_IGNORE = 3;

    const TYPE_COMPANY = 2;
    const TYPE_USER_BOSS = 3;

    public function getRecord() {
        return $this->hasOne(CompanyRecord::className(), ["uid" => "uid"])->andWhere(["status" => self::STATUS_VERIFY])->orderBy("created_at desc");
    }

    public function getRefuseRecord() {
        return $this->hasOne(CompanyRecord::className(), ["uid" => "uid"])->andWhere(["status" => self::STATUS_FORBID])->orderBy("created_at desc");
    }

    public static function Bind($uid, $data, $isAdd = false) {
        $user = User::findOne($uid);
        if ($user->type == User::TYPE_USER)
            return "您已经是求职者了";
        $company = static::findOne($uid);
        if ($company && $isAdd)
            return "您已经是招聘者了";
        $company or $company = new static;
        $type = ArrayHelper::getValue($data, "type", 0);
        $cid = ArrayHelper::getValue($data, "cid", 0);
        $aid = ArrayHelper::getValue($data, "aid", 0);
        $name = ArrayHelper::getValue($data, "name", "");
        $icon = ArrayHelper::getValue($data, "icon", "");
        $cover = ArrayHelper::getValue($data, "cover", "");
        $position = ArrayHelper::getValue($data, "position", "");
        $description = ArrayHelper::getValue($data, "description", "");
        $latitude = ArrayHelper::getValue($data, "latitude", "");
        $longitude = ArrayHelper::getValue($data, "longitude", "");
        $attaches = ArrayHelper::getValue($data, "attaches", []);
        $phone = ArrayHelper::getValue($data, "phone", "");
        if ($isAdd && !StringHelper::isMobile($phone))
            return "请输入真实的手机号";
        if (empty($type))
            return "请选择类型";
        $companyTypeText = $type == self::TYPE_COMPANY ? "企业" : "个人";
        if (empty($name))
            return "请输入{$companyTypeText}名称";
        if (empty($cid))
            return "请选择{$companyTypeText}默认的招聘城市";
        if (empty($position) || empty($latitude) || empty($longitude))
            return "请选择{$companyTypeText}所在的地址";
        if (empty($description))
            return "请输入{$companyTypeText}简介";
        if ($isAdd && empty($attaches))
            return "请上传认证资料";
        if ($company->status != self::STATUS_PASS) {
            $company->type = $type;
            $company->uid = $uid;
            $company->name = $name;
            $company->icon = $icon;
            $company->cover = $cover;
            $company->position = $position;
            $company->latitude = $latitude;
            $company->longitude = $longitude;
            $company->description = $description;
            $company->status = self::STATUS_VERIFY;
            if ($company->isNewRecord)
                $company->created_at = time();
            else
                $company->updated_at = time();
            if (!$company->save()) {
                Yii::warning($company->errors, "保存Company出错");
                return false;
            }
        }
        $record = new CompanyRecord;
        $record->uid = $uid;
        $record->type = $type;
        $record->name = $name;
        $record->icon = $icon;
        $record->cover = $cover;
        $record->position = $position;
        $record->latitude = $latitude;
        $record->longitude = $longitude;
        $record->description = $description;
        $record->status = self::STATUS_VERIFY;
        $record->created_at = time();
        if (!$record->save()) {
            Yii::warning($record->errors, "保存CompanyRecord出错");
            return false;
        }
        $oldRecords = CompanyRecord::find()->where(["uid" => $uid, "status" => self::STATUS_VERIFY])->all();
        /* @var $oldRecords CompanyRecord[] */
        foreach ($oldRecords as $r) {
            if ($record->attributes['id'] == $r->id)
                continue;
            $r->status = self::STATUS_IGNORE;
            $r->updated_at = time();
            $r->save();
        }
        //attach
        foreach ($attaches as $path) {
            $attach = new Attach;
            $attach->type = Attach::COMPANY_RECORD;
            $attach->tid = $record->attributes['id'];
            $attach->path = $path;
            $attach->status = Attach::STATUS_ON;
            $attach->created_at = time();
            if (!$attach->save()) {
                Yii::warning($attach->errors, "保存CompanyAttaches出错");
                return false;
            }
        }

        if ($isAdd) {
            $user->city_id = $cid;
            $user->area_id = $aid;
            $user->type = $type;
            $user->phone = $phone;
            $user->save();
        }
        return true;
    }

    public function jobs($type = 0, $page = 1, $limit = 10) {
        $uid = $this->uid;
        $jobs = Yii::$app->db->cache(function () use ($uid, $page, $limit) {
            $query = Job::find()
                ->where(["uid" => $uid])
                ->andWhere(["<>", "status", Job::DEL])
                ->offset(($page - 1) * $limit)->limit($limit)
                ->orderBy([
                    "status"     => [Job::ON, Job::FULL, Job::OFF, Job::END],
                    "created_at" => SORT_DESC
                ]);
            return $query->all();
        }, 10);
        /* @var $jobs Job[] */
        if ($type == 0)
            return Job::formatJobs($jobs, $uid);
        else {
            $data = [];
            foreach ($jobs as $job) {
                $data[] = $job->sampleInfo();
            }
            return $data;
        }
    }

    public function icon() {
        return empty($this->icon) ? "youzhun.jpeg" : $this->icon;
    }

    public function cover() {
        return $this->cover;
    }

    public function refuseReason() {
        if ($this->status != self::STATUS_FORBID)
            return "";
        return $this->refuseRecord ? $this->refuseRecord->reason : "";
    }

    public function info() {
        $refuseRecord = $this->status == self::STATUS_FORBID ? $this->refuseRecord : null;
        return [
            "name"         => $this->name,
            "type"         => $this->type,
            "icon"         => Img::format($this->icon()),
            "cover"        => Img::format($this->cover()),
            "status"       => $this->status,
            "description"  => $this->description,
            "tips"         => $this->tips,
            "position"     => [
                "longitude" => $this->longitude,
                "latitude"  => $this->latitude,
                "address"   => $this->position
            ],
            "refuseReason" => $refuseRecord ? $refuseRecord->reason : "",
            "refuseRid"    => $refuseRecord ? $refuseRecord->id : 0,
        ];
    }

    public function dailyRecords($status = UserJobDaily::PROVIDE, $page = 1, $limit = 10) {
        $records = Yii::$app->db->cache(function () use ($status, $page, $limit) {
            return UserJobDaily::find()->where(["cid" => $this->uid, "status" => $status])
                ->offset(($page - 1) * $limit)
                ->limit($limit)
                ->orderBy("created_at desc")
                ->all();
        }, 15);
        /* @var $records UserJobDaily[] */
        $data = [];
        foreach ($records as $record) {
            $info = [
                "user"   => $record->user(),
                "job"    => $record->job(),
                "clocks" => $record->clocks(),
                "info"   => $record->info(),
            ];
            $data[] = $info;
        }
        return $data;
    }

}