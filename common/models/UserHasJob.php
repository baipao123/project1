<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018-04-12
 * Time: 17:21:20
 */

namespace common\models;


class UserHasJob extends \common\models\base\UserHasJob
{
    const APPLY = 0;
    const ON = 1;
    const VERIFY = 2;
    const REFUSE = 3;
    const END = 10;

    public static function isOn($uid, $jid) {
        return self::find()->where(["uid"=>$uid,"jid"=>$jid,"status"=>self::ON])->exists();
    }
}