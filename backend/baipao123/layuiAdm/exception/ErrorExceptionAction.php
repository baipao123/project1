<?php
/**
 * Created by PhpStorm.
 * User: huangchen
 * Date: 2018/4/14
 * Time: 下午2:23
 */

namespace backend\baipao123\layuiAdm\exception;

use Yii;
use yii\web\ErrorAction;

class ErrorExceptionAction extends ErrorAction
{
    public function beforeRun() {
        return parent::beforeRun();
    }

    public function run() {
//        echo 404;exit;
        $controller = Yii::$app->controller;
        if ($this->findException()->getMessage() == "Page not found.")
            return $controller->renderContent($controller->renderFile(__DIR__ . "/views/404.php"));
        else
            return $controller->renderContent($controller->renderFile(__DIR__ . "/views/500.php"));

    }

}