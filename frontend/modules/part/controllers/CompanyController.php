<?php
/**
 * Created by PhpStorm.
 * User: huangchen
 * Date: 2018/4/12
 * Time: 下午7:40
 */

namespace frontend\modules\part\controllers;

use common\models\Company;
use common\models\UserHasJob;
use common\tools\Tool;
use Yii;

class CompanyController extends \frontend\controllers\BaseController
{
    public function actionJoin() {

    }

    public function actionEditJoin() {

    }

    public function actionVerifyUser() {
        $string = $this->getPost("code");
        list($jid, $key, $uid) = explode(";", $string);
        $job = UserHasJob::findOne(["jid" => $jid, "uid" => $uid]);
        if (!$job || $job->status != UserHasJob::APPLY || $job->auth_key != $key)
            return Tool::reJson(null, "二维码不正确或已过期", Tool::FAIL);
        $job->status = UserHasJob::ON;
        $job->save();
        return Tool::reJson(1);
    }

}