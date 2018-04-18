<?php
namespace frontend\controllers;

use common\tools\Tool;
use frontend\models\UserIdentify;
use Yii;
use yii\web\Controller;

class BaseController extends Controller
{
    public function beforeAction($action) {
//        if ($action->id !== "program-login") {
//            echo Tool::reJson(null, "请先登录", Tool::NEED_LOGIN);
//            return false;
//        }
        return parent::beforeAction($action);
    }

    public function getPost($name = "", $defaultValue = "") {
        return Yii::$app->request->post($name, $defaultValue);
    }

    /**
     * @return UserIdentify
     */
    public function getUser() {
        if (!Yii::$app->user->isGuest)
            return null;
        return Yii::$app->user->identity;
    }

    //小程序登录
    public function actionAppLogin() {
        if (!Yii::$app->user->isGuest) {
            return Tool::reJson(1);
        }
        $code = $this->getPost("code");
        $user = UserIdentify::findUserByAppCode($code);
        if ($user) {
            Yii::$app->user->login($user, 3600 * 2);
            $user = Yii::$app->user->identity;
            /* @var $user UserIdentify*/
            return Tool::reJson([
                "userType"     => $user ? $user->type : -1,
                "needUserInfo" => $user && empty($user->nickname) ? true : false
            ]);
        } else
            return Tool::reJson(null, "登录失败", Tool::FAIL);
    }

    public function actionLogout() {
        Yii::$app->user->logout();
        return Tool::reJson(1);
    }

    //小程序 录入用户的基本信息
    public function actionAppUser() {
        $openId = $this->getPost("openId");
        $user = Yii::$app->user->identity;
        /* @var $user UserIdentify */
        if ($user->openId != $openId)
            return Tool::reJson(null, "用户信息不匹配失败", Tool::FAIL);
        $rawData = $this->getPost("rawData");
        $signature = $this->getPost("signature");
        if ($user->verifyUserInfo($rawData, $signature))
            return Tool::reJson(1);
        return Tool::reJson(null, "用户信息不匹配失败", Tool::FAIL);
    }

    public function actionE(){
        echo 1;
    }
}
