<?php
/**
 * Created by PhpStorm.
 * User: huangchen
 * Date: 2018/5/27
 * Time: 下午4:24
 */

namespace frontend\modules\part\controllers;


use common\models\Slider;
use common\tools\Tool;
use frontend\controllers\BaseController;

class SliderController extends BaseController
{
    public function actionIndex() {
        return Tool::reJson(["list" => Slider::index()]);
    }
}