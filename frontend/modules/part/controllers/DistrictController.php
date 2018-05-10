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

    public function actionAll($pid = 1, $cid = 0, $aid = 0) {
        $cities = District::all($pid);
        $value = [0, 0];
        if (!empty($cid) || !empty($aid)) {
            foreach ($cities as $index => $city) {
                if (!empty($cid)) {
                    if ($city['cid'] == $cid) {
                        $value[0] = $index;
                        foreach ($city['areas'] as $i => $area) {
                            if ($area['aid'] == $aid) {
                                $value[1] = $i;
                                break;
                            }
                        }
                        break;
                    }
                } else {
                    foreach ($city['areas'] as $i => $area) {
                        if ($area['aid'] == $aid) {
                            $value = [$index, $i];
                            $cid = $city['cid'];
                            break;
                        }
                    }
                    if ($value != [0, 0])
                        break;
                }
            }
        }
        return Tool::reJson([
            "cities" => District::all($pid),
            "value" => $value,
            "cid" => $cid,
            "aid" => $aid,
        ]);
    }

}