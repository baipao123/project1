<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018-04-11
 * Time: 14:41:38
 */

namespace backend\controllers;

use Yii;
use common\models\Admin;

class UserController extends BaseController
{
    public $basicActions = ["reset-pass"];

    public function actionResetPass() {
        $admin = Admin::findOne(Yii::$app->user->id);
        if (!$admin || $admin->status != Admin::STATUS_ACTIVE) {
            Yii::$app->session->setFlash("danger", "用户不存在或已被禁用");
        } else if (Yii::$app->request->isPost) {
            $oldPwd = Yii::$app->request->post("oldPwd", "");
            $newPwd = Yii::$app->request->post("newPwd", "");
            $newPwd2 = Yii::$app->request->post("newPwd2", "");
            if (!$admin->checkPassword($oldPwd))
                Yii::$app->session->setFlash("warning","原密码错误");
            else if ($newPwd != $newPwd2)
                Yii::$app->session->setFlash("warning","新密码不一致");
            else if ($newPwd == $oldPwd)
                Yii::$app->session->setFlash("warning", "新密码要求与原密码不一致");
            else{
                $admin->password = $admin->setPassword($newPwd);
                $admin->save();
                Yii::warning($admin->errors);
                Yii::$app->session->setFlash("success","修改密码成功");
            }
        }
        return $this->render("reset-pass", [
            "admin" => $admin,
        ]);
    }

    public function actionResetUserPass() {

    }
}