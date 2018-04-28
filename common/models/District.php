<?php
/**
 * Created by PhpStorm.
 * User: huangchen
 * Date: 2018/4/27
 * Time: 下午10:47
 */

namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;

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

    public static function all($pid = 1,$isAll = true) {
        $records = self::find()->where(['status' => self::ON])->orderBy("cid ASC")->all();
        /* @var $records self[] */
        $data = [];
        foreach ($records as $district) {
            if ($district->cid == 0) {
                $data[ $district->id ]['cid'] = $district->id;
                $data[ $district->id ]['city'] = $district->name;
                if($isAll)
                    $data[$district->id]['areas'][] = [
                        "aid" => 0,
                        "area" => "--全部--"
                    ];
            } else {
                $data[ $district->cid ]['areas'][] = [
                    "aid"  => $district->id,
                    "area" => $district->name
                ];
            }
        }
        return array_values($data);
    }

}