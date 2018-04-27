<?php
/**
 * Created by PhpStorm.
 * User: huangchen
 * Date: 2018/4/27
 * Time: 下午10:47
 */

namespace common\models;

use Yii;

class District extends \common\models\base\District
{
    const ON = 1;
    const OFF = 0;
    const DEL = 2;

    public static function cities($pid = 1) {
        return self::find()->where(['pid' => $pid, 'cid' => 0, 'status' => self::ON])->select("id,name")->asArray()->all();
    }

    public static function areas($cid) {
        if (empty($cid))
            return [];
        return self::find()->where(['cid' => $cid, 'status' => self::ON])->select("id,name")->asArray()->all();
    }

}