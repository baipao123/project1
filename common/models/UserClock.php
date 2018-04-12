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
    const TYPE_START = 0;
    const TYPE_POSITION = 1;
    const TYPE_END = 2;

    public static function record($uid, $jid, $date = "") {
        $start_at = empty($date) ? strtotime(date("Y-m-d")) : strtotime(date("Y-m-d", strtotime($date)));
        $end_at = $start_at + 3600 * 24 - 1;
        return self::find()->where(["uid" => $uid, "jid" => $jid])->andWhere(["BETWEEN", "created_at", $start_at, $end_at])->all();
    }

    public static function userIsStart($uid, $jid) {
        $last = self::find()->where(["uid" => $uid, "jid" => $jid])->orderBy("created_at desc")->one();
        /* @var $last self */
        if ($last && $last->type != self::TYPE_END)
            return true;
        return false;
    }

}