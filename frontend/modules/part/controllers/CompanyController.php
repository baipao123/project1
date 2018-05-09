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
        if ($res === true)
            return Tool::reJson(1, "您的信息已提交审核");
        return Tool::reJson(null, $res === false ? "导入数据失败，请稍后重试" : $res, Tool::FAIL);
    }

    public function actionEdit() {
        $res = Company::Bind(Yii::$app->user->id, $_POST, false);
        if ($res === true)
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
        $res = Job::saveJob($_POST, $this->getPost("tmp", 0) > 0,$jid);
        if ($res === true)
            return Tool::reJson(["jid" => $jid], "发布招聘信息成功");
        return Tool::reJson(null, $res === false ? "发布招聘信息失败，请稍后重试" : $res, Tool::FAIL);
    }

    public function actionEditJob($jid = 0) {
        $user = $this->getUser();
        if (!$user || $user->type == User::TYPE_USER)
            return Tool::reJson(null, "您无权保存招聘信息", Tool::FAIL);
        $company = $user->company;
        if (!$company || $company->status != Company::STATUS_PASS)
            return Tool::reJson(null, "您的企业资料未审核通过，暂时无法保存招聘信息", Tool::FAIL);
        $jid = $_GET['jid'];
        $res = Job::saveJob($_POST, $this->getPost("tmp", 0) > 0, $jid);
        if ($res === true)
            return Tool::reJson(["jid" => $jid], "修改招聘信息成功");
        return Tool::reJson(null, $res === false ? "修改招聘信息失败，请稍后重试" : $res, Tool::FAIL);
    }

    public function actionCopyJob() {
        $user = $this->getUser();
        if (!$user || $user->type == User::TYPE_USER)
            return Tool::reJson(null, "您无权保存招聘信息", Tool::FAIL);
        $company = $user->company;
        if (!$company || $company->status != Company::STATUS_PASS)
            return Tool::reJson(null, "您的企业资料未审核通过，暂时无法保存招聘信息", Tool::FAIL);
        $jid = $this->getPost("jid", 0);
        $job = Job::findOne($jid);
        if (!$job || $job->uid != Yii::$app->user->id)
            return "未找到原招聘信息";
        $newJob = new Job;
        $newJob->attributes = $job->attributes;
        $newJob->start_at = 0;
        $newJob->end_at = 0;
        $newJob->status = Job::TMP;
        $newJob->save();
        return Tool::reJson($newJob->info(), "复制招聘信息失败");
    }

    public function actionToggleJob() {
        $jid = $this->getPost("jid", 0);
        $job = Job::findOne($jid);
        if (!$job || $job->uid != Yii::$app->user->id || $job->status == Job::DEL)
            return Tool::reJson(null, "未找到原招聘信息", Tool::FAIL);
        if ($job->status == Job::TMP)
            return Tool::reJson(null, "草稿状态不能上架", Tool::FAIL);
        $company = $job->company;
        if (!$company || $company->status != Company::STATUS_PASS)
            return Tool::reJson(null, "您的企业资料未审核通过，暂时无法操作招聘信息", Tool::FAIL);
        $status = $this->getPost("status", 0);
        if (!in_array($status, [Job::OFF, Job::ON]))
            return Tool::reJson(null, "非法请求", Tool::FAIL);
        $job->status = $status;
        if ($job->save())
            return Tool::reJson(1, $status == Job::ON ? "上架成功" : "下架成功");
        Yii::warning($job->errors, "保存Job失败");
        return Tool::reJson(null, "失败", Tool::FAIL);
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

    public function actionInfo() {
        $company = Company::findOne(["uid" => $this->user_id()]);
        return Tool::reJson([
            "company" => $company ? $company->info() : (object)[]
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