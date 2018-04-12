<?php
namespace frontend\controllers;

use common\tools\Tool;
use frontend\models\UserIdentify;
use Yii;
use yii\web\Controller;

class SiteController extends Controller
{
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
}
