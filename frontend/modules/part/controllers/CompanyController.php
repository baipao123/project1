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
use common\models\UserJobDaily;
use common\tools\Tool;
use common\tools\WxApp;
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
        $res = Job::saveJob($_POST, $this->getPost("tmp", 0) > 0, $jid);
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
        $newJob->id = "";
        $newJob->jobNo = "";
        $newJob->jobNo = $newJob->getJobNoFromRedis();
        $newJob->start_at = date("Ymd");
        $newJob->end_at = date("Ymd");
        $newJob->status = Job::OFF;
        if ($newJob->save())
            return Tool::reJson(["jid" => $newJob->attributes['id']]);
        else {
            Yii::warning($newJob->errors, "保存Job出错");
            return Tool::reJson(null, "复制招聘信息失败", Tool::FAIL);
        }

    }

    public function actionToggleJob() {
        $jid = $this->getPost("jid", 0);
        $job = Job::findOne($jid);
        if (!$job || $job->uid != Yii::$app->user->id || $job->status == Job::DEL)
            return Tool::reJson(null, "未找到原招聘信息", Tool::FAIL);
        if ($job->status == Job::TMP)
            return Tool::reJson(null, "草稿状态不能发布", Tool::FAIL);
        $company = $job->company;
        if (!$company || $company->status != Company::STATUS_PASS)
            return Tool::reJson(null, "您的企业资料未审核通过，暂时无法操作招聘信息", Tool::FAIL);
        $status = $this->getPost("status", 0);
        if (!in_array($status, [Job::OFF, Job::ON, Job::FULL, Job::DEL]))
            return Tool::reJson(null, "非法请求", Tool::FAIL);
        if ($status == Job::DEL && UserHasJob::find()->where(["jid" => $jid])->exists())
            return Tool::reJson(null, "本岗位已有人报名，无法删除", Tool::FAIL);
        $statusText = [
            Job::OFF  => "已设为未发布",
            Job::ON   => "发布成功",
            Job::FULL => "设为已招满成功",
            Job::DEL  => "删除成功"
        ];
        $job->status = $status;
        if ($job->save()) {
            if ($status == Job::FULL) {
                UserHasJob::updateAll(["jid" => $jid, "status" => UserHasJob::APPLY], ["status" => UserHasJob::REFUSE, "updated_at" => time()]);
            }
            return Tool::reJson(1, $statusText[ $status ]);
        }
        Yii::warning($job->errors, "保存Job失败");
        return Tool::reJson(null, "失败", Tool::FAIL);
    }

    public function actionJobList($page = 1, $limit = 10) {
        $user = $this->getUser();
        if (!$user || $user->type == User::TYPE_USER)
            return Tool::reJson(null, "您没有招聘权限", Tool::FAIL);
        $company = $user->company;
        if (!$company || $company->status != Company::STATUS_PASS)
            return Tool::reJson(null, "您没有招聘权限", Tool::FAIL);
        return Tool::reJson($company->jobs(0, $page, $limit));
    }

    public function actionJobsSample($page = 1, $limit = 10) {
        $user = $this->getUser();
        if (!$user || $user->type == User::TYPE_USER)
            return Tool::reJson(null, "您没有招聘权限", Tool::FAIL);
        $company = $user->company;
        if (!$company || $company->status != Company::STATUS_PASS)
            return Tool::reJson(null, "您没有招聘权限", Tool::FAIL);
        return Tool::reJson(["list" => $company->jobs(1, $page, $limit)]);
    }

    public function actionInfo() {
        $company = Company::findOne(["uid" => $this->user_id()]);
        return Tool::reJson([
            "company" => $company ? $company->info() : (object)[]
        ]);
    }


    public function actionVerifyUser() {
        $string = $this->getPost("code");
        list($uJid, $jid, $key, $uid) = explode(";", $string);
        $uJob = UserHasJob::findOne($uJid);
        if (!$uJob || $uJob->auth_key != $key || $uJob->uid != $uid || $uJob->jid != $jid || $uJob->status != UserHasJob::APPLY || $uJob->job->uid != $this->user_id())
            return Tool::reJson(null, "二维码不正确或已过期", Tool::FAIL);
        $job = $uJob->job;
        if ($this->getPost("type", 0) == 1) {
            $uJob->status = UserHasJob::ON;
            $uJob->auth_at = time();
            $text = "确认入职成功";
        } else {
            $uJob->status = UserHasJob::REFUSE;
            $uJob->updated_at = time();
            $text = "拒绝入职成功";
            $job->formId = $this->getPost("formId");
        }
        $uJob->cFormId = $this->getPost("formId");
        $uJob->save();
        $user = $uJob->user;
        $user->sendTpl(WxApp::TPL_Job_Apply_Result, [
            $uJob->user->realname,
            date("Y年m月d日 H:i:s", $uJob->created_at),
            $job->name,
            $job->company->typeStr(),
            $this->getPost("type", 0) == 1 ? "审核通过，已入职" : "被拒绝"
        ], $uJob->formId, "/pages/job/getInfo?id=" . $uJid);
        return Tool::reJson(1, $text);
    }

    public function actionRefuseJob() {
        $user = $this->getUser();
        if (!$user || $user->type == User::TYPE_USER)
            return Tool::reJson(null, "您没有招聘权限", Tool::FAIL);
        $company = $user->company;
        if (!$company || $company->status != Company::STATUS_PASS)
            return Tool::reJson(null, "您没有招聘权限", Tool::FAIL);
        $uJid = $this->getPost("uJid", 0);
        $uJob = UserHasJob::findOne($uJid);
        if (!$uJob || $uJob->job->uid != $this->user_id())
            return Tool::reJson(null, "未找到入职申请", Tool::FAIL);
        $uJob->status = UserHasJob::REFUSE;
        $uJob->cFormId = $this->getPost("formId");
        $uJob->updated_at = time();
        $text = "拒绝入职成功";
        $uJob->save();
        $job = $uJob->job;
        $job->formId = $this->getPost("formId");
        $user->sendTpl(WxApp::TPL_Job_Apply_Result, [
            $uJob->user->realname,
            date("Y年m月d日 H:i:s", $uJob->created_at),
            $job->name,
            $job->company->typeStr(),
            "被拒绝"
        ], $uJob->formId, "/pages/job/getInfo?id=" . $uJid);
        return Tool::reJson(1, $text);
    }

    public function actionQrUser() {
        $string = $this->getPost("code");
        list($uJid, $jid, $key, $uid) = explode(";", $string);
        $uJob = UserHasJob::findOne($uJid);
        if (!$uJob || $uJob->auth_key != $key || $uJob->uid != $uid || $uJob->jid != $jid || $uJob->job->uid != $this->user_id())
            return Tool::reJson(null, "二维码不正确或已过期", Tool::FAIL);
        return Tool::reJson(["uJob" => $uJob->user()]);
    }

    public function actionTimeVerifyList($page = 1, $limit = 10) {
        $uid = $this->user_id();
        $user = $this->getUser();
        if (!$user || $user->type <= User::TYPE_USER)
            return Tool::reJson(null, "无此权限", Tool::FAIL);
        $company = Company::findOne(["uid" => $uid]);
        if (!$company)
            return Tool::reJson(null, "无此权限", Tool::FAIL);
        return Tool::reJson(["list" => $company->dailyRecords(UserJobDaily::PROVIDE, $page, $limit)]);
    }
}