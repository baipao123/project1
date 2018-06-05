<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018-04-12
 * Time: 16:59:34
 */

namespace frontend\modules\part\controllers;

use common\models\UserClock;
use common\models\UserHasJob;
use common\tools\Tool;
use common\tools\WxApp;
use Yii;

class ClockController extends \frontend\controllers\BaseController
{
    public function actionInfo() {

    }

    public function actionClock() {
        $uJid = $this->getPost("uJid", 0);
        $uid = $this->user_id();
        $uJob = UserHasJob::findOne($uJid);
        if (!$uJob || $uJob->status != UserHasJob::ON)
            return Tool::reJson(null, "工作已结束或仍未开始", Tool::FAIL);
        $position = $this->getPost("pos", "");
        //        if (empty($position))
        //            return Tool::reJson(null, "请开启定位设置", Tool::FAIL);
        $type = $this->getPost("type", 0);
        $lastClock = UserClock::lastClock($uJid);
        if ($lastClock && time() - $lastClock->created_at < 300)
            return Tool::reJson(null, "2次打卡最少间隔5分钟", Tool::FAIL);

        if (empty($type))
            $type = $lastClock && $lastClock->type == UserClock::TYPE_START && $lastClock->created_at > strtotime(date("Y-m-d")) ? UserClock::TYPE_END : UserClock::TYPE_START;

        $clock = new UserClock;
        $clock->type = $type;
        $clock->uid = $uid;
        $clock->uJid = $uJid;
        $clock->jid = $uJob->jid;
        $clock->position = $position;
        $clock->latitude = $this->getPost("lat", "");
        $clock->longitude = $this->getPost("long", "");
        $clock->accuracy = $this->getPost("acc", "");
        $clock->msg = $this->getPost("msg", "");
        $clock->created_at = time();
        $clock->save();
        $user = $this->getUser();
        $user->sendTpl(WxApp::TPL_Sign, [$user->realname, date("Y年m月d日 H:i:s"), $uJob->job->name], $this->getPost("formId"), "/pages/job/getInfo?id=" . $uJid);
        return Tool::reJson(["info" => $clock->info()], "打卡成功");
    }

    public function actionJobDaily($uJid = 0) {
        $uid = $this->user_id();
        $uJob = UserHasJob::findOne($uJid);
        if (!$uJob || $uJob->uid != $uid)
            return Tool::reJson(null, "未发现任务报名记录", Tool::FAIL);
        return Tool::reJson(["clocks" => $uJob->dailyRecords()]);
    }
}