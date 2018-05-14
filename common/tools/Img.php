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
    public static function format($path, $width = 0, $height = 0, $isHost = false) {
        if (strpos($path, "http") === 0)
            return $path;
        $src = $isHost ? Yii::$app->params['qiniu']['domain'] . "/" . ltrim($path, "/") : ltrim($path, "/");
        if (empty($width) && empty($height))
            return $src;
        if (is_int(strpos($src, "?")))
            $src .= "|imageslim|";//ios 竖线无法识别，要用%7C
        else
            $src .= "?imageslim|";
        //纯图片缩放，不需要裁切
        if (empty($width) || empty($height)) {
            //图片只能缩小，不能放大
            //$src .= "imageView2/2/w/{$width}/h/{$height}/interlace/1";
            if (empty($width))
                $src .= "imageMogr2/format/png/thumbnail/x{$height}";
            elseif (empty($height))
                $src .= "imageMogr2/format/png/thumbnail/{$width}x";
            return $src;
        } else {
            //图片只能缩小，不能放大
            //$src .= "imageView2/1/w/{$width}/h/{$height}/interlace/1";
            //先缩放
            $src .= "imageMogr2/format/png/thumbnail/!{$width}x{$height}r";
            //再裁切
            $src .= "/gravity/Center/crop/{$width}x{$height}";
            $src .= "/interlace/1";
            return $src;
        }
    }
}