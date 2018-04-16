<?php
/**
 * Created by PhpStorm.
 * User: huangchen
 * Date: 2018/4/15
 * Time: 下午2:22
 */

namespace backend\baipao123\layuiAdm;

use backend\baipao123\layuiAdm\actions\ErrorExceptionAction;
use Yii;
use backend\baipao123\layuiAdm\actions\IndexAction;
use backend\baipao123\layuiAdm\actions\MenuAction;

class Init
{
    public static function SiteActions($admName = "") {
        return [
            'error' => [
                'class' => ErrorExceptionAction::className(),
            ],
            'index' => [
                'class'     => IndexAction::className(),
                'homeTitle' => $admName
            ],
            'menu'  => [
                'class'  => MenuAction::className(),
                'module' => Yii::$app->controller->module
            ],
        ];
    }

    public static function Layout($action) {
        if (in_array($action->id, Yii::$app->controller->basicActions))
            Yii::$app->controller->layout = "@backend/baipao123/layuiAdm/views/layouts/basic.php";
        else
            Yii::$app->controller->layout = "@backend/baipao123/layuiAdm/views/layouts/main.php";
    }

}