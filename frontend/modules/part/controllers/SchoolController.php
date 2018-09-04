<?php
/**
 * Created by PhpStorm.
 * User: huangchen
 * Date: 2018/9/4
 * Time: 下午9:21
 */

namespace frontend\modules\part\controllers;


use common\models\School;
use common\tools\Tool;
use frontend\controllers\BaseController;
use yii\helpers\ArrayHelper;

class SchoolController extends BaseController
{
    public function actionList($city_id = 0, $school_id = 0) {
        //        $user = $this->getUser();
        $data = School::getList($city_id);
        $data = array_merge([["id" => 0, "name" => "请选择学校"]], $data);
        $school_ids = ArrayHelper::getColumn($data, "id");
        $index = array_search($school_id, $school_ids);
        return Tool::reJson([
            "schools" => $data,
            "index"   => $index,
            "school_ids" => $school_ids,
        ]);
    }
}