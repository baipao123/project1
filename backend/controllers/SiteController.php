<?php
namespace backend\controllers;

use Yii;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;

/**
 * Site controller
 */
class SiteController extends BaseController
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['login', 'error'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout', 'index'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Login action.
     * @param string $url
     * @return string
     */
    public function actionLogin($url = "")
    {
        if (!Yii::$app->user->isGuest) {
            return empty($url) ? $this->goHome() : $this->redirect($url);
        }
        $error = "123123";
        $username = "";
        if (Yii::$app->request->isPost) {
            $username = Yii::$app->request->post("username", "");
            $identify = \backend\models\UserIdentify::findByUsername($username);
            if (!$identify)
                $error = "用户名不存在";
            else if ($identify->checkPassword(Yii::$app->request->post("password", ""))) {
                if (Yii::$app->user->login($identify, Yii::$app->request->post("remember", 0) > 0 ? 3600 * 24 * 30 : 3600))
                    return empty($url) ? $this->goHome() : $this->redirect($url);
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
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }
}
