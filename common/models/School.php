<?php
/**
 * Created by PhpStorm.
 * User: huangchen
 * Date: 2018/9/4
 * Time: 下午8:47
 */

namespace common\models;

use Yii;

/**
 * @property District $city
 */
class School extends \common\models\base\School
{
    const ON = 1;
    const OFF = 0;
    const DEL = 2;

    public function getCity() {
        return $this->hasOne(District::className(), ["id" => "city_id"]);
    }

    public static function getList($cid = 0) {
        return Yii::$app->db->cache(function () use ($cid) {
            return self::find()->where(["city_id" => $cid, "status" => self::ON])->select("id,name")->asArray()->all();
        }, 30);
    }

}