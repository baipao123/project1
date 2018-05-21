<?php
/**
 * Created by PhpStorm.
 * User: huangchen
 * Date: 2018/4/12
 * Time: 下午7:59
 */

namespace common\models;

use Yii;

/**
 * @property Job $job
 * @property UserHasJob $uJob
 * @property UserClock[] $clocks
 * */
class UserJobDaily extends \common\models\base\UserJobDaily
{
    const NOTHING = 0;
    const PROVIDE = 1;
    const REFUSE = 2;
    const PASS = 3;

    const TYPE_HOUR = 0;
    const TYPE_HALF_DAY = 1;
    const TYPE_WHOLE_DAY = 2;

    public function getJob() {
        return $this->hasOne(Job::className(), ["id" => "jid"]);
    }

    public function getUJob() {
        return $this->hasOne(UserHasJob::className(), ["id" => "uJid"]);
    }

    public function getClocks() {
        return $this->hasMany(UserClock::className(), ["uJid" => "uJid"])->andWhere(["BETWEEN", 'created_at', strtotime($this->date), strtotime($this->date) + 24 * 3600 - 1]);
    }

    public function dateStr() {
        $format = substr($this->date, 0, 4) == date("Y") ? "m-d" : "Y-m-d";
        return date($format, strtotime($this->date));
    }

    public function numStr() {
        if ($this->type == self::TYPE_WHOLE_DAY)
            return "一天";
        if ($this->type == self::TYPE_HALF_DAY)
            return "半天";
        if ($this->type == self::TYPE_HOUR && $this->num == 0)
            return "未工作";
        return $this->num . "小时";
    }

    public function info() {
        return [
            'id'     => $this->id,
            "date"   => $this->dateStr(),
            "type"   => $this->type,
            "num"    => $this->num,
            "numStr" => $this->numStr(),
            "status" => $this->status,
            "msg"    => $this->msg,
        ];
    }

    public function job() {
        $job = $this->job;
        $uJob = $this->uJob;
        if (!$job || !$uJob)
            return [];
        return [
            "jid"        => $this->jid,
            "uJid"       => $this->uJid,
            "name"       => $job->name,
            "work_start" => date("Y-m-d H:i:s", $uJob->auth_at),
            "prize"      => $job->prizeStr()
        ];
    }

    public function clocks() {
        $clocks = $this->clocks;
        $data = [];
        foreach ($clocks as $clock) {
            $data[] = $clock->info(true);
        }
        return $data;
    }


    public function afterSave($insert, $changedAttributes) {
        Yii::info(json_encode($this->attributes), "job-daily");
        parent::afterSave($insert, $changedAttributes);
    }
}