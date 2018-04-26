<?php
/**
 * Created by PhpStorm.
 * User: huangchen
 * Date: 2018/4/12
 * Time: 下午7:40
 */

namespace frontend\modules\part\controllers;

use common\models\Company;
use common\models\Job;
use common\models\User;
use common\models\UserHasJob;
use common\tools\Tool;
use Yii;

class CompanyController extends \frontend\controllers\BaseController
{
    public function actionJoin() {
        $res = Company::Bind(Yii::$app->user->id, $_POST, true);
        if ($res == true)
            return Tool::reJson(1, "您的企业信息已提交审核");
        return Tool::reJson(null, $res === false ? "导入数据失败，请稍后重试" : $res, Tool::FAIL);
    }

    public function actionEdit() {
        $res = Company::Bind(Yii::$app->user->id, $_POST, false);
        if ($res == true)
            return Tool::reJson(1);
        return Tool::reJson(null, $res === false ? "导入数据失败，请稍后重试" : $res, Tool::FAIL);
    }

    public function actionAddJob() {
        $user = $this->getUser();
        if (!$user || $user->type == User::TYPE_USER)
            return Tool::reJson(null, "您无权发布招聘信息", Tool::FAIL);
        $company = $user->company;
        if (!$company || $company->status != Company::STATUS_PASS)
            return Tool::reJson(null, "您的企业资料未审核通过，暂时无法发布招聘信息", Tool::FAIL);

        $job = new Job;
        $job->uid = $this->user_id();

    }

    public function actionEditJob() {

    }

    public function actionJobList() {
        $user = $this->getUser();
        if (!$user || $user->type == User::TYPE_USER)
            return Tool::reJson([]);
        $company = $user->company;
        if (!$company || $company->status != Company::STATUS_PASS)
            return Tool::reJson([]);
        return Tool::reJson($company->jobs());
    }

    public function actionInfo(){
        $user = $this->getUser();
        if (!$user || $user->type == User::TYPE_USER)
            return Tool::reJson([]);
        $company = $user->company;
        if (!$company || $company->status != Company::STATUS_PASS)
            return Tool::reJson([]);
        return Tool::reJson([
            "user"=>$user->info(),
            "company" => $company->info(),
        ]);
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