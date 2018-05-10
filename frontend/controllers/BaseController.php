<?php

namespace frontend\controllers;

use common\tools\QiNiu;
use common\tools\Tool;
use frontend\actions\base\ErrorAction;
use frontend\models\UserIdentify;
use Yii;
use yii\helpers\ArrayHelper;
use yii\rest\Controller;


class BaseController extends Controller
{
    public function actions() {
        return ArrayHelper::merge(parent::actions(), [
            "error" => ErrorAction::className()
        ]);
    }

    public function beforeAction($action) {
        if (Yii::$app->user->isGuest && !in_array($action->id, ["app-login", "error", "qiniu-token"])) {
            echo Tool::reJson(null, "请先登录", Tool::NEED_LOGIN);
            return false;
        }
        Yii::$app->response->format = 'json';
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

    public function user_id(){
        return Yii::$app->user->id;
    }

    //小程序登录
    public function actionAppLogin() {
        $code = $this->getPost("code");
        $user = UserIdentify::findUserByAppCode($code);
        if ($user) {
            Yii::$app->user->login($user, 3600 * 2);
            $user = Yii::$app->user->identity;
            /* @var $user UserIdentify */
            return Tool::reJson(["user" => $user->info()]);
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
            return Tool::reJson(["user" => $user->info()]);
        return Tool::reJson(null, "用户信息不匹配失败", Tool::FAIL);
    }

    public function actionQiniuToken() {
        $token = QiNiu::getToken();
        return json_encode(["uptoken" => $token]);
    }
}
