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
    public static function toggle($uid, $jid) {
        if (empty($uid) || empty($jid))
            return false;
        if (self::deleteAll(["uid" => $uid, "jid" => $jid]) > 0)
            return true;
        $j = new self;
        $j->uid = $uid;
        $j->jid = $jid;
        $j->created_at = time();
        return $j->save();
    }

}