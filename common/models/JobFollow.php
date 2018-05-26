<?php
/**
 * Created by PhpStorm.
 * User: huangchen
 * Date: 2018/4/12
 * Time: 下午8:31
 */

namespace common\models;

use Yii;

class JobFollow extends \common\models\base\JobFollow
{
    const Follow = 1;
    const CancelFollow = 2;

    public static function toggle($uid, $jid) {
        if (empty($uid) || empty($jid))
            return false;
        if (self::deleteAll(["uid" => $uid, "jid" => $jid]) > 0) {
            Yii::$app->db->createCommand("UPDATE `job` SET `follow_num`=`follow_num`+1 WHERE `id`=:id", [":id" => $jid])->execute();
            return 2;
        }
        $j = new self;
        $j->uid = $uid;
        $j->jid = $jid;
        $j->created_at = time();
        $r =  $j->save() ? 1 : false;
        if ($r)
            Yii::$app->db->createCommand("UPDATE `job` SET `follow_num`=`follow_num`-1 WHERE `id`=:id", [":id" => $jid])->execute();
        return $r;
    }

}