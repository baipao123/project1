<?php
/**
 * Created by PhpStorm.
 * User: huangchen
 * Date: 2018/4/15
 * Time: 下午1:57
 */

namespace backend\baipao123\layuiAdm\layouts\assets;

class AdmAssets extends \yii\web\AssetBundle
{
    public $basePath = "";

    public $baseUrl;

    public $css = [
        "/layui/css/layui.css",
        "/layui/css/layui.hc.css",
    ];
    public $js = [
        "/jQuery.3.3.1.min.js",
        "/layui/layui.all.js",
    ];
    public $depends = [
    ];

    public function init() {
        $this->baseUrl = \Yii::$app->assetManager->publish(dirname(__FILE__) . '/../../assets')[1];
    }
}