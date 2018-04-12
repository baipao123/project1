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
use Yii;

class ClockController extends \frontend\controllers\BaseController
{
    public function actionInfo() {

    }

    public function actionClock() {
        $jid = $this->getPost("jid", 0);
        $uid = Yii::$app->user->id;
        if (!UserHasJob::isOn($uid, $jid))
            return Tool::reJson(null, "工作已结束或仍未开始", Tool::FAIL);
        $position = $this->getPost("pos", "");
        if (empty($position))
            return Tool::reJson(null, "请开启定位设置", Tool::FAIL);
        $clock = new UserClock;
        $clock->type = Yii::$app->request->post("type", UserClock::userIsStart($uid, $jid) ? UserClock::TYPE_END : UserClock::TYPE_START);
        $clock->uid = $uid;
        $clock->jid = $jid;
        $clock->position = $position;
        $clock->latitude = $this->getPost("lat", "");
        $clock->longitude = $this->getPost("long", "");
        $clock->accuracy = $this->getPost("acc", "");
        $clock->msg = $this->getPost("msg", "");
        $clock->created_at = time();
        $clock->save();
        return Tool::reJson(1);
    }
}