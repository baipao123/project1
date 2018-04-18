<?php
namespace frontend\controllers;

use common\tools\Tool;
use frontend\actions\base\ErrorAction;
use frontend\models\UserIdentify;
use Yii;
use yii\helpers\ArrayHelper;
use yii\base\Controller;


class BaseController extends Controller
{
    public function actions() {
        return ArrayHelper::merge(parent::actions(), [
            "error" => ErrorAction::className()
        ]);
    }

    public function beforeAction($action) {
        if (Yii::$app->user->isGuest && !in_array($action->id, ["app-login", "error"])) {
            echo Tool::reJson(null, "请先登录", Tool::NEED_LOGIN);
            return false;
        }
        return parent::beforeAction($action);
    }

    public function getPost($name = "", $defaultValue = "") {
        return Yii::$app->request->post($name, $defaultValue);
    }

    /**
     * @return UserIdentify
     */
    public function getUser() {
        return Yii::$app->user->getIdentity();
    }

    //小程序登录
    public function actionAppLogin() {
//        if (!Yii::$app->user->isGuest) {
//            $user = $this->getUser();
//            return Tool::reJson([
//                "userType"     => $user ? $user->type : -1,
//                "needUserInfo" => $user && empty($user->nickname) ? true : false
//            ]);
//        }
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
        $user = $this->getUser();
        /* @var $user UserIdentify */
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
