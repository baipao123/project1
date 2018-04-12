<?php
namespace frontend\controllers;

use common\tools\Tool;
use frontend\models\UserIdentify;
use Yii;
use yii\web\Controller;

class BaseController extends Controller
{
    public function beforeAction($action) {
        if ($action->id !== "program-login") {
            echo Tool::reJson(null, "请先登录", Tool::NEED_LOGIN);
            return false;
        }
        return parent::beforeAction($action);
    }

    //小程序登录
    public function actionProgramLogin() {
        if (!Yii::$app->user->isGuest) {
            return Tool::reJson(1);
        }
        $code = Yii::$app->request->post("code");
        $user = UserIdentify::findUserByProgramCode($code);
        if ($user) {
            Yii::$app->user->login($user, 3600 * 2);
            return Tool::reJson(1);
        } else
            return Tool::reJson(null, "登录失败", Tool::FAIL);
    }

    public function actionLogout() {
        Yii::$app->user->logout();
        return Tool::reJson(1);
    }

    //小程序 录入用户的基本信息
    public function actionProgramUser() {
        $openId = Yii::$app->request->post("openId");
        $user = Yii::$app->user->identity;
        /* @var $user UserIdentify */
        if ($user->openId != $openId)
            return Tool::reJson(null, "用户信息不匹配失败", Tool::FAIL);
        $rawData = Yii::$app->request->post("rawData");
        $signature = Yii::$app->request->post("signature");
        if ($user->verifyUserInfo($rawData, $signature))
            return Tool::reJson(1);
        return Tool::reJson(null, "用户信息不匹配失败", Tool::FAIL);
    }
}
