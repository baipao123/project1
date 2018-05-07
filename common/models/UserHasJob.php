<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018-04-12
 * Time: 17:21:20
 */

namespace common\models;

/**
 * @property Job $job
 * @property User $user
 * */
class UserHasJob extends \common\models\base\UserHasJob
{
    const APPLY = 0;
    const ON = 1;
    const REFUSE = 9;
    const END = 10;

    public function getJob() {
        return $this->hasOne(Job::className(), ["jid" => "id"]);
    }

    public function getUser() {
        return $this->hasOne(User::className(), ["uid" => "id"]);
    }

    public static function isOn($uid, $jid) {
        return self::find()->where(["uid" => $uid, "jid" => $jid, "status" => self::ON])->exists();
    }

    public static function getJobs($uid, $page = 1, $limit = 10) {
        $uJobs = self::find()
            ->where(["uid" => $uid, "status" => [self::APPLY, self::ON, self::END]])
            ->offset(($page - 1) * $limit)
            ->limit($limit)
            ->orderBy(["status=" . self::END . " ASC,status DESC,created_at DESC"])
            ->all();
        /* @var $uJobs self[] */
        $data = [];
        foreach ($uJobs as $uJob) {
            $job = $uJob->job;
            $info = $job->format();
            $info['status'] = $uJob->status;
            $info['time'] = date("Y-m-d H:i:s", $uJob->created_at);
            $data[] = $info;
        }
        return $data;
    }

    public function user() {
        $user = $this->user;
        return [
            "name"   => $user->realname,
            "phone"  => $user->phone,
            "time"   => date("Y-m-d H:i:s", $this->created_at),
            "status" => $this->status
        ];
    }
}