<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018-04-12
 * Time: 17:21:20
 */

namespace common\models;

use yii\helpers\ArrayHelper;

/**
 * @property Job $job
 * @property User $user
 * @property UserClock[] $clocks
 * @property UserJobDaily[] $dayHours
 * */
class UserHasJob extends \common\models\base\UserHasJob
{
    const APPLY = 1;
    const ON = 2;
    const REFUSE = 9;
    const END = 10;

    public function getJob() {
        return $this->hasOne(Job::className(), ["id" => "jid"]);
    }

    public function getUser() {
        return $this->hasOne(User::className(), ["id" => "uid"]);
    }

    public function getClocks() {
        return $this->hasMany(UserClock::className(), ["uJid" => "id"]);
    }

    public function getDayHours() {
        return $this->hasMany(UserJobDaily::className(), ["uJid" => "id"])->orderBy("created_at desc");
    }

    public function user() {
        $user = $this->user;
        return [
            "job"    => $this->job->name,
            "name"   => $user->realname,
            "phone"  => $user->phone,
            "time"   => date("Y-m-d H:i:s", $this->created_at),
            "status" => $this->status
        ];
    }

    public function info() {
        return [
            "id"         => $this->id,
            "jid"        => $this->jid,
            "uid"        => $this->uid,
            "status"     => $this->status,
            "key"        => $this->auth_key,
            "content"    => $this->content,
            "workTime"   => $this->workTimeStr(),
            "created_at" => date("Y-m-d H:i:s", $this->created_at),
            "auth_at"    => date("Y-m-d H:i:s", $this->auth_at),
            "end_at"     => date("Y-m-d H:i:s", $this->end_at),
            "refuse_at"  => date("Y-m-d H:i:s", $this->updated_at),
        ];
    }

    public function workTimeStr() {
        $str = "";
        $day = $this->worktime_2 + $this->worktime_1 / 2;
        if ($day > 0)
            $str = $day . "天";
        if ($this->worktime_0 > 0)
            $str .= " " . $this->worktime_0 . "小时";
        return trim($str);
    }

    public function clocks() {
        $clocks = $this->clocks;
        $data = [];
        foreach ($clocks as $clock) {
            $info = $clock->info();
            $data[ $info['date'] ][] = $info;
        }
        return $data;
    }

    public function dailyRecords() {
        if ($this->status == self::APPLY)
            return [];
        $clocks = $this->clocks;
        $data = [];
        $weekly = ["日", "一", "二", "三", "四", "五", "六"];
        $emptyInfo = [
            "type"   => 0,
            "num"    => 0,
            "numStr" => '未工作',
            "status" => UserJobDaily::NOTHING,
            "msg"    => "",
            "items"  => (array)[]
        ];
        // 未打过卡
        if (empty($clocks)) {
            $info = $emptyInfo;
            $info['date'] = date("m-d");
            $info['weekly'] = "星期" . $weekly[ date("w") ];
            $data[] = $info;
            return $data;
        }
        $records = $this->dayHours;
        $dailyData = [];
        foreach ($records as $record) {
            $info = $record->info();
            $date = strlen($info['date']) > 5 ? $info['date'] : date("Y-") . $info['date'];
            $dailyData[ $date ] = $info;
        }
        $lastTime = 0; // 上一次打卡时间
        $lastDate = ""; // 上一次打卡的日期
        $todaySecond = 0; // 今天的工作时长
        $dayInfo = [
            "items" => []
        ];
        $thisYear = date("Y");
        foreach ($clocks as $index => $clock) {
            $tmpDate = date("Y-m-d", $clock->created_at);
            if ($tmpDate == $lastDate) {
                $dayInfo['items'][] = $clock->info();
                if ($clock->type == UserClock::TYPE_END) {
                    $todaySecond += $clock->created_at - $lastTime;
                }
            } else {
                $lastDateStart = strtotime($lastDate);
                $tmpDateStart = strtotime($tmpDate);
                if ($index > 0) {
                    $dayInfo['num'] = round($todaySecond / 3600, 1);
                    $dayInfo['numStr'] = $dayInfo['num'] . "小时";
                    $dayInfo['date'] = substr($lastDate, 0, 4) == $thisYear ? substr($lastDate, 5) : $lastDate;
                    $dayInfo['weekly'] = "星期" . $weekly[ date("w", $lastDateStart) ];
                    $dailyInfo = isset($dailyData[ $lastDate ]) ? $dailyData[ $lastDate ] : [];
                    $data[] = ArrayHelper::merge($emptyInfo, $dayInfo, $dailyInfo);

                    $dayInfo = [
                        "items" => [
                            $clock->info()
                        ]
                    ];
                    $todaySecond = 0;
                    if ($tmpDateStart - $lastDateStart > 24 * 3600) {
                        $Arr = self::fillNoClockDaily($lastDateStart, $tmpDateStart, $emptyInfo, $dailyData);
                        foreach ($Arr as $a) {
                            $data[] = $a;
                        }
                    }
                } else {
                    $dayInfo['items'][] = $clock->info();
                }
            }
            $lastTime = $clock->created_at;
            $lastDate = $tmpDate;
        }
        // 最后一天的
        $lastDateStart = strtotime($lastDate);
        $dayInfo['num'] = round($todaySecond / 3600, 1);
        $dayInfo['numStr'] = $dayInfo['num'] . "小时";
        $dayInfo['date'] = substr($lastDate, 0, 4) == $thisYear ? substr($lastDate, 5) : $lastDate;
        $dayInfo['weekly'] = "星期" . $weekly[ date("w", $lastDateStart) ];
        $dailyInfo = isset($dailyData[ $lastDate ]) ? $dailyData[ $lastDate ] : [];
        $data[] = ArrayHelper::merge($emptyInfo, $dayInfo, $dailyInfo);
        // 最后一天到今天的 -- 先判断是否还在上班
        if ($this->status != self::ON)
            return $data;
        $tmpDateStart = strtotime(date("Y-m-d", strtotime("+1 day")));
        if ($tmpDateStart - $lastDateStart > 24 * 3600) {
            $arr2 = self::fillNoClockDaily($lastDateStart, $tmpDateStart, $emptyInfo, $dailyData);
            foreach ($arr2 as $a) {
                $data[] = $a;
            }
        }
        return $data;
    }

    public static function fillNoClockDaily($lastStart, $thisStart, $emptyArr, $dailyData) {
        $data = [];
        $thisYear = date("Y");
        $weekly = ["日", "一", "二", "三", "四", "五", "六"];
        for ($tTime = $lastStart + 24 * 3600; $tTime < $thisStart; $tTime += 24 * 3600) {
            $tDate = date("Y-m-d", $tTime);
            $tmpInfo = [
                "date"   => substr($tDate, 0, 4) == $thisYear ? substr($tDate, 5) : $tDate,
                "weekly" => "星期" . $weekly[ date("w", $tTime) ],
            ];
            $dailyInfo = isset($dailyData[ $tDate ]) ? $dailyData[ $tDate ] : [];
            $data[] = ArrayHelper::merge($emptyArr, $tmpInfo, $dailyInfo);
        }
        return $data;
    }

    public function todayClocks() {
        $start = strtotime(date("Y-m-d"));
        $end = $start + 24 * 3600 - 1;
        $clocks = UserClock::find()->where(["uid" => $this->uid, "uJid" => $this->id])->andWhere(["BETWEEN", "created_at", $start, $end])->orderBy("created_at desc")->all();
        /* @var $clocks UserClock[] */
        $daily = UserJobDaily::findOne(["uJid" => $this->id, "uid" => $this->uid, "date" => date("Ymd")]);
        $items = [];
        $lastTime = 0;
        $num = 0;
        foreach ($clocks as $clock) {
            $items[] = $clock->info();
            if ($clock->type == UserClock::TYPE_END) {
                $num += $clock->created_at - $lastTime;
            }
            $lastTime = $clock->created_at;
        }
        if ($daily) {
            $data = $daily->info();
            $data['items'] = $items;
        } else {
            $hour = round($num / 3600);
            $data = [
                'id'     => $this->id,
                "date"   => date("Y-m-d"),
                "type"   => UserJobDaily::TYPE_HOUR,
                "num"    => $hour,
                "numStr" => $hour . "小时",
                "status" => UserJobDaily::NOTHING,
                "msg"    => '',
                "items"  => $items
            ];
        }
        return $data;
    }
}