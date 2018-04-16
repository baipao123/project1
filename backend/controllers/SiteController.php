<?php
namespace backend\controllers;

use layuiAdm\Init;
use Yii;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

/**
 * Site controller
 */
class SiteController extends BaseController
{
    public $enableCsrfValidation = false;

    /**
     * {@inheritdoc}
     */
    public function actions() {
        return ArrayHelper::merge(parent::actions(), Init::SiteActions("黄琛的后台"), [

        ]);
    }

    /**
     * Login action.
     * @return string
     */
    public function actionLogin(){
        if (!Yii::$app->user->isGuest) {
            return $this->goBack();
        }
        $error = "";
        $username = "";
        if (Yii::$app->request->isPost) {
            $username = Yii::$app->request->post("username", "");
            $identify = \backend\models\UserIdentify::findByUsername($username);
            if (!$identify)
                $error = "用户名不存在";
            else if ($identify->checkPassword(Yii::$app->request->post("password", ""))) {
                if (Yii::$app->user->login($identify, Yii::$app->request->post("remember", 0) > 0 ? 3600 * 24 * 30 : 3600))
                    return $this->goBack();
                else
                    $error = "登陆失败";
            } else
                $error = "密码错误";
        }
        return $this->renderPartial('login', [
            "username" => $username,
            "error"    => $error,
        ]);
    }

    /**
     * Logout action.
     *
     * @return string
     */
    public function actionLogout() {
        Yii::$app->user->logout();
        return $this->redirect("/site/login");
    }
}
