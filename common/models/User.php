<?php

namespace common\models;

use common\tools\Img;
use common\tools\WxApp;
use Yii;

/**
 * @property Company $company
 * @property District $city
 * @property District $area
 * @property School $school
 * */
class User extends \common\models\base\User
{
    const TYPE_USER = 1;
    const TYPE_COMPANY = 2;
    const TYPE_USER_BOSS = 3;

    public function getCompany() {
        return $this->hasOne(Company::className(), ["uid" => "id"]);
    }

    public function getCity() {
        return $this->hasOne(District::className(), ["id" => "city_id"]);
    }

    public function getArea() {
        return $this->hasOne(District::className(), ["id" => "area_id"]);
    }

    public function getSchool(){
        return $this->hasOne(School::className(), ["id" => "school_id"]);
    }

    /**
     * @param string $code
     * @return static
     */
    public static function findUserByAppCode($code) {
        $data = WxApp::decryptUserCode($code);
        if (!$data || !isset($data['openid']) || empty($data['openid']))
            return null;
        $user = static::findOne(["openId" => $data['openid']]);
        $user or $user = new static;
        $user->openId = $data['openid'];
        $user->unionId = isset($data['unionid']) ? $data['unionid'] : "";
        $user->session_key = isset($data['session_key']) ? $data['session_key'] : "";
        if ($user->isNewRecord)
            $user->created_at = time();
        $user->save();
        return $user;
    }

    public function verifyUserInfo($rawData, $signature) {
        $sign = sha1($rawData . $this->session_key);
        if ($sign === $signature) {
            $userInfo = json_decode($rawData, true);
            $this->avatar = $userInfo['avatarUrl'];
            $this->nickname = $userInfo['nickName'];
            $this->gender = $userInfo['gender'];
            $this->cityName = $userInfo['city'];
            $this->province = $userInfo['province'];
            $this->country = $userInfo['country'];
            $this->save();
            return true;
        }
        return false;
    }

    public function isCompany() {
        return $this->type > 1;
    }

    public function cityStr() {
        $str = "";
        if ($this->city) {
            $str = $this->city->name;
        }
        if ($this->area)
            $str .= " " . $this->area->name;
        return trim($str);
    }


    public function info() {
        return [
            "uid"       => $this->id,
            "type"      => $this->type,
            "username"  => empty($this->realname) ? $this->nickname : $this->realname,
            "avatar"    => Img::format($this->avatar),
            "phone"     => $this->phone(),
            "purePhone" => $this->phone,
            "gender"    => $this->gender,
            "city_id"   => $this->city_id,
            "area_id"   => $this->area_id,
            "cityStr"   => $this->cityStr(),
            "school_id" => $this->school_id,
            "school_name" => $this->school ? $this->school->name : "",
            "school_year" => $this->school_year,
        ];
    }

    public function phone($full = false) {
        if (empty($this->phone) || $full)
            return $this->phone;
        return substr_replace($this->phone, '****', 3, 4);
    }

    public function jobs($status = Job::ON, $page = 1, $limit = 10) {
        if ($this->type == 0)
            return [];
        if ($this->type == self::TYPE_USER) {
            $uid = $this->id;
            $jids = Yii::$app->db->cache(function () use ($status, $uid, $page, $limit) {
                $query = UserHasJob::find()
                    ->where(["uid" => $uid])
                    //                    ->andWhere(["<>", "status", UserHasJob::REFUSE])
                    ->offset(($page - 1) * $limit)->limit($limit)
                    ->select("jid")
                    ->orderBy([
                        "status"     => [UserHasJob::WORKING, UserHasJob::ON, UserHasJob::APPLY, UserHasJob::END, UserHasJob::REFUSE],
                        "created_at" => SORT_DESC
                    ]);
                if ($status > 0)
                    $query->andWhere(["status" => $status]);
                return $query->column();
            }, 30);
            /* @var $jobs Job[] */
            $jobs = Job::find()->where(["id" => $jids])->orderBy(["id" => $jids])->all();
            return Job::formatJobs($jobs, $this->id);
        }
        $company = $this->company;
        return $company ? $company->jobs(0, $page, $limit) : [];
    }

    public function followJobs($page = 1, $limit = 10) {
        if ($this->type != self::TYPE_USER)
            return [];
        $uid = $this->id;
        $jids = Yii::$app->db->cache(function () use ($uid, $page, $limit) {
            return JobFollow::find()
                ->where(["uid" => $uid])
                ->offset(($page - 1) * $limit)->limit($limit)
                ->select("jid")
                ->orderBy("created_at desc")
                ->column();
        }, 15);
        $jobs = Job::find()->where(["id" => $jids])->andWhere(["NOT IN", "status", [Job::OFF, Job::DEL]])->orderBy(["id" => $jids])->all();
        return Job::formatJobs($jobs, $this->id);
    }

    public function sendTpl($type, $data, $formId, $page = "", $color = [], $keyword = "") {
        $tplData = [];
        for ($j = 1; $j <= count($data); $j++) {
            $tplData[ "keyword" . $j ] = [
                "value" => $data[ $j - 1 ]
            ];
            if (isset($color[ $j ]))
                $tplData[ "keyword" . $j ]['color'] = $color[ $j ];
        }
        $accessToken = WxApp::getAccessToken();
        WxApp::sendTpl($accessToken, $this->openId, $type, $tplData, $page, $formId, $keyword);
    }
}
