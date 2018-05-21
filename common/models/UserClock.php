<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018-04-12
 * Time: 16:53:59
 */

namespace common\models;

use Yii;

class UserClock extends \common\models\base\UserClock
{
    const TYPE_START = 1;
    const TYPE_POSITION = 2;// 中途定位
    const TYPE_END = 3;

    public static function record($uid, $jid, $date = "") {
        $start_at = empty($date) ? strtotime(date("Y-m-d")) : strtotime(date("Y-m-d", strtotime($date)));
        $end_at = $start_at + 3600 * 24 - 1;
        return self::find()->where(["uid" => $uid, "jid" => $jid])->andWhere(["BETWEEN", "created_at", $start_at, $end_at])->all();
    }

    /**
     * @param $uJid
     * @return self
     */
    public static function lastClock($uJid) {
        return self::find()->where(["uJid" => $uJid])->orderBy("created_at desc")->one();
    }

    public function info($hasPosition = false) {
        $info = [
            "id"        => $this->id,
            "type"      => $this->type,
            "timestamp" => $this->created_at,
            "date"      => date("Y-m-d", $this->created_at),
            "time"      => date("H:i:s", $this->created_at),
            "isToday"   => date("Y-m-d", $this->created_at) == date("Y-m-d")
        ];
        if ($hasPosition){
            $info['longitude'] = $this->longitude;
            $info['latitude'] = $this->latitude;
        }
        return $info;
    }

}