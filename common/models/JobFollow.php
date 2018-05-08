<?php
/**
 * Created by PhpStorm.
 * User: huangchen
 * Date: 2018/4/12
 * Time: 下午8:31
 */

namespace common\models;


class JobFollow extends \common\models\base\JobFollow
{
    const Follow = 1;
    const CancelFollow = 2;

    public static function toggle($uid, $jid) {
        if (empty($uid) || empty($jid))
            return false;
        if (self::deleteAll(["uid" => $uid, "jid" => $jid]) > 0)
            return 2;
        $j = new self;
        $j->uid = $uid;
        $j->jid = $jid;
        $j->created_at = time();
        $r =  $j->save() ? 1 : false;
        return $r;
    }

}