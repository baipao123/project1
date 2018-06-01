<?php
/**
 * Created by PhpStorm.
 * User: huangchen
 * Date: 2018/4/12
 * Time: 下午8:01
 */

namespace frontend\modules\part\controllers;

use common\models\Job;
use common\models\JobFollow;
use common\models\User;
use common\models\UserHasJob;
use common\models\UserJobDaily;
use common\tools\StringHelper;
use common\tools\Tool;
use Yii;

class JobController extends \frontend\controllers\BaseController
{
    public function actionInfo($id = 1) {
        $job = Job::findOne($id);
        if (!$job)
            return Tool::reJson(null, "岗位不存在", Tool::FAIL);
        if ($this->getUser()->isCompany() && $this->user_id() != $job->uid)
            return Tool::reJson(null, "您未发布此岗位", Tool::FAIL);
        $job->addViewNum();
        return Tool::reJson(["job" => $job->info($this->user_id()), "company" => $job->company->info()]);
    }

    public function actionApply() {
        if ($this->getuser()->type != User::TYPE_USER)
            return Tool::reJson(null, "您无权报名", Tool::FAIL);
        $jid = $this->getPost("jid", 0);
        $job = Job::findOne($jid);
        if (!$job || $job->status != Job::ON)
            return Tool::reJson(null, "工作不存在", Tool::FAIL);
        if (UserHasJob::findOne(["uid" => Yii::$app->user->id, "jid" => $jid]))
            return Tool::reJson(null, "您已经报名该工作了", Tool::FAIL);
        $record = new UserHasJob;
        $record->formId = $this->getPost("formId", "");
        $record->uid = $this->user_id();
        $record->jid = $jid;
        $record->auth_key = StringHelper::nonce(8);
        $record->status = UserHasJob::APPLY;
        $record->created_at = time();
        if (!$record->save()) {
            Yii::warning($record->errors, "添加UserHasJob失败");
            return Tool::reJson(null, "报名失败", Tool::FAIL);
        }
        //TODO 模板消息
        return Tool::reJson(["id" => $record->attributes['id']]);
    }

    public function actionForbid() {
        $uJid = $this->getPost("uJid", 0);
        $userJob = UserHasJob::findOne($uJid);
        if (!$userJob || $userJob->status != UserHasJob::APPLY)
            return Tool::reJson(null, "工作不存在", Tool::FAIL);
        $job = $userJob->job;
        if (!$job || $job->status != Job::ON || $job->uid != Yii::$app->user->id)
            return Tool::reJson(null, "工作不存在", Tool::FAIL);

        $userJob->status = UserHasJob::REFUSE;
        $userJob->content = $this->getPost("reason", "");
        $userJob->updated_at = time();
        if (!$userJob->save()) {
            Yii::warning($userJob->errors, "保存UserHasJob失败");
            return Tool::reJson(null, "拒绝失败", Tool::FAIL);
        }
        //TODO 模板消息
        return Tool::reJson(1);

    }

    public function actionList($cid = -1, $aid = -1, $text = "", $page = 1, $limit = 10) {
        $user = $this->getUser();
        if ($user->isCompany()) {
            $list = $user->company ? $user->company->jobs(0, $page, $limit) : [];
        } else {
            // 默认与全部
            if (empty($cid) && empty($aid)) {
                $cid = $user->city_id;
                $aid = $user->area_id;
            }
            $list = (array)Job::getList($this->user_id(), $text, $cid, $aid, $page, $limit);
        }
        return Tool::reJson(["list" => $list]);
    }

    public function actionTimeUp() {
        $type = $this->getPost("type", 0);
        $num = $this->getPost("num", 0);
        if (in_array($type, [UserJobDaily::TYPE_HOUR, UserJobDaily::TYPE_COUNT]) && intval($num) != $num)
            return Tool::reJson(null, "工时应为整数", Tool::FAIL);
        $uJid = $this->getPost("uJid", 0);
        $date = $this->getPost("date", date("Ymd"));
        $date = str_replace("-", "", $date);
        if ($date < 10000)
            $date = date("Y") . $date;
        $daily = UserJobDaily::findOne(["uJid" => $uJid, "date" => $date]);
        if ($daily && $daily->status == UserJobDaily::PASS)
            return Tool::reJson(null, "当天工时已审核通过，无法修改", Tool::FAIL);
        $uJob = UserHasJob::findOne($uJid);
        if (!$uJob || $uJob->uid != Yii::$app->user->id || in_array($uJob->status, [UserHasJob::REFUSE, UserHasJob::APPLY]))
            return Tool::reJson(null, "未查到工作记录", Tool::FAIL);
        $daily or $daily = new UserJobDaily;
        $daily->uid = Yii::$app->user->id;
        $daily->uJid = $uJid;
        $daily->jid = $uJob->jid;
        $daily->cid = $uJob->job->uid;
        $daily->type = $type;
        $daily->date = $date;
        $daily->num = in_array($type, [UserJobDaily::TYPE_HOUR, UserJobDaily::TYPE_COUNT]) ? $num : 1;
        $daily->status = UserJobDaily::PROVIDE;
        $daily->isNewRecord ? $daily->created_at = time() : $daily->updated_at = time();
        $msg = $this->getPost("msg");
        $daily->msg = $msg;
        if ($daily->save())
            //TODO 模板消息
            return Tool::reJson(["info" => $daily->info()]);
        else {
            Yii::warning($daily->errors, "UserJobDaily 保存出错");
            return Tool::reJson(null, "工时上报失败", Tool::FAIL);
        }
    }

    public function actionTimePass() {
        if ($this->getUser()->type != User::TYPE_COMPANY)
            return Tool::reJson(null, "无此操作权限", Tool::FAIL);
        $did = $this->getPost("did");
        $daily = UserJobDaily::findOne($did);
        if (!$daily || $daily->status != UserJobDaily::PROVIDE)
            return Tool::reJson(null, "无需处理", Tool::FAIL);
        $job = Job::findOne($daily->jid);
        if (!$job || $job->uid != Yii::$app->user->id)
            return Tool::reJson(null, "无需处理", Tool::FAIL);
        $daily->status = UserJobDaily::PASS;
        $daily->updated_at = time();
        $daily->msg = '';
        $daily->save();
        $uJob = $daily->uJob;
        if ($daily->type == 0)
            $uJob->worktime_0 += $daily->num;
        elseif ($daily->type == 1)
            $uJob->worktime_1 += 1;
        elseif ($daily->type == 2)
            $uJob->worktime_2 += 1;
        elseif ($daily->type == 3)
            $uJob->worktime_3 += $daily->num;
        if (!$uJob->save())
            Yii::warning($uJob->errors, "保存UserHasJob失败");

        return Tool::reJson(1, "已通过工时");
    }

    public function actionTimeRefuse() {
        if ($this->getUser()->type != User::TYPE_COMPANY)
            return Tool::reJson(null, "无此操作权限", Tool::FAIL);
        $did = $this->getPost("did");
        $daily = UserJobDaily::findOne($did);
        if (!$daily || $daily->status != UserJobDaily::PROVIDE)
            return Tool::reJson(null, "无需处理", Tool::FAIL);
        $job = Job::findOne($daily->jid);
        if (!$job || $job->uid != Yii::$app->user->id)
            return Tool::reJson(null, "无需处理", Tool::FAIL);

        $daily->status = UserJobDaily::REFUSE;
        $daily->updated_at = time();
        $daily->msg = $this->getPost("msg", '');
        $daily->save();
        //TODO 模板消息
        return Tool::reJson(1, "已拒绝工时");
    }

    public function actionFollow() {
        $jid = $this->getPost("jid", 0);
        $res = JobFollow::toggle($this->user_id(), $jid);
        if ($res)
            return Tool::reJson(1, $res == JobFollow::CancelFollow ? "取消关注成功" : "关注成功");
        return Tool::reJson(null, "操作失败", Tool::FAIL);
    }

    public function actionMyJob($uJid = 0) {
        $uid = $this->user_id();
        $uJob = UserHasJob::findOne($uJid);
        if (!$uJob || $uJob->uid != $uid)
            return Tool::reJson(null, "未发现任务报名记录", Tool::FAIL);
        return Tool::reJson(["job" => $uJob->job->sampleInfo(), "uJob" => $uJob->info(), "clocks" => $uJob->todayClocks()]);
    }

    public function actionStatus() {
        $uJid = $this->getPost("uJid", 0);
        $uJob = UserHasJob::findOne($uJid);
        if (!$uJob || $uJob->uid != $this->user_id())
            return Tool::reJson(null, "未发现任务报名记录", Tool::FAIL);
        return Tool::reJson(["status" => $uJob->status]);
    }

    public function actionUsers($id = 0, $page = 1, $limit = 10, $status = UserHasJob::ON) {
        $user = $this->getUser();
        if ($user->type <= User::TYPE_USER)
            return Tool::reJson(null, '无权查看岗位员工', Tool::FAIL);
        $job = Job::findOne($id);
        if (!$job || $job->status == Job::DEL)
            return Tool::reJson(null, '岗位不存在或未发布', Tool::FAIL);
        if ($job->uid != $this->user_id())
            return Tool::reJson(null, '无权查看此岗位员工', Tool::FAIL);
        return Tool::reJson(["users" => $job->users($status, $page, $limit), "job" => $job->sampleInfo()]);
    }

    public function actionExpireJob() {
        $jid = $this->getPost("jid", 0);
        $user = $this->getUser();
        if ($user->type <= User::TYPE_USER)
            return Tool::reJson(null, '无权操作岗位', Tool::FAIL);
        $job = Job::findOne($jid);
        if (!$job || $job->status == Job::DEL)
            return Tool::reJson(null, '岗位不存在或已删除', Tool::FAIL);
        $job->status = Job::END;
        $job->save();
        UserHasJob::updateAll(["jid" => $jid, "status" => [UserHasJob::WORKING, UserHasJob::ON]], ["status" => UserHasJob::END, "updated_at" => time()]);
        return Tool::reJson(1, "操作成功");
    }

    public function actionExpireUserJob() {
        $uJid = $this->getPost("uJid", 0);
        $user = $this->getUser();
        if ($user->type != User::TYPE_USER)
            return Tool::reJson(null, '无权操作此工作岗位', Tool::FAIL);
        $uJob = UserHasJob::findOne($uJid);
        if (!$uJob || $uJob->status != UserHasJob::ON || $uJob->uid != $this->user_id())
            return Tool::reJson(null, '无权操作或已经操作过了', Tool::FAIL);
        $uJob->status = UserHasJob::END;
        $uJob->updated_at = time();
        $uJob->save();
        return Tool::reJson(1, "操作成功");
    }
}