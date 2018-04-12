<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018-04-12
 * Time: 16:59:34
 */

namespace frontend\controllers;

use common\models\UserClock;
use common\models\UserHasJob;
use common\tools\Tool;
use Yii;

class ClockControllers extends BaseController
{
    public function actionInfo() {

    }

    public function actionClock() {
        $jid = Yii::$app->request->post("jid", 0);
        $uid = Yii::$app->user->id;
        if (!UserHasJob::isOn($uid, $jid))
            return Tool::reJson(null, "工作已结束或仍未开始", Tool::FAIL);
        $position = Yii::$app->request->post("pos", "");
        if (empty($position))
            return Tool::reJson(null, "请开启定位设置", Tool::FAIL);
        $clock = new UserClock;
        $clock->type = Yii::$app->request->post("type", UserClock::userIsStart($uid, $jid) ? UserClock::TYPE_END : UserClock::TYPE_START);
        $clock->uid = $uid;
        $clock->jid = $jid;
        $clock->position = $position;
        $clock->latitude = Yii::$app->request->post("lat", "");
        $clock->longitude = Yii::$app->request->post("long", "");
        $clock->accuracy = Yii::$app->request->post("acc", "");
        $clock->msg = Yii::$app->request->post("msg", "");
        $clock->created_at = time();
        $clock->save();
        return Tool::reJson(1);
    }
}