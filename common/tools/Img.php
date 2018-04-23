<?php
/**
 * Created by PhpStorm.
 * User: huangchen
 * Date: 2018/4/23
 * Time: 下午7:57
 */

namespace common\tools;

use Yii;

class Img
{
    public static function format($path) {
        if (strpos("http", $path) == 0)
            return $path;
        return Yii::$app->params['qiniu']['doamin'] . ltrim($path, "/");
    }
}