<?php
/**
 * Created by PhpStorm.
 * User: huangchen
 * Date: 2018/4/27
 * Time: 下午10:52
 */

namespace frontend\modules\part\controllers;


use common\models\District;
use common\tools\Tool;
use frontend\controllers\BaseController;

class DistrictController extends BaseController
{
    public function actionCities($pid = 1) {
        return Tool::reJson(["cities" => District::cities($pid)]);
    }

    public function actionAreas($cid = 0) {
        return Tool::reJson(["cities" => District::areas($cid)]);
    }

    public function actionAll($pid=1){
    	  return Tool::reJson(["cities" => District::all($pid)]);
    }

}